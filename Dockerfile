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
    zip \
    unzip

RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Solo crear usuario/grupo si no es root (ID != 0)
RUN if [ ${USER_ID} -ne 0 ] && [ ${GROUP_ID} -ne 0 ]; then \
        groupadd -g ${GROUP_ID} ${GROUP_NAME} \
        && useradd -u ${USER_ID} -g ${GROUP_NAME} -m -s /bin/bash ${USER_NAME}; \
    fi

WORKDIR /var/www/html

COPY . .

# Establecer los permisos correctos
RUN if [ ${USER_ID} -ne 0 ] && [ ${GROUP_ID} -ne 0 ]; then \
        chown -R ${USER_NAME}:${GROUP_NAME} /var/www/html; \
    else \
        chown -R root:root /var/www/html; \
    fi \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Cambiar al usuario especificado o mantener root
USER ${USER_ID}

RUN composer install --no-interaction --optimize-autoloader

EXPOSE 9000

CMD ["php-fpm"]
