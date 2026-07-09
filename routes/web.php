<?php

use App\Http\Controllers\PropostaController;
use App\Http\Controllers\BDIController;
use App\Http\Controllers\HistoricoController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('propostas.index'));

// Propostas
Route::resource('propostas', PropostaController::class)->only(['index', 'create', 'store', 'show']);
Route::post('propostas/preview', [PropostaController::class, 'preview'])->name('propostas.preview');
Route::get('propostas/editais/{empresa}', [PropostaController::class, 'editais'])->name('propostas.editais');

// Calculadora BDI
Route::get('bdi', [BDIController::class, 'index'])->name('bdi.index');
Route::post('bdi/calcular', [BDIController::class, 'calcular'])->name('bdi.calcular');

// Histórico
Route::get('historico', [HistoricoController::class, 'index'])->name('historico.index');
Route::post('historico', [HistoricoController::class, 'store'])->name('historico.store');
Route::put('historico/{id}', [HistoricoController::class, 'update']);
Route::delete('historico/{id}', [HistoricoController::class, 'destroy']);
