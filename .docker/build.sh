#!/bin/sh

APP_DIR="/var/www"

# Aguarda volume montado
while [ ! -d "$APP_DIR" ]; do
  echo "Aguardando montagem de volume..."
  sleep 1
done

cd "$APP_DIR" || exit 1

# Instala dependencias do Laravel se vendor não existir
if [ ! -d "vendor" ]; then
  echo "Executando composer install..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Gera cache se .env existir
if [ -f ".env" ]; then
  php artisan config:cache
  php artisan route:cache
fi

# Inicia o PHP-FPM
exec php-fpm
