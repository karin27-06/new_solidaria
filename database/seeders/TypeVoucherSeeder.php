<?php

namespace Database\Seeders;

use App\Models\TypeVoucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeVoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TypeVoucher::create([
            'code' => 1,
            'prefix' => 'FS',
            'name' => 'FACTURA',
        ]);
        TypeVoucher::create([
            'code' => 2,
            'prefix' => 'TS',
            'name' => 'TICKET',
        ]);
        TypeVoucher::create([
            'code' => 3,
            'prefix' => 'BS',
            'name' => 'BOLETA',
        ]);
        TypeVoucher::create([
            'code' => 7,
            'prefix' => 'NC',
            'name' => 'NOTA DE CREDITO',
        ]);
        TypeVoucher::create([
            'code' => 8,
            'prefix' => 'ND',
            'name' => 'NOTA DE DEBITO',
        ]);
        TypeVoucher::create([
            'code' => 9,
            'prefix' => 'GR',
            'name' => 'GUIA DE REMISION',
        ]);
        TypeVoucher::create([
            'code' => 10,
            'prefix' => 'AN',
            'name' => 'ANULACION',
        ]);
    }
}
