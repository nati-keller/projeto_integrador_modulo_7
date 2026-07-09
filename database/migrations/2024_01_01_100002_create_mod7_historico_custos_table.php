<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mod7_historico_custos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('proposta_id');     // FK → mod7_propostas_precos.id
            $table->uuid('edital_id');       // FK → mod4_tempmod1.edital_id
            $table->uuid('empresa_id');      // FK → mod4_tempmod2.company_id

            $table->string('tipo_documento', 50);  // ORCAMENTO | COTACAO | NOTA_FISCAL | OUTRO
            $table->text('url_arquivo');
            $table->decimal('valor_referencia', 15, 2);
            $table->date('data_documento');

            // Imutável — criado apenas com registrado_em; sem updated_at
            $table->timestamp('registrado_em')->useCurrent();

            // SEM updated_at propositalmente — tabela é append-only (RNF02)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mod7_historico_custos');
    }
};
