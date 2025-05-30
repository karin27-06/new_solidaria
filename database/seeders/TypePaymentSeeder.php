<?php

namespace Database\Seeders;

use App\Models\TypePayment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypePaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TypePayment::create([
            'name' => 'Efectivo',
            'status' => true,
        ]);
        TypePayment::create([
            'name' => 'Tarjeta',
            'status' => true,
        ]);
        TypePayment::create([
            'name' => 'Yape',
            'status' => true,
        ]);
        TypePayment::create([
            'name' => 'Plin',
            'status' => true,
        ]);
    }
}
