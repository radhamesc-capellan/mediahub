FROM php:8.2-cli

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git curl \
    npm \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www