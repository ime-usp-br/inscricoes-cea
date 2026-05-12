<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Application;
use App\Models\Semester;
use App\Models\User;
use App\Models\BankSlip;
use Illuminate\Support\Facades\Mail;
use Mockery;

class FinancialReportTest extends TestCase
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

        if (!\Spatie\Permission\Models\Permission::where('name', 'admin')->where('guard_name', 'senhaunica')->exists()) {
            \Spatie\Permission\Models\Permission::create(['name' => 'admin', 'guard_name' => 'senhaunica']);
        }
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createAdminUser()
    {
        $user = User::factory()->create();
        $user->assignRole('Administrador');
        return $user;
    }

    private function createSecretariaUser()
    {
        $user = User::factory()->create();
        $user->assignRole('Secretaria');
        return $user;
    }

    private function createDocenteUser()
    {
        $user = User::factory()->create();
        $user->assignRole('Docente');
        return $user;
    }

    public function test_admin_can_access_financial_report_index()
    {
        $admin = $this->createAdminUser();
        $semester = Semester::factory()->create(['year' => date('Y'), 'period' => '1º Semestre']);
        $app = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);

        $response = $this->actingAs($admin)->get(route('financial-reports.index'));
        $response->assertStatus(200);
        $response->assertSee($app->protocol);
        $response->assertSee($app->CPFCNPJ);
        $response->assertSee($app->bdName);
    }

    public function test_secretaria_can_access_financial_report_index()
    {
        $sec = $this->createSecretariaUser();
        $semester = Semester::factory()->create(['year' => date('Y'), 'period' => '1º Semestre']);
        $app = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);

        $response = $this->actingAs($sec)->get(route('financial-reports.index'));
        $response->assertStatus(200);
        $response->assertSee($app->protocol);
        $response->assertSee($app->CPFCNPJ);
        $response->assertSee($app->bdName);
    }

    public function test_docente_cannot_access_financial_report_index()
    {
        $doc = $this->createDocenteUser();
        $semester = Semester::factory()->create(['year' => date('Y'), 'period' => '1º Semestre']);
        Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);

        $response = $this->actingAs($doc)->get(route('financial-reports.index'));
        $response->assertStatus(403);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_sync_endpoint_updates_pending_bank_slip_and_returns_json()
    {
        Mail::fake();
        \nusoap_client::$mockStatus = 'P';

        $admin = $this->createAdminUser();
        $semester = Semester::factory()->create(['year' => date('Y'), 'period' => '1º Semestre']);
        $app = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);

        $boleto = BankSlip::factory()->create([
            'applicationID' => $app->id,
            'relativoA' => 'Taxa de Inscrição',
            'statusBoletoBancario' => 'E',
            'dataVencimentoBoleto' => now()->addDays(5)->format('d/m/Y'),
        ]);

        $response = $this->actingAs($admin)
                         ->post(route('financial-reports.sync', $app));

        $response->assertStatus(200);
        $response->assertJson([
            'inscription' => 'Pago',
            'project' => 'Não Emitido',
            'complementary' => '—',
        ]);

        $boleto->refresh();
        $this->assertEquals('P', $boleto->statusBoletoBancario);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_sync_endpoint_returns_json_without_pending_slips()
    {
        Mail::fake();
        \nusoap_client::$mockStatus = 'E';

        $admin = $this->createAdminUser();
        $semester = Semester::factory()->create(['year' => date('Y'), 'period' => '1º Semestre']);
        $app = Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);

        $response = $this->actingAs($admin)
                         ->post(route('financial-reports.sync', $app));

        $response->assertStatus(200);
        $response->assertJson([
            'inscription' => 'Não Emitido',
            'project' => 'Não Emitido',
            'complementary' => '—',
        ]);
    }

    public function test_financial_report_exports_csv()
    {
        $admin = $this->createAdminUser();
        $semester = Semester::factory()->create(['year' => date('Y'), 'period' => '1º Semestre']);
        Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);

        $response = $this->actingAs($admin)->get(route('financial-reports.index', ['format' => 'csv']));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_financial_report_exports_excel()
    {
        $admin = $this->createAdminUser();
        $semester = Semester::factory()->create(['year' => date('Y'), 'period' => '1º Semestre']);
        Application::factory()->create(['semesterID' => $semester->id, 'serviceType' => 'Consulta']);

        $response = $this->actingAs($admin)->get(route('financial-reports.index', ['format' => 'excel']));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

}
