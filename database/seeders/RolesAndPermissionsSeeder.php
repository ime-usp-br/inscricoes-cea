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


        Role::firstOrCreate(['name' => 'Secretaria'])
        ->givePermissionTo('visualizar semestres')
        ->givePermissionTo('visualizar inscrições')
        ->givePermissionTo('visualizar triagens');

        Role::firstOrCreate(['name' => 'Docente'])
        ->givePermissionTo('visualizar semestres')
        ->givePermissionTo('visualizar inscrições')
        ->givePermissionTo('visualizar triagens');


        Role::firstOrCreate(['name' => 'Administrador'])
            ->givePermissionTo(Permission::all());
    }
}
