#!/bin/sh
set -e

DB_FILE=./database/database.sqlite

if [ -f "$DB_FILE" ]; then
    echo "database.sqlite found, skipping creation"
else
    echo "database.sqlite not found, creating..."
    touch "$DB_FILE"
    chown www-data:www-data "$DB_FILE"
    chmod 775 "$DB_FILE"
fi

echo "Starting migrations..."
php artisan migrate --force

if [ ! -f "./.env" ]; then
    echo ".env not found, generating..."
    cp ./.env.prod ./.env
    php artisan key:generate --ansi
fi

apache2-foreground
