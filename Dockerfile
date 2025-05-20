FROM php:8.4-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client \
    libicu-dev \
    libsqlite3-dev \
    npm \
    apache2-dev

# Instalar extensiones PHP
RUN docker-php-ext-install \
    pdo_mysql \
    mysqli \
    zip \
    intl \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    pdo_sqlite

# Habilitar soporte SQLite
RUN docker-php-ext-enable pdo_sqlite

# Configuraciones de Apache
RUN a2enmod rewrite headers

# Configuración de seguridad
RUN echo "ServerSignature Off" >> /etc/apache2/apache2.conf \
    && echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configurar encabezados de seguridad
RUN touch /etc/apache2/conf-available/security-headers.conf \
    && echo "Header unset Server" >> /etc/apache2/conf-available/security-headers.conf \
    && echo "Header unset X-Powered-By" >> /etc/apache2/conf-available/security-headers.conf \
    && a2enconf security-headers

# Deshabilitar exposición de versión PHP
RUN echo "expose_php = Off" >> /usr/local/etc/php/conf.d/security.ini

# Directorio de trabajo
WORKDIR /var/www/html

# Crear directorios necesarios para Laravel
RUN mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/logs \
    && mkdir -p database

# Preparar base de datos SQLite
RUN touch /var/www/html/database/database.sqlite \
    && chmod 666 /var/www/html/database/database.sqlite

# Copiar archivos del proyecto
COPY . /var/www/html/

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 777 /var/www/html/storage/framework \
    && chmod -R 777 /var/www/html/storage/logs \
    && chmod -R 777 /var/www/html/database \
    && chmod 666 /var/www/html/database/database.sqlite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias npm y construir assets
RUN npm install && npm run build

# Configurar Apache para Laravel
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Establecer DocumentRoot de Apache al directorio public de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Exponer puerto 80
EXPOSE 80

# Ejecutar Apache en primer plano
CMD ["apache2-foreground"]
