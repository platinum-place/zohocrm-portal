#!/bin/bash

# Configuración fija para usar www-data en lugar de root
cat > /usr/local/etc/php-fpm.d/www.conf << 'EOL'
[www]
user = www-data
group = www-data
listen = 9000
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
EOL

# Asegurar que los directorios críticos tengan los permisos correctos
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

# Ejecutar el comando original
exec "$@"
