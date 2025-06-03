# SoucissNote Docker Deployment - Corrections résumées

## Problèmes corrigés:

1. **Problème: Migrations manquantes lors du montage de volumes**
   - Solution: Sauvegarde des migrations dans un répertoire spécial à l'intérieur du conteneur
   - Script de restauration dans docker-entrypoint.sh
   - Montage de volumes modifié pour ne monter que database.sqlite et non le répertoire complet

2. **Problème: Permissions incorrectes**
   - Solution: Commandes chmod explicites dans docker-entrypoint.sh
   - Vérification des permissions dans le script de diagnostic

3. **Problème: Initialisation de la base de données**
   - Solution: Création manuelle des tables critiques si les migrations échouent
   - Vérification de l'existence des tables avant de continuer

4. **Problème: Script d'initialisation des volumes incomplet**
   - Solution: Mise à jour pour créer la structure complète des répertoires
   - Support pour copier les migrations depuis le conteneur

## Nouveaux fichiers:

1. **copy-migrations.sh**
   - Script pour copier les migrations depuis le conteneur vers le volume hôte
   - À exécuter après le premier déploiement

2. **troubleshoot.sh**
   - Script de diagnostic et réparation complet
   - Peut être exécuté à l'intérieur du conteneur pour identifier et corriger les problèmes

3. **DEPLOY.md**
   - Guide de déploiement complet
   - Instructions étape par étape pour le serveur Ubuntu avec Portainer
   - Section de résolution des problèmes courants

## Modifications des fichiers:

1. **Dockerfile**
   - Amélioration de la sauvegarde des migrations
   - Ajout du script de diagnostic

2. **docker-entrypoint.sh**
   - Meilleure gestion des migrations manquantes
   - Création manuelle des tables critiques si nécessaire
   - Amélioration des messages d'erreur et du reporting

3. **portainer-stack.yml**
   - Montage de volumes optimisé pour éviter les problèmes de migrations
   - Montage de database.sqlite au lieu du répertoire complet

4. **init-volumes.sh**
   - Création du répertoire de migrations
   - Instructions pour copier les migrations depuis le conteneur

## Comment déployer:

1. Reconstruire l'image Docker avec ces changements
2. Exécuter init-volumes.sh sur le serveur hôte
3. Déployer avec Portainer en utilisant le portainer-stack.yml mis à jour
4. Exécuter copy-migrations.sh après le premier déploiement
5. Redémarrer le conteneur

Si des problèmes persistent, exécuter le script troubleshoot.sh à l'intérieur du conteneur:
```
docker exec -it soucissnote /var/www/troubleshoot.sh
```
