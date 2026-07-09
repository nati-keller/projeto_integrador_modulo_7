<?php

namespace App\Http\Controllers;

use App\Models\HistoricoCusto;
use App\Models\PropostaPreco;
use Illuminate\Http\Request;

class HistoricoController extends Controller
{
    public function index()
    {
        $historicos = HistoricoCusto::with('proposta')
            ->orderByDesc('registrado_em')
            ->paginate(20);

        return view('historico.index', compact('historicos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proposta_id'      => 'required|uuid|exists:mod7_propostas_precos,id',
            'tipo_documento'   => 'required|in:ORCAMENTO,COTACAO,NOTA_FISCAL,OUTRO',
            'valor_referencia' => 'required|numeric|min:0',
            'data_documento'   => 'required|date',
            'url_arquivo'      => 'required|string',
        ]);

        $proposta = PropostaPreco::findOrFail($request->proposta_id);

        HistoricoCusto::create([
            'proposta_id'      => $proposta->id,
            'edital_id'        => $proposta->edital_id,
            'empresa_id'       => $proposta->empresa_id,
            'tipo_documento'   => $request->tipo_documento,
            'url_arquivo'      => $request->url_arquivo,
            'valor_referencia' => $request->valor_referencia,
            'data_documento'   => $request->data_documento,
        ]);

        return back()->with('success', 'Documento registrado no histórico.');
    }

    // Tentativa de edição retorna 405 (RNF02)
    public function update(Request $request, $id)
    {
        abort(405, 'Registros do histórico de custos são imutáveis (RNF02).');
    }

    public function destroy($id)
    {
        abort(405, 'Registros do histórico de custos são imutáveis (RNF02).');
    }
}
