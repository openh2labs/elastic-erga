FROM php:5.6-apache

# Install Packages
RUN apt-get update && \
  DEBIAN_FRONTEND=noninteractive apt-get install -y \
    libmysqlclient18 \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng12-dev \
    libbz2-dev \
    php-pear \
    curl \
    git \
    subversion \
    unzip \
  && rm -r /var/lib/apt/lists/*

# Install Extensions
RUN docker-php-ext-install mcrypt zip bz2 mbstring pdo_mysql

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set GitHub OAuth token to go over the API rate limit
# Head to https://github.com/settings/tokens/new?scopes=repo&description=Composer to get one
RUN composer -g config github-oauth.github.com 46b8b77a4f900b4954ac33c0b8633cc1eab5608f 

COPY htdocs/ /var/www/laravel/

RUN rmdir /var/www/html && ln -s /var/www/laravel/public /var/www/html

WORKDIR /var/www/laravel/

RUN composer install

RUN chown -R www-data:www-data /var/www/laravel/storage && \
    chown -R www-data:www-data /var/www/laravel/bootstrap/cache

EXPOSE 80
