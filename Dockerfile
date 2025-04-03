FROM php:7.3.12-apache

# Configuraciones iniciales
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Instalando dependencias para el stack LAMP
RUN apt-get clean && \
    apt-get update && \
    apt-get install -y libicu-dev g++ unzip libpng-dev libzip-dev && \
    docker-php-ext-install mysqli && \
    docker-php-ext-enable mysqli && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl && \
    docker-php-ext-configure gd && \
    docker-php-ext-install gd && \
    docker-php-ext-install zip
RUN a2enmod rewrite

# Instalamos composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY ["composer.json","composer.lock", "/var/www/html/"]
RUN composer install

# Copia de archivos dentro del contenedor
WORKDIR /var/www/html/
COPY [".", "/var/www/html/"]

# Permisos de carpetas por usuario
RUN chown www-data:www-data /var/www/html/writable/cache
RUN chown www-data:www-data /var/www/html/writable/session
RUN chown www-data:www-data /var/www/html/writable/uploads
RUN chown www-data:www-data /var/www/html/writable/logs

# Permisos de de carpetas
RUN chmod 777 -R /var/www/html/writable/cache/
RUN chmod 777 -R /var/www/html/writable/session/
RUN chmod 777 -R /var/www/html/writable/uploads/
RUN chmod 777 -R /var/www/html/writable/logs/

# Configuraciones de php.ini para tamaños de archivos
RUN sed -E -i 's/(;?)(post_max_size\s*=\s*)[0-9]+M/\220M/g' /usr/local/etc/php/php.ini
RUN sed -E -i 's/(;?)(upload_max_filesize\s*=\s*)[0-9]+M/\220M/g' /usr/local/etc/php/php.ini