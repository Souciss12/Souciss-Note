#!/bin/bash
# Script pour initialiser manuellement la base de données SQLite dans le conteneur

echo "Tentative d'initialisation manuelle de la base de données SQLite..."

# Se connecter au conteneur
docker exec -it soucissnote bash -c "
cd /var/www && 
echo 'Vérification des dossiers et fichiers de migrations...' && 
ls -la database/ && 
ls -la database/migrations/ && 
echo 'Vérification des permissions de la base de données...' && 
ls -la database/database.sqlite && 
chmod 666 database/database.sqlite && 
echo 'Installation des tables de migration...' && 
php artisan migrate:install --no-interaction &&
echo 'Exécution des migrations manuellement...' && 
php artisan migrate:fresh --seed --force --verbose && 
echo 'Vérification des tables créées:' && 
sqlite3 database/database.sqlite \"SELECT name FROM sqlite_master WHERE type='table';\"
"

echo "Initialisation terminée."
