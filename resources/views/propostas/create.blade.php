@extends('layouts.app')

@section('title', 'Nova Proposta')
@section('page-title', 'Calcular Preço Mínimo')
@section('page-subtitle', 'Preencha os dados para calcular o preço mínimo e avaliar a viabilidade.')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Preview em tempo real --}}
    <div id="preview-card" class="card-accent p-6 hidden">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-primary-600 font-bold uppercase tracking-[0.1em] mb-1">Preço Mínimo Calculado</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-xl font-medium text-surface-400">R$</span>
                    <p id="preview-preco" class="text-4xl font-extrabold text-surface-900 tracking-tight" title="Preço Unitário">0,00</p>
                </div>
                <div class="mt-1 text-sm text-surface-500 font-medium hidden" id="preview-total-container">
                    Total: R$ <span id="preview-total" class="font-bold">0,00</span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-surface-500 font-medium mb-1.5 uppercase tracking-wider">Status da Margem</p>
                <span id="preview-badge" class="badge-amarelo text-sm px-3 py-1.5 shadow-sm">—</span>
            </div>
        </div>
        
        <div id="preview-alerta" class="hidden mt-5 px-4 py-3 bg-red-50/50 border border-red-200 rounded-xl text-sm text-red-700 flex gap-3 items-start">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <div>
                <strong>Atenção (Inexequibilidade):</strong> Desconto acima de 70% do estimado. Exige comprovação de exequibilidade (Art. 59, Lei 14.133).
            </div>
        </div>
        
        <div class="mt-6 pt-5 border-t border-primary-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm" id="preview-detalhamento">
            <!-- Injetado via JS -->
        </div>
    </div>

    {{-- Formulário --}}
    <form method="POST" action="{{ route('propostas.store') }}" id="form-proposta">
        @csrf

        {{-- Seleção de Empresa e Edital --}}
        <div class="card mb-6 overflow-visible">
            <div class="card-header bg-surface-50/50 rounded-t-2xl">
                <h3 class="font-semibold text-surface-800 text-sm flex items-center gap-2">
                    <span class="w-6 h-6 rounded bg-primary-100 text-primary-600 flex items-center justify-center text-xs">1</span>
                    Vínculo
                </h3>
            </div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Empresa <span class="text-red-500">*</span></label>
                    <select name="empresa_id" id="empresa-select" class="form-input shadow-sm" required>
                        <option value="">Selecione a empresa</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->company_id }}" {{ old('empresa_id') == $empresa->company_id ? 'selected' : '' }}>
                                {{ $empresa->company_name }} ({{ $empresa->cnpj }})
                            </option>
                        @endforeach
                    </select>
                    @error('empresa_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Edital <span class="text-red-500">*</span></label>
                    <select name="edital_id" id="edital-select" class="form-input shadow-sm bg-surface-50" required disabled>
                        <option value="">Selecione a empresa primeiro</option>
                    </select>
                    @error('edital_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Identificação do item --}}
        <div class="card mb-6">
            <div class="card-header bg-surface-50/50 rounded-t-2xl">
                <h3 class="font-semibold text-surface-800 text-sm flex items-center gap-2">
                    <span class="w-6 h-6 rounded bg-primary-100 text-primary-600 flex items-center justify-center text-xs">2</span>
                    Identificação
                </h3>
            </div>
            <div class="card-body grid grid-cols-1 md:grid-cols-12 gap-5">
                <div class="md:col-span-12">
                    <label class="form-label">Descrição do Item <span class="text-red-500">*</span></label>
                    <input type="text" name="item_descricao" value="{{ old('item_descricao') }}"
                           class="form-input" placeholder="Ex: Cadeira Ergonômica..." required>
                    @error('item_descricao')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-4">
                    <label class="form-label text-surface-500">Quantidade <span class="text-red-500">*</span></label>
                    <input type="number" name="quantidade" id="quantidade" value="{{ old('quantidade', 1) }}"
                           class="form-input calc-trigger text-surface-900 font-mono" min="1" placeholder="1" required>
                    @error('quantidade')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-4">
                    <label class="form-label text-surface-500">Valor Unit. Estimado (R$)</label>
                    <input type="number" name="preco_estimado_pncp" id="preco_estimado_pncp" value="{{ old('preco_estimado_pncp') }}"
                           class="form-input calc-trigger text-surface-900 font-mono" step="0.01" min="0" placeholder="0.00">
                    @error('preco_estimado_pncp')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-4">
                    <label class="form-label text-surface-500">Valor Total Estimado (R$)</label>
                    <input type="text" id="valor_total_estimado_pncp" class="form-input bg-surface-50 text-surface-900 font-mono" readonly placeholder="0,00">
                </div>
            </div>
        </div>

        {{-- Custos --}}
        <div class="card mb-6">
            <div class="card-header bg-surface-50/50 rounded-t-2xl flex justify-between items-center">
                <h3 class="font-semibold text-surface-800 text-sm flex items-center gap-2">
                    <span class="w-6 h-6 rounded bg-primary-100 text-primary-600 flex items-center justify-center text-xs">3</span>
                    Composição de Custos Diretos
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="col-span-2 md:col-span-3 lg:col-span-2">
                        <label class="form-label text-primary-700 font-semibold">Custo Base (R$) *</label>
                        <input type="number" name="custo_base" value="{{ old('custo_base') }}"
                               class="form-input calc-trigger font-mono font-medium border-primary-200 bg-primary-50/30 focus:bg-white focus:border-primary-400" step="0.01" min="0.01" placeholder="0.00" required>
                        @error('custo_base')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label text-surface-500">Frete (R$)</label>
                        <div class="relative">
                            <input type="number" name="frete" value="{{ old('frete', 0) }}" class="form-input calc-trigger font-mono text-sm pr-[4.5rem] w-full" step="0.01" min="0">
                            <div class="absolute inset-y-0 right-0 flex items-center">
                                <select name="frete_tipo" class="h-full py-0 pl-1 pr-6 border-transparent bg-transparent text-primary-600 font-medium text-[10px] uppercase tracking-wider focus:ring-0 cursor-pointer calc-trigger text-right">
                                    <option value="unitario">/ UN</option>
                                    <option value="total">/ TOT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-surface-500">Garantia (R$)</label>
                        <div class="relative">
                            <input type="number" name="garantia" value="{{ old('garantia', 0) }}" class="form-input calc-trigger font-mono text-sm pr-[4.5rem] w-full" step="0.01" min="0">
                            <div class="absolute inset-y-0 right-0 flex items-center">
                                <select name="garantia_tipo" class="h-full py-0 pl-1 pr-6 border-transparent bg-transparent text-primary-600 font-medium text-[10px] uppercase tracking-wider focus:ring-0 cursor-pointer calc-trigger text-right">
                                    <option value="unitario">/ UN</option>
                                    <option value="total">/ TOT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-surface-500">Mão de Obra</label>
                        <div class="relative">
                            <input type="number" name="mao_de_obra" value="{{ old('mao_de_obra', 0) }}" class="form-input calc-trigger font-mono text-sm pr-[4.5rem] w-full" step="0.01" min="0">
                            <div class="absolute inset-y-0 right-0 flex items-center">
                                <select name="mao_de_obra_tipo" class="h-full py-0 pl-1 pr-6 border-transparent bg-transparent text-primary-600 font-medium text-[10px] uppercase tracking-wider focus:ring-0 cursor-pointer calc-trigger text-right">
                                    <option value="unitario">/ UN</option>
                                    <option value="total">/ TOT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-surface-500">Instalação</label>
                        <div class="relative">
                            <input type="number" name="instalacao" value="{{ old('instalacao', 0) }}" class="form-input calc-trigger font-mono text-sm pr-[4.5rem] w-full" step="0.01" min="0">
                            <div class="absolute inset-y-0 right-0 flex items-center">
                                <select name="instalacao_tipo" class="h-full py-0 pl-1 pr-6 border-transparent bg-transparent text-primary-600 font-medium text-[10px] uppercase tracking-wider focus:ring-0 cursor-pointer calc-trigger text-right">
                                    <option value="unitario">/ UN</option>
                                    <option value="total">/ TOT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Campos ocultos para impostos (não usado na fórmula nova mas mantido para não quebrar seeders antigos se houver) --}}
                <input type="hidden" name="impostos" value="0">
            </div>
        </div>

        {{-- Margem e BDI --}}
        <div class="card mb-8">
            <div class="card-header bg-surface-50/50 rounded-t-2xl">
                <h3 class="font-semibold text-surface-800 text-sm flex items-center gap-2">
                    <span class="w-6 h-6 rounded bg-primary-100 text-primary-600 flex items-center justify-center text-xs">4</span>
                    Índices
                </h3>
            </div>
            <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-surface-50 rounded-xl p-4 border border-surface-100">
                    <label class="form-label font-semibold text-surface-800">Margem de Lucro Desejada <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="margem_lucro" value="{{ old('margem_lucro', '0.10') }}"
                               class="form-input calc-trigger font-mono w-32" step="0.01" min="0" max="1" required>
                        <span class="text-sm text-surface-500">Ex: 0.10 = 10%</span>
                    </div>
                    @error('margem_lucro')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="bg-primary-50/50 rounded-xl p-4 border border-primary-100">
                    <div class="flex justify-between items-start mb-1.5">
                        <label class="form-label font-semibold text-primary-800 mb-0">BDI (%) <span class="text-red-500">*</span></label>
                        <a href="{{ route('bdi.index') }}" target="_blank" class="text-xs font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1">
                            Calculadora <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="number" name="bdi_percentual" value="{{ old('bdi_percentual') }}"
                               class="form-input calc-trigger font-mono w-32 border-primary-200 focus:border-primary-500" step="0.0001" min="0" max="1" placeholder="0.2770" required>
                        <span class="text-sm text-primary-600/70 font-medium">Ex: 0.2770</span>
                    </div>
                    @error('bdi_percentual')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Ações --}}
        <div class="flex items-center justify-between border-t border-surface-200 pt-6 mt-6 pb-4 sticky bottom-0 bg-surface-100/90 backdrop-blur-sm z-20">
            <a href="{{ route('propostas.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary shadow-lg shadow-primary-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Salvar Proposta
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-proposta');
    const previewCard = document.getElementById('preview-card');
    const previewPreco = document.getElementById('preview-preco');
    const previewTotalContainer = document.getElementById('preview-total-container');
    const previewTotal = document.getElementById('preview-total');
    const previewBadge = document.getElementById('preview-badge');
    const previewAlerta = document.getElementById('preview-alerta');
    const previewDetalhamento = document.getElementById('preview-detalhamento');
    const empresaSelect = document.getElementById('empresa-select');
    const editalSelect = document.getElementById('edital-select');

    // Carregar editais
    let editaisLoaded = [];
    
    empresaSelect.addEventListener('change', async function() {
        const empresaId = this.value;
        editalSelect.innerHTML = '<option value="">Carregando...</option>';
        editalSelect.disabled = true;
        editalSelect.classList.add('bg-surface-50');

        if (!empresaId) {
            editalSelect.innerHTML = '<option value="">Selecione a empresa primeiro</option>';
            return;
        }

        try {
            const url = '{{ route("propostas.editais", ":id") }}'.replace(':id', empresaId);
            const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
            editaisLoaded = await response.json();

            editalSelect.innerHTML = '<option value="">Selecione o edital</option>';
            editaisLoaded.forEach(e => {
                const opt = document.createElement('option');
                opt.value = e.edital_id;
                opt.textContent = `${e.orgao} — ${e.objeto?.substring(0, 60) ?? ''}...`;
                editalSelect.appendChild(opt);
            });
            editalSelect.disabled = false;
            editalSelect.classList.remove('bg-surface-50');
        } catch (err) {
            editalSelect.innerHTML = '<option value="">Erro ao carregar</option>';
        }
    });

    // Ao selecionar um edital, autocompletar descrição e preço estimado
    editalSelect.addEventListener('change', function() {
        const selectedId = this.value;
        const edital = editaisLoaded.find(e => e.edital_id === selectedId);
        
        const inputDescricao = document.querySelector('input[name="item_descricao"]');
        const inputQuantidade = document.querySelector('input[name="quantidade"]');
        const inputEstimado = document.querySelector('input[name="preco_estimado_pncp"]');

        if (edital) {
            // Autocompleta a descrição do item com o objeto do edital
            inputDescricao.value = edital.objeto || '';
            
            // Autocompleta a quantidade com a quantidade do edital
            if (edital.quantidade) {
                inputQuantidade.value = edital.quantidade;
            } else {
                inputQuantidade.value = 1;
            }
            
            // Extrai o valor estimado do JSON financial_summary
            if (edital.financial_summary) {
                try {
                    const finance = JSON.parse(edital.financial_summary);
                    if (finance.estimativa) {
                        inputEstimado.value = parseFloat(finance.estimativa).toFixed(2);
                    }
                } catch(e) {
                    console.error('Erro ao parsear financial_summary', e);
                }
            }
        } else {
            inputDescricao.value = '';
            inputEstimado.value = '';
        }
        
        // Dispara o recálculo do preview e do valor total
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(calcularPreview, 300);
        updateEstimadoTotal();
    });

    // Calcula valor total estimado
    function updateEstimadoTotal() {
        const qtdInput = document.querySelector('input[name="quantidade"]');
        const unitarioInput = document.querySelector('input[name="preco_estimado_pncp"]');
        const totalInput = document.getElementById('valor_total_estimado_pncp');
        
        if (qtdInput && unitarioInput && totalInput) {
            const qtd = parseFloat(qtdInput.value) || 0;
            const unit = parseFloat(unitarioInput.value) || 0;
            const total = qtd * unit;
            
            totalInput.value = total > 0 ? total.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
        }
    }

    document.querySelector('input[name="quantidade"]').addEventListener('input', updateEstimadoTotal);
    document.querySelector('input[name="preco_estimado_pncp"]').addEventListener('input', updateEstimadoTotal);
    updateEstimadoTotal();

    // Preview AJAX
    let debounceTimer;
    document.querySelectorAll('.calc-trigger').forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(calcularPreview, 300);
        });
    });

    async function calcularPreview() {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        if (!data.custo_base || parseFloat(data.custo_base) <= 0 || !data.bdi_percentual) {
            previewCard.classList.add('hidden');
            return;
        }

        try {
            const response = await fetch('{{ route("propostas.preview") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();
            if (result.error) return;

            // Update UI
            previewCard.classList.remove('hidden');
            
            // Formatador moeda
            const format = (v) => parseFloat(v).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            previewPreco.textContent = format(result.preco_minimo);
            
            if (result.quantidade) {
                previewTotalContainer.classList.remove('hidden');
                previewTotal.textContent = format(result.preco_total);
            } else {
                previewTotalContainer.classList.add('hidden');
            }

            const badgeMap = {
                'VERDE': 'badge-verde',
                'AMARELO': 'badge-amarelo',
                'VERMELHO': 'badge-vermelho',
            };
            
            let badgeIcon = '';
            if(result.margem_status === 'VERDE') badgeIcon = '<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
            if(result.margem_status === 'AMARELO') badgeIcon = '<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';
            if(result.margem_status === 'VERMELHO') badgeIcon = '<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';
            
            previewBadge.className = badgeMap[result.margem_status] || 'badge-amarelo';
            previewBadge.innerHTML = badgeIcon + ' ' + result.margem_status;

            previewAlerta.classList.toggle('hidden', !result.alerta_inexequibilidade);

            previewDetalhamento.innerHTML = '';
            if (result.detalhamento) {
                const mapLabels = {
                    'Custo Direto Total': 'Custo Direto',
                    'Custo Indireto (BDI)': 'BDI (R$)',
                    'Preço Calculado': 'Mínimo Calculado',
                    'Preço Estimado PNCP': 'Estimativa PNCP'
                };
                
                for (const [key, value] of Object.entries(result.detalhamento)) {
                    const label = mapLabels[key] || key;
                    previewDetalhamento.innerHTML += `
                        <div class="flex flex-col">
                            <span class="text-surface-500 font-medium mb-0.5">${label}</span>
                            <span class="font-mono text-surface-800">R$ ${format(value)}</span>
                        </div>
                    `;
                }
            }
        } catch (e) {
            console.error('Erro no preview:', e);
        }
    }
});
</script>
@endpush
