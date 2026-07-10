<?php

// STUB LOCAL — espelha schema real do Módulo 4 (Supabase).
// Remover/desabilitar quando conectar ao banco compartilhado real.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mod4_tempmod1', function (Blueprint $table) {
            $table->uuid('edital_id')->primary();
            $table->string('orgao')->nullable();
            $table->text('objeto')->nullable();
            $table->integer('quantidade')->nullable();
            $table->text('doc_1')->nullable();
            $table->text('doc_2')->nullable();
            $table->text('doc_3')->nullable();
            $table->text('doc_4')->nullable();
            $table->text('doc_5')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->bigInteger('mod1_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mod4_tempmod1');
    }
};
