<?php

namespace App\Http\Controllers;

use App\Http\Requests\PessoaRequest;
use App\Models\Pessoa;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class PessoaController extends Controller{
    public function index(){
        $pessoas = Pessoa::all();
        return inertia('pessoas/Index', compact('pessoas'));
    }

    public function manutencao(): Response {
        return inertia('pessoas/Manutencao');
    }
    
    public function store(PessoaRequest $request): RedirectResponse {
        Pessoa::create($request->validated());
        return to_route('pessoas.index')->with('success', 'Pessoa cadastrada com sucesso.');
    }

    public function edit(Pessoa $pessoa): Response {
        return inertia('pessoas/Manutencao', compact('pessoa'));
    }

    public function update(PessoaRequest $request, Pessoa $pessoa): RedirectResponse {
        $pessoa->update($request->validated());
        return to_route('pessoas.index')->with('success', 'Pessoa atualizada com sucesso.');
    }

    public function destroy(Pessoa $pessoa): RedirectResponse {
        $pessoa->delete();
        return to_route('pessoas.index')->with('success', 'Pessoa removida com sucesso.');
    }
}