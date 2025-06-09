<?php

namespace App\Services\Estabelecimento;

use App\Services\Estabelecimento\Strategies\EstabelecimentoStrategyCPF;
use App\Services\Estabelecimento\Strategies\EstabelecimentoStrategyCNPJ;
use App\Services\Estabelecimento\Strategies\EstabelecimentoStrategyInterface;
use Illuminate\Http\Request;

class EstabelecimentoContext
{
    protected EstabelecimentoStrategyInterface $strategy;

    public function __construct(string $tipo)
    {
        $this->strategy = match ($tipo) {
            'cpf'  => new EstabelecimentoStrategyCpf(),
            'cnpj' => new EstabelecimentoStrategyCnpj(),
            default => throw new \InvalidArgumentException('Tipo de documento inválido')
        };
    }

    public function handle(Request $request): array
    {
        return $this->strategy->handle($request);
    }
}
