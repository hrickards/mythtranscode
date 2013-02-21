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

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // Mythtranscode instance ID - it should be named as the first character of the module.

// Retrieve course/instance details.
if ($id) {
    $cm         = get_coursemodule_from_id('mythtranscode', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $mythtranscode  = $DB->get_record('mythtranscode', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $mythtranscode  = $DB->get_record('mythtranscode', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $mythtranscode->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('mythtranscode', $mythtranscode->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('must_specify_id', 'mythtranscode'));
}

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

// When linking to videos, either the course or instance ID must also
// be sent
if ($id) {
    $param_string = "id={$id}";
} else {
    $param_string = "n={$n}";
}

// Get the video filename, title, date and channel
$basename = required_param('basename', PARAM_CLEAN);
list($filename, $title, $date, $channel) = mythtranscode_get_filename_metadata($basename);

// Output the video using a renderer.
$output = $PAGE->get_renderer('mod_mythtranscode');
$video = new mythtranscode_video($filename, $param_string, $title, $date, $channel);
echo $output->render($video);

// Finish the page.
echo $OUTPUT->footer();
