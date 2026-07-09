<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropostaRequest;
use App\Models\PropostaPreco;
use App\Repositories\EditalRepository;
use App\Services\CalculadoraPrecoMinimoService;
use Illuminate\Http\Request;

class PropostaController extends Controller
{
    public function __construct(
        private CalculadoraPrecoMinimoService $calculadora,
        private EditalRepository $editalRepo,
    ) {}

    public function index()
    {
        $propostas = PropostaPreco::latest()->paginate(15);
        return view('propostas.index', compact('propostas'));
    }

    public function create()
    {
        $empresas = $this->editalRepo->empresasDisponiveis();
        return view('propostas.create', compact('empresas'));
    }

    public function store(StorePropostaRequest $request)
    {
        try {
            $resultado = $this->calculadora->calcular($request->validated());

            return redirect()
                ->route('propostas.show', $resultado['proposta'])
                ->with('success', 'Proposta calculada e salva com sucesso!');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['geral' => $e->getMessage()]);
        }
    }

    public function show(PropostaPreco $proposta)
    {
        $proposta->load('historicos');
        return view('propostas.show', compact('proposta'));
    }

    // AJAX — cálculo em tempo real sem salvar
    public function preview(Request $request)
    {
        try {
            $resultado = $this->calculadora->calcularSemSalvar($request->all());
            return response()->json($resultado);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    // AJAX — editais disponíveis para uma empresa
    public function editais(string $empresaId)
    {
        $editais = $this->editalRepo->editaisSelecionados($empresaId);
        return response()->json($editais);
    }
}
