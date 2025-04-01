<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $administrador = Role::create(['name' => 'administrador']);
        $vendedor = Role::create(['name' => 'vendedor']);
        $almacen = Role::create(['name' => 'almacen']);
        $auditor = Role::create(['name' => 'auditor']);
    }
}
