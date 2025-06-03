#!/bin/sh
set -e

# Attendre que le système de fichiers soit prêt
echo "Initialisation de l'application Laravel..."

# Vérifier que vendor/autoload.php existe
if [ ! -f vendor/autoload.php ]; then
    echo "ERROR: vendor/autoload.php not found! Composer dependencies are not properly installed."
    echo "Running composer install now..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Vérifier si la clé d'application existe, sinon en générer une nouvelle
if [ -z "$APP_KEY" ]; then
    php artisan key:generate
fi

# Vérifier si le fichier .env existe
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Créer la structure du dossier storage et bootstrap si elle n'existe pas
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public
mkdir -p bootstrap/cache

# Vérifier si la base de données SQLite existe
if [ ! -f database/database.sqlite ]; then
    echo "Création de la base de données SQLite..."
    touch database/database.sqlite
fi

# Exécuter les migrations (toujours pour s'assurer que toutes les tables existent)
echo "Exécution des migrations..."
php artisan migrate --force

# Exécuter les seeders si nécessaire
echo "Exécution des seeders..."
php artisan db:seed --force || true

# Exécuter uniquement les commandes de cache qui ne dépendent pas de la base de données
echo "Optimisation de l'application..."
php artisan view:clear
php artisan route:clear
php artisan config:clear

# Recréer le cache après les migrations
php artisan view:cache
php artisan route:cache
php artisan config:cache

# Mettre à jour les permissions pour les dossiers de stockage
chmod -R 775 storage bootstrap/cache database
chown -R www-data:www-data storage bootstrap/cache database

# Migration de la base de données si nécessaire
if [ "$DB_AUTO_MIGRATE" = "true" ]; then
    php artisan migrate --force
fi

# Exécuter la commande principale 
exec "$@"
