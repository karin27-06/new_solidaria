<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Zone::create([
            'name' => 'ALMACEN',
            'status' => true,
        ]);
        Zone::create([
            'name' => 'Tumbes',
            'status' => true,
        ]);
        Zone::create([
            'name' => 'Piura',
            'status' => true,
        ]);
        Zone::create([
            'name' => 'Lima',
            'status' => true,
        ]);
    }
}
