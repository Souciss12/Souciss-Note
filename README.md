# SoucissNote

SoucissNote is an intuitive web note-taking application with Markdown.

## About the project

SoucissNote is designed to offer an easy way to take your notes. It is design that you can access your notes from everywhere.

## Main features

- **Note management**: Create, edit and delete notes with a title and content
- **Hierarchical organization**: Organize your notes in folders and subfolders
- **Multi-user**: Each user has their own space for notes and folders
- **Responsive interface**: A smooth user experience on all devices


## Étapes de déploiement

### 1. Préparation du serveur

Connectez-vous à votre serveur via SSH et créez les dossiers nécessaires :

```bash
# Se connecter au serveur
ssh user@votre-serveur

# Créer les dossiers pour l'application
sudo mkdir -p /mnt/datas/docker/soucissnote
```

### 2. Clone le projet

```bash
# ou vous voulez
git clone https://github.com/Souciss12/Souciss-Note.git
```

### 3. Initialiser les volumes sur le serveur

```bash
# dans le clone du projet
chmod +x ./init-volumes.sh
./init-volumes.sh
```

### 4. Construire l'image Docker

```bash
# dans le clone du projet
docker build -t soucissnote:latest .
```

### 5. Déployer avec Portainer

1. Accédez à l'interface web de Portainer
2. Allez dans "Stacks" > "Add stack"
3. Nommez votre stack (ex: "soucissnote")
4. Copiez le contenu du fichier `docker-compose.yml` dans l'éditeur
5. Ajouter les deux variables d'environnements nécessaire
    - APP_KEY clédelapp -> `php artisan key:generate --show`
    - APP_URL http://souciss.ch:25565
5. Cliquez sur "Deploy the stack"

### 6. Copier les migrations (IMPORTANT)

```bash
# dans le clone du projet
sudo chmod +x ./copy-migrations.sh
sudo ./copy-migrations.sh
```

### 7. Redémarrer le conteneur

```bash
sudo docker restart soucissnote
```
