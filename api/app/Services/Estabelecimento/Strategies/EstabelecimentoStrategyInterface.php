<?php

namespace App\Services\Estabelecimento\Strategies;

use Illuminate\Http\Request;

interface EstabelecimentoStrategyInterface
{
    public function handle(Request $request): array;
}