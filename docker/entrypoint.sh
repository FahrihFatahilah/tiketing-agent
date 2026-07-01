#!/bin/bash

# Generate app key if not exists
if [ ! -f /var/www/.env ]; then
    cp /var/www/.env.example /var/www/.env
fi

# Generate key
php /var/www/artisan key:generate --force

# Clear all cache
php /var/www/artisan config:clear
php /var/www/artisan route:clear
php /var/www/artisan view:clear
php /var/www/artisan cache:clear

# Run migrations
php /var/www/artisan migrate --force

# Seed database (hanya jika tabel users kosong)
USER_COUNT=$(php /var/www/artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "Seeding database..."
    php /var/www/artisan db:seed --force
fi

# Create storage link
php /var/www/artisan storage:link 2>/dev/null || true

# Set proper permissions
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
