FROM php:7.3-cli
WORKDIR /opt
RUN apt-get update && apt-get install -y \
        libzip-dev \
        git \
        unzip \
    && docker-php-ext-install -j$(nproc) zip \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --quiet \
    && rm composer-setup.php \
    && mv composer.phar /usr/local/bin/composer