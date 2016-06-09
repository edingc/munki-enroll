#!/bin/sh 

# Gather computer information
IDENTIFIER=$( defaults read /Library/Preferences/ManagedInstalls ClientIdentifier ); 
HOSTNAMETEMP=$( scutil --get ComputerName );
# Subs in underscores for spaces in the HOSTNAME
HOSTNAME=${HOSTNAMETEMP// /_}

# Change this URL to the location fo your Munki Enroll install
SUBMITURL="http://localhost:8888/munki/munki-enroll/enroll.php"

# Test the connection to the server
SHORTURL=$(echo "$SUBMITURL" | awk -F/ '{print $3}')
PINGTEST=$(ping -o "$SHORTURL" | grep "64 bytes")

if [ ! -z "$PINGTEST" ]; then

    # Application paths
    CURL="/usr/bin/curl"

    $CURL --max-time 5 --silent --get \
        -d hostname="$HOSTNAME" \
        -d identifier="$IDENTIFIER" \
        "$SUBMITURL"

    # This is a fix for clients based on a manifest in the root /manifests directory
    # See GitHub issue No. 5
    if [ $( echo "$IDENTIFIER" | grep "/" ) ]
    then
    IDENTIFIER_PATH=$( echo "$IDENTIFIER" | sed 's/\/[^/]*$//' ); 
    defaults write /Library/Preferences/ManagedInstalls ClientIdentifier "$IDENTIFIER_PATH/clients/$HOSTNAME"
    else
     defaults write /Library/Preferences/ManagedInstalls ClientIdentifier "clients/$HOSTNAME"
    fi
 
    exit 0

else
    # No good connection to the server
    exit 1

fi
