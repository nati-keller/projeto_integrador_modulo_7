<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EditalRepository
{
    /**
     * Editais com status 'go' e processamento concluído para uma empresa específica.
     * SEMPRE filtra por empresa — sem isso, uma empresa vê proposta de outra.
     */
    public function editaisSelecionados(string $empresaId): Collection
    {
        return DB::table('mod4_analysis as a')
            ->join('mod4_tempmod1 as e', 'a.edital_id', '=', 'e.edital_id')
            ->where('a.id_cliente', $empresaId)
            ->where('a.status', 'go')
            ->where('a.processing_status', 'done')
            ->select('e.edital_id', 'e.orgao', 'e.objeto', 'e.quantidade',
                     'a.match_score', 'a.financial_summary')
            ->orderByDesc('a.match_score')
            ->get();
    }

    /**
     * Todas as empresas/fornecedores cadastrados no M4.
     */
    public function empresasDisponiveis(): Collection
    {
        return DB::table('mod4_tempmod2')
            ->select('company_id', 'company_name', 'cnpj', 'company_details')
            ->get();
    }

    /**
     * Busca uma empresa específica pelo ID.
     */
    public function findEmpresa(string $companyId): ?object
    {
        return DB::table('mod4_tempmod2')
            ->where('company_id', $companyId)
            ->first();
    }
}
