#Mythtranscode Moodle Plugin
Moodle activity module for accessing MythTV recordings, transcoded to HTML5 video formats using mythtranscode.

## Installation instructions
 - Install Mythtranscode into MythTv, and mount the output directory over NFS on the moodle server
 - Clone or unzip this folder into the mod folder of your root moodle directory
 - Login to moodle and follow the onscreen instructions to install the plugin
 - Fill out the onscreen settings page. You'll probably need to change the database details (Host, Port, Username, Password, Database and Table), and the base path (the folder under which the video recordsings are stored), but the rest should be fine as default. If you're storing video in more than just WebM, you will also need to change the Video formats setting.
 - Add to a course as with any other activity module

## Details
mod\_form.php is the file called when a user adds an instance of the activity module. This contains a link to choose.php, which contains the code for esarching/listing recordings, and clicking on one takes the user to chosen.php where it's recorded in the session. When the user clicks on the instance of the activity module, this takes them to view.php, which uses access\_file.php to proxy the video files (with authentication) from locally on the moodle server.

access\_file.php sends the necessary headers to allow video plaback and downloading as if a normal file were being played, and also allows seeking-without-buffering capability if the browser supports it.

index.php just displays a message indicating this functionality is not available.  Potentially, this would be something akin to allowing access to all recorings/all recordings added under the current course, but is not implemented in thsi version. However, moodle still requires the page for compatibility.

Videos cannot be searched or viewed without the user being logged in and a member of a course with Mythtranscode as an activity, or as a member with permissions to access a manual Mythtranscode activity instance.

## Notes
Note, changing the columns shown (in the settings page) may need tweaks to the CSS (in styles.css, although you will need to reload the Moodle css by clicking 'Clear theme caches' in Site Administration > Appearance > Themes > Theme selector).

If required, download links can be disabled in the settings. Note that it will still be relatively easy to to download the video files though (simply by viewing the source of the HTML).

Licensed under GPLv3 or later (see http://www.gnu.org/copyleft/gpl.html).

Television icon dedicated to the public domain by chrisdesign of Open Clip Art Library under the CC0 1.0 Universal license.
