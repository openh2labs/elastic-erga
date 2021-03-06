# official latest php container with apache
FROM php:apache

# pretty
ENV TERM="xterm"

# add nodejs repository, the script will run apt-get update
# install packages
# cron - runs elastic-erga-schedule
# git - for pulling composer dependencies
# nodejs - installs node and npm for dependencies in package.json
# zlib1g-dev - to build php zip extension, which is required by composer
RUN curl -sL https://deb.nodesource.com/setup_6.x | bash - && \
    apt-get install -y \
    cron \
    git \
    nodejs \
    zlib1g-dev \
    && rm -r /var/lib/apt/lists/*
# remove downloaded packages from disk

# enable rewrite rules for laravel
RUN a2enmod rewrite

# install php extensions
# pdo_mysql - php mysql driver
# zip - composer dependency
RUN docker-php-ext-install \
    pdo_mysql \
    zip

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# parallel install of composer dependencies
RUN composer global require "hirak/prestissimo:^0.3"

# install gulp
RUN npm install --global gulp-cli

# copy source code to container
# on dev a volume is mounted on top of this, which means bootstrap needs to be run again
COPY src /var/www/elastic-erga

# give write access to webserver
RUN chown -R www-data:www-data /var/www/elastic-erga/laravel/storage
# php artisan clear-compiled writes to services.php in bootstrap/cache
RUN chown -R www-data:www-data /var/www/elastic-erga/laravel/bootstrap/cache

# set up project webroot
RUN rmdir /var/www/html && ln -s /var/www/elastic-erga/laravel/public /var/www/html

# set up cron
ADD devops/docker/app/crontab /etc/cron.d/elastic-erga-schedule

# import and run bootstrap script
ADD devops/docker/app/bootstrap.sh /usr/local/bin/bootstrap.sh
RUN /usr/local/bin/bootstrap.sh

# container start script
ADD devops/docker/app/startup.sh /usr/local/bin/startup.sh

EXPOSE 80

WORKDIR /var/www/elastic-erga

CMD ["/usr/local/bin/startup.sh"]