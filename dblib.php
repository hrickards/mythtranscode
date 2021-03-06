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
 * Internal library of database functions for module mythtranscode
 *
 * Contains all mythtranscode-specific database functions. These are for
 * accessing the mythtranscode and mythtv databases, not moodle's
 * database.
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Given a recorded.basename field, finds the corresponding record
 * in mythexport and returns mythexport.file, along with some other metadata
 *
 * @param string $basename
 * @return array[string]
 */
function mythtranscode_get_filename_metadata($basename) {
    global $CFG;

    // Connect to the database.
    $mysqli = new mysqli($CFG->mod_mythtranscode_host, $CFG->mod_mythtranscode_username,
        $CFG->mod_mythtranscode_password, $CFG->mod_mythtranscode_database,
        $CFG->mod_mythtranscode_port);

    // Retrive the record from recorded
    // For explanation of depth of error-reporting, see
    // http://stackoverflow.com/questions/2552545
    $stmt = $mysqli->prepare("SELECT title, description, starttime, chanid, seriesid FROM {$CFG->mod_mythtranscode_table} WHERE basename = ? LIMIT 1");
    if (false===$stmt) {
        print_error(get_string('prepare_error', 'mythtranscode'));
    }
    $rc = $stmt->bind_param('s', $basename);
    if (false===$rc) {
        print_error(get_string('bind_param_error', 'mythtranscode'));
    }
    $rc = $stmt->execute();
    if (false===$rc) {
        print_error(get_string('execute_error', 'mythtranscode'));
    }
    $rc = $stmt->bind_result($title, $description, $starttime, $chanid, $seriesid);
    if (false===$rc) {
        print_error(get_string('bind_result_error', 'mythtranscode'));
    }
    $rc = $stmt->fetch();
    if (false===$rc) {
        print_error(get_string('fetch_error', 'mythtranscode'));
    }
    $stmt->close();

    // Find the channel name.
    $stmt = $mysqli->prepare("SELECT name from {$CFG->mod_mythtranscode_channel_table} WHERE chanid = ? LIMIT 1");
    if (false===$stmt) {
        print_error(get_string('prepare_error', 'mythtranscode'));
    }
    $rc = $stmt->bind_param('d', $chanid);
    if (false===$rc) {
        print_error(get_string('bind_param_error', 'mythtranscode'));
    }
    $rc = $stmt->execute();
    if (false===$rc) {
        print_error(get_string('execute_error', 'mythtranscode'));
    }
    $rc = $stmt->bind_result($channel);
    if (false===$rc) {
        print_error(get_string('bind_result_error', 'mythtranscode'));
    }
    $rc = $stmt->fetch();
    if (false===$rc) {
        print_error(get_string('fetch_error', 'mythtranscode'));
    }
    $stmt->close();

    // If we don't have a channel, guess one from the seriesid
    if (!$channel) {
        if (strpos($seriesid, 'bbc')) {
            $channel = 'BBC';
        } else if (strpos($seriesid, 'itv')) {
            $channel = 'ITV';
        } else if (strpos($seriesid, '4')) {
            $channel = 'Channel Four';
        } else if (strpos($seriesid, 'five')) {
            $channel = 'Five';
        }
    }

    // Find the corresponding mythexport record by finding a record where the
    // title, description and starttime->airDate match exactly
    $stmt = $mysqli->prepare("SELECT file FROM {$CFG->mod_mythtranscode_encoded_table}
                              WHERE title = ? AND description = ? AND airDate = ? AND file IS NOT NULL
                              LIMIT 1");
    if (false===$stmt) {
        print_error(get_string('prepare_error', 'mythtranscode'));
    }
    $rc = $stmt->bind_param('sss', $title, $description, $starttime);
    if (false===$rc) {
        print_error(get_string('bind_param_error', 'mythtranscode'));
    }
    $rc = $stmt->execute();
    if (false===$rc) {
        print_error(get_string('execute_error', 'mythtranscode'));
    }
    $rc = $stmt->bind_result($filename);
    if (false===$rc) {
        print_error(get_string('bind_result_error', 'mythtranscode'));
    }
    $rc = $stmt->fetch();
    if (false===$rc) {
        print_error(get_string('fetch_error', 'mythtranscode'));
    }
    $stmt->close();

    return array($filename, $title, $starttime, $channel);
}

/**
 * Retrieves and returns the first NUM_MATCHING matching results, as well
 * as the total number of matching results (recorded shows).
 *
 * @param string $query
 * @return Array Retrieves and returns the first NUM_MATCHING matching results, as well
 * as the total number of matching results (recorded shows). Offsets by $start.
 *
 * @param string $query
 * @param int $start
 * @return array[string, int]
 */
function mythtranscode_retrieve_results($query, $start) {
    global $CFG;

    // Connect to the database.
    $mysqli = new mysqli($CFG->mod_mythtranscode_host, $CFG->mod_mythtranscode_username,
        $CFG->mod_mythtranscode_password, $CFG->mod_mythtranscode_database,
        $CFG->mod_mythtranscode_port);

    // Initialise a base SQL query, on which we'll add more. Using raw SQL
    // for easy natural language full-text search.
    $base_query = "FROM {$CFG->mod_mythtranscode_table}";

    // If we have a query, search for it using full-text search.
    if (!empty($query)) {
        $match_fields = mythtranscode_get_query_fields();

        // Make sure $query and $match_fields are safe.
        $query = $mysqli->real_escape_string($query);
        foreach ($match_fields as &$field) {
            $field = $mysqli->real_escape_string($field);
        }
        unset($field);

        // Give each field a weighting (higher is more important), in
        // descending powers of two.
        $weightings = array();
        foreach (array_reverse($match_fields) as $i => $field) {
            $weightings[$field] = pow(2, $i);
        }

        // Generate an IF and WHERE statement, corresponding to the format
        // described in the comment below.
        $if_statements = array();
        $where_statements = array();
        foreach ($match_fields as $field) {
            $if = "IF({$field} LIKE \"%{$query}%\", {$weightings[$field]}, 0)";
            $where = "{$field} LIKE \"%{$query}%\"";

            array_push($if_statements, $if);
            array_push($where_statements, $where);
        }

        /*
         * Looks a lot more complicated than it is. Produces statements of the form
         * , IF(
         *          title LIKE "%wonders of the solar system%", 4,
         *       IF(title LIKE "%wonders of the solar system%", 2, 0)
         *     )
         *     + IF( description LIKE "%wonders of the solar system%", 1, 0)
         *     AS weight
         * FROM recorded
         * WHERE (
         *     title LIKE "%wonders of the solar system%"
         *     OR  description LIKE "%wonders of the solar system%"
         * )
         * ORDER BY weight DESC
         * For more information, see http://stackoverflow.com/questions/6496866
         */
        $base_query = ",
                            IF(
                                   {$match_fields[0]} LIKE \"%{$query}%\", " . pow(2,(count($match_fields))) . ",
                                IF({$match_fields[0]} LIKE \"%{$query}%\", " . pow(2,(count($match_fields)-1)) . ", 0)
                              )
                              + " . implode("\n + ", array_slice($if_statements, 1)) . "
                            AS weight
                        FROM {$CFG->mod_mythtranscode_table}
                        WHERE (
                            " . implode("\n OR ", $where_statements) . "
                        )
                        ORDER BY weight DESC";
    } else {
        $base_query .= " ORDER BY starttime DESC";
    }

    // Retrieve a limited number of results. Note: string interpolation cannot
    // be done simply with constants.
    $results = $mysqli->query("SELECT * {$base_query}
                               LIMIT {$start}, " . $CFG->mod_mythtranscode_num_results);

    // Retrieve the total no. results.
    $count = $mysqli->query("SELECT COUNT(*) {$base_query}");

    // $results/$count is false if an error was encountered.
    if ($results == false or $count == false) {
        print_error(get_string('database_error', 'mythtranscode'));
        return array(false, false);
    } else if ($results->num_rows < 1) {
        return array(false, false);
    } else {
        // Return the count of *all* matching results.
        $total_count = $count->fetch_row();
        return array($results, $total_count[0]);
    }
}
