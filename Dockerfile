FROM php:8.4-fpm

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

RUN groupadd -g ${GROUP_ID} ${GROUP_NAME} \
    && useradd -u ${USER_ID} -g ${GROUP_NAME} -m -s /bin/bash ${USER_NAME}

WORKDIR /var/www/html

COPY . .

RUN chown -R ${USER_NAME}:${GROUP_NAME} /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

USER ${USER_NAME}

RUN composer install --no-interaction --optimize-autoloader

EXPOSE 9000

CMD ["php-fpm"]
