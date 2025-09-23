FROM php:8.2-apache
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configuration files
COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY php.conf /usr/local/etc/php/conf.d/docker-php.ini

# Project files
COPY . /var/www/html
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 database storage bootstrap/cache

# Enable .htaccess
RUN a2enmod rewrite

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip \
    && composer install --no-dev --optimize-autoloader \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean
    # && ln -sf /dev/stdout storage/logs/laravel.log

EXPOSE 80

ENTRYPOINT [ "./docker-entrypoint.sh" ]
