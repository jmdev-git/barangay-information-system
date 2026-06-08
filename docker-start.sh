#!/bin/bash
set -e

# Create SQLite database file if it doesn't exist
if [ ! -f /var/data/database.sqlite ]; then
    touch /var/data/database.sqlite
    echo "SQLite database created at /var/data/database.sqlite"
fi

# Point DB to persistent disk
export DB_DATABASE=/var/data/database.sqlite

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link || true

# Cache config, routes, views for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Laravel
echo "Starting Laravel on port 10000..."
php artisan serve --host=0.0.0.0 --port=10000
