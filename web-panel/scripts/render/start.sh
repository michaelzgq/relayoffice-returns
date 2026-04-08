#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

source /usr/local/bin/bootstrap-app-env.sh

export PORT="${PORT:-10000}"
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
