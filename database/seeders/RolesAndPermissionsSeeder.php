<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::firstOrCreate(['name' => 'editar usuario']);
        Permission::firstOrCreate(['name' => 'visualizar semestres']);
        Permission::firstOrCreate(['name' => 'visualizar inscrições']);
        Permission::firstOrCreate(['name' => 'visualizar triagens']);
        Permission::firstOrCreate(['name' => 'visualizar reuniões de consulta']);
        Permission::firstOrCreate(['name' => 'Editar E-mails']);


        Role::firstOrCreate(['name' => 'Secretaria'])
        ->givePermissionTo('visualizar semestres')
        ->givePermissionTo('visualizar inscrições')
        ->givePermissionTo('visualizar triagens')
        ->givePermissionTo('visualizar reuniões de consulta');

        Role::firstOrCreate(['name' => 'Docente'])
        ->givePermissionTo('visualizar semestres')
        ->givePermissionTo('visualizar inscrições')
        ->givePermissionTo('visualizar triagens')
        ->givePermissionTo('visualizar reuniões de consulta');


        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $adminRole->syncPermissions(Permission::where('guard_name', 'web')->get());
    }
}
