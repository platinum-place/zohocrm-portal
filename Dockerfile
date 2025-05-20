# Usar imagen base más ligera
FROM php:8.4-apache

# Instalar dependencias en una sola capa para reducir tamaño
RUN apt-get update && apt-get install -y --no-install-recommends \
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
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP con limpieza
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
    pdo_sqlite \
    && docker-php-source delete

# Configuraciones de Apache
RUN a2enmod rewrite headers

# Usar multi-stage build para Composer
FROM composer:latest AS composer

# Etapa final
FROM php:8.4-apache-slim

# Copiar extensiones de la primera etapa
COPY --from=php:8.4-apache-slim /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=php:8.4-apache-slim /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copiar Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Reinstalar dependencias mínimas
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Directorio de trabajo
WORKDIR /var/www/html

# Crear directorios con permisos
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

# Copiar archivos del proyecto
COPY --chown=www-data:www-data . /var/www/html/

# Instalar dependencias de Composer con optimización
RUN composer install \
    --no-dev \
    --no-interaction \
    --optimize-autoloader \
    --no-scripts \
    --no-progress

# Instalar y construir assets de npm
RUN npm ci && npm run build

# Configuración de Apache
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel \
    && sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Configuraciones de seguridad
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "expose_php = Off" >> /usr/local/etc/php/conf.d/security.ini

# Exponer puerto 80
EXPOSE 80

# Usuario no root
USER www-data

# Comando de inicio
CMD ["apache2-foreground"]
