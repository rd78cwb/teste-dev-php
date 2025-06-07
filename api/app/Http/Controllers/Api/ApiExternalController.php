<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponse;

/**
 * Classe base para controladores que realizam chamadas HTTP externas.
 */
abstract class ApiExternalController
{
    protected string $url;    // URL Servico externo
    protected apiResponse $apiResponse;

    public function __construct()
    {
        $this->apiResponse = new ApiResponse();
    }

    /**
     * Realiza uma requisição HTTP GET genérica a um serviço externo.
     *
     * @param string $uri       Caminho do recurso (ex: /api/cnpj/v1)
     * @param string $params    Parametros do request
     * @param string $msg       Mensagem de sucesso
     * @return JsonResponse
     */
    protected function request(
        string $uri,
        array $params = []
    ): array {
        $url = $this->buildUrl($uri, $params);
        $cacheKey = 'api_cache:' . md5($url);

        try {

            // Verifica se já existe no cache (TTL de 15 segundos)
            if (Cache::has($cacheKey)) {
                $cached = Cache::get($cacheKey);

                if (is_array($cached)) {
                    return $cached;
                }
            }

            $http = Http::send('GET', $url);

            if ($http->status() === 404) {
                throw new \RuntimeException('Dado não encontrado na base de dados do serviço.', 404);
            }

            if ($http->failed()) {
                throw new \RuntimeException(
                    'Erro ao consultar o serviço externo', $http->status()
                );
            }

            $body  = $http->body();
            $dados = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($dados)) {
                throw new \RuntimeException('Resposta inválida do serviço externo (JSON malformado)', 502);
            }

            // Armazena no Redis por 15 segundos
            Cache::put($cacheKey, $dados, now()->addSeconds(15));

            return $dados;

        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * Constroi a URL para o request
     *
     * @param string $uri    Caminho da URI com placeholders (ex: /ws/:cep/json)
     * @param array  $params Array associativo com os valores para substituir na URI
     *                       (ex: ['cep' => '01001000'])
     * @return string
     */
    protected function buildUrl(string $uri, array $params = []): string
    {
        foreach ($params as $key => $value) {
            $uri = str_replace(':' . $key, $value, $uri);
        }

        return rtrim($this->url, '/') . '/' . ltrim($uri, '/');
    }

}
