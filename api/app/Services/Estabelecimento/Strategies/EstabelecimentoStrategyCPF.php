<?php

namespace App\Services\Estabelecimento\Strategies;

use App\Models\Estabelecimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\Cpf;

class EstabelecimentoStrategyCPF implements EstabelecimentoStrategyInterface
{
    public function handle(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'documento' => ['required', 'string', 'size:11', 'unique:estabelecimentos,documento', new Cpf],
            'nome' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'telefone' => 'nullable|string|max:14',
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'errors' => $validator->errors()];
        }

        $estabelecimento = Estabelecimento::create($request->all());

        return ['status' => true, 'data' => $estabelecimento];
    }
}
