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
 * Saves the chosen recording id into the session
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

// Get the course and recording IDs
$course_id = optional_param('course', 0, PARAM_INT); // Course_module ID, or
$id  = optional_param('id', 0, PARAM_INT);  // mythtranscode instance ID.
$basename = required_param('basename', PARAM_CLEAN);

// Retrieve course details.
if ($course_id) {
    $course = $DB->get_record('course', array('id' => $course_id), '*', MUST_EXIST);
} elseif ($id) {
    $cm         = get_coursemodule_from_id('mythtranscode', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}
$course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);

// Some initial setup.
require_login($course);

add_to_log($course->id, 'mythtranscode', 'choose', "chosen.php?course={$course->id}&basename={$basename}", 'mythtranscode');
$PAGE->set_url('/mod/mythtranscode/chosen.php', array('course' => $course->id, 'basename' => $basename));

// Store the recording ID into the session
$_SESSION['basename'] = $basename;

// Get the video filename (we need this for checking if any recordings are prsent)
list($filename, $title, $_, $_) = mythtranscode_get_filename_metadata($basename);

// If the recording has transcodings
if (mythtranscode_recording_has_files($filename)) {
    // Save the recording title into a cookie (for updating
    // the form with JS to reflect the chosen recording).
    setcookie('recording', $title, 0, '/');

    // Automatically close the popup window
    echo '<script type="text/javascript">self.close()</script>';

    // Output starts here.
    echo $OUTPUT->header();

    // Output a close window button
    $close_text = get_string('close_window', 'mythtranscode');
    echo "<button onclick='self.close();'>{$close_text}</button>";
} else {
    // Print the page header.
    $PAGE->set_title(get_string('choose_title', 'mythtranscode'));
    $PAGE->set_heading(format_string($course->fullname));

    // Output starts here.
    echo $OUTPUT->header();

    // Output a message saying the recording is currently unavailable, and ask them to try again
    echo $OUTPUT->notification(get_string('unavailable_recording', 'mythtranscode') . ' ' .
        get_string('pick_another', 'mythtranscode'));

    // Output footer
    echo $OUTPUT->footer();
}
