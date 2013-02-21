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
 * Provides authenticated access to a video file
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/repository/filesystem/lib.php');
require_once(dirname(__FILE__).'/lib.php');

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

// Get the filename of the video to access.
$filename = optional_param('filename', '', PARAM_CLEAN);

// Split the filename on dots (e.g., foo.bar.baz.qux.webm ->
// ['foo', 'bar', 'baz', 'qux', 'webm']).
$parts = explode('.', $filename);

// Take the last part as the file extension, and remove any non-alphanumeric
// characters.
$extension = preg_replace("/[^A-Za-z0-9]/", '', array_pop($parts));

// Take the second to last part as the filename, and remove any
// non-(alphanumeric|dash|underscore) characters.
$filename = preg_replace("/[^A-Za-z0-9_\-]/", '', array_pop($parts));

// Set the content type. Guessing it like this from the file extension is a
// bit of a hack, but works wull enough for all of the HTML5 video types.
header('Content-Type: video/' . $extension);

// Read the file and return it, if it exists
$filepath = "{$CFG->mod_mythtranscode_base_path}/{$filename}.{$extension}";
if (file_exists($filepath)) {
    echo readfile($filepath);
} else {
    print_error(get_string('file_not_found', 'mythtranscode'));
}
