#!/bin/bash

CURRENT_UID=$(id -u)
CURRENT_GID=$(id -g)

sed -i "s/user = www-data/user = $CURRENT_UID/g" /usr/local/etc/php-fpm.d/www.conf
sed -i "s/group = www-data/group = $CURRENT_GID/g" /usr/local/etc/php-fpm.d/www.conf

chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

if [ "$CURRENT_UID" != "0" ]; then
    chown -R $CURRENT_UID:$CURRENT_GID /var/www/html/storage
    chown -R $CURRENT_UID:$CURRENT_GID /var/www/html/bootstrap/cache
fi

exec "$@"
