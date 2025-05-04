<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            LocalSeeder::class,
            UserSeeder::class,
            //SupplierSeeder::class,
            //CategorySeeder::class,
            //LaboratorySeeder::class,
            //DoctorSeeder::class,
            ZoneSeeder::class,
            ClientTypeSeeder::class,
            //ProductSeeder::class,
            TypeMovementSeeder::class,
            //GuideSeeder::class,
        ]);
    }
}
