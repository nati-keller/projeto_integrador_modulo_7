<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresaFiscalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mod7_empresa_fiscal')->insert([
            [
                'company_id'        => Mod4StubSeeder::EMPRESA_ACRIBLU_ID,
                'regime_tributario'  => 'SIMPLES_NACIONAL',
                'aliquota_simples'   => 0.0600, // 6% — faixa até R$ 180k/ano
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'company_id'        => Mod4StubSeeder::EMPRESA_FORNECEDOR_ID,
                'regime_tributario'  => 'LUCRO_REAL',
                'aliquota_simples'   => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);
    }
}
