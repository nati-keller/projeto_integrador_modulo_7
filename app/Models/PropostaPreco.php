<?php

namespace App\Models;

use App\Enums\MargemStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PropostaPreco extends Model
{
    use HasUuids;

    protected $table = 'mod7_propostas_precos';

    protected $fillable = [
        'edital_id', 'empresa_id', 'item_descricao', 'quantidade',
        'custo_base', 'frete', 'garantia', 'mao_de_obra',
        'instalacao', 'impostos', 'margem_lucro', 'bdi_percentual',
        'preco_minimo_calculado', 'preco_total_calculado', 'preco_estimado_pncp',
        'margem_status', 'alerta_inexequibilidade',
    ];

    protected $casts = [
        'custo_base'              => 'decimal:2',
        'frete'                   => 'decimal:2',
        'garantia'                => 'decimal:2',
        'mao_de_obra'             => 'decimal:2',
        'instalacao'              => 'decimal:2',
        'impostos'                => 'decimal:2',
        'margem_lucro'            => 'decimal:4',
        'bdi_percentual'          => 'decimal:4',
        'preco_minimo_calculado'  => 'decimal:2',
        'preco_estimado_pncp'     => 'decimal:2',
        'margem_status'           => MargemStatus::class,
        'alerta_inexequibilidade' => 'boolean',
    ];

    public function historicos()
    {
        return $this->hasMany(HistoricoCusto::class, 'proposta_id');
    }

    // Accessor — desconto em relação ao PNCP
    public function getDescontoPercentualAttribute(): ?float
    {
        if (!$this->preco_estimado_pncp || $this->preco_estimado_pncp == 0) {
            return null;
        }
        return (($this->preco_estimado_pncp - $this->preco_minimo_calculado) / $this->preco_estimado_pncp) * 100;
    }
}
