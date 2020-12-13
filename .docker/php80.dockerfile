FROM php:8.0-cli
WORKDIR /opt
ENV XDEBUG_MODE=coverage
RUN apt-get update && apt-get install -y \
        libzip-dev \
        git \
        unzip \
    && pecl install xdebug \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-enable xdebug \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --quiet \
    && rm composer-setup.php \
    && mv composer.phar /usr/local/bin/composer
