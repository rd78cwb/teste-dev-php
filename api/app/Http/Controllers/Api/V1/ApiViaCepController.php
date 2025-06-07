<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiExternalController;
use App\Helpers\ApiResponse;

/**
 * Controlador responsável por buscar CEPs na API ViaCEP.
 *
 * Exemplo de endpoint: https://viacep.com.br/ws/01001000/json/
 */
class ApiViaCepController extends ApiExternalController
{
    protected string $uriCep;

    public function __construct()
    {
        parent::__construct(); // <- Isso é essencial
        $this->url     = 'https://viacep.com.br/';
        $this->uriCep  = '/ws/:cep/json';
    }

    /**
     * Consulta o CEP
     *
     * @param string $cep : CEP
     * @return JsonResponse
     */
    public function consultarCep(string $cep): JsonResponse
    {
        try {
            $cep = preg_replace('/\D/', '', $cep);
            $cep = str_pad($cep, 8, '0', STR_PAD_LEFT);

            if (strlen($cep) > 8) {
                throw new \Exception('CEP inválido', 422);
            }

            $dados = $this->request($this->uriCep, ['cep'=>$cep]);

            return $this->apiResponse
                ->success('Consulta de CEP realizada com sucesso', $dados, 200)
                ->send();

        } catch (\Throwable $e) {
            return $this->apiResponse
                ->error($e->getMessage(), [], $e->getCode())
                ->send();
        }
    }
}
