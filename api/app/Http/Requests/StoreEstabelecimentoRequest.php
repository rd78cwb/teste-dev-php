<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Cpf;
use App\Rules\Cnpj;

class StoreEstabelecimentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => 'required|in:cpf,cnpj',
            'documento' => ['required', 'string', 'unique:estabelecimentos,documento'],
            'nome' => 'required|string|max:100',
            'contato' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'telefone' => 'nullable|string|max:14',
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('documento', [new Cpf], function ($input) {
            return $input->tipo === 'cpf';
        });

        $validator->sometimes('documento', [new Cnpj], function ($input) {
            return $input->tipo === 'cnpj';
        });
    }
}
