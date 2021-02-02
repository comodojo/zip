#!/bin/bash

# Set default vars
PACKAGE=zip
CURRENTIMAGE=1
SOURCEDIR=`pwd`
DOCKER=/usr/bin/docker
COMPOSER=/usr/local/bin/composer
ENVS=(74 80)
declare -A RESULTS

for E in ${ENVS[@]}; do
    if [[ "$($DOCKER images -q local:$PACKAGE-test-$CURRENTIMAGE-$E 2> /dev/null)" == "" ]]; then
        $DOCKER build -t local:$PACKAGE-test-$CURRENTIMAGE-$E -f $SOURCEDIR/php$E.dockerfile .
    fi
done

for E in ${ENVS[@]}; do
    $DOCKER run --rm -v ${PWD}:/opt local:$PACKAGE-test-$CURRENTIMAGE-$E /bin/bash -c "$COMPOSER update; php vendor/bin/phpunit --coverage-text"
    [[ $? -ne 0 ]] && RESULTS[$E]='FAIL' || RESULTS[$E]='SUCCESS'
done

# Print result summary
printf "\n*******************\n"
printf "Test result summary:\n"
for i in "${!RESULTS[@]}"
do
  printf "PHP-$i: ${RESULTS[$i]}\n"
done
printf "*******************\n"