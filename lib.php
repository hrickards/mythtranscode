<?PHP
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
 * Library of interface functions and constants for module mythtranscode
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the mythtranscode specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");

defined('MOODLE_INTERNAL') || die();

// Moodle core API.

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function mythtranscode_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mythtranscode into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $mythtranscode An object from the form in mod_form.php
 * @param mod_mythtranscode_mod_form $mform
 * @return int The id of the newly inserted mythtranscode record
 */
function mythtranscode_add_instance(stdClass $mythtranscode, mod_mythtranscode_mod_form $mform = null) {
    global $DB;

    // Add the basename from the session to the record, if it's there.
    // If not, show an error;
    $basename = $_SESSION['basename'];
    if (isset($basename)) {
        $mythtranscode->basename = $basename;
    } else {
        print_error(get_string('basename_not_found', 'mythtranscode'));
    }

    $mythtranscode->timecreated = time();

    return $DB->insert_record('mythtranscode', $mythtranscode);
}

/**
 * Updates an instance of the mythtranscode in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $mythtranscode An object from the form in mod_form.php
 * @param mod_mythtranscode_mod_form $mform
 * @return boolean Success/Fail
 */
function mythtranscode_update_instance(stdClass $mythtranscode, mod_mythtranscode_mod_form $mform = null) {
    global $DB;

    $mythtranscode->timemodified = time();
    $mythtranscode->id = $mythtranscode->instance;

    // Add the basename from the session to the record, if it's there.
    $basename = $_SESSION['basename'];
    if (isset($basename)) {
        $mythtranscode->basename = $basename;
    }

    return $DB->update_record('mythtranscode', $mythtranscode);
}

/**
 * Removes an instance of the mythtranscode from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function mythtranscode_delete_instance($id) {
    global $DB;

    if (! $mythtranscode = $DB->get_record('mythtranscode', array('id' => $id))) {
        return false;
    }

    $DB->delete_records('mythtranscode', array('id' => $mythtranscode->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function mythtranscode_user_outline($course, $user, $mod, $mythtranscode) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $mythtranscode the module instance record
 * @return void, is supposed to echp directly
 */
function mythtranscode_user_complete($course, $user, $mod, $mythtranscode) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in mythtranscode activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function mythtranscode_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false.
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link mythtranscode_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function mythtranscode_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see mythtranscode_get_recent_mod_activity()}
 * @return void
 */
function mythtranscode_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function mythtranscode_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function mythtranscode_get_extra_capabilities() {
    return array();
}
