FROM php:8.3-fpm

# Install system dependencies and PHP extensions in one layer
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    git \
    curl \
    nodejs \
    npm \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copier tout le code source d'abord
COPY . .

# Faire une copie de sauvegarde des migrations
RUN mkdir -p /var/www/database/migrations-src && \
    if [ -d /var/www/database/migrations ] && [ -n "$(ls -A /var/www/database/migrations 2>/dev/null)" ]; then \
    cp -r /var/www/database/migrations/* /var/www/database/migrations-src/ && \
    echo "Sauvegarde des migrations effectuée" ; \
    else \
    echo "Aucune migration à sauvegarder" ; \
    fi && \
    # Garantir que tous les fichiers de migration sont copiés dans migrations-src
    find /var/www -name "*.php" -path "*/migrations/*" -exec cp {} /var/www/database/migrations-src/ \; 2>/dev/null || true && \
    echo "Fichiers de migration copiés depuis le système de fichiers."

# Installer les dépendances Composer
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction

# Installer les dépendances npm
RUN npm ci

# Make the entrypoint script executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY troubleshoot.sh /var/www/troubleshoot.sh
COPY fix-colors.sh /var/www/fix-colors.sh
RUN chmod +x /usr/local/bin/docker-entrypoint /var/www/troubleshoot.sh /var/www/fix-colors.sh

# Build assets
RUN npm run build

# Préparation des répertoires pour Laravel
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && chmod -R 775 storage bootstrap/cache

# Set proper permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

# Utilisez le script d'entrypoint pour initialiser et php artisan serve pour exécuter
ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=9000"]