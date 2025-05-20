
FROM php:8.4-apache

# Update packages and install necessary dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
 -11,21 +11,50

RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    default-mysql-client \
    libicu-dev \
    libsqlite3-dev \
    npm \
    apache2-dev

# Install PHP extensions
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

# Enable SQLite support
RUN docker-php-ext-enable pdo_sqlite

# Enable mod_rewrite and headers for Apache
RUN a2enmod rewrite headers

# Disable server signature and expose version details
RUN echo "ServerSignature Off" >> /etc/apache2/apache2.conf \
    && echo "ServerTokens Prod" >> /etc/apache2/apache2.conf

# Create security headers configuration
RUN touch /etc/apache2/conf-available/security-headers.conf \
    && echo "Header unset Server" >> /etc/apache2/conf-available/security-headers.conf \
    && echo "Header unset X-Powered-By" >> /etc/apache2/conf-available/security-headers.conf \
    && a2enconf security-headers

# Disable PHP version exposure
RUN echo "expose_php = Off" >> /usr/local/etc/php/conf.d/security.ini

# Define arguments for user and group
ARG USERID
ARG GROUPID
ARG USER
ARG GROUP

# Create non-root group and user (with verification)
RUN set -e; \
    if ! getent group "${GROUP}" > /dev/null; then \
        addgroup --gid "${GROUPID}" "${GROUP}"; \
 -34,42 +63,65  RUN set -e; \
        adduser --disabled-password --gecos "" --uid "${USERID}" --gid "${GROUPID}" "${USER}"; \
    fi

# Add the created user to Apache group (www-data)
RUN usermod -aG www-data "${USER}"

# Configure Apache to use non-root user and group
RUN sed -i "s/^User www-data/User ${USER}/" /etc/apache2/apache2.conf \
    && sed -i "s/^Group www-data/Group ${GROUP}/" /etc/apache2/apache2.conf

# Set up project directory
WORKDIR /var/www/html

# Create necessary Laravel directories if they don't exist
RUN mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/logs \
    && mkdir -p database

# Ensure SQLite database file can be created and is writable
RUN touch /var/www/html/database/database.sqlite \
    && chmod 666 /var/www/html/database/database.sqlite

# Copy project files
COPY . /var/www/html/

# Set correct permissions
RUN chown -R "${USER}:${GROUP}" /var/www/html \
    && chmod -R 775 /var/www/html \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 777 /var/www/html/storage/framework \
    && chmod -R 777 /var/www/html/storage/logs \
    && chmod -R 777 /var/www/html/database \
    && chmod 666 /var/www/html/database/database.sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Switch to non-root user to install Composer dependencies
USER "${USER}"
RUN composer install --no-dev --optimize-autoloader

# Install npm dependencies and build assets
RUN npm install && npm run build

USER root

# Configure Apache for Laravel
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Set Apache DocumentRoot to Laravel's public directory
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Run Apache in foreground as non-root user
USER "${USER}"
CMD ["apache2-foreground"]
