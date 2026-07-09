<?php

namespace App\Models;

use App\Enums\RegimeTributario;
use Illuminate\Database\Eloquent\Model;

class EmpresaFiscal extends Model
{
    protected $table = 'mod7_empresa_fiscal';
    protected $primaryKey = 'company_id';

    // PK é uuid vindo de mod4_tempmod2, NÃO auto-gerado
    public $incrementing = false;
    protected $keyType = 'string';

    // Sem HasUuids — company_id é sempre fornecido explicitamente (vem do M4)
    protected $fillable = [
        'company_id', 'regime_tributario', 'aliquota_simples',
    ];

    protected $casts = [
        'regime_tributario' => RegimeTributario::class,
        'aliquota_simples'  => 'decimal:4',
    ];
}
