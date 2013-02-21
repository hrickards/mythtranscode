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
 * Prints one instance of mythtranscode.
 *
 * Prints a particular instance of mythtranscode, containing a search form
 * and results from that form. Users can click on results and be taken
 * through to chosen.php, where their choice is saved in the session.
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

$course_id = required_param('course', PARAM_INT); // Course_module ID. or

// Retrieve course details.
$course = $DB->get_record('course', array('id' => $course_id), '*', MUST_EXIST);

// Some initial setup.
require_login($course);

add_to_log($course->id, 'mythtranscode', 'choose', "choose.php?course={$course_id}", $mythtranscode->name);

// Print the page header.

$PAGE->set_url('/mod/mythtranscode/choose.php', array('course' => $course_id));
$PAGE->set_title(format_string($mythtranscode->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('heading', 'mythtranscode'));

// Get search parameters --- the query and the no. of results to start at (for pagination).
$query = optional_param('query', '', PARAM_TEXT);
$start = optional_param('start', 0, PARAM_INT);

// Need to pass on course id
$base_data = array('course' => $course_id);

// Output a search form.
$mform = new mythtranscode_search_form($base_data, $query);
echo $mform->render();

// Retrieve the results and total number of matching records.
list($results, $count) = mythtranscode_retrieve_results($query, $start);

// If no results found.
if (!$results) {
    echo $OUTPUT->box(get_string('no_results', 'mythtranscode'));
} else {
    // Create an array from the results (needed for rendering using moodle's)
    // functions.
    $data = array();
    while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
        array_push($data, $row);
    }

    // Render the results.
    $output = $PAGE->get_renderer('mod_mythtranscode');
    $results_table = new mythtranscode_results_table($data, $base_data);
    echo $output->render($results_table);

    // Calculate the no. buttons needed for pagination, and the
    // current page.
    $num_pages = ceil($count/$CFG->mod_mythtranscode_num_results);
    $current_page = min(ceil($start/$CFG->mod_mythtranscode_num_results)+1, $num_pages);

    // The base data that will be sent with all navigation links.
    $base_link_data = $base_data;
    $base_link_data['query'] = $query;

    // Render the pagination buttons, if we have more than one page.
    if ($num_pages>1) {
        $pagination = new mythtranscode_pagination($num_pages, $current_page,
            $base_link_data);
        echo $pagination->render();
    }

}

// Finish the page.
echo $OUTPUT->footer();
