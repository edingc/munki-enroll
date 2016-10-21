<?php
namespace CFPropertyList;

// require cfpropertylist
require_once('cfpropertylist-2.0.1/CFPropertyList.php');

// get the directory where munki-enroll is installed
// munki-enroll should always be placed in the same directory the manifests directory resides in
$munki_dir = dirname(__DIR__);

// debug output
echo "Remote munki-enroll location: " . $munki_dir . PHP_EOL;

// get the varibles passed by the enroll script
$identifier = $_GET["identifier"];
$hostname   = $_GET["hostname"];

// debug output
echo "Computer hostname is: " . $hostname . PHP_EOL;
echo "Current ClientIdentifier is: " . $identifier . PHP_EOL;

// split the manifest path up to determine directory structure
$directories     = explode("/", $identifier, -1);
$total           = count($directories);
$n               = 0;
$identifier_path = "";
while ($n < $total) {
    $identifier_path .= $directories[$n] . '/';
    $n++;
}

// debug output
echo "Target manifest location: " . $munki_dir . '/manifests/' . $identifier_path . 'clients/' . $hostname . PHP_EOL;

// check if manifest already exists for this machine
if (file_exists($munki_dir . '/manifests/' . $identifier_path . 'clients/' . $hostname)) {

    // debug output
    echo "Computer manifest for " . $hostname . " already exists." . PHP_EOL;
    
} else {

    // debug output
    echo "Computer manifest for " . $hostname . " does not exist. Will create." . PHP_EOL;
    
    if (!is_dir($munki_dir . '/manifests/' . $identifier_path . 'clients/')) {
        //debug output 
        echo "Clients folder " . $munki_dir . '/manifests/' . $identifier_path . "clients/ does not exist. Will create." . PHP_EOL;
        
        mkdir($munki_dir . '/manifests/' . $identifier_path . 'clients/', 0755, true);
        
    }
    
    // get the catalog(s) from the old manifest
    $plistold       = new CFPropertyList($munki_dir . '/manifests/' . $identifier);
    $plistold_array = $plistold->toArray();
    $old_catalogs   = $plistold_array['catalogs'];
    
    // create the new manifest plist
    $plist = new CFPropertyList();
    $plist->add($dict = new CFDictionary());
    
    // add manifest to existing catalogs
    $dict->add('catalogs', $array = new CFArray());
    foreach ($old_catalogs AS $old_catalog) {
        $array->add(new CFString($old_catalog));
    }
    
    // Add parent manifest to included_manifests to achieve waterfall effect
    $dict->add('included_manifests', $array = new CFArray());
    $array->add(new CFString($identifier));
    
    // Save the newly created plist
    $plistnew = $munki_dir . '/manifests/' . $identifier_path . 'clients/' . $hostname;
    echo "Writing new plist to " . $plistnew . PHP_EOL;
    $plist->saveXML($plistnew);
    
}

?>
