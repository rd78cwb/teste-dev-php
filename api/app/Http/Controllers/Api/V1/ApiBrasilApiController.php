<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiExternalController;
use App\Rules\Cnpj;

/**
 * Controlador responsável por buscar informações na BrasilAPI.
 */
class ApiBrasilApiController extends ApiExternalController
{
    protected string $uriCnpj;
    protected string $uriCep;

    public function __construct()
    {
        parent::__construct();
        $this->url     = 'https://brasilapi.com.br';
        $this->uriCnpj = '/api/cnpj/v1/:cnpj';
        $this->uriCep  = '/api/cep/v1/:cep';
    }

    /**
     * Consulta o CNPJ
     *
     * @param string $cnpj : CNPJ
     * @return JsonResponse
     */
    public function consultarCnpj(string $cnpj): JsonResponse
    {
        try {
            $cnpj = preg_replace('/\D/', '', $cnpj);
            $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);

            $validator = Validator::make(['cnpj' => $cnpj], ['cnpj' => ['required', new Cnpj]]);

            if ($validator->fails()) {
                throw new \Exception('CNPJ inválido', 422);
            }

            $dados = $this->request($this->uriCnpj, ['cnpj' => $cnpj]);

            return $this->apiResponse
                ->success('Consulta de CNPJ realizada com sucesso', $dados, 200)
                ->send();

        } catch (\Throwable $e) {
            return $this->apiResponse
                ->error($e->getMessage(), [], $e->getCode() ?: 400)
                ->send();
        }
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

            $dados = $this->request($this->uriCep, ['cep' => $cep] );

            return $this->apiResponse
                ->success('Consulta de CEP realizada com sucesso', $dados, 200)
                ->send();

        } catch (\Throwable $e) {

            return $this->apiResponse
                ->error($e->getMessage(), [], $e->getCode() ?: 400)
                ->send();
        }
    }

}
