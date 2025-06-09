## Teste para Desenvolvedor PHP/Laravel

Bem-vindo ao teste de desenvolvimento para a posição de Desenvolvedor PHP/Laravel.

O objetivo deste teste é desenvolver uma API Rest para o cadastro de fornecedores, permitindo a busca por CNPJ ou CPF, utilizando Laravel no backend.

## Descrição do Projeto

### Backend (API Laravel):

#### CRUD de Fornecedores:
- **Criar Fornecedor:**
  - Permita o cadastro de fornecedores usando CNPJ ou CPF, incluindo informações como nome/nome da empresa, contato, endereço, etc.
  - Valide a integridade e o formato dos dados, como o formato correto de CNPJ/CPF e a obrigatoriedade de campos.

- **Editar Fornecedor:**
  - Facilite a atualização das informações de fornecedores, mantendo a validação dos dados.

- **Excluir Fornecedor:**
  - Possibilite a remoção segura de fornecedores.

- **Listar Fornecedores:**
  - Apresente uma lista paginada de fornecedores, com filtragem e ordenação.

#### Migrations:
- Utilize migrations do Laravel para definir a estrutura do banco de dados, garantindo uma boa organização e facilidade de manutenção.

## Requisitos

### Backend:
- Implementar busca por CNPJ na [BrasilAPI](https://brasilapi.com.br/docs#tag/CNPJ/paths/~1cnpj~1v1~1{cnpj}/get) ou qualquer outro endpoint público.

## Tecnologias a serem utilizadas
- Framework Laravel (PHP) 9.x ou superior
- MySQL ou Postgres

## Critérios de Avaliação
- Adesão aos requisitos funcionais e técnicos.
- Qualidade do código, incluindo organização, padrões de desenvolvimento e segurança.
- Documentação do projeto, incluindo um README detalhado com instruções de instalação e operação.

## Bônus
- Implementação de Strategie Pattern.
- Implementação de testes automatizados.
- Dockerização do ambiente de desenvolvimento.
- Implementação de cache para otimizar o desempenho.

## Entrega
- Para iniciar o teste, faça um fork deste repositório; Se você apenas clonar o repositório não vai conseguir fazer push.
- Crie uma branch com o nome que desejar;
- Altere o arquivo README.md com as informações necessárias para executar o seu teste (comandos, migrations, seeds, etc);
- Depois de finalizado, envie-nos o pull request;

## Teste Realizado

### Backend (API Laravel):

Optei por utilizar o conceito de estabelecimentos, uma vez que temos clientes e fornecedores como um único cadastro.

#### Design Pattern - Strategy
Foi utilizado o padrão de projeto **Strategy** para implementar a criação de estabelecimentos de forma desacoplada, com base no tipo de documento informado (`cpf` ou `cnpj`).

- Cada tipo de documento possui sua própria classe de estratégia responsável pela validação e persistência.
- A estratégia correta é selecionada dinamicamente conforme o campo `tipo` enviado na requisição (`cpf` ou `cnpj`).
- Isso garante que cada regra de negócio fique isolada e facilmente extensível — por exemplo, para adicionar um novo tipo de documento como **passaporte**, basta criar uma nova estratégia.
- O padrão também reforça o princípio da responsabilidade única e melhora a testabilidade da lógica de criação.

#### CRUD de Estabelecimentos:
- **Criar Estabelecimento:**
  - Permite o cadastro de estabelecimentos usando CNPJ ou CPF, incluindo informações como nome, contato, e-mail e telefone.
  - Validação rigorosa dos dados de entrada, incluindo formatos e tamanhos.
  - A estratégia correta é automaticamente aplicada com base no tipo de documento informado (`cpf` ou `cnpj`).

- **Editar Estabelecimento:**
  - Atualiza informações do estabelecimento, mantendo as regras de validação.

- **Excluir Estabelecimento:**
  - Exclusão lógica com `softDeletes`.

- **Listar Estabelecimentos:**
  - Lista paginada de estabelecimentos.

- **Buscar por CPF/CNPJ:**
  - Endpoint específico para localizar estabelecimento por documento.

#### Migrations:
- Utilização de migrations do Laravel para estruturação do banco de dados.
- Utilização de UUID como identificador principal na tabela de estabelecimentos.

## Requisitos

### Backend:
- Implementar busca por CNPJ na [BrasilAPI](https://brasilapi.com.br/docs#tag/CNPJ/paths/~1cnpj~1v1~1{cnpj}/get).

## Tecnologias Utilizadas
- PHP 8.4
- Laravel 12+
- PostgreSQL
- Redis (Cache)
- Docker

## Bônus
- Dockerização
- Cache para listagem
- Implementação de testes com PHPUnit

## Entrega
Este repositório contém a implementação da API de Fornecedores(Estabelecimentos).

## Configuração do Ambiente

### Instalação

1. Clone o repositório:
    ```bash
    git clone <REPOSITORIO> teste-dev-php
    cd teste-dev-php
    ```

2. Copie o `.env`:
    ```bash
    cp .env.development .env
    ```

3. Gere a chave:
    ```bash
    php artisan key:generate
    ```

4. Execute o build.sh para gerar os containers
    ```bash
    ./build.sh
    ```

5. Execute as migrations:
    ```bash
    docker exec -it app php artisan migrate
    ```

A API estará em `http://localhost:8000/api/`.

### Ambiente Docker

O Docker já está configurado para ser executado em modo desenvolvimento

1. Configure o `.env` (`DB_HOST=postgres` para Docker).

2. O projeto utiliza uma pasta `.docker/` contendo os arquivos de definição dos serviços necessários para o ambiente da aplicação (app, banco de dados etc). O Docker está configurado com **duas redes separadas**, garantindo uma maior segurança entre os serviços.

Volumes são utilizados para persistência dos dados do banco e sincronização do código entre host e container.

## Executando os Testes

Docker:
```bash
docker exec -it app php artisan test
```

### Testes Realizados com PHPUnit
- Criar um estabelecimento com sucesso
- Impedir criação com dados inválidos (CNPJ duplicado)
- Atualizar um estabelecimento e validar mudança no campo `nome`
- Listar estabelecimentos com paginação
- Buscar estabelecimento por CPF/CNPJ
- Excluir estabelecimento (soft delete) e verificar que não afeta unicidade

## Arquivo Postman

O arquivo `postman.json` está incluído no repositório para facilitar os testes. Importe-o no Postman para acessar todos os endpoints disponíveis.

## Endpoints da API

### Estabelecimentos (`/api/v1/estabelecimentos`)

- `GET /`: Lista os estabelecimentos paginados.
- `POST /`: Cria novo estabelecimento.
- `GET /{uuid}`: Consulta por UUID.
- `PUT /{uuid}`: Atualiza dados.
- `DELETE /{uuid}`: Exclui logicamente.
- `GET /by-cpf-cnpj/{documento}`: Busca por CPF ou CNPJ.

### BrasilAPI

- `GET /api/v1/external/brasilapi/cep/{cep}`: Consulta CEP via BrasilAPI.
- `GET /api/v1/external/brasilapi/cnpj/{cnpj}`: Consulta CNPJ via BrasilAPI.

### ViaCEP

- `GET /api/v1/external/viacep/cep/{cep}`: Consulta CEP via ViaCEP.

### Sistema

- `GET /api/sistema/config/cache`: Consulta configuração de cache.

## Considerações

- Soft delete implementado. Verificações de unicidade consideram registros ativos (com `deleted_at` NULL).
- Respostas padronizadas com classe `ApiResponse`, incluindo metadados (`status`, `msg`, `http`, `data`, `time`).
- Consulta externa de CNPJ via BrasilAPI integrada.

## Modelo de Retorno

```json
{
    "http": 200,
    "status": true,
    "msg": "Consulta de CEP realizada com sucesso",
    "data": {
        "cep": "81330140",
        "state": "PR",
        "city": "Curitiba",
        "neighborhood": "Fazendinha",
        "street": "Rua Henrique Mattioli",
        "service": "open-cep",
        "caches": true,
        "cache": "api_cache:814d34c193c511d5aed9cd4fda7f4914"
    },
    "time": 0.0996
}
```
cache : Indica que a api foi salva em cache por 30s


```json
{
    "http": 201,
    "status": true,
    "msg": "Estabelecimento criado com sucesso",
    "data": {
        "nome": "Novo Fornecedor SA",
        "tipo": "cnpj",
        "documento": "98765432000100",
        "email": "contato@novofornecedor.com",
        "telefone": "21988887777",
        "uuid": "f5de4ec4-ae1b-4b54-8d7a-98e326dfab95",
        "updated_at": "2025-06-08T23:25:27.000000Z",
        "created_at": "2025-06-08T23:25:27.000000Z",
        "id": 10
    },
    "time": 0.0434
}