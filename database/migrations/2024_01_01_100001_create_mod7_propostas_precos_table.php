<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mod7_propostas_precos', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Chaves estrangeiras — referências ao banco compartilhado
            $table->uuid('edital_id');       // FK → mod4_tempmod1.edital_id (Supabase)
            $table->uuid('empresa_id');      // FK → mod4_tempmod2.company_id (Supabase)

            // Dados do item
            $table->text('item_descricao');
            $table->integer('quantidade')->default(1);

            // Componentes de custo
            $table->decimal('custo_base', 15, 2);           // obrigatório, > 0
            $table->decimal('frete', 15, 2)->default(0);
            $table->decimal('garantia', 15, 2)->default(0);
            $table->decimal('mao_de_obra', 15, 2)->default(0);
            $table->decimal('instalacao', 15, 2)->default(0);
            $table->decimal('impostos', 15, 2)->default(0);

            // Margem e BDI
            $table->decimal('margem_lucro', 5, 4);           // ∈ [0, 1]
            $table->decimal('bdi_percentual', 5, 4);         // ∈ [0, 1]

            // Resultados calculados
            $table->decimal('preco_minimo_calculado', 15, 2);
            $table->decimal('preco_total_calculado', 15, 2)->nullable();
            $table->decimal('preco_estimado_pncp', 15, 2)->nullable();

            // Semáforo
            $table->string('margem_status', 10); // VERDE | AMARELO | VERMELHO
            $table->boolean('alerta_inexequibilidade')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mod7_propostas_precos');
    }
};
