#!/bin/sh

# Exit immediately if a command exits with a non-zero status
set -e

echo "Starting entrypoint script..."

# Ensure storage directories exist and are writable
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/database

# Ensure correct permissions for web user
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/database

# SQLite Setup
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    export DB_CONNECTION=sqlite
    # If DB_DATABASE is not set, set it to the default path
    if [ -z "$DB_DATABASE" ]; then
        export DB_DATABASE="/var/www/html/database/database.sqlite"
    fi
    
    # Touch the SQLite file if it doesn't exist
    if [ ! -f "$DB_DATABASE" ]; then
        echo "Creating SQLite database at $DB_DATABASE..."
        touch "$DB_DATABASE"
        chown www-data:www-data "$DB_DATABASE"
        
        # Run migrations and seeders for new DB
        echo "Running migrations & seeders..."
        php artisan migrate --force --seed
    else
        echo "SQLite database file exists at $DB_DATABASE. Running migrations..."
        php artisan migrate --force
    fi
else
    echo "Using external database ($DB_CONNECTION). Running migrations..."
    php artisan migrate --force
    if [ "$DB_SEED" = "true" ]; then
        echo "Running seeders..."
        php artisan db:seed --force
    fi
fi

# Optimize Laravel for production
echo "Caching configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
php-fpm -D

# Start Nginx in foreground
echo "Starting Nginx..."
exec nginx -g "daemon off;"
