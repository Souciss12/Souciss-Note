#!/bin/sh
set -e

# Attendre que le système de fichiers soit prêt
echo "Initialisation de l'application Laravel..."

# Vérifier si le répertoire migrations existe et s'il est vide
if [ ! -d /var/www/database/migrations ] || [ -z "$(ls -A /var/www/database/migrations 2>/dev/null)" ]; then
    echo "Répertoire migrations manquant ou vide, préparation..."
    
    # S'assurer que le répertoire existe
    mkdir -p /var/www/database/migrations
    
    # Copier les migrations depuis le répertoire source de l'image
    if [ -d /var/www/database/migrations-src ] && [ -n "$(ls -A /var/www/database/migrations-src 2>/dev/null)" ]; then
        echo "Copie des migrations depuis la source interne..."
        cp -r /var/www/database/migrations-src/* /var/www/database/migrations/
        echo "Migrations copiées avec succès depuis la source interne."
    # Si les migrations-src n'existent pas, essayer de restaurer à partir des migrations par défaut dans l'image
    elif [ -d /var/www/vendor/laravel/framework/database/migrations ]; then
        echo "Restauration des migrations de base Laravel..."
        cp -r /var/www/vendor/laravel/framework/database/migrations/* /var/www/database/migrations/
        echo "Migrations de base Laravel restaurées."
    else
        echo "ERREUR: Aucune source de migrations trouvée!"
    fi
fi

# Afficher le contenu du répertoire migrations
echo "Contenu du répertoire migrations:"
ls -la /var/www/database/migrations/ || echo "Impossible de lister le répertoire migrations!"

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
    chmod 666 database/database.sqlite
fi

# S'assurer que la base de données a les bonnes permissions
chmod 666 database/database.sqlite
chown www-data:www-data database/database.sqlite

# Initialiser la table des migrations
echo "Initialisation de la table des migrations..."
php artisan migrate:install --no-interaction || true

# Afficher le contenu des migrations pour vérifier
echo "Liste des fichiers de migration disponibles:"
ls -la /var/www/database/migrations/ || echo "ALERTE: Impossible de lister les migrations!"

# Exécuter les migrations
echo "Vérification de l'état des migrations..."
php artisan migrate:status --no-interaction || echo "Impossible de vérifier le statut des migrations"

echo "Exécution des migrations avec debug..."
php artisan migrate --force --verbose || echo "Impossible d'exécuter les migrations mais on continue"

# Vérifier que les tables critiques existent
echo "Vérification des tables critiques..."
for table in sessions cache users notes folders; do
    echo "Vérification de la table: $table"
    if ! sqlite3 database/database.sqlite "SELECT name FROM sqlite_master WHERE type='table' AND name='$table';" | grep -q "$table"; then
        echo "ATTENTION: La table $table n'existe pas après les migrations!"
        
        # Essayer de créer la table manuellement pour les tables critiques
        case "$table" in
            sessions)
                echo "Tentative de création manuelle de la table sessions..."
                sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS sessions (
                    id VARCHAR(255) PRIMARY KEY,
                    user_id INTEGER NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent TEXT NULL,
                    payload TEXT NOT NULL,
                    last_activity INTEGER NOT NULL
                );"
                sqlite3 database/database.sqlite "CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions (user_id);"
                sqlite3 database/database.sqlite "CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions (last_activity);"
                ;;
            cache)
                echo "Tentative de création manuelle de la table cache..."
                sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS cache (
                    key VARCHAR(255) PRIMARY KEY,
                    value TEXT NOT NULL,
                    expiration INTEGER NOT NULL
                );"
                ;;
        esac
    else
        echo "OK: La table $table existe."
    fi
done

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
