# Guide de déploiement de SoucissNote avec Docker

Ce guide détaille les étapes pour déployer correctement SoucissNote sur un serveur Ubuntu avec Portainer.

## Prérequis

- Serveur Ubuntu avec Docker et Portainer installés
- Accès SSH au serveur
- Droits sudo sur le serveur

## Étapes de déploiement

### 1. Préparation du serveur

Connectez-vous à votre serveur via SSH et créez les dossiers nécessaires :

```bash
# Se connecter au serveur
ssh user@votre-serveur

# Créer les dossiers pour l'application
sudo mkdir -p /mnt/datas/docker/soucissnote
```

### 2. Copier les fichiers nécessaires

Copiez les fichiers suivants sur votre serveur :
- Dockerfile
- docker-entrypoint.sh
- init-volumes.sh
- copy-migrations.sh
- portainer-stack.yml

```bash
# Depuis votre machine locale
scp Dockerfile docker-entrypoint.sh init-volumes.sh copy-migrations.sh portainer-stack.yml user@votre-serveur:/chemin/temporaire/
```

### 3. Initialiser les volumes sur le serveur

```bash
# Sur le serveur
sudo chmod +x /chemin/temporaire/init-volumes.sh
sudo /chemin/temporaire/init-volumes.sh
```

### 4. Construire l'image Docker

```bash
# Sur le serveur
cd /chemin/temporaire/
sudo docker build -t soucissnote:latest .
```

### 5. Déployer avec Portainer

1. Accédez à l'interface web de Portainer (généralement sur le port 9000)
2. Allez dans "Stacks" > "Add stack"
3. Nommez votre stack (ex: "soucissnote")
4. Copiez le contenu du fichier `portainer-stack.yml` dans l'éditeur
5. Cliquez sur "Deploy the stack"

### 6. Copier les migrations (IMPORTANT)

Une fois le conteneur en cours d'exécution, exécutez le script pour copier les migrations :

```bash
# Sur le serveur
sudo chmod +x /chemin/temporaire/copy-migrations.sh
sudo /chemin/temporaire/copy-migrations.sh
```

### 7. Redémarrer le conteneur

```bash
# Sur le serveur
sudo docker restart soucissnote
```

## Vérification du déploiement

Pour vérifier que l'application fonctionne correctement :

```bash
# Voir les logs du conteneur
sudo docker logs soucissnote

# Vérifier que le conteneur est en cours d'exécution
sudo docker ps | grep soucissnote
```

Vous devriez pouvoir accéder à l'application via http://votre-serveur:25565

## Résolution des problèmes courants

### Problème : Le conteneur redémarre en boucle

Vérifiez les logs pour identifier le problème :
```bash
sudo docker logs soucissnote
```

Solutions possibles :
1. Problème de migrations :
   ```bash
   sudo /chemin/temporaire/copy-migrations.sh
   ```

2. Problème de permissions :
   ```bash
   sudo chmod -R 775 /mnt/datas/docker/soucissnote
   sudo chmod 666 /mnt/datas/docker/soucissnote/database/database.sqlite
   ```

3. Problème de configuration .env :
   ```bash
   sudo nano /mnt/datas/docker/soucissnote/.env
   # Vérifier que APP_KEY est défini correctement
   ```

### Problème : Tables manquantes dans la base de données

Si vous rencontrez des erreurs concernant des tables manquantes :

```bash
# Entrer dans le conteneur
sudo docker exec -it soucissnote sh

# Exécuter les migrations manuellement
cd /var/www
php artisan migrate --force

# Vérifier les tables
sqlite3 database/database.sqlite .tables
```
