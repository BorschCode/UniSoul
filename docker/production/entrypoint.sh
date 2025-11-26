#!/usr/bin/env bash
set -e

# Create .composer directory if it doesn't exist
if [ ! -d /.composer ]; then
    mkdir /.composer
fi

chmod -R ugo+rw /.composer

# Run migrations and cache optimization
php artisan migrate --force --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache

# If arguments are provided, execute them
if [ $# -gt 0 ]; then
    exec gosu www-data "$@"
else
    # Start supervisord to run the application
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
fi
