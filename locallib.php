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
 * Internal library of functions for module mythtranscode
 *
 * Contains all mythtranscode-specific functions
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Require dblib.php --- equivalent to this file, but containing DB
// helper functions
require_once(dirname(__FILE__).'/dblib.php');

/**
 * Returns a URL for the video file containing a television show, given
 * the filename of a television show
 *
 * @param string $filename
 * @param string $format
 * @param string $paramstring --- non-video parameters to include in the URL
 * @return string
 */
function mythtranscode_create_video_url($filename, $format, $param_string) {
    global $CFG;

    $filename = urlencode(preg_replace('/\.(m4v|mp4|webm)$/', '', $filename));

    // MP4 format can be .mp4 or .m4v, so there's a setting for it
    if ($format == 'mp4') {
        $format = $CFG->mod_mythtranscode_mp4extension;
    }

    return "access_file.php?{$param_string}&filename={$filename}.{$format}";
}

/**
 * Returns the database fields which should be shown in the results list
 *
 * @return array[string]
 */
function mythtranscode_get_keys() {
    global $CFG;
    return explode(',', $CFG->mod_mythtranscode_keys);
}

/**
 * Returns the database fields which should be linked in the results list
 *
 * @return array[string]
 */
function mythtranscode_get_link_keys() {
    global $CFG;
    return explode(',', $CFG->mod_mythtranscode_link_keys);
}

/**
 * Returns the available video formats
 *
 * @return array[string]
 */
function mythtranscode_get_formats() {
    global $CFG;
    return explode(',', $CFG->mod_mythtranscode_formats);
}

/**
 * Takes in a MySQL-format date, and returns one in a user-friendly format
 *
 * @param string $date
 * @return string
 */
function mythtranscode_format_date($date) {
    $phpdate = strtotime($date);
    return date('d/m/y H:i', $phpdate);
}

/**
 * Return the database fields to query against
 *
 * @return array[string]
 */
function mythtranscode_get_query_fields() {
    global $CFG;
    return explode(',', $CFG->mod_mythtranscode_query_fields);
}

/**
 * Return the database fields to bolden
 *
 * @return array[string]
 */
function mythtranscode_get_bold_fields() {
    global $CFG;
    return explode(',', $CFG->mod_mythtranscode_bold_fields);
}

/**
 * Returns true iff the recording with the passed filename has transcodings present
 *
 * @param string $filename
 * @return bool
 */
function mythtranscode_recording_has_files($filename) {
    global $CFG;

    $filepath = "{$CFG->mod_mythtranscode_base_path}/{$filename}";
    return !is_null($filename) and file_exists($filepath);
}
