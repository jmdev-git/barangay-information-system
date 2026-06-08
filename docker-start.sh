#!/bin/bash
set -e

echo "=== Barangay IS startup ==="

# Use /var/data if it exists (persistent disk), otherwise fall back to local storage
if [ -d "/var/data" ]; then
    export DB_DATABASE="/var/data/database.sqlite"
else
    export DB_DATABASE="/var/www/database/database.sqlite"
fi

echo "Using database: $DB_DATABASE"

# Create SQLite file if it doesn't exist
if [ ! -f "$DB_DATABASE" ]; then
    touch "$DB_DATABASE"
    chmod 664 "$DB_DATABASE"
    echo "SQLite file created."
fi

# Clear only file-based config cache (no DB needed)
php artisan config:clear

# Run migrations FIRST before anything that touches the DB
echo "Running migrations..."
php artisan migrate --force

# Storage symlink
php artisan storage:link || true

# Set permissions
chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Now safe to cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Starting Laravel on port 10000 ==="
php artisan serve --host=0.0.0.0 --port=10000
