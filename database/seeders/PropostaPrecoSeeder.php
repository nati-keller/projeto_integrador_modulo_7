<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropostaPrecoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mod7_propostas_precos')->insert([
            // 1. Margem Verde (Preço Calculado 8.56 < Estimado 9.50)
            [
                'id'                      => Str::uuid()->toString(),
                'edital_id'               => Mod4StubSeeder::EDITAL_1_ID,
                'empresa_id'              => Mod4StubSeeder::EMPRESA_ACRIBLU_ID,
                'item_descricao'          => 'Medalha Mista Acrílico e MDF 6,3cm',
                'custo_base'              => 4.40,
                'frete'                   => 0.50,
                'garantia'                => 0.10,
                'mao_de_obra'             => 0.80,
                'instalacao'              => 0.00,
                'impostos'                => 0.30,
                'margem_lucro'            => 0.10,
                'bdi_percentual'          => 0.2770,
                'preco_minimo_calculado'  => 8.56,
                'preco_estimado_pncp'     => 9.50,
                'margem_status'           => 'VERDE',
                'alerta_inexequibilidade' => false,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
            // 2. Margem Amarela (Empate: Preço Calculado = Estimado)
            [
                'id'                      => Str::uuid()->toString(),
                'edital_id'               => Mod4StubSeeder::EDITAL_2_ID,
                'empresa_id'              => Mod4StubSeeder::EMPRESA_ACRIBLU_ID,
                'item_descricao'          => 'Troféu Campeão Jogos 2025',
                'custo_base'              => 50.00,
                'frete'                   => 5.00,
                'garantia'                => 0.00,
                'mao_de_obra'             => 10.00,
                'instalacao'              => 0.00,
                'impostos'                => 5.00,
                'margem_lucro'            => 0.15,
                'bdi_percentual'          => 0.20,
                'preco_minimo_calculado'  => 96.60,
                'preco_estimado_pncp'     => 96.60,
                'margem_status'           => 'AMARELO',
                'alerta_inexequibilidade' => false,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
            // 3. Margem Vermelha (Preço Calculado > Estimado)
            [
                'id'                      => Str::uuid()->toString(),
                'edital_id'               => Mod4StubSeeder::EDITAL_3_ID,
                'empresa_id'              => Mod4StubSeeder::EMPRESA_FORNECEDOR_ID,
                'item_descricao'          => 'Kit Escritório Completo',
                'custo_base'              => 100.00,
                'frete'                   => 10.00,
                'garantia'                => 5.00,
                'mao_de_obra'             => 0.00,
                'instalacao'              => 0.00,
                'impostos'                => 15.00,
                'margem_lucro'            => 0.20,
                'bdi_percentual'          => 0.25,
                'preco_minimo_calculado'  => 195.00,
                'preco_estimado_pncp'     => 150.00,
                'margem_status'           => 'VERMELHO',
                'alerta_inexequibilidade' => false,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
            // 4. Alerta de Inexequibilidade (Desconto > 70%)
            [
                'id'                      => Str::uuid()->toString(),
                'edital_id'               => Mod4StubSeeder::EDITAL_1_ID,
                'empresa_id'              => Mod4StubSeeder::EMPRESA_ACRIBLU_ID,
                'item_descricao'          => 'Lote Especial de Placas (Inexequível)',
                'custo_base'              => 20.00,
                'frete'                   => 0.00,
                'garantia'                => 0.00,
                'mao_de_obra'             => 5.00,
                'instalacao'              => 0.00,
                'impostos'                => 0.00,
                'margem_lucro'            => 0.00,
                'bdi_percentual'          => 0.16,
                'preco_minimo_calculado'  => 29.00,
                'preco_estimado_pncp'     => 150.00,
                'margem_status'           => 'VERDE',
                'alerta_inexequibilidade' => true,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
        ]);
    }
}
