<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropostaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'edital_id'           => 'required|uuid',
            'empresa_id'          => 'required|uuid',
            'item_descricao'      => 'required|string|max:500',
            'custo_base'          => 'required|numeric|min:0.01',
            'frete'               => 'nullable|numeric|min:0',
            'garantia'            => 'nullable|numeric|min:0',
            'mao_de_obra'         => 'nullable|numeric|min:0',
            'instalacao'          => 'nullable|numeric|min:0',
            'impostos'            => 'nullable|numeric|min:0',
            'margem_lucro'        => 'required|numeric|min:0|max:1',
            'bdi_percentual'      => 'required|numeric|min:0|max:1',
            'preco_estimado_pncp' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'custo_base.min'     => 'Custo base deve ser um valor positivo.',
            'margem_lucro.max'   => 'Margem de lucro deve estar entre 0 e 1 (ex: 0.10 para 10%).',
            'bdi_percentual.max' => 'BDI deve estar entre 0 e 1.',
        ];
    }
}
