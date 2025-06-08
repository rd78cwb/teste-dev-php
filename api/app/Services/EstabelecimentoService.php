<?php

namespace App\Services;

use App\Models\Estabelecimento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;

class EstabelecimentoService
{
    public function list($perPage = 10)
    {
        return Estabelecimento::paginate($perPage);
    }

    public function find(string|int $value): Estabelecimento
    {
        $value = trim($value);
        $cacheKey = $this->generateCacheKey($value);

        // Busca primeiro no cache
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return new Estabelecimento($cached);
        }

        // Busca no banco por uuid, documento ou id
        $est = Estabelecimento::where('uuid', $value)
            ->orWhere('documento', $value)
            ->orWhere('id', $value)
            ->first();

        if (!$est) {
            throw new ModelNotFoundException('Registro não localizado');
        }

        // Armazena no cache - 15 minutos
        Cache::put($cacheKey, $est->toArray(), now()->addMinutes(15));

        return $est;
    }

    private function generateCacheKey(string|int $value): string
    {
        return "establishment:{$value}";
    }

    public function create(array $data): Estabelecimento
    {
        $validator = Validator::make($data, [
            'tipo' => 'required|in:cpf,cnpj',
            'documento' => 'required|string|size:14|unique:estabelecimentos,documento',
            'nome' => 'required|string|max:100',
            'contato' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'telefone' => 'nullable|string|max:14',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $est = Estabelecimento::create($validator->validated());

        // Armazena no cache após criação
        $this->updateCache($est);

        return $est;
    }

    public function update(string $uuid, array $data): Estabelecimento
    {
        $est = $this->find($uuid);

        $validator = Validator::make($data, [
            'tipo' => 'in:cpf,cnpj',
            'documento' => 'string|size:14|unique:estabelecimentos,documento,' . $est->id,
            'nome' => 'string|max:100',
            'contato' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'telefone' => 'nullable|string|max:14',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $est->update($validator->validated());

        // Atualiza os valores no cache (por todas as chaves possíveis)
        $this->updateCache($est);

        return $est;
    }

    public function delete(string $uuid): void
    {
        $est = $this->find($uuid);
        $est->delete();

        // Limpa o cache relacionado
        Cache::forget($this->generateCacheKey($uuid));
        Cache::forget($this->generateCacheKey($est->documento));
        Cache::forget($this->generateCacheKey($est->id));
    }

    private function updateCache(Estabelecimento $est): void
    {
        $ttl = now()->addMinutes(15);

        Cache::put($this->generateCacheKey($est->uuid), $est->toArray(), $ttl);
        Cache::put($this->generateCacheKey($est->documento), $est->toArray(), $ttl);
        Cache::put($this->generateCacheKey($est->id), $est->toArray(), $ttl);
    }
}
