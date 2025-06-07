<?php

namespace Database\Seeders;

use App\Models\ClientType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClientType::create([
            'name' => 'DNI',
            'tipo_doc' => '1', // DNI
            'state' => true,
        ]);
        ClientType::create([
            'name' => 'RUC',
            'tipo_doc' => '6', // RUC
            'state' => true,
        ]);
    }
}
