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
            'documento' => '12345678901230',
            'nome' => 'Empresa Teste Ltda',
            'contato' => 'Joao da Silva',
            'email' => 'joao@empresa.com',
            'telefone' => '11999998888',
        ];

        $response = $this->postJson('/api/v1/estabelecimentos', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nome' => 'Empresa Teste Ltda']);

        $this->assertDatabaseHas('estabelecimentos', [
            'documento' => '12345678901230',
        ]);
    }

    public function test_criar_estabelecimento_duplicado()
    {
        Estabelecimento::factory()->create([
            'documento' => '12345678901230',
        ]);

        $payload = [
            'tipo' => 'cnpj',
            'documento' => '12345678901230',
            'nome' => 'Empresa Teste 2',
        ];

        $response = $this->postJson('/api/v1/estabelecimentos', $payload);

        $response->assertStatus(422)
                 ->assertJsonFragment(['msg' => 'Erro de validação']);
    }

    public function test_criar_estabelecimento_com_cpf_valido()
    {
        $cpf = $this->gerarCpfValido();

        $payload = [
            'tipo' => 'cpf',
            'documento' => $cpf,
            'nome' => 'Joana Teste',
            'contato' => 'Joana',
            'email' => 'joana@testecpf.com',
            'telefone' => '11988887777',
        ];

        $response = $this->postJson('/api/v1/estabelecimentos', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nome' => 'Joana Teste']);

        $this->assertDatabaseHas('estabelecimentos', [
            'documento' => $cpf,
        ]);
    }

    public function test_criar_estabelecimento_com_cnpj_valido()
    {
        $cnpj = $this->gerarCnpjValido();

        $payload = [
            'tipo' => 'cnpj',
            'documento' => $cnpj,
            'nome' => 'Empresa CNPJ Válido',
            'contato' => 'Carlos',
            'email' => 'cnpjvalido@empresa.com',
            'telefone' => '11977776666',
        ];

        $response = $this->postJson('/api/v1/estabelecimentos', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nome' => 'Empresa CNPJ Válido']);

        $this->assertDatabaseHas('estabelecimentos', [
            'documento' => $cnpj,
        ]);
    }

    public function test_nao_criar_estabelecimento_com_cpf_invalido()
    {
        $cpfInvalido = '12345678900';

        $payload = [
            'tipo' => 'cpf',
            'documento' => $cpfInvalido,
            'nome' => 'Teste CPF Inválido',
            'contato' => 'Pessoa Inválida',
            'email' => 'invalido@cpf.com',
            'telefone' => '11988887777',
        ];

        $response = $this->postJson('/api/v1/estabelecimentos', $payload);

        $response->assertStatus(422)
                 ->assertJsonFragment(['msg' => 'Erro de validação']);
    }

    public function test_nao_criar_estabelecimento_com_cnpj_invalido()
    {
        $cnpjInvalido = '12345678901231';

        $payload = [
            'tipo' => 'cnpj',
            'documento' => $cnpjInvalido,
            'nome' => 'Empresa CNPJ Inválido',
            'contato' => 'Carlos Inválido',
            'email' => 'invalido@empresa.com',
            'telefone' => '11977776666',
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

    /**
     * Funcoes auxiliares
     */
    private function gerarCpfValido(): string
    {
        $n = [];
        for ($i = 0; $i < 9; $i++) {
            $n[$i] = mt_rand(0, 9);
        }

        // Digito 1
        $n[9] = ((10 * $n[0]) + (9 * $n[1]) + (8 * $n[2]) + (7 * $n[3]) + (6 * $n[4]) +
            (5 * $n[5]) + (4 * $n[6]) + (3 * $n[7]) + (2 * $n[8])) % 11;
        $n[9] = ($n[9] < 2) ? 0 : 11 - $n[9];

        // Digito 2
        $n[10] = ((11 * $n[0]) + (10 * $n[1]) + (9 * $n[2]) + (8 * $n[3]) + (7 * $n[4]) +
            (6 * $n[5]) + (5 * $n[6]) + (4 * $n[7]) + (3 * $n[8]) + (2 * $n[9])) % 11;
        $n[10] = ($n[10] < 2) ? 0 : 11 - $n[10];

        return implode('', $n);
    }

    private function gerarCnpjValido(): string
    {
        $n = [];
        for ($i = 0; $i < 12; $i++) {
            $n[$i] = mt_rand(0, 9);
        }

        $peso1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $peso2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $soma1 = 0;
        for ($i = 0; $i < 12; $i++) {
            $soma1 += $n[$i] * $peso1[$i];
        }
        $n[12] = ($soma1 % 11 < 2) ? 0 : 11 - ($soma1 % 11);

        $soma2 = 0;
        for ($i = 0; $i < 13; $i++) {
            $soma2 += $n[$i] * $peso2[$i];
        }
        $n[13] = ($soma2 % 11 < 2) ? 0 : 11 - ($soma2 % 11);

        return implode('', $n);
    }


}
