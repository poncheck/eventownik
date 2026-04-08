#!/bin/bash
set -e

cd /var/www

# Wait for DB
echo "Waiting for database..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    sleep 2
done
echo "Database ready."

# Generate app key if missing
if [ -z "$(grep '^APP_KEY=base64:' /var/www/.env 2>/dev/null)" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations and seeders
php artisan migrate --force
php artisan db:seed --force

# Clear and cache config
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

php-fpm
