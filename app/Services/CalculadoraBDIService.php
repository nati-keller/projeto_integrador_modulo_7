<?php

namespace App\Services;

use App\Enums\RegimeTributario;
use InvalidArgumentException;

class CalculadoraBDIService
{
    /**
     * Calcula o BDI conforme fórmula do professor e regime tributário.
     *
     * Fórmula: BDI = [(1 + %Desp.Adm) × (1 + %Desp.Fin) × (1 + %Lucro Bruto) / (1 − %Tributos)] − 1
     *
     * RN01 (Lucro Real): IRPJ e CSLL NÃO entram — tributos = ISS + PIS + COFINS
     * RN02 (Simples Nacional): tributos = alíquota consolidada (ISS/PIS/COFINS ocultos)
     *
     * Exemplo do professor: DA=5%, DF=1%, LB=10%, ISS=5%, PIS=0.65%, COFINS=3%
     * BDI = (1.05 × 1.01 × 1.10) / (1 − 0.0865) − 1 = 27,70%
     *
     * @param  RegimeTributario  $regime
     * @param  array             $input  Percentuais decimais (0..1)
     * @return array  ['bdi' => float, 'regime' => string, 'componentes' => array]
     * @throws InvalidArgumentException
     */
    public function calcular(RegimeTributario $regime, array $input): array
    {
        $this->validarInput($input, $regime);

        return match ($regime) {
            RegimeTributario::LUCRO_REAL       => $this->calcularLucroReal($input),
            RegimeTributario::SIMPLES_NACIONAL => $this->calcularSimplesNacional($input),
        };
    }

    // ── Lucro Real: tributos = ISS + PIS + COFINS (RN01: IRPJ e CSLL excluídos) ──
    private function calcularLucroReal(array $i): array
    {
        $da  = (float) ($i['desp_administrativas'] ?? 0);
        $df  = (float) ($i['desp_financeiras']     ?? 0);
        $lb  = (float) ($i['lucro_bruto']          ?? 0);
        $iss = (float) ($i['iss']                  ?? 0);
        $pis = (float) ($i['pis']                  ?? 0);
        $cof = (float) ($i['cofins']               ?? 0);

        $tributos = $iss + $pis + $cof;

        $numerador   = (1 + $da) * (1 + $df) * (1 + $lb);
        $denominador = 1 - $tributos;

        if ($denominador <= 0) {
            throw new InvalidArgumentException(
                'Soma dos tributos ≥ 100% — combinação inválida (denominador ≤ 0).'
            );
        }

        $bdi = ($numerador / $denominador) - 1;

        return [
            'bdi'         => round($bdi, 4),
            'regime'      => RegimeTributario::LUCRO_REAL->value,
            'componentes' => [
                'desp_administrativas' => $da,
                'desp_financeiras'     => $df,
                'lucro_bruto'          => $lb,
                'iss'                  => $iss,
                'pis'                  => $pis,
                'cofins'               => $cof,
                'tributos_total'       => $tributos,
            ],
        ];
    }

    // ── Simples Nacional: tributos = alíquota consolidada (RN02) ──
    private function calcularSimplesNacional(array $i): array
    {
        $da       = (float) ($i['desp_administrativas'] ?? 0);
        $df       = (float) ($i['desp_financeiras']     ?? 0);
        $lb       = (float) ($i['lucro_bruto']          ?? 0);
        $aliquota = (float) ($i['aliquota_simples']     ?? 0);

        $numerador   = (1 + $da) * (1 + $df) * (1 + $lb);
        $denominador = 1 - $aliquota;

        if ($denominador <= 0) {
            throw new InvalidArgumentException(
                'Alíquota do Simples ≥ 100% — combinação inválida (denominador ≤ 0).'
            );
        }

        $bdi = ($numerador / $denominador) - 1;

        return [
            'bdi'         => round($bdi, 4),
            'regime'      => RegimeTributario::SIMPLES_NACIONAL->value,
            'componentes' => [
                'desp_administrativas' => $da,
                'desp_financeiras'     => $df,
                'lucro_bruto'          => $lb,
                'aliquota_simples'     => $aliquota,
            ],
        ];
    }

    private function validarInput(array $input, RegimeTributario $regime): void
    {
        $campos = ['desp_administrativas', 'desp_financeiras', 'lucro_bruto'];

        if ($regime === RegimeTributario::SIMPLES_NACIONAL) {
            $campos[] = 'aliquota_simples';
        } else {
            $campos = array_merge($campos, ['iss', 'pis', 'cofins']);
        }

        foreach ($campos as $campo) {
            $valor = $input[$campo] ?? 0;
            if ((float) $valor < 0 || (float) $valor > 1) {
                throw new InvalidArgumentException(
                    "Campo '{$campo}' deve estar entre 0 e 1. Valor recebido: {$valor}"
                );
            }
        }
    }
}
