FROM php:8.2-fpm

# Instalar dependencias con limpieza para reducir el tamaño de la imagen
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar y configurar extensiones PHP recomendadas para Laravel
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache

# Configurar límites de PHP para manejar archivos grandes (valores más equilibrados)
RUN echo "upload_max_filesize = 150M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 150M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Configurar OPcache para mejor rendimiento
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar primero solo los archivos necesarios para la instalación de dependencias
COPY composer.json composer.lock* ./
COPY packages ./packages

# Instalar dependencias como www-data
USER www-data
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copiar el resto de los archivos
USER root
COPY . .
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Completar la instalación de composer
USER www-data
RUN composer dump-autoload --optimize

# Configurar permisos
USER root
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 9000

# Volver a root para el entrypoint (que requiere privilegios)
USER root
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
