@extends('layouts.app')

@section('title', 'Detalhes da Proposta')
@section('page-title', 'Detalhes da Proposta')
@section('page-subtitle', 'Visualização do cálculo e histórico de mudanças.')

@section('header-actions')
    <a href="{{ route('propostas.index') }}" class="btn-secondary">Voltar</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Coluna principal (Esquerda) --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Card de Preço em destaque --}}
        <div class="card-accent p-8 relative overflow-hidden">
            <div class="absolute right-0 top-0 -mt-10 -mr-10 opacity-[0.03]">
                <svg width="200" height="200" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>
            
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold text-surface-900">{{ $proposta->item_descricao }}</h2>
                    <p class="text-sm text-surface-500 mt-1">Ref: Edital {{ substr($proposta->edital_id, 0, 8) }}</p>
                </div>
                <span class="{{ $proposta->margem_status->badgeClass() }} text-sm px-3 py-1 shadow-sm">
                    {{ $proposta->margem_status->value }}
                </span>
            </div>

            <div class="bg-surface-50 rounded-xl p-6 border border-surface-200/60 flex justify-between items-center mt-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-surface-500 mb-1">Preço Mínimo Unitário</p>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-surface-400 font-medium">R$</span>
                        <span class="text-4xl font-extrabold text-surface-900 tracking-tight">{{ number_format($proposta->preco_minimo_calculado, 2, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="text-right">
                    <p class="text-xs font-bold uppercase tracking-wider text-surface-500 mb-1">Qtd</p>
                    <div class="text-xl font-bold text-surface-700">{{ $proposta->quantidade }}</div>
                </div>
                
                <div class="text-right border-l border-surface-200 pl-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-surface-500 mb-1">Preço Total</p>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-surface-400 font-medium">R$</span>
                        <span class="text-3xl font-extrabold text-surface-900 tracking-tight">{{ number_format($proposta->preco_total_calculado, 2, ',', '.') }}</span>
                    </div>
                </div>
                
                @if($proposta->alerta_inexequibilidade)
                    <div class="flex items-center gap-2 text-red-600 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span class="text-sm font-semibold">Desconto > 70%</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Composição de Custos --}}
        <div class="card">
            <div class="card-header bg-surface-50/50">
                <h3 class="font-semibold text-surface-800">Composição de Custos Diretos</h3>
            </div>
            <div class="card-body p-0">
                <table class="table-base">
                    <tbody>
                        <tr>
                            <td class="w-2/3 font-medium">Custo Base</td>
                            <td class="font-mono text-right">R$ {{ number_format($proposta->custo_base, 2, ',', '.') }}</td>
                        </tr>
                        @if($proposta->frete > 0)
                        <tr>
                            <td class="w-2/3 font-medium">Frete</td>
                            <td class="font-mono text-right">R$ {{ number_format($proposta->frete, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($proposta->garantia > 0)
                        <tr>
                            <td class="w-2/3 font-medium">Garantia</td>
                            <td class="font-mono text-right">R$ {{ number_format($proposta->garantia, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($proposta->mao_de_obra > 0)
                        <tr>
                            <td class="w-2/3 font-medium">Mão de Obra</td>
                            <td class="font-mono text-right">R$ {{ number_format($proposta->mao_de_obra, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($proposta->instalacao > 0)
                        <tr>
                            <td class="w-2/3 font-medium">Instalação</td>
                            <td class="font-mono text-right">R$ {{ number_format($proposta->instalacao, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="bg-surface-50">
                            <td class="font-bold text-surface-900">Total Direto</td>
                            @php
                                $totalDireto = $proposta->custo_base + $proposta->frete + $proposta->garantia + $proposta->mao_de_obra + $proposta->instalacao;
                            @endphp
                            <td class="font-mono font-bold text-surface-900 text-right border-t-2 border-surface-300">
                                R$ {{ number_format($totalDireto, 2, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Coluna Secundária (Direita) --}}
    <div class="space-y-6">

        {{-- Índices Aplicados --}}
        <div class="stat-card">
            <h3 class="text-sm font-bold uppercase tracking-wider text-surface-500 mb-4">Índices Aplicados</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-surface-700 font-medium">Margem</span>
                    <span class="px-2 py-1 bg-surface-100 rounded text-sm font-semibold text-surface-900">{{ number_format($proposta->margem_lucro * 100, 2, ',', '.') }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-surface-700 font-medium">BDI</span>
                    <span class="px-2 py-1 bg-primary-50 border border-primary-100 rounded text-sm font-semibold text-primary-700">{{ number_format($proposta->bdi_percentual * 100, 2, ',', '.') }}%</span>
                </div>
                @if($proposta->preco_estimado_pncp)
                <div class="pt-4 mt-2 border-t border-surface-200">
                    <span class="text-xs text-surface-500 block mb-1">Valor Unitário Estimado (PNCP)</span>
                    <span class="font-mono text-sm font-semibold">R$ {{ number_format($proposta->preco_estimado_pncp, 2, ',', '.') }}</span>
                </div>
                <div class="pt-2 mt-2 border-t border-surface-200 border-dashed">
                    <span class="text-xs text-surface-500 block mb-1">Valor Total Estimado (PNCP)</span>
                    <span class="font-mono text-sm font-semibold">R$ {{ number_format($proposta->preco_estimado_pncp * $proposta->quantidade, 2, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Histórico de Custos --}}
        <div class="card">
            <div class="card-header bg-surface-50/50 flex justify-between items-center">
                <h3 class="font-semibold text-surface-800">Histórico de Custos</h3>
                <a href="{{ route('historico.index') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Ver Todos</a>
            </div>
            <div class="card-body p-0">
                @if($proposta->historicos->isEmpty())
                    <div class="p-5 text-center">
                        <p class="text-sm text-surface-500">Nenhum registro para este item.</p>
                    </div>
                @else
                    <div class="divide-y divide-surface-100">
                        @foreach($proposta->historicos as $hist)
                            <div class="p-4 flex justify-between items-center hover:bg-surface-50 transition-colors">
                                <div>
                                    <div class="font-medium text-sm text-surface-800">{{ ucfirst(strtolower(str_replace('_', ' ', $hist->tipo_documento))) }}</div>
                                    <div class="text-xs text-surface-400 mt-0.5">Emissão: {{ \Carbon\Carbon::parse($hist->data_documento)->format('d/m/Y') }}</div>
                                </div>
                                <div class="font-mono text-sm font-semibold text-surface-700">
                                    R$ {{ number_format($hist->valor_referencia, 2, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
