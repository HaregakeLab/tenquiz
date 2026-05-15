#!/bin/bash
# Laravel deploy script
# Called by deployer with env vars: DEPLOY_APP_DIR, DEPLOY_REPO_NAME, DEPLOY_BASE_URL
set -euo pipefail

cd "$DEPLOY_APP_DIR"

# --- Composer ---
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# --- Environment ---
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        echo "Creating .env from .env.example..."
        cp .env.example .env
        php artisan key:generate --force
    fi
fi

# Set production values
if [ -f .env ]; then
    sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" .env
    sed -i "s|^APP_ENV=.*|APP_ENV=production|" .env
    sed -i "s|^APP_URL=.*|APP_URL=${DEPLOY_BASE_URL}|" .env
    # Set absolute path for SQLite
    if grep -q "^DB_CONNECTION=sqlite" .env; then
        SQLITE_PATH="${DEPLOY_APP_DIR}/database/database.sqlite"
        if grep -q "^DB_DATABASE=" .env; then
            sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${SQLITE_PATH}|" .env
        else
            echo "DB_DATABASE=${SQLITE_PATH}" >> .env
        fi
    fi
fi

# Merge SOPS-decrypted secrets (deployer creates .env.secrets) into .env
if [ -f .env.secrets ]; then
    echo "Merging .env.secrets into .env..."
    while IFS= read -r line || [ -n "$line" ]; do
        [ -z "$line" ] && continue
        case "$line" in \#*) continue ;; esac
        key="${line%%=*}"
        if grep -q "^${key}=" .env; then
            # Use # as sed delimiter; values may contain / and |
            sed -i "s#^${key}=.*#${line}#" .env
        else
            echo "$line" >> .env
        fi
    done < .env.secrets
fi

# --- Database ---
mkdir -p database
if [ ! -f database/database.sqlite ]; then
    echo "Creating SQLite database..."
    touch database/database.sqlite
fi

echo "Running migrations..."
php artisan migrate --force --no-interaction

# --- Laravel housekeeping ---
php artisan storage:link --force 2>/dev/null || true
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# --- Tailwind CSS ---
TAILWIND_BIN="/var/www/apps/.tailwindcss"
TAILWIND_VERSION="v4.1.4"

if [ ! -f "$TAILWIND_BIN" ]; then
    echo "Downloading Tailwind CSS CLI ${TAILWIND_VERSION}..."
    curl -sL "https://github.com/tailwindlabs/tailwindcss/releases/download/${TAILWIND_VERSION}/tailwindcss-linux-x64" -o "$TAILWIND_BIN"
    chmod +x "$TAILWIND_BIN"
fi

if [ -f resources/css/app.css ]; then
    echo "Building CSS..."
    mkdir -p public/css
    "$TAILWIND_BIN" -i resources/css/app.css -o public/css/app.css --minify
fi

# --- Permissions ---
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chmod 664 database/database.sqlite 2>/dev/null || true

echo "Laravel deploy complete."
