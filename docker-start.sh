#!/bin/bash
set -e

# Use /var/data if it exists (persistent disk), otherwise fall back to local storage
if [ -d "/var/data" ]; then
    DB_PATH="/var/data/database.sqlite"
else
    DB_PATH="/var/www/database/database.sqlite"
fi

# Create SQLite database file if it doesn't exist
if [ ! -f "$DB_PATH" ]; then
    touch "$DB_PATH"
    echo "SQLite database created at $DB_PATH"
fi

# Set the DB path
export DB_DATABASE="$DB_PATH"

# Run migrations
php artisan migrate --force

# Create storage symlink (ignore if already exists)
php artisan storage:link || true

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Laravel
echo "Starting Laravel on port 10000..."
php artisan serve --host=0.0.0.0 --port=10000
