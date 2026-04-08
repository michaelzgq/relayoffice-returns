#!/usr/bin/env bash
set -euo pipefail

if [[ -n "${APP_KEY_BASE64:-}" && -z "${APP_KEY:-}" ]]; then
  export APP_KEY="base64:${APP_KEY_BASE64}"
fi

mkdir -p \
  /var/www/html/bootstrap/cache \
  /var/www/html/storage/framework/cache/data \
  /var/www/html/storage/framework/sessions \
  /var/www/html/storage/framework/views \
  /var/www/html/storage/app/public/return-cases

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

php artisan storage:link >/dev/null 2>&1 || true
