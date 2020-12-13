# [Choice] PHP version: 7, 7.4, 7.3
ARG VARIANT=7
FROM php:${VARIANT}-cli

# [Option] Upgrade OS packages to their latest versions
ARG UPGRADE_PACKAGES="true"

# Install needed packages and setup non-root user. Use a separate RUN statement to add your own dependencies.
ARG USERNAME=vscode
ARG USER_UID=1000
ARG USER_GID=$USER_UID
ENV XDEBUG_MODE=coverage
COPY scripts/setup.sh /tmp/scripts/
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && bash /tmp/scripts/setup.sh "${USERNAME}" "${USER_UID}" "${USER_GID}" "${UPGRADE_PACKAGES}" \
    # && pecl install zip \
    && pecl install xdebug \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-enable xdebug \
    # && echo "extension=zip.so" > /usr/local/etc/php/conf.d/zip.ini \
    && apt-get clean -y && rm -rf /var/lib/apt/lists/* /tmp/scripts

# Install composer
RUN curl -sSL https://getcomposer.org/installer | php \
    && chmod +x composer.phar \
    && mv composer.phar /usr/local/bin/composer

# [Optional] Uncomment this section to install additional packages.
# RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
#     && apt-get -y install --no-install-recommends <your-package-list-here>
