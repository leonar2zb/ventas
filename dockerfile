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

# Da permisos a storage y bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Asegura que exista el archivo .env antes de generar la clave de aplicación
# RUN cp .env.example .env

# Expone el puerto 80 por defecto (Apache)
EXPOSE 80

# Si el archivo .env no existe, créalo sin sobrescribir variables de Render
RUN [ -f .env ] || touch .env

# Instalación de dependencias y configuración en pasos separados para facilitar depuración
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs
RUN php artisan key:generate
RUN php artisan migrate --force
RUN php artisan db:seed --force