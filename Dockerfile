FROM php:8.1-apache

# 1. Instalar dependencias del sistema
RUN apt-get update && \
    apt-get install -y \
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
      libapache2-mod-security2 \
      openssl && \
    rm -rf /var/lib/apt/lists/*

# 2. Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mysqli zip intl mbstring exif pcntl bcmath gd

# 3. Habilitar y deshabilitar módulos de Apache
RUN a2enmod rewrite ssl headers socache_shmcb security2 && \
    a2dismod -f autoindex info status userdir

# 4. Configuración de seguridad de Apache
RUN printf 'ServerTokens Prod\nServerSignature Off\nTraceEnable Off\nTimeout 60\nFileETag None\n' >> /etc/apache2/apache2.conf && \
    printf '<Directory /var/www/>\n    Options -Indexes\n    AllowOverride None\n    Require all granted\n</Directory>\n' >> /etc/apache2/apache2.conf && \
    printf 'Header always set X-Frame-Options "SAMEORIGIN"\nHeader always set X-XSS-Protection "1; mode=block"\nHeader always set X-Content-Type-Options "nosniff"\nHeader always edit Set-Cookie ^(.*)$ $1;HttpOnly;Secure\n' > /etc/apache2/conf-available/security-hardening.conf && \
    a2enconf security-hardening

# 5. Crear certificados SSL autofirmados (reemplazar en producción)
RUN mkdir -p /etc/apache2/ssl && \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
      -keyout /etc/apache2/ssl/server.key \
      -out /etc/apache2/ssl/server.crt \
      -subj "/C=US/ST=Hardening/L=Security/O=Example/CN=localhost"

# 6. Generar parámetros Diffie-Hellman
RUN openssl dhparam -out /etc/apache2/ssl/dhparam.pem 2048

# 7. Configurar VirtualHost SSL
RUN printf '<IfModule mod_ssl.c>\n<VirtualHost _default_:443>\n    ServerAdmin webmaster@localhost\n    DocumentRoot /var/www/html/public\n    SSLEngine on\n    SSLCertificateFile /etc/apache2/ssl/server.crt\n    SSLCertificateKeyFile /etc/apache2/ssl/server.key\n    SSLOpenSSLConfCmd DHParameters "/etc/apache2/ssl/dhparam.pem"\n    SSLProtocol -ALL +TLSv1.2 +TLSv1.3\n    SSLCipherSuite HIGH:!aNULL:!MD5:!3DES\n    <Directory /var/www/html/public>\n        Options -Indexes +FollowSymLinks\n        AllowOverride All\n        Require all granted\n    </Directory>\n</VirtualHost>\n</IfModule>\n' > /etc/apache2/sites-available/default-ssl.conf && \
    a2ensite default-ssl

# 8. Definir usuario y grupo no-root
ARG USERID
ARG GROUPID
ARG USER
ARG GROUP
RUN set -e; \
    if ! getent group "${GROUP}"; then groupadd --gid "${GROUPID}" "${GROUP}"; fi; \
    if ! id -u "${USER}"; then useradd --disabled-password --gecos "" --uid "${USERID}" --gid "${GROUPID}" "${USER}"; fi && \
    usermod -aG www-data "${USER}"

# 9. Configurar Apache para usar usuario no-root
RUN sed -i "s/^User www-data/User ${USER}/" /etc/apache2/apache2.conf && \
    sed -i "s/^Group www-data/Group ${GROUP}/" /etc/apache2/apache2.conf

# 10. Copiar código, permisos y Composer
WORKDIR /var/www/html
COPY . .
RUN chown -R "${USER}:${GROUP}" /var/www/html && \
    chmod -R 775 /var/www/html && \
    chmod -R 777 /var/www/html/writable
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
USER "${USER}"
RUN composer install --no-dev --optimize-autoloader
USER root

# 11. Configurar Apache para CodeIgniter
RUN printf '<Directory /var/www/html/public>\n    Options Indexes FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>\n' > /etc/apache2/conf-available/codeigniter.conf && \
    a2enconf codeigniter && \
    sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf


RUN echo "ServerTokens Prod\nServerSignature Off" >> /etc/apache2/conf-available/security.conf \
&& echo "Header unset X-Powered-By" >> /etc/apache2/conf-available/security.conf

# 12. Exponer puertos HTTP y HTTPS
EXPOSE 80 443

# 13. Ejecutar Apache
USER "${USER}"
CMD ["apache2-foreground"]
