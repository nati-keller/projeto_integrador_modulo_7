<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBDIRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'empresa_id'           => 'required|uuid',
            'desp_administrativas' => 'nullable|numeric|min:0|max:1',
            'desp_financeiras'     => 'nullable|numeric|min:0|max:1',
            'lucro_bruto'          => 'nullable|numeric|min:0|max:1',
            'iss'                  => 'nullable|numeric|min:0|max:1',
            'pis'                  => 'nullable|numeric|min:0|max:1',
            'cofins'               => 'nullable|numeric|min:0|max:1',
            'aliquota_simples'     => 'nullable|numeric|min:0|max:1',
        ];
    }

    public function messages(): array
    {
        return [
            '*.max' => 'Percentuais devem estar entre 0 e 1 (ex: 0.05 para 5%).',
        ];
    }
}
