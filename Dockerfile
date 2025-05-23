FROM php:8.2-fpm

ARG USER_ID=1000
ARG GROUP_ID=1000
ARG USER_NAME=laravel
ARG GROUP_NAME=laravel

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

RUN git config --global --add safe.directory /var/www/html

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

RUN echo "upload_max_filesize = 150M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 150M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Create user and group with specified IDs if they don't exist
RUN groupadd -g ${GROUP_ID} ${GROUP_NAME} || getent group ${GROUP_ID} | cut -d: -f1 | xargs groupmod -n ${GROUP_NAME} \
    && useradd -u ${USER_ID} -g ${GROUP_ID} -m -s /bin/bash ${USER_NAME} || usermod -u ${USER_ID} -g ${GROUP_ID} -l ${USER_NAME} $(getent passwd ${USER_ID} | cut -d: -f1)

RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R ${USER_ID}:${GROUP_ID} /var/www/html

COPY --chown=${USER_ID}:${GROUP_ID} composer.json composer.lock* ./
COPY --chown=${USER_ID}:${GROUP_ID} packages ./packages

USER ${USER_NAME}
RUN composer install --no-scripts --no-autoloader --prefer-dist

USER root
COPY --chown=${USER_ID}:${GROUP_ID} . .
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R ${USER_ID}:${GROUP_ID} /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod 600 /var/www/html/storage/oauth-*.key \
    && touch /var/www/html/storage/logs/laravel.log \
    && chown ${USER_ID}:${GROUP_ID} /var/www/html/storage/logs/laravel.log \
    && chmod 664 /var/www/html/storage/logs/laravel.log

USER ${USER_NAME}
RUN composer dump-autoload --optimize

EXPOSE 9000

USER root
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
