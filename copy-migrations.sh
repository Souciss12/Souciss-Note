#!/bin/bash
# Script pour copier les migrations depuis l'image Docker vers le volume

# Vérifier que le conteneur est en cours d'exécution
if ! docker ps | grep -q "soucissnote"; then
    echo "ERREUR: Le conteneur soucissnote n'est pas en cours d'exécution"
    exit 1
fi

# Créer le répertoire cible s'il n'existe pas
mkdir -p /mnt/datas/docker/soucissnote/database/migrations

# Arrêter le conteneur pour éviter les problèmes de verrouillage de fichiers
echo "Arrêt temporaire du conteneur..."
docker stop soucissnote

# Copier les fichiers sources de l'application
echo "Copie des fichiers sources de l'application..."
docker run --rm -v /mnt/datas/docker/soucissnote:/backup soucissnote:latest tar -cf /backup/app_source.tar /var/www

# Démarrer le conteneur
echo "Redémarrage du conteneur..."
docker start soucissnote

# Extraire les migrations
echo "Extraction des migrations depuis la sauvegarde..."
tar -xf /mnt/datas/docker/soucissnote/app_source.tar -C /tmp var/www/database/migrations --strip-components=3
cp -r /tmp/migrations/* /mnt/datas/docker/soucissnote/database/migrations/

# Vérifier que les fichiers sont présents
if [ -n "$(ls -A /mnt/datas/docker/soucissnote/database/migrations 2>/dev/null)" ]; then
    echo "Migrations extraites avec succès"
    echo "Contenu du répertoire des migrations:"
    ls -la /mnt/datas/docker/soucissnote/database/migrations/
    
    # Copier également la migration de la table colors
    cp -f /mnt/datas/docker/soucissnote/database/migrations/0001_01_01_000004_create_colors_table.php /mnt/datas/docker/soucissnote/database/migrations/ 2>/dev/null || echo "Fichier colors déjà présent"
else
    echo "ERREUR: Aucune migration n'a été extraite"
    
    # Créer au moins les migrations essentielles
    echo "Création des migrations essentielles manuellement..."
    cat > /mnt/datas/docker/soucissnote/database/migrations/0001_01_01_000004_create_colors_table.php << 'EOF'
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
    echo "Migration colors créée manuellement"
fi

# Définir les permissions appropriées
chmod -R 775 /mnt/datas/docker/soucissnote/database/migrations
chmod 664 /mnt/datas/docker/soucissnote/database/migrations/*.php
echo "Permissions mises à jour"

# Redémarrer le conteneur pour appliquer les modifications
echo "Redémarrage du conteneur pour appliquer les migrations..."
docker restart soucissnote

# Nettoyer les fichiers temporaires
rm -f /mnt/datas/docker/soucissnote/app_source.tar
rm -rf /tmp/migrations

echo "Script terminé. Vérifiez les logs du conteneur pour vous assurer que les migrations ont été appliquées."
echo "docker logs soucissnote"
