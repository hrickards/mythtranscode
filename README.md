#Mythtranscode Moodle Plugin
Moodle activity module for accessing MythTV recordings, transcoded to HTML5 video formats using mythtranscode.

## Installation instructions
 - Install Mythtranscode into MythTv, and mount the output directory over NFS on the moodle server
 - Clone or unzip this folder into the mod folder of your root moodle directory
 - Login to moodle and follow the onscreen instructions to install the plugin
 - Fill out the onscreen settings page. You'll probably need to change the database details (Host, Port, Username, Password, Database and Table), and the base path (the folder under which the video recordsings are stored), but the rest should be fine as default. If you're storing video in more than just WebM, you will also need to change the Video formats setting.
 - Add to a course as with any other activity module

## Details
view.php contains the code for searching/listing recordings, and clicking on one takes the user to watch.php. This uses access\_file.php to proxy the video files from locally on the moodle server.

index.php just redirects to view.php.

Videos cannot be searched or viewed without the user being logged in and a member of a course with Mythtranscode as an activity, or as a member with permissions to access a manual Mythtranscode activity instance.

## Notes
Note, changing the columns shown (in the settings page) may need tweaks to the CSS (in styles.css, although you will need to reload the Moodle css by clicking 'Clear theme caches' in Site Administration > Appearance > Themes > Theme selector).

If required, download links can be disabled in the settings. Note that it will still be relatively easy to to download the video files though (simply by viewing the source of the HTML).

Licensed under GPLv3 or later (see http://www.gnu.org/copyleft/gpl.html).

Television icon dedicated to the public domain by chrisdesign of Open Clip Art Library under the CC0 1.0 Universal license.
