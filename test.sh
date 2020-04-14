#!/bin/sh

# Set default vars
CURRENTIMAGE=1
SOURCEDIR=`pwd`
DOCKER=/usr/local/bin/docker
COMPOSER=/usr/local/bin/composer

# Build PHP containers (if needed)
if [[ "$(docker images -q local:zip-test-$CURRENTIMAGE-73 2> /dev/null)" == "" ]]; then
    $DOCKER build -t local:zip-test-$CURRENTIMAGE-73 -f $SOURCEDIR/.docker/php73.dockerfile .
fi
if [[ "$(docker images -q local:zip-test-$CURRENTIMAGE-74 2> /dev/null)" == "" ]]; then
    $DOCKER build -t local:zip-test-$CURRENTIMAGE-74 -f $SOURCEDIR/.docker/php74.dockerfile .
fi

# Test packages
$DOCKER run --rm -v ${PWD}:/opt local:zip-test-$CURRENTIMAGE-73 /bin/bash -c "composer update; php vendor/bin/phpunit"
[[ $? -ne 0 ]] && PHP73='FAIL' || PHP73='SUCCESS'
$DOCKER run --rm -v ${PWD}:/opt local:zip-test-$CURRENTIMAGE-74 /bin/bash -c "composer update; php vendor/bin/phpunit"
[[ $? -ne 0 ]] && PHP74='FAIL' || PHP74='SUCCESS'

# Print result summary
echo "Test result summary:\n"
echo "PHP 7.3: $PHP73"
echo "PHP 7.4: $PHP74"
echo "\n"

