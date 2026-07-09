<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * ⚠️ MODO DE TESTE LOCAL — este projeto está rodando com stubs das tabelas
 * do Módulo 4 (mod4_tempmod1, mod4_tempmod2, mod4_analysis), NÃO com o banco
 * Supabase compartilhado real.
 *
 * Antes de considerar o M7 pronto para integração:
 * 1. Trocar DB_CONNECTION para pgsql/Supabase no .env
 * 2. Desabilitar as 3 migrations de stub (090001, 090002, 090003)
 * 3. Desabilitar Mod4StubSeeder abaixo
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            Mod4StubSeeder::class,       // STUB M4 — remover em integração real
            EmpresaFiscalSeeder::class,  // Dados fiscais M7 (mantém em produção)
            PropostaPrecoSeeder::class,  // Proposta demo M7
        ]);
    }
}
