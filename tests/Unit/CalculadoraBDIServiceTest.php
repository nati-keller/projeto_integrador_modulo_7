<?php

namespace Tests\Unit;

use App\Enums\RegimeTributario;
use App\Services\CalculadoraBDIService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CalculadoraBDIServiceTest extends TestCase
{
    private CalculadoraBDIService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CalculadoraBDIService();
    }

    /**
     * Exemplo do professor: DA=5%, DF=1%, LB=10%, ISS=5%, PIS=0.65%, COFINS=3%
     * BDI = (1.05 × 1.01 × 1.10) / (1 − 0.0865) − 1 = 0.2770 (27,70%)
     */
    public function test_lucro_real_formula_professor(): void
    {
        $resultado = $this->service->calcular(RegimeTributario::LUCRO_REAL, [
            'desp_administrativas' => 0.05,
            'desp_financeiras'     => 0.01,
            'lucro_bruto'          => 0.10,
            'iss'                  => 0.05,
            'pis'                  => 0.0065,
            'cofins'               => 0.03,
        ]);

        $this->assertEqualsWithDelta(0.2770, $resultado['bdi'], 0.001,
            'BDI com valores do professor deve ser ≈ 27,70%');
        $this->assertEquals('LUCRO_REAL', $resultado['regime']);
        $this->assertArrayHasKey('componentes', $resultado);
        $this->assertEquals(0.0865, $resultado['componentes']['tributos_total']);
    }

    /**
     * Simples Nacional: usa alíquota consolidada em vez de ISS+PIS+COFINS.
     */
    public function test_simples_nacional_aliquota_consolidada(): void
    {
        $resultado = $this->service->calcular(RegimeTributario::SIMPLES_NACIONAL, [
            'desp_administrativas' => 0.05,
            'desp_financeiras'     => 0.01,
            'lucro_bruto'          => 0.10,
            'aliquota_simples'     => 0.06, // 6%
        ]);

        // (1.05 × 1.01 × 1.10) / (1 − 0.06) − 1 = 1.16655 / 0.94 − 1 = 0.24101...
        $this->assertEqualsWithDelta(0.2410, $resultado['bdi'], 0.001);
        $this->assertEquals('SIMPLES_NACIONAL', $resultado['regime']);
        $this->assertArrayHasKey('aliquota_simples', $resultado['componentes']);
        $this->assertArrayNotHasKey('iss', $resultado['componentes']);
    }

    /**
     * Lucro Real com todos os componentes zerados → BDI = 0.
     */
    public function test_lucro_real_componentes_zerados(): void
    {
        $resultado = $this->service->calcular(RegimeTributario::LUCRO_REAL, [
            'desp_administrativas' => 0,
            'desp_financeiras'     => 0,
            'lucro_bruto'          => 0,
            'iss'                  => 0,
            'pis'                  => 0,
            'cofins'               => 0,
        ]);

        $this->assertEquals(0.0, $resultado['bdi']);
    }

    /**
     * Tributos = 100% → denominador ≤ 0 → exceção.
     */
    public function test_denominador_zero_lanca_excecao(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->calcular(RegimeTributario::LUCRO_REAL, [
            'desp_administrativas' => 0.05,
            'desp_financeiras'     => 0.01,
            'lucro_bruto'          => 0.10,
            'iss'                  => 0.50,
            'pis'                  => 0.30,
            'cofins'               => 0.20, // total tributos = 1.0 → denominador = 0
        ]);
    }

    /**
     * Campo com valor > 1 → exceção de validação.
     */
    public function test_input_invalido_campo_maior_que_1(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('desp_administrativas');

        $this->service->calcular(RegimeTributario::LUCRO_REAL, [
            'desp_administrativas' => 1.5, // inválido
            'desp_financeiras'     => 0.01,
            'lucro_bruto'          => 0.10,
            'iss'                  => 0.05,
            'pis'                  => 0.0065,
            'cofins'               => 0.03,
        ]);
    }

    /**
     * Campo com valor negativo → exceção de validação.
     */
    public function test_input_invalido_campo_negativo(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->calcular(RegimeTributario::SIMPLES_NACIONAL, [
            'desp_administrativas' => 0.05,
            'desp_financeiras'     => -0.01, // inválido
            'lucro_bruto'          => 0.10,
            'aliquota_simples'     => 0.06,
        ]);
    }

    /**
     * Simples Nacional sem alíquota (default 0) → BDI calculável, sem exceção.
     */
    public function test_simples_nacional_sem_aliquota(): void
    {
        $resultado = $this->service->calcular(RegimeTributario::SIMPLES_NACIONAL, [
            'desp_administrativas' => 0.05,
            'desp_financeiras'     => 0.01,
            'lucro_bruto'          => 0.10,
            // aliquota_simples omitida → default 0
        ]);

        // (1.05 × 1.01 × 1.10) / (1 − 0) − 1 = 1.16655 − 1 = 0.16655
        $this->assertEqualsWithDelta(0.1666, $resultado['bdi'], 0.001);
    }
}
