<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Displays a television recording
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/renderer.php');

$id = required_param('id', PARAM_INT); // Course_module ID,

// Retrieve course details.
$cm         = get_coursemodule_from_id('mythtranscode', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$mythtranscode  = $DB->get_record('mythtranscode', array('id' => $cm->instance), '*', MUST_EXIST);

// Some initial setup.
require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'mythtranscode', 'view', "view.php?id={$cm->id}", $mythtranscode->name, $cm->id);

// Print the page header.

$PAGE->set_url('/mod/mythtranscode/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($mythtranscode->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here.
echo $OUTPUT->header();

if ($mythtranscode->intro) { // Conditions to show the intro can change to look for own settings or whatever.
    echo $OUTPUT->box(format_module_intro('mythtranscode', $mythtranscode, $cm->id),
        'generalbox mod_introbox', 'mythtranscodeintro');
}

// When linking to videos, the course id must be sent
// be sent
$param_string = "id={$id}";

// Get the basename from the retrieved course details
$basename = $mythtranscode->basename;

// Get the video filename, title, date and channel
list($filename, $title, $date, $channel) = mythtranscode_get_filename_metadata($basename);

// If the recording has transcodings, output the video using a renderer.
if (mythtranscode_recording_has_files($filename)) {
    $output = $PAGE->get_renderer('mod_mythtranscode');
    $video = new mythtranscode_video($filename, $param_string, $title, $date, $channel);
    echo $output->render($video);
} else {
    // Otherwise, display an informative message
    // TODO Do this when the teacher chooses a recording as well
    echo $OUTPUT->notification(get_string('unavailable_recording', 'mythtranscode'));
}

// Finish the page.
echo $OUTPUT->footer();
