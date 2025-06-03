#!/bin/bash
# Script pour copier les migrations depuis l'image Docker vers le volume

# Vérifier que le conteneur est en cours d'exécution
if ! docker ps | grep -q "soucissnote"; then
    echo "ERREUR: Le conteneur soucissnote n'est pas en cours d'exécution"
    exit 1
fi

# Créer le répertoire cible s'il n'existe pas
mkdir -p /mnt/datas/docker/soucissnote/database/migrations

# Copier les migrations depuis le conteneur vers le volume
echo "Copie des migrations depuis le conteneur..."
docker cp soucissnote:/var/www/database/migrations-src/. /mnt/datas/docker/soucissnote/database/migrations/

# Vérifier que la copie a réussi
if [ -n "$(ls -A /mnt/datas/docker/soucissnote/database/migrations 2>/dev/null)" ]; then
    echo "Migrations copiées avec succès"
    echo "Contenu du répertoire des migrations:"
    ls -la /mnt/datas/docker/soucissnote/database/migrations/
else
    echo "ERREUR: Aucune migration n'a été copiée"
    echo "Essai de copie directe des migrations du conteneur..."
    docker cp soucissnote:/var/www/database/migrations/. /mnt/datas/docker/soucissnote/database/migrations/
    
    # Vérifier à nouveau
    if [ -n "$(ls -A /mnt/datas/docker/soucissnote/database/migrations 2>/dev/null)" ]; then
        echo "Migrations copiées avec succès depuis le répertoire principal"
    else
        echo "ÉCHEC: Impossible de copier les migrations"
    fi
fi

# Définir les permissions appropriées
chmod -R 775 /mnt/datas/docker/soucissnote/database/migrations
echo "Permissions mises à jour"
