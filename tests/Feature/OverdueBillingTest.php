<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Application;
use App\Models\Semester;
use App\Models\User;
use App\Models\BankSlip;
use App\Models\MailTemplate;
use App\Models\Event;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyOverdueBankSlip;

class OverdueBillingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        foreach (['Administrador', 'Secretaria', 'Docente'] as $role) {
            if (!\Spatie\Permission\Models\Role::where('name', $role)->exists()) {
                \Spatie\Permission\Models\Role::create(['name' => $role]);
            }
        }

        // Required by senhaunica-socialite / laravel-usp-theme menu parsing
        if (!\Spatie\Permission\Models\Permission::where('name', 'admin')->where('guard_name', 'senhaunica')->exists()) {
            \Spatie\Permission\Models\Permission::create(['name' => 'admin', 'guard_name' => 'senhaunica']);
        }
    }

    private function createAdminUser()
    {
        $user = User::factory()->create();
        $user->assignRole('Administrador');
        return $user;
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_overdue_index_shows_only_overdue_bank_slips()
    {
        Mail::fake();
        \nusoap_client::$mockStatus = 'E';

        $admin = $this->createAdminUser();
        $semester = Semester::factory()->create([
            'year' => date('Y'),
            'period' => '1º Semestre',
        ]);

        // 1. Application with a PAID fee -> should NOT appear
        $appPaid = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);
        BankSlip::factory()->create([
            'applicationID' => $appPaid->id,
            'relativoA' => 'Taxa de Inscrição',
            'statusBoletoBancario' => 'P',
            'dataVencimentoBoleto' => now()->subDays(5)->format('d/m/Y'),
        ]);

        // 2. Application with a valid ISSUED fee (future due date) -> should NOT appear
        $appIssued = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);
        BankSlip::factory()->create([
            'applicationID' => $appIssued->id,
            'relativoA' => 'Taxa de Inscrição',
            'statusBoletoBancario' => 'E',
            'dataVencimentoBoleto' => now()->addDays(5)->format('d/m/Y'),
        ]);

        // 3. Application with a REPLACED (Substituído) fee -> should NOT appear
        $appReplaced = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);
        BankSlip::factory()->create([
            'applicationID' => $appReplaced->id,
            'relativoA' => 'Taxa de Inscrição (Substituído)',
            'statusBoletoBancario' => 'E',
            'dataVencimentoBoleto' => now()->subDays(5)->format('d/m/Y'),
        ]);
        // And a new valid fee
        BankSlip::factory()->create([
            'applicationID' => $appReplaced->id,
            'relativoA' => 'Taxa de Inscrição',
            'statusBoletoBancario' => 'E',
            'dataVencimentoBoleto' => now()->addDays(5)->format('d/m/Y'),
        ]);

        // 4. Application with an OVERDUE fee -> SHOULD appear
        $appOverdue = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);
        BankSlip::factory()->create([
            'applicationID' => $appOverdue->id,
            'relativoA' => 'Taxa de Inscrição',
            'statusBoletoBancario' => 'E',
            'dataVencimentoBoleto' => now()->subDays(5)->format('d/m/Y'),
            'valorDocumento' => 140.00,
        ]);

        // 5. Application with EMPTY due date -> should NOT appear and must not crash
        $appEmptyDate = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);
        BankSlip::factory()->create([
            'applicationID' => $appEmptyDate->id,
            'relativoA' => 'Taxa de Inscrição',
            'statusBoletoBancario' => 'E',
            'dataVencimentoBoleto' => '',
        ]);

        $response = $this->actingAs($admin)->get(route('applications.overdue_index'));

        $response->assertStatus(200);
        $response->assertSee($appOverdue->protocol);
        $response->assertDontSee($appPaid->protocol);
        $response->assertDontSee($appIssued->protocol);
        $response->assertDontSee($appReplaced->protocol);
        $response->assertDontSee($appEmptyDate->protocol);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_send_overdue_reminders_sends_mails_and_creates_events()
    {
        Mail::fake();
        \nusoap_client::$mockStatus = 'E';

        $admin = $this->createAdminUser();
        $semester = Semester::factory()->create([
            'year' => date('Y'),
            'period' => '1º Semestre',
        ]);

        $app1 = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);
        BankSlip::factory()->create([
            'applicationID' => $app1->id,
            'relativoA' => 'Taxa de Inscrição',
            'statusBoletoBancario' => 'E',
            'dataVencimentoBoleto' => now()->subDays(5)->format('d/m/Y'),
        ]);

        $app2 = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);
        BankSlip::factory()->create([
            'applicationID' => $app2->id,
            'relativoA' => 'Taxa de Inscrição',
            'statusBoletoBancario' => 'E',
            'dataVencimentoBoleto' => now()->subDays(5)->format('d/m/Y'),
        ]);

        MailTemplate::factory()->create([
            'mail_class' => 'NotifyOverdueBankSlip',
            'active' => true,
            'sending_frequency' => 'Manual',
            'subject' => 'Cobrança',
            'body' => 'Você possui boleto vencido.',
        ]);

        $response = $this->actingAs($admin)
                         ->post(route('applications.sendOverdueReminders'), [
                             'application_ids' => [$app1->id, $app2->id],
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('alert-success');

        Mail::assertQueued(NotifyOverdueBankSlip::class, function ($mail) use ($app1) {
            return $mail->application->id === $app1->id;
        });

        Mail::assertQueued(NotifyOverdueBankSlip::class, function ($mail) use ($app2) {
            return $mail->application->id === $app2->id;
        });

        $this->assertDatabaseHas('events', [
            'applicationID' => $app1->id,
            'name' => 'Cobrança Manual',
        ]);

        $this->assertDatabaseHas('events', [
            'applicationID' => $app2->id,
            'name' => 'Cobrança Manual',
        ]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_confirm_manual_payment_updates_bank_slip_and_creates_event()
    {
        Mail::fake();
        \nusoap_client::$mockStatus = 'E';

        $admin = $this->createAdminUser();
        $semester = Semester::factory()->create([
            'year' => date('Y'),
            'period' => '1º Semestre',
        ]);

        $app = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);
        $bankSlip = BankSlip::factory()->create([
            'applicationID' => $app->id,
            'relativoA' => 'Taxa de Inscrição',
            'statusBoletoBancario' => 'E',
            'dataVencimentoBoleto' => now()->subDays(5)->format('d/m/Y'),
            'valorDocumento' => 140.00,
        ]);

        $response = $this->actingAs($admin)
                         ->post(route('bankslips.confirmManualPayment', $bankSlip));

        $response->assertRedirect();
        $response->assertSessionHas('alert-success');

        $bankSlip->refresh();
        // Status bancário NÃO deve ser alterado
        $this->assertEquals('E', $bankSlip->statusBoletoBancario);
        // Pagamento manual deve estar confirmado
        $this->assertTrue((bool) $bankSlip->manual_payment_confirmed);
        $this->assertNotNull($bankSlip->manual_payment_confirmed_at);
        $this->assertEquals($admin->name, $bankSlip->manual_payment_confirmed_by);

        $this->assertDatabaseHas('events', [
            'applicationID' => $app->id,
            'name' => 'Pagamento Manual Confirmado',
        ]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_confirm_manual_payment_rejects_substituted_slip()
    {
        Mail::fake();
        \nusoap_client::$mockStatus = 'E';

        $admin = $this->createAdminUser();
        $semester = Semester::factory()->create([
            'year' => date('Y'),
            'period' => '1º Semestre',
        ]);

        $app = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);
        $bankSlip = BankSlip::factory()->create([
            'applicationID' => $app->id,
            'relativoA' => 'Taxa de Inscrição (Substituído)',
            'statusBoletoBancario' => 'E',
            'dataVencimentoBoleto' => now()->subDays(5)->format('d/m/Y'),
        ]);

        $response = $this->actingAs($admin)
                         ->post(route('bankslips.confirmManualPayment', $bankSlip));

        $response->assertRedirect();
        $response->assertSessionHas('alert-danger');

        $bankSlip->refresh();
        $this->assertEquals('E', $bankSlip->statusBoletoBancario);
    }
}
