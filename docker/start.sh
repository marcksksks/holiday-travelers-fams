#!/bin/sh
set -e

PORT="${PORT:-8080}"

case "$PORT" in
    *[!0-9]*|'')
        echo "Invalid PORT value."
        exit 1
        ;;
esac

echo "Configuring nginx to listen on port ${PORT}..."

sed -i \
    "s/listen 8080;/listen ${PORT};/" \
    /etc/nginx/http.d/default.conf

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

exec supervisord -c /etc/supervisord.conf
