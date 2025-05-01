FROM php:8.4.6-apache

# Actualizar paquetes e instalar dependencias necesarias
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
    libicu-dev

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mysqli zip intl mbstring exif pcntl bcmath gd

# Habilitar mod_rewrite para Apache
RUN a2enmod rewrite

# Definir argumentos para el usuario y grupo
ARG USERID
ARG GROUPID
ARG USER
ARG GROUP

# Crear grupo y usuario no-root (con verificación)
RUN set -e; \
    if ! getent group "${GROUP}" > /dev/null; then \
        addgroup --gid "${GROUPID}" "${GROUP}"; \
    fi; \
    if ! id -u "${USER}" > /dev/null 2>&1; then \
        adduser --disabled-password --gecos "" --uid "${USERID}" --gid "${GROUPID}" "${USER}"; \
    fi

# Agregar el usuario creado al grupo de Apache (www-data)
RUN usermod -aG www-data "${USER}"

# Configurar Apache para usar el usuario y grupo no-root
RUN sed -i "s/^User www-data/User ${USER}/" /etc/apache2/apache2.conf \
    && sed -i "s/^Group www-data/Group ${GROUP}/" /etc/apache2/apache2.conf

# Establecer permisos correctos para las carpetas del proyecto
WORKDIR /var/www/html
COPY . /var/www/html/
RUN chown -R "${USER}:${GROUP}" /var/www/html \
    && chmod -R 775 /var/www/html \
    && chmod -R 777 /var/www/html/writable

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Cambiar al usuario para instalar las dependencias de Composer
USER "${USER}"
RUN composer install --no-dev --optimize-autoloader
USER root

# Configurar Apache para servir correctamente los archivos del proyecto
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/codeigniter.conf \
    && a2enconf codeigniter

# Configurar el DocumentRoot de Apache
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Exponer el puerto 80
EXPOSE 80

# Ejecutar Apache en primer plano como usuario no root
USER "${USER}"
CMD ["apache2-foreground"]