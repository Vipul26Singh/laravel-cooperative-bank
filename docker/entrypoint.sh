#!/bin/sh
set -e

cd /var/www/html

# Create .env if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Apply docker-compose environment overrides into .env
# This ensures artisan commands read the right values
[ -n "$APP_ENV" ] && sed -i "s|^APP_ENV=.*|APP_ENV=$APP_ENV|" .env
[ -n "$APP_DEBUG" ] && sed -i "s|^APP_DEBUG=.*|APP_DEBUG=$APP_DEBUG|" .env
[ -n "$APP_URL" ] && sed -i "s|^APP_URL=.*|APP_URL=$APP_URL|" .env
[ -n "$DB_CONNECTION" ] && sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=$DB_CONNECTION|" .env
[ -n "$DB_DATABASE" ] && sed -i "s|^DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|" .env
[ -n "$QUEUE_CONNECTION" ] && sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=$QUEUE_CONNECTION|" .env
[ -n "$CACHE_STORE" ] && sed -i "s|^CACHE_STORE=.*|CACHE_STORE=$CACHE_STORE|" .env
[ -n "$SESSION_DRIVER" ] && sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=$SESSION_DRIVER|" .env
[ -n "$MAIL_MAILER" ] && sed -i "s|^MAIL_MAILER=.*|MAIL_MAILER=$MAIL_MAILER|" .env

# Generate app key if not already set in .env
if grep -q "^APP_KEY=$" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Create SQLite database if it doesn't exist
if [ "$DB_CONNECTION" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "$DB_PATH" ]; then
        echo "Creating SQLite database..."
        touch "$DB_PATH"
        chown www-data:www-data "$DB_PATH"
    fi
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Seed database if fresh install (check if users table is empty)
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "Seeding database..."
    php artisan db:seed --force --no-interaction
fi

# Cache configuration only in production
if [ "$APP_ENV" != "local" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    echo "Dev mode: caches disabled for live reloading"
fi

# Create supervisor log directory
mkdir -p /var/log/supervisor

# Fix permissions
chown -R www-data:www-data storage bootstrap/cache database

echo "============================================"
echo "  Cooperative Bank is ready!"
echo "  Visit: ${APP_URL:-http://localhost:8000}"
echo "============================================"

exec "$@"
