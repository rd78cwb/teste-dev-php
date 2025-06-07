<?php

namespace App\Http\Controllers\Api\Sistema;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Api\ApiExternalController;

class ApiSistemaController extends ApiExternalController
{
    public function cacheConfig(): JsonResponse
    {
        try {
            $config = [
                'driver'      => Config::get('cache.default'),
                'prefix'      => Config::get('cache.prefix'),
                'store'       => Config::get('cache.stores.' . Config::get('cache.default')),
                'ttl_default' => ini_get('default_socket_timeout'), // TTL padrão de conexões
                'enabled'     => Config::get('cache.default') !== 'null'
            ];

            return (new ApiResponse)
                ->success('Configurações de cache carregadas com sucesso', $config)
                ->send();

        } catch (\Throwable $e) {
            return (new ApiResponse)
                ->error('Erro ao obter configurações de cache', ['exception' => $e->getMessage()], 500)
                ->send();
        }
    }
}
