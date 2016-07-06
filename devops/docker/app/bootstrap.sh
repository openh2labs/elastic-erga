#!/usr/bin/env bash

set -e
set -o pipefail

echo 'Bootstrapping elastic erga ..'

# set working directory
cd /var/www/elastic-erga/laravel

# install composer packages
composer install -o

# install npm packages
npm install

# run gulp tasks
gulp
# todo: deprecate gulp

cd /var/www/elastic-erga/react-boilerplate

npm install
npm run build