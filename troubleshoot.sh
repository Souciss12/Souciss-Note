#!/bin/sh
# Script de diagnostic et réparation pour SoucissNote
# À exécuter à l'intérieur du conteneur avec: docker exec -it soucissnote /var/www/troubleshoot.sh

echo "=== DIAGNOSTIC SOUCISSNOTE ==="
echo "Date/heure: $(date)"
echo ""

# Vérifier l'environnement PHP
echo "=== VERSION PHP ==="
php -v
echo ""

# Vérifier les extensions PHP
echo "=== EXTENSIONS PHP ==="
php -m | grep -E 'pdo|sqlite|mbstring|exif|pcntl|bcmath|gd|zip'
echo ""

# Vérifier la structure des répertoires
echo "=== STRUCTURE DES RÉPERTOIRES ==="
echo "Répertoire de travail: $(pwd)"
ls -la
echo ""

echo "=== VÉRIFICATION DES RÉPERTOIRES CLÉS ==="
for dir in storage bootstrap/cache database database/migrations; do
    echo "Vérification de $dir:"
    if [ -d "$dir" ]; then
        ls -la "$dir"
    else
        echo "ERREUR: Le répertoire $dir n'existe pas!"
        mkdir -p "$dir"
        echo "Répertoire $dir créé"
    fi
    echo ""
done

# Vérifier la configuration Laravel
echo "=== CONFIGURATION LARAVEL ==="
echo "Contenu du fichier .env:"
if [ -f .env ]; then
    grep -v "DB_PASSWORD\|APP_KEY" .env
else
    echo "ERREUR: Fichier .env manquant!"
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "Fichier .env créé à partir de .env.example"
    fi
fi
echo ""

# Vérifier la base de données
echo "=== BASE DE DONNÉES ==="
if [ -f database/database.sqlite ]; then
    echo "Taille de la base de données: $(du -h database/database.sqlite)"
    echo "Permissions: $(ls -la database/database.sqlite)"
    echo "Tables dans la base de données:"
    sqlite3 database/database.sqlite ".tables"
else
    echo "ERREUR: Fichier database.sqlite manquant!"
    touch database/database.sqlite
    chmod 666 database/database.sqlite
    echo "Fichier database.sqlite créé avec permissions 666"
fi
echo ""

# Vérifier l'état des migrations
echo "=== MIGRATIONS ==="
echo "Fichiers de migration disponibles:"
if [ -d database/migrations ] && [ -n "$(ls -A database/migrations 2>/dev/null)" ]; then
    ls -la database/migrations
else
    echo "ERREUR: Répertoire migrations vide ou manquant!"
    if [ -d /var/www/database/migrations-src ] && [ -n "$(ls -A /var/www/database/migrations-src 2>/dev/null)" ]; then
        echo "Restauration des migrations depuis la sauvegarde..."
        mkdir -p database/migrations
        cp -r /var/www/database/migrations-src/* database/migrations/
        echo "Migrations restaurées depuis la sauvegarde."
    fi
fi

echo "État des migrations:"
php artisan migrate:status || echo "Impossible de vérifier l'état des migrations"
echo ""

# Tentative de réparation des problèmes courants
echo "=== TENTATIVES DE RÉPARATION ==="

# 1. Réparation des permissions
echo "1. Réparation des permissions..."
chmod -R 775 storage bootstrap/cache database
chown -R www-data:www-data storage bootstrap/cache database
chmod 666 database/database.sqlite
echo "Permissions réparées."

# 2. Réinitialisation du cache
echo "2. Réinitialisation du cache Laravel..."
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear
echo "Cache réinitialisé."

# 3. Réparation de la table migrations
echo "3. Vérification/réparation de la table migrations..."
if ! sqlite3 database/database.sqlite "SELECT name FROM sqlite_master WHERE type='table' AND name='migrations';" | grep -q "migrations"; then
    echo "Table migrations manquante, création..."
    sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS migrations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        migration VARCHAR(255) NOT NULL,
        batch INTEGER NOT NULL
    );"
    echo "Table migrations créée."
else
    echo "Table migrations existante, OK."
fi

# 4. Vérification et réparation de la table colors
echo "4. Vérification de la table colors..."
if ! sqlite3 database/database.sqlite "SELECT name FROM sqlite_master WHERE type='table' AND name='colors';" | grep -q "colors"; then
    echo "Table colors manquante, création..."
    sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS colors (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        colors TEXT NOT NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    );"
    sqlite3 database/database.sqlite "CREATE INDEX IF NOT EXISTS colors_user_id_index ON colors (user_id);"
    echo "Table colors créée."
else
    echo "Table colors existante, OK."
fi

# 5. Création de la migration pour colors si elle n'existe pas
echo "5. Vérification de la migration pour colors..."
if [ ! -f database/migrations/0001_01_01_000004_create_colors_table.php ]; then
    echo "Migration pour colors manquante, création..."
    cat > database/migrations/0001_01_01_000004_create_colors_table.php << 'EOF'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('colors');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
EOF
    echo "Migration pour colors créée."
else
    echo "Migration pour colors existante, OK."
fi

# 6. Exécution des migrations
echo "6. Tentative d'exécution des migrations..."
php artisan migrate --force || echo "Impossible d'exécuter les migrations automatiquement."

# Vérification finale
echo "=== VÉRIFICATION FINALE ==="
echo "Tables dans la base de données:"
sqlite3 database/database.sqlite ".tables"

echo ""
echo "=== DIAGNOSTIC TERMINÉ ==="
echo "Si des problèmes persistent, consultez le guide DEPLOY.md"
