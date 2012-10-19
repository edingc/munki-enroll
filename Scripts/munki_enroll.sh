#!/bin/sh 
 
IDENTIFIER=$( defaults read /Library/Preferences/ManagedInstalls ClientIdentifier ); 
HOSTNAME=$( scutil --get ComputerName );

SUBMITURL="https://munki/munki-enroll/enroll.php"

# Application paths
CURL="/usr/bin/curl"

$CURL --max-time 5 --silent --get \
    -d hostname="$HOSTNAME" \
    -d identifier="$IDENTIFIER" \
    "$SUBMITURL"
 	
IDENTIFIER_PATH=$( echo "$IDENTIFIER" | sed 's/\/[^/]*$//' ); 
 	
defaults write /Library/Preferences/ManagedInstalls ClientIdentifier "$IDENTIFIER_PATH/clients/$HOSTNAME"
 
exit 0