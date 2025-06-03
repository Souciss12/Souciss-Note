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

# Mettre à jour les permissions pour les dossiers de stockage
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Migration de la base de données si nécessaire
if [ "$DB_AUTO_MIGRATE" = "true" ]; then
    php artisan migrate --force
fi

# Exécuter la commande principale 
exec "$@"
