#!/bin/sh

# Exit on error
set -e

# Run database migrations if database connection is available
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Cache configurations and routes for performance
echo "Caching configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP-FPM in the background
echo "Starting PHP-FPM..."
php-fpm -D

# Start Nginx in the foreground
echo "Starting Nginx..."
nginx -g "daemon off;"
