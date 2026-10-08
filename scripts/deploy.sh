#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="$(pwd)"
cd "$APP_DIR"

echo "==> RED BAG deploy: $(date -Is)"

test -f artisan || { echo "ERROR: artisan not found in $APP_DIR"; exit 1; }
test -f .env || { echo "ERROR: .env not found. Configure the production environment first."; exit 1; }

echo "==> Fetching latest main"
git fetch origin main
git checkout main
git reset --hard origin/main

echo "==> Installing PHP dependencies"
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

echo "==> Installing frontend dependencies"
npm ci
npm run build

echo "==> Backing up database before migration"
bash scripts/backup-db.sh

echo "==> Running database migrations"
php artisan migrate --force

echo "==> Clearing Laravel caches"
php artisan optimize:clear

echo "==> Rebuilding Laravel caches"
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "==> Deployment successful: $(git rev-parse --short HEAD)"
