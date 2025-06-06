#!/bin/sh

APP_DIR="/var/www"

cd "$APP_DIR" || exit 1

# Espera até o volume estar montado
while [ ! -d "$APP_DIR" ]; do
  echo "Aguardando montagem de volume..."
  sleep 1
done

# Verifica se o diretório vendor existe
if [ -d "vendor" ]; then
  echo "Diretório 'vendor' já existe. Pulando composer install."
else
  echo "Diretório 'vendor' não encontrado. Executando composer install..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Mantém o container em execução
exec php-fpm
