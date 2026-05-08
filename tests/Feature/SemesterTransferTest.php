<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Application;
use App\Models\Semester;
use App\Models\User;
use App\Models\Triage;
use Mockery;

class SemesterTransferTest extends TestCase
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

    public function test_transfer_manual_when_next_semester_exists()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $current = Semester::factory()->create(['year' => 2025, 'period' => '1° Semestre']);
        $next = Semester::factory()->create(['year' => 2025, 'period' => '2° Semestre']);

        $app = Application::factory()->create([
            'semesterID' => $current->id,
            'serviceType' => 'Projeto',
            'status' => 'Aprovado como projeto',
        ]);

        $response = $this->actingAs($admin)
                         ->patch(route('applications.transferSemester', $app));

        $response->assertRedirect();
        $response->assertSessionHas('alert-success');

        $app->refresh();
        $this->assertEquals($next->id, $app->semesterID);
        $this->assertFalse($app->transfer_pending);
        $this->assertEquals('Aguardando agendamento da triagem', $app->status);

        $this->assertDatabaseHas('events', [
            'applicationID' => $app->id,
            'name' => 'Transferência de Semestre',
        ]);
    }

    public function test_transfer_manual_when_next_semester_does_not_exist()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $current = Semester::factory()->create(['year' => 2025, 'period' => '2° Semestre']);

        $app = Application::factory()->create([
            'semesterID' => $current->id,
            'serviceType' => 'Consulta',
            'status' => 'Aprovado como Consulta',
        ]);

        $response = $this->actingAs($admin)
                         ->patch(route('applications.transferSemester', $app));

        $app->refresh();
        $this->assertTrue($app->transfer_pending);
        $this->assertEquals($current->id, $app->semesterID);

        $this->assertDatabaseHas('events', [
            'applicationID' => $app->id,
            'name' => 'Transferência de Semestre Pendente',
        ]);
    }

    public function test_unauthorized_user_cannot_transfer()
    {
        $user = User::factory()->create();
        // Sem role de Administrador/Secretaria

        $app = Application::factory()->create();

        $response = $this->actingAs($user)
                         ->patch(route('applications.transferSemester', $app));

        $response->assertStatus(403);
    }

    public function test_creating_new_semester_migrates_pending_applications()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $oldSemester = Semester::factory()->create(['year' => 2025, 'period' => '1° Semestre']);

        $pendingApp = Application::factory()->create([
            'semesterID' => $oldSemester->id,
            'transfer_pending' => true,
            'serviceType' => 'Projeto',
            'status' => 'Aprovado como projeto',
        ]);

        $response = $this->actingAs($admin)->post(route('semesters.store'), [
            'year' => 2025,
            'period' => '2° Semestre',
            'started_at' => '01/03/2025',
            'finished_at' => '30/06/2025',
            'start_date_enrollments' => '01/01/2025',
            'end_date_enrollments' => '28/02/2025',
        ]);

        $response->assertRedirect('/semesters');

        $newSemester = Semester::where(['year' => 2025, 'period' => '2° Semestre'])->first();
        $pendingApp->refresh();

        $this->assertEquals($newSemester->id, $pendingApp->semesterID);
        $this->assertFalse($pendingApp->transfer_pending);
        $this->assertEquals('Aguardando agendamento da triagem', $pendingApp->status);

        $this->assertDatabaseHas('events', [
            'applicationID' => $pendingApp->id,
            'name' => 'Transferência de Semestre (Automática)',
        ]);
    }

    public function test_updating_existing_semester_does_not_migrate_pending()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $semester = Semester::factory()->create([
            'year' => 2025,
            'period' => '1° Semestre',
            'started_at' => '01/01/2025',
            'finished_at' => '30/06/2025',
        ]);

        $pendingApp = Application::factory()->create([
            'semesterID' => $semester->id,
            'transfer_pending' => true,
        ]);

        $response = $this->actingAs($admin)->patch(route('semesters.update', $semester), [
            'year' => 2025,
            'period' => '1° Semestre',
            'started_at' => '01/02/2025', // alterado
            'finished_at' => '30/06/2025',
            'start_date_enrollments' => '01/01/2025',
            'end_date_enrollments' => '31/01/2025',
        ]);

        $response->assertRedirect('/semesters');

        $pendingApp->refresh();
        $this->assertTrue($pendingApp->transfer_pending);
        $this->assertEquals($semester->id, $pendingApp->semesterID);
    }

    public function test_show_page_displays_old_data_badge_for_transferred_application()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrador');

        $semester = Semester::factory()->create([
            'year' => 2025,
            'period' => '1° Semestre',
        ]);

        $app = Application::factory()->create([
            'semesterID' => $semester->id,
            'serviceType' => 'Projeto',
        ]);

        // Triagem com data ANTERIOR à criação do semestre
        $triage = Triage::factory()->create([
            'applicationID' => $app->id,
            'date' => now()->subDays(10)->format('d/m/Y'),
        ]);

        $response = $this->actingAs($admin)
                         ->get(route('applications.show', $app));

        $response->assertStatus(200);
        $response->assertSee('Dados de semestre anterior');
    }
}
