#!/bin/bash

# Configuración optimizada para PHP-FPM según recomendaciones de Laravel
cat > /usr/local/etc/php-fpm.d/www.conf << 'EOL'
[www]
user = www-data
group = www-data
listen = 9000

; Configuración del administrador de procesos optimizada
pm = dynamic
pm.max_children = 12
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 1000
pm.process_idle_timeout = 10s

; Configuración de registro
access.log = /proc/self/fd/2
access.format = "%R - %u %t \"%m %r%Q%q\" %s %f %{mili}d %{kilo}M %C%%"
catch_workers_output = yes
decorate_workers_output = no

; Límites ajustados para manejar base64 grandes pero con valores razonables
php_admin_value[post_max_size] = 150M
php_admin_value[upload_max_filesize] = 150M
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 300
php_admin_value[max_input_time] = 300
php_admin_value[max_input_vars] = 5000
php_admin_value[default_socket_timeout] = 120

; Optimizaciones de rendimiento recomendadas para Laravel
php_admin_value[opcache.enable] = 1
php_admin_value[opcache.memory_consumption] = 128
php_admin_value[opcache.interned_strings_buffer] = 16
php_admin_value[opcache.max_accelerated_files] = 10000
php_admin_value[opcache.validate_timestamps] = 1
php_admin_value[opcache.revalidate_freq] = 2
EOL

# También actualizar el php.ini global con valores coherentes
cat > /usr/local/etc/php/conf.d/custom.ini << 'EOL'
; Configuración para manejar archivos grandes
upload_max_filesize = 150M
post_max_size = 150M
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
max_input_vars = 5000
default_socket_timeout = 120

; Optimización de rendimiento
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 1
opcache.revalidate_freq = 2
realpath_cache_size = 4096K
realpath_cache_ttl = 600

; Configuración de errores para producción
display_errors = Off
display_startup_errors = Off
log_errors = On
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
EOL

# Asegurar que los directorios críticos tengan los permisos correctos
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

# Crear directorios de caché si no existen
mkdir -p /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/bootstrap/cache

# Ejecutar el comando original
exec "$@"
