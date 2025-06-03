#!/bin/bash
# Script pour initialiser manuellement la base de données SQLite dans le conteneur

echo "Tentative d'initialisation manuelle de la base de données SQLite..."

# Se connecter au conteneur
docker exec -it soucissnote bash -c "
cd /var/www && 
echo 'Vérification des permissions de la base de données...' && 
ls -la database/database.sqlite && 
chmod 666 database/database.sqlite && 
echo 'Installation des tables de migration...' && 
php artisan migrate:install --no-interaction &&
echo 'Exécution des migrations...' && 
php artisan migrate:refresh --seed --force
"

echo "Initialisation terminée."
