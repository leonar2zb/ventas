FROM php:8.2-apache

# Instala dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    zip && \
    docker-php-ext-install pdo pdo_pgsql zip intl

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia el código de la app
COPY . /var/www/html

WORKDIR /var/www/html

# Da permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expone el puerto 80 por defecto (Apache)
EXPOSE 80

# Instala dependencias y corre migraciones y seeders en build
RUN composer install --no-dev --optimize-autoloader
RUN php artisan key:generate
RUN php artisan migrate --force
RUN php artisan db:seed --force
