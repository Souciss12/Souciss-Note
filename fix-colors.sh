#!/bin/sh
# Script rapide pour fixer le problème de la table colors
# À exécuter à l'intérieur du conteneur avec: docker exec -it soucissnote /var/www/fix-colors.sh

echo "=== FIXATION RAPIDE DE LA TABLE COLORS ==="

# Vérifier si la table colors existe
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
    echo "La table colors existe déjà."
fi

# S'assurer que la base de données a les bonnes permissions
chmod 666 database/database.sqlite
echo "Permissions mises à jour."

# Vérifier les tables de la base de données
echo "Tables dans la base de données:"
sqlite3 database/database.sqlite ".tables"

echo "=== FIXATION TERMINÉE ==="
echo "Si l'application continue à afficher des erreurs, exécutez le script troubleshoot.sh complet."
