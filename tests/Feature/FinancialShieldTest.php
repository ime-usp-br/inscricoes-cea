<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Application;
use App\Models\Semester;
use App\Models\User;
use App\Models\BankSlip;
use App\Models\Triage;
use App\Models\ConsultationMeeting;
use Illuminate\Support\Facades\Mail;
use Mockery;

class FinancialShieldTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_approved_triage_does_not_generate_boleto_when_project_fee_already_paid()
    {
        Mail::fake();
        \nusoap_client::$mockStatus = 'E';

        $docente = User::factory()->create();
        $docente->assignRole('Docente');

        $app = Application::factory()->create(['serviceType' => 'Projeto']);

        $triage = Triage::factory()->create([
            'applicationID' => $app->id,
            'date' => now()->addDays(3)->format('d/m/Y'),
            'hour' => '14:00',
            'meetingMode' => 'Remoto',
        ]);

        // Simular taxa de projeto PAGA
        BankSlip::factory()->create([
            'applicationID' => $app->id,
            'relativoA' => 'Taxa de Projeto',
            'statusBoletoBancario' => 'P',
            'dataVencimentoBoleto' => now()->addDays(5)->format('d/m/Y'),
        ]);

        $countBefore = BankSlip::where('applicationID', $app->id)->count();

        $response = $this->actingAs($docente)
                         ->patch(route('triages.informdecision', $triage), [
                             'decision' => 'Aprovado como projeto',
                             'note' => 'Nota teste',
                         ]);

        $countAfter = BankSlip::where('applicationID', $app->id)->count();

        $this->assertEquals($countBefore, $countAfter);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_approved_triage_generates_boleto_when_project_fee_not_paid()
    {
        Mail::fake();
        \nusoap_client::$mockStatus = 'E';

        $docente = User::factory()->create();
        $docente->assignRole('Docente');

        $app = Application::factory()->create(['serviceType' => 'Projeto']);

        $triage = Triage::factory()->create([
            'applicationID' => $app->id,
            'date' => now()->addDays(3)->format('d/m/Y'),
            'hour' => '14:00',
            'meetingMode' => 'Remoto',
        ]);

        $response = $this->actingAs($docente)
                         ->patch(route('triages.informdecision', $triage), [
                             'decision' => 'Aprovado como projeto',
                             'note' => 'Nota teste',
                         ]);

        $this->assertDatabaseHas('bank_slips', [
            'applicationID' => $app->id,
            'relativoA' => 'Taxa de Projeto',
            'valorDocumento' => '250.00',
        ]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_approved_consultation_does_not_generate_boleto_when_project_fee_already_paid()
    {
        Mail::fake();
        \nusoap_client::$mockStatus = 'E';

        $docente = User::factory()->create();
        $docente->assignRole('Docente');

        $app = Application::factory()->create(['serviceType' => 'Consulta']);

        $consultation = ConsultationMeeting::factory()->create([
            'applicationID' => $app->id,
            'date' => now()->addDays(3)->format('d/m/Y'),
            'hour' => '14:00',
            'meetingMode' => 'Remoto',
        ]);

        // Simular taxa de projeto PAGA
        BankSlip::factory()->create([
            'applicationID' => $app->id,
            'relativoA' => 'Taxa de Projeto',
            'statusBoletoBancario' => 'P',
            'dataVencimentoBoleto' => now()->addDays(5)->format('d/m/Y'),
        ]);

        $countBefore = BankSlip::where('applicationID', $app->id)->count();

        $response = $this->actingAs($docente)
                         ->patch(route('consultationmeetings.informdecision', $consultation), [
                             'decision' => 'Aprovado como projeto',
                             'note' => 'Nota teste',
                         ]);

        $countAfter = BankSlip::where('applicationID', $app->id)->count();

        $this->assertEquals($countBefore, $countAfter);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_approved_consultation_generates_boleto_when_project_fee_not_paid()
    {
        Mail::fake();
        \nusoap_client::$mockStatus = 'E';

        $docente = User::factory()->create();
        $docente->assignRole('Docente');

        $app = Application::factory()->create(['serviceType' => 'Consulta']);

        $consultation = ConsultationMeeting::factory()->create([
            'applicationID' => $app->id,
            'date' => now()->addDays(3)->format('d/m/Y'),
            'hour' => '14:00',
            'meetingMode' => 'Remoto',
        ]);

        $response = $this->actingAs($docente)
                         ->patch(route('consultationmeetings.informdecision', $consultation), [
                             'decision' => 'Aprovado como projeto',
                             'note' => 'Nota teste',
                         ]);

        $this->assertDatabaseHas('bank_slips', [
            'applicationID' => $app->id,
            'relativoA' => 'Taxa de Projeto',
            'valorDocumento' => '250.00',
        ]);
    }
}
