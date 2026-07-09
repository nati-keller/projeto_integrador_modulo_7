@extends('layouts.app')

@section('title', 'Histórico de Custos')
@section('page-title', 'Banco de Preços e Custos')
@section('page-subtitle', 'Consulte preços praticados no mercado para balizar as propostas.')

@section('header-actions')
    <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Registrar Custo
    </button>
@endsection

@section('content')
<div class="card overflow-hidden">
    @if($historicos->isEmpty())
        <div class="py-16 text-center">
            <div class="w-16 h-16 bg-surface-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-surface-800">Nenhum custo registrado</h3>
            <p class="text-sm text-surface-500 mt-1">Mantenha um histórico de cotações para embasar orçamentos.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th class="w-1/3">Item</th>
                        <th>Fornecedor</th>
                        <th>Valor Unitário</th>
                        <th>Fonte</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @foreach($historicos as $hist)
                        <tr>
                            <td class="text-surface-500 text-sm whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($hist->registrado_em)->format('d/m/Y') }}
                            </td>
                            <td class="font-medium text-surface-900">
                                {{ $hist->item_descricao }}
                                @if($hist->proposta)
                                    <div class="text-xs text-primary-600 mt-0.5">Ref: Proposta</div>
                                @endif
                            </td>
                            <td class="text-surface-600 text-sm">
                                {{ $hist->fornecedor_nome }}
                                @if($hist->fornecedor_cnpj)
                                    <span class="block text-xs text-surface-400 font-mono">{{ $hist->fornecedor_cnpj }}</span>
                                @endif
                            </td>
                            <td class="font-mono font-medium text-surface-800">
                                R$ {{ number_format($hist->valor_unitario, 2, ',', '.') }}
                            </td>
                            <td class="text-surface-500 text-sm">
                                @if($hist->fonte_dado === 'COTACAO')
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs border border-blue-100">Cotação</span>
                                @elseif($hist->fonte_dado === 'NOTA_FISCAL')
                                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded text-xs border border-purple-100">Nota Fiscal</span>
                                @else
                                    <span class="px-2 py-0.5 bg-gray-50 text-gray-700 rounded text-xs border border-gray-200">Outros</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-surface-200 bg-surface-50">
            {{ $historicos->links() }}
        </div>
    @endif
</div>

{{-- Modal Simplificado para Adicionar --}}
<div id="modal-add" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-surface-900/40 backdrop-blur-sm" onclick="document.getElementById('modal-add').classList.add('hidden')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg">
        <div class="card p-0 shadow-2xl">
            <div class="card-header bg-surface-50/80 border-b border-surface-200 flex justify-between items-center rounded-t-2xl">
                <h3 class="font-semibold text-surface-900">Registrar Novo Custo</h3>
                <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-surface-400 hover:text-surface-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form action="{{ route('historico.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Descrição do Item *</label>
                        <input type="text" name="item_descricao" class="form-input" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Fornecedor *</label>
                            <input type="text" name="fornecedor_nome" class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">CNPJ</label>
                            <input type="text" name="fornecedor_cnpj" class="form-input" placeholder="Opcional">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Valor Unitário (R$) *</label>
                            <input type="number" step="0.01" min="0.01" name="valor_unitario" class="form-input font-mono" required>
                        </div>
                        <div>
                            <label class="form-label">Fonte</label>
                            <select name="fonte_dado" class="form-input">
                                <option value="COTACAO">Cotação</option>
                                <option value="NOTA_FISCAL">Nota Fiscal</option>
                                <option value="CONTRATO">Contrato</option>
                                <option value="SISTEMA_EXTERNO">Sistema Externo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-surface-100">
                    <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">Salvar Custo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
