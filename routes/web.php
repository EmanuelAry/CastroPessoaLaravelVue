<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PessoaController;

Route::get('/', [PessoaController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pessoas', [PessoaController::class, 'index'])->name('pessoas.index');
    Route::get('/pessoas/manutencao', [PessoaController::class, 'manutencao'])->name('pessoas.manutencao');
    Route::get('/pessoas/edit/{pessoa}', [PessoaController::class, 'edit'])->name('pessoas.edit');
    Route::post('/pessoas', [PessoaController::class, 'store'])->name('pessoas.store');
    Route::put('/pessoas/edit/{pessoa}', [PessoaController::class, 'update'])->name('pessoas.update');
    Route::delete('/pessoas/{pessoa}', [PessoaController::class, 'destroy'])->name('pessoas.destroy');
});

require __DIR__.'/settings.php';
