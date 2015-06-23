#!/bin/sh

#  jenkins-deploy.sh
#  famous
#
#  Created by Guy Bar-Nahum on 6/22/15.
#

PWD=$(pwd)

DST=${1-"/var/www/famous"}
DOMAIN=${2-"http://famous.com:8080"}
DT=$(date)
TS=$(date +%s)
GIT_VER=$(git describe 2>/dev/null)
VER=$(echo "$GIT_VER-$TS")

echo $VER

# Check that we are running in a jenkins environment
if [[ "$PWD" =~ "/var/lib/jenkins" ]]; then
    echo "Running post jenkins deployment script for $DST, $DT";

    #
    # Copy files and setup permissions
    sudo cp -R * "$DST/"
    sudo chown --recursive www-data:www-data "$DST/"
    sudo chmod --recursive 777 "$DST/storage"

    cd "/var/$DST"
    sudo cp --no-clobber --parents conf/famous.ini.dist "$DST"

    # Prime and migrate laraval
    # not sure how to resolve db conflicts!
    sudo composer update
    sudo php artisan migrate
    sudo php artisan db:seed

else
    echo "Invalid jenkins environment $DST, timestamp, $DT";
fi

if [[ -e "$DST/.env" ]]; then
    # inject build ver and oath callback variables

    SUBS="s@^OATH_REDIRECT_URL=.*@OATH_REDIRECT_URL=$DOMAIN/callback@"
    sudo sed -e  $SUBS -i .sav "$DST/.env"

    SUBS="s@^BUILD_VER_STRING=.*@BUILD_VER_STRING\=$VER@"
    sudo sed -e  $SUBS -i .sav "$DST/.env"

else
    echo "Could not locate .env file in $DST.."
fi
