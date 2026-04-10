#!/bin/sh
set -eu

APP_DIR="/var/www/html"

mkdir -p "$APP_DIR/storage/logs" "$APP_DIR/bootstrap/cache"

# Os volumes podem preservar ownership e cache de builds anteriores.
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

find "$APP_DIR/bootstrap/cache" -maxdepth 1 -type f \( -name '*.php' -o -name '*.json' \) -delete

su -s /bin/sh www-data -c "cd '$APP_DIR' && php artisan package:discover --ansi >/dev/null"

exec apache2-foreground