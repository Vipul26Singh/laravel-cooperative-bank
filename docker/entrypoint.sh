#!/bin/sh
set -e

cd /var/www/html

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Create SQLite database if it doesn't exist
if [ "$DB_CONNECTION" = "sqlite" ] && [ ! -f "$DB_DATABASE" ]; then
    echo "Creating SQLite database..."
    touch "$DB_DATABASE"
    chown www-data:www-data "$DB_DATABASE"
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Seed database if fresh install (no users exist)
if ! php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | grep -q '^[1-9]'; then
    echo "Seeding database..."
    php artisan db:seed --force --no-interaction
fi

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create supervisor log directory
mkdir -p /var/log/supervisor

# Fix permissions
chown -R www-data:www-data storage bootstrap/cache database

echo "============================================"
echo "  Cooperative Bank is ready!"
echo "  Visit: ${APP_URL:-http://localhost:8000}"
echo "============================================"

exec "$@"
