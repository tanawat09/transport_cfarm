#!/bin/sh
set -eu

mkdir -p \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache

if [ ! -f /var/www/html/vendor/autoload.php ] || [ ! -f /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/resources/exceptions/renderer/dist/styles.css ]; then
    echo "[entrypoint] Installing Composer dependencies..."
    COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction --prefer-dist --optimize-autoloader
fi

exec apache2-foreground
