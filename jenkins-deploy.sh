#!/bin/sh

#  jenkins-deploy.sh
#  famous
#
#  Created by Guy Bar-Nahum on 6/22/15.
#

PWD=$(pwd)

DST=$1
DOMAIN=$2
OPT=$3

DT=$(date)
TS=$(date +%s)
GIT_VER=$(git describe 2>/dev/null)
VER=$(echo "$GIT_VER-$TS")

echo $VER

# do we have a destination to deploy into?
if [[ -z "$DST" ]]; then
    echo "No destination path provided, aborting.."
    exit 1
fi

# Check that we are running in a jenkins environment
if [[ "$PWD" =~ "/var/lib/jenkins" ]]; then
    echo "Running post jenkins deployment script for $DST, $DT";

    # Prime and migrate laraval
    # not sure how to resolve db conflicts!
    if [[ $OPT == *"composer"*  ]];then
        echo "+ composer update"
        sudo composer --no-interaction update
        echo "- composer update"
    else
        echo "+ composer dumpautoload"
        sudo composer --no-interaction dumpautoload
        echo "- composer dumpautoload"
    fi

    #
    # Copy files and setup permissions
    echo "insall into $DST"

    sudo cp -R * "$DST/"
    sudo chown --recursive www-data:www-data "$DST/"
    sudo chmod --recursive 777 "$DST/storage"

    cd "$DST"
    sudo cp --no-clobber --parents conf/famous.ini.dist "$DST"

    if [[ $OPT == *"migrate"* ]];then
        echo "+ artisan migrate"
        sudo php artisan --no-interaction migrate
        echo "- artisan migrate"
    fi

    if [[ $OPT == *"seed"* ]]; then
        echo "+ artisan db:seed"
        sudo php artisan --no-interaction db:seed
        echo "- artisan db:seed"
    fi

else
    echo "Invalid jenkins environment $DST, timestamp, $DT";
fi

#
# Attempt to inject build ver and oath callback variables
#
if [[ -e "$DST/.env" ]]; then
    OS=$(uname -s)

    # sed on Mac OS X is funny that way..
    EXT="--in-place=.sav"
    if [[ "$OS" == "Darwin" ]]; then
        EXT="-i .sav"
    fi

    # inject oath_redirect_url if domain specified
    if [[ ! -z "$DOMAIN" ]]; then
        SUBS="s@^OATH_REDIRECT_URL=.*@OATH_REDIRECT_URL=$DOMAIN/callback@"
        sudo sed -e $SUBS $EXT "$DST/.env"
    fi

    # inject build version
    SUBS="s@^BUILD_VER_STRING=.*@BUILD_VER_STRING=$VER@"
    sudo sed -e $SUBS $EXT "$DST/.env"

else
    echo "Could not locate .env file in $DST.."
fi
