#!/bin/bash

# Set default vars
CURRENTIMAGE=1
SOURCEDIR=`pwd`
DOCKER=/usr/bin/docker
COMPOSER=/usr/local/bin/composer

# Build PHP containers (if needed)
if [[ "$($DOCKER images -q local:zip-test-$CURRENTIMAGE-73 2> /dev/null)" == "" ]]; then
    $DOCKER build -t local:zip-test-$CURRENTIMAGE-73 -f $SOURCEDIR/.docker/php73.dockerfile .
fi
if [[ "$($DOCKER images -q local:zip-test-$CURRENTIMAGE-74 2> /dev/null)" == "" ]]; then
    $DOCKER build -t local:zip-test-$CURRENTIMAGE-74 -f $SOURCEDIR/.docker/php74.dockerfile .
fi

# Test packages
$DOCKER run --rm -v ${PWD}:/opt local:zip-test-$CURRENTIMAGE-73 /bin/bash -c "$COMPOSER update; php vendor/bin/phpunit --coverage-text"
[[ $? -ne 0 ]] && PHP73='FAIL' || PHP73='SUCCESS'
$DOCKER run --rm -v ${PWD}:/opt local:zip-test-$CURRENTIMAGE-74 /bin/bash -c "$COMPOSER update; php vendor/bin/phpunit --coverage-text"
[[ $? -ne 0 ]] && PHP74='FAIL' || PHP74='SUCCESS'

# Print result summary
printf "\n*******************\n"
printf "Test result summary:\n"
printf "* PHP 7.3: $PHP73\n"
printf "* PHP 7.4: $PHP74\n"
printf "*******************\n"
