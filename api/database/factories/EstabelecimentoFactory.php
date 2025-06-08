<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EstabelecimentoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'tipo' => $this->faker->randomElement(['cpf', 'cnpj']),
            'documento' => $this->faker->numerify('##############'), // 14 dígitos
            'nome' => $this->faker->company,
            'contato' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'telefone' => $this->faker->numerify('###########'),
        ];
    }
}
