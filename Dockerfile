# 1. Usar una imagen oficial de PHP con Apache
FROM php:8.2-apache

# 2. Instalar dependencias del sistema requeridas por Laravel y Node.js para Vite
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm

# Limpiar caché de instalaciones
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Instalar las extensiones de PHP que necesita Laravel (y la base de datos)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 4. Habilitar la reescritura de URLs en Apache (Crucial para las rutas de Laravel)
RUN a2enmod rewrite

# 5. Instalar Composer (el gestor de paquetes de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Establecer el directorio de trabajo dentro del servidor
WORKDIR /var/www/html

# 7. Copiar todos los archivos de tu proyecto al servidor
COPY . .

# 8. Instalar las dependencias de PHP de tu proyecto
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 9. Instalar las dependencias de Node (NPM) y compilar los assets de Vite
RUN if [ -f package.json ]; then npm install && npm run build; fi


RUN mkdir -p /var/www/html/storage/framework/views
RUN mkdir -p /var/www/html/storage/framework/cache
RUN mkdir -p /var/www/html/storage/framework/sessions
RUN mkdir -p /var/www/html/bootstrap/cache

# 10. Darle los permisos correctos a las carpetas que Laravel necesita modificar
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Decirle a Apache que la carpeta pública de tu app es la carpeta "public" de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 12. Exponer el puerto web estándar
EXPOSE 80

#LOCAL
#FROM php:8.2-cli

# Instalar extensiones necesarias
#RUN apt-get update && apt-get install -y \
#    libzip-dev unzip git curl \
#    npm \
#    && docker-php-ext-install pdo pdo_mysql

#WORKDIR /var/www