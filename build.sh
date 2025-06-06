#!/bin/bash

set -e

ENV_TYPE=${ENV_TYPE:-development}

echo "Ambiente: $ENV_TYPE"

# Lista de diretórios de volume necessários
DIRS=(
  "./data/${ENV_TYPE}/pg"
  "./data/${ENV_TYPE}/logs/redis"
  "./data/${ENV_TYPE}/logs/postgres"
  "./data/${ENV_TYPE}/logs/php"
  "./data/${ENV_TYPE}/logs/nginx"
)

echo "Criando diretórios necessários para volumes..."
for dir in "${DIRS[@]}"; do
  if [ ! -d "$dir" ]; then
    mkdir -p "$dir"
    echo "✔️ Criado: $dir"
  else
    echo "↪️ Já existe: $dir"
  fi
done

echo "Buildando containers..."
docker-compose build

echo "Subindo ambiente..."
docker-compose up -d

echo "Ambiente pronto!"
