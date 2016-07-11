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

# TODO: possbile incomaptibility here between dev and container
npm install
#npm rebuild node-sass
npm run build