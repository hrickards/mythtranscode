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
$filename = required_param('filename', '', PARAM_CLEAN);

// Split the filename on dots (e.g., foo.bar.baz.qux.webm ->
// ['foo', 'bar', 'baz', 'qux', 'webm']).
$parts = explode('.', $filename);

// Take the last part as the file extension, and remove any non-alphanumeric
// characters.
$extension = preg_replace("/[^A-Za-z0-9]/", '', array_pop($parts));

// Take the second to last part as the filename, and remove any
// non-(alphanumeric|dash|underscore) characters.
$filename = preg_replace("/[^A-Za-z0-9_\-]/", '', array_pop($parts));

// The format is just the extension, or mp4 if the extension is m4v
if ($extension === 'm4v') {
    $format = 'mp4';
} else {
    $format = $extension;
}

// Read the file and return it, if it exists
$filepath = "{$CFG->mod_mythtranscode_base_path}/{$filename}.{$extension}";

// Following code from mobiforge (http://mobiforge.com/developing/story/content-delivery-mobile-devices#byte-ranges)
// and Thomas Thomassen (http://www.thomthom.net/blog/2007/09/php-resumable-download-server/)
if (is_file($filepath)) {
    // Set the content type. Guessing it like this from the file extension is a
    // bit of a hack, but works well enough for all of the HTML5 video types.
    header('Content-Type: video/' . $format);

    // Set the filename of the video to be downloaded; if we didn't do this it
    // would default to access_file.php in some browsers (firefox)
    header("Content-Disposition: attachment; filename=\"{$filename}.{$extension}\"");

    // If the browser has range download capability (very useful for seeking), use it.
    if (isset($_SERVER['HTTP_RANGE'])) {
        rangeDownload($filepath);
    } else {
        header("Content-Length: ".filesize($filepath));
        echo readfile($filepath);
    }
} else {
    // If the file can't be found, display a 404
    header('HTTP/1.0 404 Not Found');
    echo '<h1>404 Not Found</h1>';
    echo 'The page that you have requested could not be found.';
}


/**
 * Code from  Thomas Thomassen (http://www.thomthom.net/blog/2007/09/php-resumable-download-server/) to
 * stream a file to an Apple device)
 *
 * @param $file --- the file path
 */
function rangeDownload($file) {

    $fp = @fopen($file, 'rb');

    $size   = filesize($file); // File size
    $length = $size;           // Content length
    $start  = 0;               // Start byte
    $end    = $size - 1;       // End byte
    // Now that we've gotten so far without errors we send the accept range header
    /* At the moment we only support single ranges.
     * Multiple ranges requires some more work to ensure it works correctly
     * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
     *
     * Multirange support annouces itself with:
     * header('Accept-Ranges: bytes');
     *
     * Multirange content must be sent with multipart/byteranges mediatype,
     * (mediatype = mimetype)
     * as well as a boundry header to indicate the various chunks of data.
     */
    header("Accept-Ranges: 0-$length");
    // header('Accept-Ranges: bytes');
    // multipart/byteranges
    // http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
    if (isset($_SERVER['HTTP_RANGE'])) {

        $c_start = $start;
        $c_end   = $end;
        // Extract the range string
        list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
        // Make sure the client hasn't sent us a multibyte range
        if (strpos($range, ',') !== false) {

            // (?) Shoud this be issued here, or should the first
            // range be used? Or should the header be ignored and
            // we output the whole content?
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            // (?) Echo some info to the client?
            exit;
        }
        // If the range starts with an '-' we start from the beginning
        // If not, we forward the file pointer
        // And make sure to get the end byte if spesified
        if ($range == '-') {

            // The n-number of the last bytes is requested
            $c_start = $size - substr($range, 1);
        }
        else {

            $range  = explode('-', $range);
            $c_start = $range[0];
            $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
        }
        /* Check the range and make sure it's treated according to the specs.
         * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
         */
        // End bytes can not be larger than $end.
        $c_end = ($c_end > $end) ? $end : $c_end;
        // Validate the requested range and return an error if it's not correct.
        if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {

            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            // (?) Echo some info to the client?
            exit;
        }
        $start  = $c_start;
        $end    = $c_end;
        $length = $end - $start + 1; // Calculate new content length
        fseek($fp, $start);
        header('HTTP/1.1 206 Partial Content');
    }
    // Notify the client the byte range we'll be outputting
    header("Content-Range: bytes $start-$end/$size");
    header("Content-Length: $length");

    // Start buffered download
    $buffer = 1024 * 8;
    while(!feof($fp) && ($p = ftell($fp)) <= $end) {

        if ($p + $buffer > $end) {

            // In case we're only outputtin a chunk, make sure we don't
            // read past the length
            $buffer = $end - $p + 1;
        }
        set_time_limit(0); // Reset time limit for big files
        echo fread($fp, $buffer);
        flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
    }

    fclose($fp);

}
