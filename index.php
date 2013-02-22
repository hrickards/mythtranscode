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
 * Just displays a message at the moment
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// TODO Put on moodle modules site

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

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

// Output a notification, saying this feature is currently unsupported (potentially, this would be
// something akin to allowing access to all recorings/all recordings added under the current
// course). However, moodle still requires this page for compatibility.
echo $OUTPUT->notification(get_string('no_index_page', 'mythtranscode'));

// Finish the page.
echo $OUTPUT->footer();
