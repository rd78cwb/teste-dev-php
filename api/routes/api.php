<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ApiBrasilApiController;
use App\Http\Controllers\Api\V1\ApiViaCepController;
use App\Http\Controllers\Api\Sistema\ApiSistemaController;
use App\Http\Controllers\Api\V1\EstabelecimentoController;

Route::prefix('v1')->group(function () {
    Route::get('external/brasilapi/cep/{cep}'  , [ApiBrasilApiController::class, 'consultarCep']);
    Route::get('external/brasilapi/cnpj/{cnpj}', [ApiBrasilApiController::class, 'consultarCnpj']);
    Route::get('external/viacep/cep/{cep}'     , [ApiViaCepController::class, 'consultarCep']);
    Route::get('estabelecimentos/by-cpf-cnpj/{cpfCnpj}', [EstabelecimentoController::class, 'buscarPorDocumento']);
    Route::apiResource('estabelecimentos', EstabelecimentoController::class);
});

Route::prefix('sistema')->group(function () {
    Route::get('/config/cache', [ApiSistemaController::class, 'cacheConfig']);
});

