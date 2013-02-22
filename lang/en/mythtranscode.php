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
 * English strings for mythtranscode
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Television Programmes';
$string['modulenameplural'] = 'Television Programmes';
$string['modulename_help'] = 'Allows access to recorded television programmes';
$string['mythtranscodefieldset'] = 'Television Programmes';
$string['mythtranscodename'] = 'Name';
$string['mythtranscodename_help'] = 'Name of the link to the television programme';
$string['mythtranscode'] = 'Mythtranscode';
$string['pluginadministration'] = 'mythtranscode administration';
$string['pluginname'] = 'Mythtranscode';

$string['must_specify_id'] = 'You must specify a course_module ID or an instance ID';
$string['heading'] = 'Television Programmes';
$string['no_results'] = 'Sorry, no results found.';
$string['search_button_text'] = 'Search';

$string['key_title'] = 'Title';
$string['key_description'] = 'Description';
$string['key_subtitle'] = 'Subtitle';
$string['key_category'] = 'Category';
$string['key_progstart'] = 'Air date';

$string['format_mp4'] = 'MP4';
$string['format_webm'] = 'WebM';
$string['format_ogg'] = 'Ogg';

$string['setting_host_title'] = 'Host';
$string['setting_host_description'] = 'Hostname of the mythtv/mythtranscode database';

$string['setting_port_title'] = 'Port';
$string['setting_port_description'] = 'Database port';

$string['setting_username_title'] = 'Username';
$string['setting_username_description'] = 'Database username';

$string['setting_password_title'] = 'Password';
$string['setting_password_description'] = 'Database password';

$string['setting_database_title'] = 'Database';
$string['setting_database_description'] = 'Database name';

$string['setting_table_title'] = 'Programmes table';
$string['setting_table_description'] = 'Database table with metadata about the programmes recorded. On a default installation of mythtv, just recorded.';

$string['setting_encoded_table_title'] = 'Encoded table';
$string['setting_encoded_table_description'] = 'Database table with filenames of the encoded programmes. On a default installation of mythtranscode, just mythexport.';

$string['setting_channel_table_title'] = 'Channel table';
$string['setting_channel_table_description'] = 'Database channel with channel information in. On a default installation of mythtv, just channel'.

$string['setting_num_results_title'] = 'Num. results';
$string['setting_num_results_description'] = 'Number of results to show per page';

$string['setting_keys_title'] = 'Shown fields';
$string['setting_keys_description'] = 'A comma-separated list of database fields to be shown in the list of results';

$string['setting_link_keys_title'] = 'Linked fields';
$string['setting_link_keys_description'] = 'A comma-separated list of database fields to be linked in the list of results';

$string['setting_formats_title'] = 'Video formats';
$string['setting_formats_description'] = 'Video formats the television recordings are stored in';

$string['setting_query_fields_title'] = 'Query fields';
$string['setting_query_fields_description'] = 'A comma-separated list of database fields to match searches against, in order of priority';

$string['setting_bold_fields_title'] = 'Bold fields';
$string['setting_bold_fields_description'] = 'A comma-separated list of database fields to make bold in the results';

$string['setting_base_path_title'] = 'Base path';
$string['setting_base_path_description'] = 'The base filesystem path under which all video recordings are located';

$string['setting_downloads_title'] = 'Downloads';
$string['setting_downloads_description'] = 'Enable links to download videos';

$string['setting_mp4extension_title'] = 'MP4 extension';
$string['setting_mp4extension_description'] = 'The file extension of videos in the mp4 format';
$string['extension_mp4'] = '.mp4';
$string['extension_m4v'] = '.m4v';

$string['file_not_found'] = 'File not found.';
$string['database_error'] = 'Error querying database. Please see your system administrator';
$string['prepare_error'] = 'Error querying database: prepare() failed. Please see your system administrator';
$string['bind_param_error'] = 'Error querying database: bind_param() failed. Please see your system administrator';
$string['bind_result_error'] = 'Error querying database: bind_result() failed. Please see your system administrator';
$string['execute_error'] = 'Error querying database: execute() failed. Please see your system administrator';
$string['fetch_error'] = 'Error querying database: fetch() failed. Please see your system administrator';

$string['copyright_string'] = 'This recording is to be used only for educational and non-commercial purposes under the terms of the ERA Licence.';
$string['unavailable_recording'] = 'Sorry, that recording is currently unavailable.';
$string['pick_another'] = 'Please choose another recording, using the back button of your browser to return to the previous page.';
$string['basename_not_found'] = 'Please select a programme and try again';
$string['no_index_page'] = 'This functionality is not available at the moment';

$string['close_window'] = 'Return to form';
