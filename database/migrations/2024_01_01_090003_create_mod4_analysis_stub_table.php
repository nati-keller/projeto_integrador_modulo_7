<?php

// STUB LOCAL — espelha schema real do Módulo 4 (Supabase).
// Remover/desabilitar quando conectar ao banco compartilhado real.
// Correções aplicadas: id uuid (não bigint), decision boolean (não string),
// colunas decided_at, notes, processing_error, processed_at adicionadas.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mod4_analysis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('edital_id');                              // ref → mod4_tempmod1.edital_id
            $table->uuid('id_cliente');                             // ref → mod4_tempmod2.company_id
            $table->decimal('match_score', 5, 2)->nullable();
            $table->boolean('decision')->nullable();                // boolean, não string
            $table->string('status')->default('new');               // new|pending|go|no_go
            $table->string('processing_status')->default('pending');// pending|processing|done|error
            $table->text('financial_summary')->nullable();          // jsonb no Postgres, text no SQLite
            $table->timestamp('decided_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('processing_error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mod4_analysis');
    }
};
