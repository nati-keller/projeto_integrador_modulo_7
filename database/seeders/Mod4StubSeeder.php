<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * STUB — dados de teste para tabelas do Módulo 4.
 * Remover quando conectar ao Supabase real.
 */
class Mod4StubSeeder extends Seeder
{
    // UUIDs fixos — reutilizados por EmpresaFiscalSeeder e PropostaPrecoSeeder
    public const EMPRESA_ACRIBLU_ID    = '11111111-1111-4111-a111-111111111111';
    public const EMPRESA_FORNECEDOR_ID = '22222222-2222-4222-a222-222222222222';
    public const EDITAL_1_ID           = 'aaaa1111-1111-4111-a111-111111111111';
    public const EDITAL_2_ID           = 'aaaa2222-2222-4222-a222-222222222222';
    public const EDITAL_3_ID           = 'aaaa3333-3333-4333-a333-333333333333';
    public const EDITAL_4_ID           = 'aaaa4444-4444-4444-a444-444444444444';

    public function run(): void
    {
        $this->seedEmpresas();
        $this->seedEditais();
        $this->seedAnalysis();
    }

    private function seedEmpresas(): void
    {
        DB::table('mod4_tempmod2')->insert([
            [
                'company_id'      => self::EMPRESA_ACRIBLU_ID,
                'cnpj'            => '17.372.674/0001-10',
                'company_name'    => 'ACRIBLU ACRILICOS LTDA',
                'keyword_1'       => 'acrilico',
                'keyword_2'       => 'medalha',
                'keyword_3'       => 'trofeu',
                'keyword_4'       => null,
                'keyword_5'       => null,
                'company_details' => 'Fabricante de medalhas e troféus em acrílico e MDF. Simples Nacional.',
                'created_at'      => now(),
            ],
            [
                'company_id'      => self::EMPRESA_FORNECEDOR_ID,
                'cnpj'            => '00.000.000/0001-00',
                'company_name'    => 'FORNECEDOR DEMO LTDA',
                'keyword_1'       => 'material',
                'keyword_2'       => 'escritorio',
                'keyword_3'       => 'informatica',
                'keyword_4'       => 'mobiliario',
                'keyword_5'       => null,
                'company_details' => 'Fornecedor geral de materiais de escritório e informática. Lucro Real.',
                'created_at'      => now(),
            ],
        ]);
    }

    private function seedEditais(): void
    {
        DB::table('mod4_tempmod1')->insert([
            [
                'edital_id'  => self::EDITAL_1_ID,
                'orgao'      => 'Prefeitura Municipal de Curitiba',
                'objeto'     => 'Aquisição de medalhas em acrílico e MDF para premiação escolar',
                'doc_1'      => null, 'doc_2' => null, 'doc_3' => null, 'doc_4' => null, 'doc_5' => null,
                'created_at' => now(),
                'mod1_id'    => 1,
            ],
            [
                'edital_id'  => self::EDITAL_2_ID,
                'orgao'      => 'Universidade Federal do Paraná',
                'objeto'     => 'Fornecimento de troféus para jogos universitários 2025',
                'doc_1'      => null, 'doc_2' => null, 'doc_3' => null, 'doc_4' => null, 'doc_5' => null,
                'created_at' => now(),
                'mod1_id'    => 2,
            ],
            [
                'edital_id'  => self::EDITAL_3_ID,
                'orgao'      => 'Secretaria de Educação — Estado do PR',
                'objeto'     => 'Aquisição de material de escritório para escolas estaduais',
                'doc_1'      => null, 'doc_2' => null, 'doc_3' => null, 'doc_4' => null, 'doc_5' => null,
                'created_at' => now(),
                'mod1_id'    => 3,
            ],
            [
                'edital_id'  => self::EDITAL_4_ID,
                'orgao'      => 'Tribunal Regional do Trabalho — 9ª Região',
                'objeto'     => 'Contratação de serviço de fornecimento de mobiliário',
                'doc_1'      => null, 'doc_2' => null, 'doc_3' => null, 'doc_4' => null, 'doc_5' => null,
                'created_at' => now(),
                'mod1_id'    => 4,
            ],
        ]);
    }

    private function seedAnalysis(): void
    {
        DB::table('mod4_analysis')->insert([
            // ACRIBLU — edital 1: go + done (deve aparecer no Repository)
            [
                'id'                => Str::uuid()->toString(),
                'edital_id'         => self::EDITAL_1_ID,
                'id_cliente'        => self::EMPRESA_ACRIBLU_ID,
                'match_score'       => 92.50,
                'decision'          => true,
                'status'            => 'go',
                'processing_status' => 'done',
                'financial_summary' => json_encode(['estimativa' => 15000, 'itens' => 3]),
                'decided_at'        => now(),
                'notes'             => null,
                'processing_error'  => null,
                'processed_at'      => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // ACRIBLU — edital 2: go + done (deve aparecer)
            [
                'id'                => Str::uuid()->toString(),
                'edital_id'         => self::EDITAL_2_ID,
                'id_cliente'        => self::EMPRESA_ACRIBLU_ID,
                'match_score'       => 78.00,
                'decision'          => true,
                'status'            => 'go',
                'processing_status' => 'done',
                'financial_summary' => json_encode(['estimativa' => 8500, 'itens' => 5]),
                'decided_at'        => now(),
                'notes'             => null,
                'processing_error'  => null,
                'processed_at'      => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // FORNECEDOR DEMO — edital 3: go + done (deve aparecer)
            [
                'id'                => Str::uuid()->toString(),
                'edital_id'         => self::EDITAL_3_ID,
                'id_cliente'        => self::EMPRESA_FORNECEDOR_ID,
                'match_score'       => 85.30,
                'decision'          => true,
                'status'            => 'go',
                'processing_status' => 'done',
                'financial_summary' => json_encode(['estimativa' => 42000, 'itens' => 12]),
                'decided_at'        => now(),
                'notes'             => null,
                'processing_error'  => null,
                'processed_at'      => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // FORNECEDOR DEMO — edital 4: no_go (NÃO deve aparecer no Repository)
            [
                'id'                => Str::uuid()->toString(),
                'edital_id'         => self::EDITAL_4_ID,
                'id_cliente'        => self::EMPRESA_FORNECEDOR_ID,
                'match_score'       => 35.00,
                'decision'          => false,
                'status'            => 'no_go',
                'processing_status' => 'done',
                'financial_summary' => null,
                'decided_at'        => now(),
                'notes'             => 'Score muito baixo para o perfil da empresa.',
                'processing_error'  => null,
                'processed_at'      => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // ACRIBLU — edital 3: pending processing (NÃO deve aparecer)
            [
                'id'                => Str::uuid()->toString(),
                'edital_id'         => self::EDITAL_3_ID,
                'id_cliente'        => self::EMPRESA_ACRIBLU_ID,
                'match_score'       => null,
                'decision'          => null,
                'status'            => 'pending',
                'processing_status' => 'pending',
                'financial_summary' => null,
                'decided_at'        => null,
                'notes'             => null,
                'processing_error'  => null,
                'processed_at'      => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}
