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
            ZoneSeeder::class,
            TypeVoucherSeeder::class,
            LocalSeeder::class,
            UserSeeder::class,
            SupplierSeeder::class,
            CategorySeeder::class,
            LaboratorySeeder::class,
            DoctorSeeder::class,
            ClientTypeSeeder::class,
            ProductSeeder::class,
            TypeMovementSeeder::class,
            GuideSeeder::class,
            CustomerSeeder::class,
            TypePaymentSeeder::class,
            SaleSeeder::class,
        ]);
    }
}
