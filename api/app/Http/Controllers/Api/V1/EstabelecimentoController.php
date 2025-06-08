<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Models\Estabelecimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstabelecimentoController extends Controller
{
    protected ApiResponse $response;

    public function __construct()
    {
        $this->response = new ApiResponse();
    }

    public function index()
    {
        $data = Estabelecimento::paginate(10);
        return $this->response->success('Lista de estabelecimentos carregada', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo' => 'required|in:cpf,cnpj',
            'documento' => 'required|string|size:14|unique:estabelecimentos,documento,NULL,id,deleted_at,NULL',
            'nome' => 'required|string|max:100',
            'contato' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'telefone' => 'nullable|string|max:14',
        ]);

        if ($validator->fails()) {
            return $this->response->error('Erro de validação', $validator->errors(), 422);
        }

        $estabelecimento = Estabelecimento::create($request->all());
        return $this->response->success('Estabelecimento criado com sucesso', $estabelecimento, 201);
    }

    public function show($uuid)
    {
        $estabelecimento = Estabelecimento::where('uuid', $uuid)->first();

        if (!$estabelecimento) {
            return $this->response->error('Estabelecimento não encontrado', null, 404);
        }

        return $this->response->success('Estabelecimento localizado', $estabelecimento);
    }

    public function update(Request $request, $uuid)
    {
        $estabelecimento = Estabelecimento::where('uuid', $uuid)->first();

        if (!$estabelecimento) {
            return $this->response->error('Estabelecimento não encontrado', null, 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo' => 'in:cpf,cnpj',
            'documento' => 'string|size:14|unique:estabelecimentos,documento,' . $estabelecimento->id . ',id,deleted_at,NULL',
            'nome' => 'string|max:100',
            'contato' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'telefone' => 'nullable|string|max:14',
        ]);

        if ($validator->fails()) {
            return $this->response->error('Erro de validação', $validator->errors(), 422);
        }

        $estabelecimento->update($request->all());
        return $this->response->success('Estabelecimento atualizado com sucesso', $estabelecimento);
    }

    public function destroy($uuid)
    {
        $estabelecimento = Estabelecimento::where('uuid', $uuid)->first();

        if (!$estabelecimento) {
            return $this->response->error('Estabelecimento não encontrado', null, 404);
        }

        $estabelecimento->delete();
        return $this->response->success('Estabelecimento excluído com sucesso');
    }

    public function buscarPorDocumento(string $cpfCnpj)
    {
        $estabelecimento = Estabelecimento::where('documento', $cpfCnpj)->first();

        if (!$estabelecimento) {
            return $this->response
                ->error('Estabelecimento não encontrado', null, 404)
                ->send();
        }

        return $this->response
            ->success('Estabelecimento localizado com sucesso', $estabelecimento)
            ->send();
    }

}
