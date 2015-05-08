<?php
namespace CFPropertyList;

require_once( 'cfpropertylist-2.0.1/CFPropertyList.php' );

// Get the varibles passed by the enroll script
$identifier = $_GET["identifier"];
$hostname   = $_GET["hostname"];

// Split the manifest path up to determine directory structure
$directories		= explode( "/", $identifier, -1 ); 
$total				= count( $directories );
$n					= 0;
$identifier_path	= "";
while ( $n < $total )
    {
        $identifier_path .= $directories[$n] . '/';
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
        
        // Create the new manifest plist
        $plist = new CFPropertyList();
        $plist->add( $dict = new CFDictionary() );
        
        // Add manifest to production catalog by default
        $dict->add( 'catalogs', $array = new CFArray() );
        $array->add( new CFString( 'production' ) );
        
        // Add parent manifest to included_manifests to achieve waterfall effect
        $dict->add( 'included_manifests', $array = new CFArray() );
        $array->add( new CFString( $identifier ) );
        
        // Save the newly created plist
        $plist->saveXML( '../manifests/' . $identifier_path . 'clients/' . $hostname );
        
    }

?>