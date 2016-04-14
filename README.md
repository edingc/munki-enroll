# Munki Enroll

A set of scripts to automatically enroll clients in Munki, allowing for a very flexible manifest structure.

## Why Munki Enroll?

My organization has a very homogenous environment consisting of several identical deployments. We deploy machines with a basic manifest, like "room_28". This works wonderfully, until computer three in room 28 needs a special piece of software.

Munki Enroll allows us this flexibility. A computer is deployed with a generic manifest, and Munki Enroll changes the manifest to a specific manifest. The new specific manifest contains the generic manifest as an included_manifests key, allowing us to easily target the whole lab and each individual computer.

### Wait, Doesn't Munki Do This Already?

Munki can target systems based on hostnames or serial numbers. However, each manifest must be created by hand. Munki Enroll allows us to create specific manifests automatically, and to allow them to contain a more generic manifest for large-scale software management.

## Installation

Munki Enroll requires PHP to be working on the webserver hosting your Munki repository.

Copy the "munki-enroll" folder to the root of your Munki repository (the same directory as pkgs, pkginfo, manifests and catalogs). 

That's it! Be sure to make note of the full URL path to the enroll.php file.

## Example manifest organization

A simple example of manifest organization in Munki Enroll is shown below:

    . /manifests
    ├── default (Software for all computers goes here.)
    ├── locationA
    │   ├── A_default (Software for locationA computers goes here. Includes default manifest.)
    │   └── clients
    │       └── computer1 (Software for computer1 goes here. Includes A_default manifest, which includes default manifest.)
    └── locationB
        ├── B_default (Software for locationB computers goes here. Includes default manifest.)
        └── clients
            └── computer2 (Software for computer2 goes here. Includes A_default manifest, which includes default manifest.)

The `default`, `A_default`, and `B_default` manifests would be manually created. Computer1 would be provisioned with its ClientIdentifier set to `locationA/A_default`. Munki Enroll would then be run on the computer to generate the `computer1` manifest in the clients folder under locationA. The computer1 manifest contains the A_default manifest, which contains the default manifest.

### Deploying packages

The default manifest might contain web browsers or other applications needed on all computers. The A_default manifest would contain location-specific packages, while the computer1 manifest would contain computer-specific packages.

This organization makes it extremely easy to target a bunch of computers or only one depending on needs.

## Client Configuration

Edit the included munki_enroll.sh script to include the full URL path to the enroll.php file on your Munki repository.

	SUBMITURL="https://munki/munki-enroll/enroll.php"

The included munki_enroll.sh script can be executed in any number of ways (Terminal, ARD, DeployStudio workflow, LaunchAgent, etc.). Once the script is executed, the Client Identifier is switched to a unique identifier based on the system's hostname.

## Caveats

Currently, Munki Enroll lacks any kind of error checking. It works perfectly fine in my environment without it. Your mileage may vary.

Your web server must have access to write to your Munki repository. I suggest combining SSL and Basic Authentication (you're doing this anyway, right?) on your Munki repository to help keep nefarious things out. To do this, edit the CURL command in munki_enroll.sh to include the following flag:

	--user "USERNAME:PASSWORD;" 

## License

Munki Enroll, like the contained CFPropertyList project, is published under the [MIT License](http://www.opensource.org/licenses/mit-license.php).
