#!/bin/bash
# Script pour initialiser les volumes pour SoucissNote

# Créer les dossiers nécessaires
mkdir -p /mnt/datas/docker/soucissnote/storage/app/public
mkdir -p /mnt/datas/docker/soucissnote/storage/framework/cache
mkdir -p /mnt/datas/docker/soucissnote/storage/framework/sessions
mkdir -p /mnt/datas/docker/soucissnote/storage/framework/views
mkdir -p /mnt/datas/docker/soucissnote/storage/logs
mkdir -p /mnt/datas/docker/soucissnote/bootstrap/cache
mkdir -p /mnt/datas/docker/soucissnote/database/migrations

# Créer un fichier .env de base
if [ ! -f /mnt/datas/docker/soucissnote/.env ]; then
    cat > /mnt/datas/docker/soucissnote/.env << EOF
APP_NAME=SoucissNote
APP_ENV=production
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:25565

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/database/database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
SESSION_LIFETIME=120
EOF
    echo "Fichier .env créé avec succès"
fi

# Créer la base de données SQLite si elle n'existe pas
if [ ! -f /mnt/datas/docker/soucissnote/database/database.sqlite ]; then
    touch /mnt/datas/docker/soucissnote/database/database.sqlite
    echo "Fichier database.sqlite créé avec succès"
fi

# Définir les permissions appropriées
chmod -R 775 /mnt/datas/docker/soucissnote
chmod 666 /mnt/datas/docker/soucissnote/database/database.sqlite
echo "Permissions mises à jour"

# Copier les migrations depuis l'image Docker si les migrations n'existent pas dans le volume
if [ -z "$(ls -A /mnt/datas/docker/soucissnote/database/migrations 2>/dev/null)" ]; then
    echo "Préparation des migrations pour le volume..."
    # Cette commande doit être exécutée lorsque le conteneur est déjà construit
    # Utiliser docker cp pour extraire les migrations depuis le conteneur
    echo "NOTE: Pour copier les migrations de l'image vers le volume, exécuter:"
    echo "docker cp soucissnote:/var/www/database/migrations-src/. /mnt/datas/docker/soucissnote/database/migrations/"
fi

echo "Initialisation des volumes terminée"
