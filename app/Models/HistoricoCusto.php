<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class HistoricoCusto extends Model
{
    use HasUuids;

    protected $table    = 'mod7_historico_custos';
    public    $timestamps = false; // tabela imutável — sem updated_at

    protected $fillable = [
        'proposta_id', 'edital_id', 'empresa_id',
        'tipo_documento', 'url_arquivo', 'valor_referencia',
        'data_documento',
    ];

    protected $casts = [
        'valor_referencia' => 'decimal:2',
        'data_documento'   => 'date',
        'registrado_em'    => 'datetime',
    ];

    // NUNCA permitir update — imutabilidade por código (RNF02)
    public function save(array $options = []): bool
    {
        if ($this->exists) {
            throw new \RuntimeException('Registros do histórico de custos são imutáveis (RNF02).');
        }
        return parent::save($options);
    }

    public function proposta()
    {
        return $this->belongsTo(PropostaPreco::class, 'proposta_id');
    }
}
