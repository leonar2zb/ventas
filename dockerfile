FROM php:8.2-cli

# Variables de entorno para que PHP use UTF-8
ENV LANG=C.UTF-8
ENV LC_ALL=C.UTF-8

# Instalar dependencias de sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    libpq-dev \
    zip &&
    docker-php-ext-install pdo pdo_pgsql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear y establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente
COPY . .

# Dar permisos necesarios
RUN chmod -R 755 .

# Generar key y cachear config al iniciar el contenedor
CMD php artisan key:generate && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=10000
