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
 * through to watch.php.
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 UCTC
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
    error(get_string('must_specify_id', 'mythtranscode'));
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

if ($mythtranscode->intro) {
    echo $OUTPUT->box(format_module_intro('mythtranscode', $mythtranscode, $cm->id),
        'generalbox mod_introbox', 'mythtranscodeintro');
}

echo $OUTPUT->heading(get_string('heading', 'mythtranscode'));

// Get search parameters --- the query and the no. of results to start at (for pagination).
$query = optional_param('query', '', PARAM_TEXT);
$start = optional_param('start', 0, PARAM_INT);

// When linking to watch.php, either the course or instance ID must also
// be sent.
if ($id) {
    $base_data = array('id' => $id);
} else {
    $base_data = array('n' => $n);
}

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
