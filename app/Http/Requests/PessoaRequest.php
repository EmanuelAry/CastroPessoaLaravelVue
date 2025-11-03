<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class PessoaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $pessoaId = $this->route('pessoa') ? $this->route('pessoa')->id : null;

        return [
            'nome' => 'required|string|max:100',
            'cpf' => [
                'required',
                'string',
                'max:18',
                Rule::unique('pessoas')->ignore($pessoaId)
            ],
            'tipo' => ['required', Rule::in(['Física', 'Jurídica'])],
            'telefone' => 'required|string|max:20',
            'email' => 'required|email|max:100'
        ];
    }


    public function messages(): array
    {
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'cpf.required' => 'O campo CPF/CNPJ é obrigatório.',
            'cpf.unique' => 'Este CPF/CNPJ já está cadastrado.',
            'tipo.required' => 'O campo tipo é obrigatório.',
            'telefone.required' => 'O campo telefone é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Digite um email válido.',
        ];
    }
}
