<?php

namespace App\Repositories;

use App\Models\Fornecedor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FornecedorRepositoryInterface
{
    public function getAll(array $filters = [], string $sortBy = 'created_at', string $sortDir = 'desc', int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Fornecedor;
    public function findByCpfCnpj(string $cpfCnpj): ?Fornecedor;
    public function create(array $data): Fornecedor;
    public function update(int $id, array $data): ?Fornecedor;
    public function delete(int $id): bool;
}