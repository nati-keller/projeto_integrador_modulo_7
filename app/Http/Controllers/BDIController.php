<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBDIRequest;
use App\Models\EmpresaFiscal;
use App\Repositories\EditalRepository;
use App\Services\CalculadoraBDIService;

class BDIController extends Controller
{
    public function __construct(
        private CalculadoraBDIService $service,
        private EditalRepository $editalRepo,
    ) {}

    public function index()
    {
        // Empresas do M4 enrichidas com dados fiscais do M7
        $empresasM4 = $this->editalRepo->empresasDisponiveis();
        $fiscais    = EmpresaFiscal::all()->keyBy('company_id');

        $empresas = $empresasM4->map(function ($e) use ($fiscais) {
            $fiscal = $fiscais->get($e->company_id);
            $e->regime_tributario = $fiscal?->regime_tributario;
            $e->aliquota_simples  = $fiscal?->aliquota_simples;
            return $e;
        });

        return view('bdi.calculadora', compact('empresas'));
    }

    public function calcular(StoreBDIRequest $request)
    {
        $fiscal = EmpresaFiscal::find($request->empresa_id);

        if (!$fiscal || !$fiscal->regime_tributario) {
            return response()->json([
                'error' => 'Regime tributário não configurado. Configure o perfil fiscal da empresa antes de calcular o BDI.'
            ], 422);
        }

        try {
            $resultado = $this->service->calcular($fiscal->regime_tributario, $request->validated());
            return response()->json($resultado);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
