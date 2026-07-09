<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mod7_empresa_fiscal', function (Blueprint $table) {
            $table->uuid('company_id')->primary(); // ref lógica → mod4_tempmod2.company_id (sem FK física)
            $table->string('regime_tributario', 20)->nullable(); // LUCRO_REAL | SIMPLES_NACIONAL
            $table->decimal('aliquota_simples', 5, 4)->nullable(); // apenas Simples Nacional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mod7_empresa_fiscal');
    }
};
