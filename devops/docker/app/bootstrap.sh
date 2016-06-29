#!/usr/bin/env bash

set -e
set -o pipefail

echo 'Bootstrapping elastic erga ..'

# set working directory
cd /var/www/elastic-erga

# install npm packages
npm install

# install composer packages
composer install -o

# run gulp tasks
gulp javascript