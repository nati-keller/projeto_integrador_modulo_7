@extends('layouts.app')

@section('title', 'Calculadora BDI')
@section('page-title', 'Calculadora de BDI')
@section('page-subtitle', 'Gere o percentual correto com base no regime tributário.')

@section('content')
<div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 items-start">

    {{-- Formulário (2 colunas) --}}
    <div class="md:col-span-2 card">
        <div class="card-header bg-surface-50/50">
            <h3 class="font-semibold text-surface-800 text-sm">Parâmetros do Cálculo</h3>
        </div>
        <div class="card-body">
            <form id="form-bdi">
                {{-- Empresa --}}
                <div class="mb-6">
                    <label class="form-label">Selecione o perfil da empresa <span class="text-red-500">*</span></label>
                    <select name="empresa_id" id="empresa-select" class="form-input" required>
                        <option value="">Selecione a empresa</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->company_id }}"
                                    data-regime="{{ $empresa->regime_tributario?->value ?? '' }}"
                                    data-aliquota="{{ $empresa->aliquota_simples ?? '' }}">
                                {{ $empresa->company_name }}
                                — {{ $empresa->regime_tributario?->label() ?? 'Sem regime fiscal' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Componentes --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 pt-2 border-t border-surface-100">
                    <div>
                        <label class="form-label">Desp. Adm. (%)</label>
                        <input type="number" name="desp_administrativas" class="form-input font-mono" step="0.0001" min="0" max="1" value="0.05">
                    </div>
                    <div>
                        <label class="form-label">Desp. Fin. (%)</label>
                        <input type="number" name="desp_financeiras" class="form-input font-mono" step="0.0001" min="0" max="1" value="0.01">
                    </div>
                    <div>
                        <label class="form-label">Lucro Bruto (%)</label>
                        <input type="number" name="lucro_bruto" class="form-input font-mono" step="0.0001" min="0" max="1" value="0.10">
                    </div>
                </div>

                {{-- Regimes --}}
                <div class="bg-surface-50 rounded-xl p-4 border border-surface-200/60 transition-all duration-300" id="box-impostos">
                    
                    <div id="campos-placeholder" class="text-sm text-surface-500 text-center py-2">
                        Selecione uma empresa para carregar os impostos.
                    </div>

                    <div id="campos-lucro-real" class="hidden animate-[fade-in_0.3s_ease-out]">
                        <h4 class="text-xs font-bold text-surface-600 uppercase tracking-wider mb-3">Tributos — Lucro Real</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div><label class="form-label text-xs">ISS</label><input type="number" name="iss" class="form-input font-mono text-sm" step="0.0001" min="0" max="1" value="0.05"></div>
                            <div><label class="form-label text-xs">PIS</label><input type="number" name="pis" class="form-input font-mono text-sm" step="0.0001" min="0" max="1" value="0.0065"></div>
                            <div><label class="form-label text-xs">COFINS</label><input type="number" name="cofins" class="form-input font-mono text-sm" step="0.0001" min="0" max="1" value="0.03"></div>
                        </div>
                    </div>

                    <div id="campos-simples" class="hidden animate-[fade-in_0.3s_ease-out]">
                        <h4 class="text-xs font-bold text-surface-600 uppercase tracking-wider mb-3">Tributos — Simples Nacional</h4>
                        <div>
                            <label class="form-label text-xs">Alíquota Consolidada</label>
                            <input type="number" name="aliquota_simples" id="aliquota-simples-input" class="form-input font-mono w-1/3 text-sm" step="0.0001" min="0" max="1">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-primary w-full sm:w-auto justify-center">Calcular Percentual</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Resultado (1 coluna) --}}
    <div class="space-y-4">
        <div id="bdi-resultado" class="card-accent p-6 hidden">
            <p class="text-xs font-bold text-primary-600 uppercase tracking-wider mb-2">BDI Final</p>
            <p id="bdi-valor" class="text-5xl font-extrabold text-surface-900 tracking-tighter mb-4">0,00%</p>
            
            <div class="flex items-center gap-2 mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span id="bdi-regime" class="text-sm font-medium text-surface-600">Lucro Real</span>
            </div>
            
            <div class="border-t border-primary-100 pt-4 mt-2 space-y-2 text-sm" id="bdi-componentes"></div>
            
            <button id="btn-copiar-bdi" class="mt-6 w-full btn-secondary justify-center border-primary-200 text-primary-700 hover:bg-primary-50">
                Copiar Valor decimal
            </button>
        </div>

        <div id="bdi-erro" class="hidden p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-medium"></div>

        <div class="bg-surface-200/40 rounded-xl p-4 text-xs text-surface-500 border border-surface-200">
            <strong>Fórmula aplicada:</strong><br>
            <span class="font-mono mt-1 block opacity-80">[(1 + %DA) × (1 + %DF) × (1 + %LB) / (1 − %Tributos)] − 1</span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const empresaSelect = document.getElementById('empresa-select');
    const camposLucroReal = document.getElementById('campos-lucro-real');
    const camposSimples = document.getElementById('campos-simples');
    const placeholder = document.getElementById('campos-placeholder');
    const aliquotaInput = document.getElementById('aliquota-simples-input');
    const form = document.getElementById('form-bdi');
    const resultadoCard = document.getElementById('bdi-resultado');
    const bdiValor = document.getElementById('bdi-valor');
    const bdiRegime = document.getElementById('bdi-regime');
    const bdiComponentes = document.getElementById('bdi-componentes');
    const bdiErro = document.getElementById('bdi-erro');

    const labelMap = {
        'desp_administrativas': 'Desp. Adm',
        'desp_financeiras': 'Desp. Fin',
        'lucro_bruto': 'Lucro Bruto',
        'iss': 'ISS',
        'pis': 'PIS',
        'cofins': 'COFINS',
        'aliquota_simples': 'Simples Nac',
        'tributos_total': 'Total Tributos',
    };

    empresaSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const regime = selected.dataset.regime;
        const aliquota = selected.dataset.aliquota;

        camposLucroReal.classList.add('hidden');
        camposSimples.classList.add('hidden');
        placeholder.classList.add('hidden');

        if (regime === 'LUCRO_REAL') {
            camposLucroReal.classList.remove('hidden');
        } else if (regime === 'SIMPLES_NACIONAL') {
            camposSimples.classList.remove('hidden');
            if (aliquota) aliquotaInput.value = aliquota;
        } else {
            placeholder.classList.remove('hidden');
        }
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        bdiErro.classList.add('hidden');
        resultadoCard.classList.add('hidden');

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch('{{ route("bdi.calcular") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.error) {
                bdiErro.textContent = result.error;
                bdiErro.classList.remove('hidden');
                return;
            }

            bdiValor.textContent = (result.bdi * 100).toFixed(2) + '%';
            bdiRegime.textContent = result.regime === 'LUCRO_REAL' ? 'Lucro Real' : 'Simples Nacional';

            bdiComponentes.innerHTML = '';
            if (result.componentes) {
                for (const [key, value] of Object.entries(result.componentes)) {
                    const label = labelMap[key] || key;
                    const isTotal = key === 'tributos_total';
                    bdiComponentes.innerHTML += `
                        <div class="flex justify-between items-center ${isTotal ? 'pt-2 mt-2 border-t border-primary-200/50 font-semibold' : ''}">
                            <span class="text-surface-500">${label}</span>
                            <span class="font-mono text-surface-800">${(value * 100).toFixed(2)}%</span>
                        </div>
                    `;
                }
            }

            resultadoCard.classList.remove('hidden');
        } catch (e) {
            bdiErro.textContent = 'Erro ao calcular. Verifique os dados.';
            bdiErro.classList.remove('hidden');
        }
    });

    document.getElementById('btn-copiar-bdi').addEventListener('click', function() {
        const valor = bdiValor.textContent;
        const decimal = (parseFloat(valor) / 100).toFixed(4);
        
        // Fallback para ambientes locais HTTP sem HTTPS
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(decimal);
        } else {
            const textArea = document.createElement("textarea");
            textArea.value = decimal;
            textArea.style.position = "absolute";
            textArea.style.left = "-999999px";
            document.body.prepend(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
            } catch (error) {
                console.error('Falha ao copiar', error);
            } finally {
                textArea.remove();
            }
        }
        
        const btn = this;
        btn.innerHTML = '<svg class="w-4 h-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Copiado!';
        btn.classList.add('bg-green-50', 'text-green-600', 'border-green-200');
        setTimeout(() => {
            btn.innerHTML = 'Copiar Valor decimal';
            btn.classList.remove('bg-green-50', 'text-green-600', 'border-green-200');
        }, 2000);
    });
});
</script>
@endpush
