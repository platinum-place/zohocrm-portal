#!/bin/bash

cat > /usr/local/etc/php-fpm.d/www.conf << 'EOL'
[www]
user = www-data
group = www-data
listen = 9000

pm = dynamic
pm.max_children = 12
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 1000
pm.process_idle_timeout = 10s

access.log = /proc/self/fd/2
access.format = "%R - %u %t \"%m %r%Q%q\" %s %f %{mili}d %{kilo}M %C%%"
catch_workers_output = yes
decorate_workers_output = no

php_admin_value[post_max_size] = 150M
php_admin_value[upload_max_filesize] = 150M
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 300
php_admin_value[max_input_time] = 300
php_admin_value[max_input_vars] = 5000
php_admin_value[default_socket_timeout] = 120

php_admin_value[opcache.enable] = 1
php_admin_value[opcache.memory_consumption] = 128
php_admin_value[opcache.interned_strings_buffer] = 16
php_admin_value[opcache.max_accelerated_files] = 10000
php_admin_value[opcache.validate_timestamps] = 1
php_admin_value[opcache.revalidate_freq] = 2
EOL

cat > /usr/local/etc/php/conf.d/custom.ini << 'EOL'
upload_max_filesize = 150M
post_max_size = 150M
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
max_input_vars = 5000
default_socket_timeout = 120

opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 1
opcache.revalidate_freq = 2
realpath_cache_size = 4096K
realpath_cache_ttl = 600

display_errors = Off
display_startup_errors = Off
log_errors = On
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
EOL

git config --global --add safe.directory /var/www/html

mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache

touch /var/www/html/storage/logs/laravel.log

chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chmod 664 /var/www/html/storage/logs/laravel.log
chmod 600 /var/www/html/storage/oauth-*.key

exec "$@"
