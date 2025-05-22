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
    unzip \
    sudo

RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Solo crear usuario/grupo si no es root (ID != 0)
RUN if [ ${USER_ID} -ne 0 ] && [ ${GROUP_ID} -ne 0 ]; then \
        groupadd -g ${GROUP_ID} ${GROUP_NAME} \
        && useradd -u ${USER_ID} -g ${GROUP_NAME} -m -s /bin/bash ${USER_NAME} \
        && echo "${USER_NAME} ALL=(ALL) NOPASSWD: ALL" > /etc/sudoers.d/${USER_NAME} \
        && chmod 0440 /etc/sudoers.d/${USER_NAME}; \
    fi

WORKDIR /var/www/html

# Copiar el script de entrada
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY . .

# Establecer los permisos correctos
RUN chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# NO cambiamos al usuario específico en el Dockerfile
# Dejamos que el entrypoint.sh maneje la configuración de usuario

RUN composer install --no-interaction --optimize-autoloader

EXPOSE 9000

# Usar el script de entrada para configurar PHP-FPM
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
