<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Estabelecimento;

class EstabelecimentoFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_criar_estabelecimento()
    {
        $payload = [
            'tipo' => 'cnpj',
            'documento' => '12345678000199',
            'nome' => 'Empresa Teste Ltda',
            'contato' => 'Joao da Silva',
            'email' => 'joao@empresa.com',
            'telefone' => '11999998888',
        ];

        $response = $this->postJson('/api/v1/estabelecimentos', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nome' => 'Empresa Teste Ltda']);

        $this->assertDatabaseHas('estabelecimentos', [
            'documento' => '12345678000199',
        ]);
    }

    public function test_criar_estabelecimento_duplicado()
    {
        Estabelecimento::factory()->create([
            'documento' => '12345678000199',
        ]);

        $payload = [
            'tipo' => 'cnpj',
            'documento' => '12345678000199',
            'nome' => 'Empresa Teste 2',
        ];

        $response = $this->postJson('/api/v1/estabelecimentos', $payload);

        $response->assertStatus(422)
                 ->assertJsonFragment(['msg' => 'Erro de validação']);
    }

    public function test_buscar_estabelecimento_por_uuid()
    {
        $estabelecimento = Estabelecimento::factory()->create();

        $response = $this->getJson("/api/v1/estabelecimentos/{$estabelecimento->uuid}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['uuid' => $estabelecimento->uuid]);
    }

    public function test_atualizar_estabelecimento()
    {
        $estabelecimento = Estabelecimento::factory()->create();

        $response = $this->putJson("/api/v1/estabelecimentos/{$estabelecimento->uuid}", [
            'nome' => 'Nome Atualizado'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nome' => 'Nome Atualizado']);
    }

    public function test_atualizar_estabelecimento_nome()
    {
        $estabelecimento = Estabelecimento::factory()->create([
            'nome' => 'Nome Original'
        ]);

        $this->putJson("/api/v1/estabelecimentos/{$estabelecimento->uuid}", [
            'nome' => 'Nome Atualizado'
        ])->assertStatus(200);

        $this->assertDatabaseHas('estabelecimentos', [
            'uuid' => $estabelecimento->uuid,
            'nome' => 'Nome Atualizado',
        ]);

        $this->assertDatabaseMissing('estabelecimentos', [
            'uuid' => $estabelecimento->uuid,
            'nome' => 'Nome Original',
        ]);
    }


    public function test_excluir_estabelecimento()
    {
        $estabelecimento = Estabelecimento::factory()->create();

        $response = $this->deleteJson("/api/v1/estabelecimentos/{$estabelecimento->uuid}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('estabelecimentos', [
            'uuid' => $estabelecimento->uuid
        ]);
    }

    public function test_listar_estabelecimentos()
    {
        Estabelecimento::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/estabelecimentos');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'http', 'status', 'msg']);
    }
}
