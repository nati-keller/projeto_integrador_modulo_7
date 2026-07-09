@extends('layouts.app')

@section('title', 'Propostas')
@section('page-title', 'Gerenciamento de Propostas')
@section('page-subtitle', 'Lista de todas as propostas precificadas.')

@section('header-actions')
    <a href="{{ route('propostas.create') }}" class="btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nova Proposta
    </a>
@endsection

@section('content')
<div class="card overflow-hidden">
    @if($propostas->isEmpty())
        <div class="py-16 text-center">
            <div class="w-16 h-16 bg-surface-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-surface-800">Nenhuma proposta</h3>
            <p class="text-sm text-surface-500 mt-1">Crie sua primeira proposta para começar.</p>
            <a href="{{ route('propostas.create') }}" class="btn-primary mt-6">
                Criar Proposta
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th class="w-1/3">Item</th>
                        <th>Custo Base</th>
                        <th>BDI</th>
                        <th>Preço Mín.</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @foreach($propostas as $proposta)
                        <tr>
                            <td>
                                <div class="font-medium text-surface-900 truncate max-w-xs">{{ $proposta->item_descricao }}</div>
                                <div class="text-xs text-surface-400 mt-0.5">Criada em {{ $proposta->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="font-mono text-sm text-surface-600">
                                R$ {{ number_format($proposta->custo_base, 2, ',', '.') }}
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-50 text-primary-700 border border-primary-100">
                                    {{ number_format($proposta->bdi_percentual * 100, 2, ',', '.') }}%
                                </span>
                            </td>
                            <td class="font-bold text-surface-900 font-mono">
                                R$ {{ number_format($proposta->preco_minimo_calculado, 2, ',', '.') }}
                            </td>
                            <td>
                                <span class="{{ $proposta->margem_status->badgeClass() }}">
                                    @if($proposta->margem_status->value == 'VERDE')
                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    @elseif($proposta->margem_status->value == 'AMARELO')
                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    @endif
                                    {{ $proposta->margem_status->value }}
                                </span>
                                @if($proposta->alerta_inexequibilidade)
                                    <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100 text-red-600" title="Inexequível">!</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('propostas.show', $proposta) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-surface-400 hover:text-primary-600 hover:bg-primary-50 transition-colors" title="Ver Detalhes">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-surface-200 bg-surface-50">
            {{ $propostas->links() }}
        </div>
    @endif
</div>
@endsection
