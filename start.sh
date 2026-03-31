#!/bin/bash

# Startup script for Kromerce
set -e

echo "🚀 Starting Kromerce..."

# Ensure Laravel Vite does not enter dev-server mode in production
rm -f public/hot

# Wait for database to be ready (if using PostgreSQL)
if [ ! -z "$DATABASE_URL" ]; then
    echo "⏳ Waiting for database..."
    # Extract host from DATABASE_URL and wait for it
    DB_HOST=$(echo $DATABASE_URL | sed -n 's/.*@\([^:]*\):.*/\1/p')
    if [ ! -z "$DB_HOST" ]; then
        until nc -z -w 2 $DB_HOST 5432; do
            echo "Waiting for PostgreSQL at $DB_HOST:5432..."
            sleep 2
        done
        echo "✅ Database is ready!"
    fi
fi

# Clear caches (safe to run at startup)
echo "🧹 Clearing caches..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# Seed database (only if not already seeded)
echo "🌱 Seeding database..."
php artisan db:seed --force

# Cache configuration for production
echo "💾 Caching configuration..."
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Start supervisor
echo "🎯 Starting services..."
exec supervisord -n
