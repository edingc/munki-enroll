<?php

require_once( 'cfpropertylist-1.1.2/CFPropertyList.php' );

// Get the varibles past from CURL on the imaged computer
$identifier = $_GET["identifier"];
$hostname   = $_GET["hostname"];

//NEED TO SPLIT IDENTIFER SLASHES SO WE CAN GET PARENT FOLDER
$pieces          = explode( "/", $identifier );
$total           = count( $pieces );
$total           = $total - 1;
$n               = 0;
$identifier_path = "";
while ( $n < $total )
    {
        $identifier_path .= $pieces[$n] . '/';
        $n++;
    }


// Check if manifest already exists for this machine
if ( file_exists( '../manifests/' . $identifier_path . '/clients/' . $hostname ) )
    {
        echo "Computer manifest already exists.";
    }
else
    {
        echo "Computer manifest does not exist. Will create.";
        
        if ( !is_dir( '../manifests/' . $identifier_path . 'clients/' ) )
            {
                mkdir( '../manifests/' . $identifier_path . 'clients/', 0755, true );
            }
        
        $plist = new CFPropertyList();
        $plist->add( $dict = new CFDictionary() );
        
        $dict->add( 'catalogs', $array = new CFArray() );
        $array->add( new CFString( 'production' ) );
        
        $dict->add( 'included_manifests', $array = new CFArray() );
        $array->add( new CFString( $identifier ) );
        
        $plist->saveXML( '../manifests/' . $identifier_path . 'clients/' . $hostname );
        
    }

?>