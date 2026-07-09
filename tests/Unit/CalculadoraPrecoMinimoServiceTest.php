<?php

namespace Tests\Unit;

use App\Enums\MargemStatus;
use App\Services\CalculadoraPrecoMinimoService;
use InvalidArgumentException;
use Tests\TestCase;

class CalculadoraPrecoMinimoServiceTest extends TestCase
{
    private CalculadoraPrecoMinimoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CalculadoraPrecoMinimoService();
    }

    public function test_margem_verde_quando_preco_calculado_menor_que_estimado()
    {
        // Custo total: 1000
        // Margem: 0.1 (10%) -> 1100
        // BDI: 0.2 (20%) -> 1100 * 1.2 = 1320
        // Estimado: 1500 (1320 < 1500 -> VERDE)
        
        $input = [
            'custo_base' => 1000,
            'margem_lucro' => 0.10,
            'bdi_percentual' => 0.20,
            'preco_estimado_pncp' => 1500,
        ];

        $result = $this->service->calcularSemSalvar($input);

        $this->assertEquals(1000, $result['custo_total']);
        $this->assertEquals(1320, $result['preco_minimo']);
        $this->assertEquals(MargemStatus::VERDE->value, $result['margem_status']);
        $this->assertFalse($result['alerta_inexequibilidade']);
    }

    public function test_margem_amarela_quando_preco_calculado_igual_ao_estimado()
    {
        // Custo total: 1000
        // Margem: 0.1 -> 1100
        // BDI: 0.2 -> 1320
        // Estimado: 1320
        
        $input = [
            'custo_base' => 1000,
            'margem_lucro' => 0.10,
            'bdi_percentual' => 0.20,
            'preco_estimado_pncp' => 1320,
        ];

        $result = $this->service->calcularSemSalvar($input);

        $this->assertEquals(MargemStatus::AMARELO->value, $result['margem_status']);
    }

    public function test_margem_amarela_quando_nao_ha_preco_estimado()
    {
        $input = [
            'custo_base' => 1000,
            'margem_lucro' => 0.10,
            'bdi_percentual' => 0.20,
            'preco_estimado_pncp' => null,
        ];

        $result = $this->service->calcularSemSalvar($input);

        $this->assertEquals(MargemStatus::AMARELO->value, $result['margem_status']);
    }

    public function test_margem_vermelha_quando_preco_calculado_maior_que_estimado()
    {
        // Custo total: 1000
        // Margem: 0.1 -> 1100
        // BDI: 0.2 -> 1320
        // Estimado: 1200
        
        $input = [
            'custo_base' => 1000,
            'margem_lucro' => 0.10,
            'bdi_percentual' => 0.20,
            'preco_estimado_pncp' => 1200,
        ];

        $result = $this->service->calcularSemSalvar($input);

        $this->assertEquals(MargemStatus::VERMELHO->value, $result['margem_status']);
    }

    public function test_alerta_inexequibilidade_quando_desconto_maior_que_70_porcento()
    {
        // Estimado: 10000
        // 70% de desconto seria 3000
        // Vamos calcular um preço de 2900
        // Custo: 2000, margem: 0, BDI: 0.45 -> 2000 * 1.45 = 2900
        
        $input = [
            'custo_base' => 2000,
            'margem_lucro' => 0,
            'bdi_percentual' => 0.45,
            'preco_estimado_pncp' => 10000,
        ];

        $result = $this->service->calcularSemSalvar($input);

        $this->assertEquals(2900, $result['preco_minimo']);
        $this->assertTrue($result['alerta_inexequibilidade']);
        $this->assertEquals(MargemStatus::VERDE->value, $result['margem_status']);
    }
}
