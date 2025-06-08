<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estabelecimentos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique('idx_estabelecimentos_uuid');

            $table->enum('tipo', ['cpf', 'cnpj']);
            $table->string('documento', 14);
            $table->string('nome', 100);
            $table->string('contato', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('telefone', 14)->nullable();

            $table->timestamps();
            $table->softDeletes();

            //Indices
            $table->index('nome', 'idx_estabelecimentos_documento');
            $table->index('nome', 'idx_estabelecimentos_nome');
            $table->index('telefone', 'idx_estabelecimentos_telefone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estabelecimentos');
    }
};
