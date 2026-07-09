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
        Schema::create('mod4_tempmod2', function (Blueprint $table) {
            $table->uuid('company_id')->primary();
            $table->string('cnpj')->nullable();
            $table->string('company_name')->nullable();
            $table->string('keyword_1')->nullable();
            $table->string('keyword_2')->nullable();
            $table->string('keyword_3')->nullable();
            $table->string('keyword_4')->nullable();
            $table->string('keyword_5')->nullable();
            $table->text('company_details')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mod4_tempmod2');
    }
};
