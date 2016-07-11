#!/usr/bin/env bash

set -e
set -o pipefail

echo 'Starting elastic erga ..'

# on dev, need to bootstrap again after the volume in mounted from host system
if [ "${APP_ENV}" == "local" ]; then
    /usr/local/bin/bootstrap.sh
fi

cd /var/www/elastic-erga/laravel

# Update database schema
php artisan migrate

# Start cron in the background
cron &

exec apache2 -DFOREGROUND