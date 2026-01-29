#!/bin/bash
set -e

# Create .env file if it doesn't exist
if [ ! -f ".env" ]; then
    if [ -f ".env.docker" ]; then
        echo "Creating .env file from .env.docker"
        cp .env.docker .env
    else
        echo "Creating .env file from .env.example"
        cp .env.example .env
    fi
fi

# Generate APP_KEY if it's not set
if [ -z "$APP_KEY" ] && ! grep -q "^APP_KEY=base64:" .env; then
    echo "Generating APP_KEY..."
    php artisan key:generate
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Cache configuration
echo "Clearing all caches..."
php artisan optimize:clear
php artisan config:cache

# Start the main process
exec "$@"
