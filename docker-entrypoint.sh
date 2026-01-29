#!/bin/bash
set -e

# Create .env file if it doesn't exist
if [ ! -f ".env" ]; then
    echo "Creating .env file from .env.example"
    cp .env.example .env
fi

# Generate APP_KEY if it's not set
if [ -z "$APP_KEY" ] && ! grep -q "^APP_KEY=base64:" .env; then
    echo "Generating APP_KEY..."
    php artisan key:generate
fi

# Cache configuration
echo "Caching configuration..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache

# Start the main process
exec "$@"
