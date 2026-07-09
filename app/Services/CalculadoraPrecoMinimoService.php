<?php

namespace App\Services;

use App\Enums\MargemStatus;
use App\Models\PropostaPreco;
use InvalidArgumentException;

class CalculadoraPrecoMinimoService
{
    /**
     * Calcula o preço mínimo e persiste no banco (RF04).
     *
     * @throws InvalidArgumentException
     */
    public function calcular(array $input): array
    {
        $this->validarInput($input);

        $custoTotal = $this->somarCustos($input);
        $precoMin   = $this->aplicarMargemEBDI($custoTotal, $input['margem_lucro'], $input['bdi_percentual']);
        $status     = $this->classificarMargem($precoMin, $input['preco_estimado_pncp'] ?? null);
        $alerta     = $this->verificarInexequibilidade($precoMin, $input['preco_estimado_pncp'] ?? null);

        $proposta = PropostaPreco::create([
            'edital_id'               => $input['edital_id'],
            'empresa_id'              => $input['empresa_id'],
            'item_descricao'          => $input['item_descricao'],
            'custo_base'              => $input['custo_base'],
            'frete'                   => $input['frete'] ?? 0,
            'garantia'                => $input['garantia'] ?? 0,
            'mao_de_obra'             => $input['mao_de_obra'] ?? 0,
            'instalacao'              => $input['instalacao'] ?? 0,
            'impostos'                => $input['impostos'] ?? 0,
            'margem_lucro'            => $input['margem_lucro'],
            'bdi_percentual'          => $input['bdi_percentual'],
            'preco_minimo_calculado'  => $precoMin,
            'preco_estimado_pncp'     => $input['preco_estimado_pncp'] ?? null,
            'margem_status'           => $status->value,
            'alerta_inexequibilidade' => $alerta,
        ]);

        return [
            'proposta'               => $proposta,
            'custo_total'            => $custoTotal,
            'preco_minimo'           => $precoMin,
            'margem_status'          => $status,
            'alerta_inexequibilidade'=> $alerta,
            'detalhamento'           => $this->detalharCustos($input, $custoTotal, $precoMin),
        ];
    }

    // ── Apenas calcula, sem persistir (para preview em tempo real) ────────
    public function calcularSemSalvar(array $input): array
    {
        $custo    = $this->somarCustos($input);
        $preco    = $this->aplicarMargemEBDI($custo, $input['margem_lucro'] ?? 0, $input['bdi_percentual'] ?? 0);
        $status   = $this->classificarMargem($preco, $input['preco_estimado_pncp'] ?? null);
        $alerta   = $this->verificarInexequibilidade($preco, $input['preco_estimado_pncp'] ?? null);

        return [
            'custo_total'             => round($custo, 2),
            'preco_minimo'            => round($preco, 2),
            'margem_status'           => $status->value,
            'alerta_inexequibilidade' => $alerta,
            'detalhamento'            => $this->detalharCustos($input, $custo, $preco),
        ];
    }

    private function somarCustos(array $i): float
    {
        return (float)($i['custo_base'] ?? 0)
             + (float)($i['frete']      ?? 0)
             + (float)($i['garantia']   ?? 0)
             + (float)($i['mao_de_obra']?? 0)
             + (float)($i['instalacao'] ?? 0)
             + (float)($i['impostos']   ?? 0);
    }

    private function aplicarMargemEBDI(float $custo, float $margem, float $bdi): float
    {
        return $custo * (1 + $margem) * (1 + $bdi);
    }

    private function classificarMargem(float $preco, ?float $estimado): MargemStatus
    {
        if ($preco <= 0) return MargemStatus::VERMELHO;
        if ($estimado === null) return MargemStatus::AMARELO;
        if ($preco < $estimado) return MargemStatus::VERDE;
        if ($preco == $estimado) return MargemStatus::AMARELO;
        return MargemStatus::VERMELHO;
    }

    private function verificarInexequibilidade(float $preco, ?float $estimado): bool
    {
        if ($estimado === null || $estimado == 0) return false;
        $desconto = ($estimado - $preco) / $estimado;
        return $desconto > 0.70; // RN03 — acima de 70% de desconto
    }

    private function detalharCustos(array $i, float $custo, float $preco): array
    {
        return [
            'Custo Base'      => (float)($i['custo_base']  ?? 0),
            'Frete'           => (float)($i['frete']       ?? 0),
            'Garantia'        => (float)($i['garantia']    ?? 0),
            'Mão de Obra'     => (float)($i['mao_de_obra'] ?? 0),
            'Instalação'      => (float)($i['instalacao']  ?? 0),
            'Impostos'        => (float)($i['impostos']    ?? 0),
            'Custo Total'     => round($custo, 2),
            'BDI'             => round($custo * ($i['bdi_percentual'] ?? 0), 2),
            'Margem de Lucro' => round($custo * ($i['margem_lucro'] ?? 0), 2),
            'Preço Mínimo'    => round($preco, 2),
        ];
    }

    private function validarInput(array $i): void
    {
        if (empty($i['custo_base']) || (float)$i['custo_base'] <= 0) {
            throw new InvalidArgumentException('Custo base deve ser um valor positivo.');
        }

        $campos = ['frete', 'garantia', 'mao_de_obra', 'instalacao', 'impostos'];
        foreach ($campos as $campo) {
            if (isset($i[$campo]) && (float)$i[$campo] < 0) {
                throw new InvalidArgumentException("Campo '{$campo}' não pode ser negativo.");
            }
        }

        $margem = $i['margem_lucro'] ?? null;
        if ($margem === null || (float)$margem < 0 || (float)$margem > 1) {
            throw new InvalidArgumentException('Margem de lucro deve estar entre 0 e 1.');
        }

        $bdi = $i['bdi_percentual'] ?? null;
        if ($bdi === null || (float)$bdi < 0 || (float)$bdi > 1) {
            throw new InvalidArgumentException('BDI deve estar entre 0 e 1.');
        }
    }
}
