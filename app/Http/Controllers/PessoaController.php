<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pessoa;

class PessoaController extends Controller{
    public function index(){
        $pessoas = Pessoa::all();
        return inertia('pessoas/Index', compact('pessoas'));
    }

    public function manutencao(){
        return inertia('pessoas/Manutencao');
    }

    public function edit(Pessoa $pessoa){
        return inertia('pessoas/Manutencao', compact('pessoa'));
    }

    public function store(Request $request){
        try{
            $validated = $request->validate([
                'nome' => 'required|string|max:100',
                'cpf' => 'required|string|max:14',
                'tipo' => 'required|string|in:Física,Jurídica',
                'telefone' => 'required|string|max:20',
                'email' => 'required|string|email|max:100',
            ]);
            Pessoa::create($validated);
            
            return redirect()->route('pessoas.index')->with('success', 'Pessoa cadastrada com sucesso.');
            
        }catch(\Exception $e){
            return redirect()->route('pessoas.manutencao')->with('error', 'Erro ao cadastrar pessoa.');
        }

    }

    public function update(Request $request, Pessoa $pessoa){
        try{
            $validated = $request->validate([
                'nome' => 'required|string|max:100',
                'cpf' => 'required|string|max:14',
                'tipo' => 'required|string|in:Física,Jurídica',
                'telefone' => 'required|string|max:20',
                'email' => 'required|string|email|max:100',
            ]);
    
            $pessoa->update([
                'nome' => $request->input('nome'),
                'cpf' => $request->input('cpf'),
                'tipo' => $request->input('tipo'),
                'telefone' => $request->input('telefone'),
                'email' => $request->input('email'),
            ]);
    
            return redirect()->route('pessoas.index')->with('success', 'Pessoa atualizada com sucesso.');
        }catch(\Exception $e){
            return redirect()->route('pessoas.index')->with('error', 'Erro ao atualizar pessoa.');
        }
    }

    public function destroy(Pessoa $pessoa){
        try{
            $pessoa->delete();
            return redirect()->route('pessoas.index')->with('success', 'Pessoa deletada com sucesso.');
        }catch(\Exception $e){
            return redirect()->route('pessoas.index')->with('error', 'Erro ao deletar pessoa.');
        }
    }
}
