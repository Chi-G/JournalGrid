#!/bin/bash
set -e

cd /var/www/html

# Cache config, routes, views for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations on every deploy (safe with --force in production)
php artisan migrate --force || true

# Create storage symlink
php artisan storage:link 2>/dev/null || true

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"
