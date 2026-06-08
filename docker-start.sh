#!/bin/bash
set -e

echo "=== Barangay IS startup ==="

# Clear file-based config cache
php artisan config:clear

# Run migrations against Neon PostgreSQL
echo "Running migrations..."
php artisan migrate --force

# Storage symlink
php artisan storage:link || true

# Set permissions
chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Starting Laravel on port 10000 ==="
php artisan serve --host=0.0.0.0 --port=10000
