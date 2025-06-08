<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

/**
 * RouteServiceProvider : responsavel por carregar e configurar as rotas da aplicacao
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Carregamento das rotas
     */
    public function boot(): void
    {
        $this->routes(function () {
            // Rotas de API
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Rotas web
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
