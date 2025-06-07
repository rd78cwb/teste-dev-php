<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

/**
 * Padroniza as respostas da APIs do sistema.
 */
class ApiResponse
{

    protected float  $startTime;  // Tempo inicial de execucao
    protected int    $http;       // Codigo HTTP
    protected bool   $status;     // Status
    protected string $msg;        // Mensagem de retorno da operação
    protected mixed  $data;       // Dados de retorno

    /**
     * Iniciando o construct
     */
    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->msg       = 'Chamada realizada';
        $this->data      = [];
        $this->http      = 404;
        $this->status    = false;
    }

    /**
     * Sucesso na requisicao
     *
     * @param string $msg     Mensagem descritiva
     * @param mixed  $data    Dados a retornar
     * @param int    $http    Código HTTP (default 200)
     * @return $this
     */
    public function success(string $msg = 'Operação realizada com sucesso', mixed $data = null, int $http = 200)
    {
        $this->status = true;
        $this->msg    = $msg;
        $this->data   = $data;
        $this->http   = $http;

        return $this->send();
    }

    /**
     * Erro na requisicao
     *
     * @param string $msg     Mensagem de erro
     * @param mixed  $data    Dados adicionais de erro (opcional)
     * @param int    $http    Código HTTP (default 400)
     * @return $this
     */
    public function error(string $msg = 'Erro na operação', mixed $data = null, int $http = 400)
    {

        $this->status = false;
        $this->msg    = $msg;
        $this->data   = $data;
        $this->http   = $http;

        return $this->send();
    }

    /**
     * Retorna a resposta JSON padronizada com os dados definidos.
     *
     * @return JsonResponse
     */
    public function send(): JsonResponse
    {

        return response()->json([
            'http'     => $this->http,
            'status'   => $this->status,
            'msg'      => $this->msg,
            'data'     => $this->data,
            'time'     => $this->getDuration()
        ], $this->http);
    }

    /**
     * Calcula o tempo de execucao da resposta em Segundos
     *
     * @return float
     */
    protected function getDuration(): float
    {
        return round((microtime(true) - $this->startTime), 4);
    }
}
