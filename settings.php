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
 * Code for a settings page
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 UCTC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

$name = 'mod_mythtranscode_host';
$title = get_string('setting_host_title', 'mythtranscode');
$description = get_string('setting_host_description', 'mythtranscode');
$default = 'localhost';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_CLEAN, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_port';
$title = get_string('setting_port_title', 'mythtranscode');
$description = get_string('setting_port_description', 'mythtranscode');
$default = '3306';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_CLEAN, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_username';
$title = get_string('setting_username_title', 'mythtranscode');
$description = get_string('setting_username_description', 'mythtranscode');
$default = 'mythtranscode';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_CLEAN, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_password';
$title = get_string('setting_password_title', 'mythtranscode');
$description = get_string('setting_password_description', 'mythtranscode');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_CLEAN, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_database';
$title = get_string('setting_database_title', 'mythtranscode');
$description = get_string('setting_database_description', 'mythtranscode');
$default = 'mythtranscode';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_CLEAN, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_table';
$title = get_string('setting_table_title', 'mythtranscode');
$description = get_string('setting_table_description', 'mythtranscode');
$default = 'recorded';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_CLEAN, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_num_results';
$title = get_string('setting_num_results_title', 'mythtranscode');
$description = get_string('setting_num_results_description', 'mythtranscode');
$default = 10;
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_keys';
$title = get_string('setting_keys_title', 'mythtranscode');
$description = get_string('setting_keys_description', 'mythtranscode');
$default = 'title,subtitle,description,category,progstart';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_CLEAN, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_link_keys';
$title = get_string('setting_link_keys_title', 'mythtranscode');
$description = get_string('setting_link_keys_description', 'mythtranscode');
$default = 'title';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_CLEAN, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_formats';
$title = get_string('setting_formats_title', 'mythtranscode');
$description = get_string('setting_formats_description', 'mythtranscode');
$defaultsetting = array('webm'=>get_string('format_webm', 'mythtranscode'));
$choices = array(
    'ogg'=>get_string('format_ogg', 'mythtranscode'),
    'webm'=>get_string('format_webm', 'mythtranscode'),
    'mp4'=>get_string('format_mp4', 'mythtranscode')
);
$setting = new admin_setting_configmulticheckbox($name, $title, $description, $defaultsetting, $choices);
$settings->add($setting);

$name = 'mod_mythtranscode_query_fields';
$title = get_string('setting_query_fields_title', 'mythtranscode');
$description = get_string('setting_query_fields_description', 'mythtranscode');
$default = 'title,description,subtitle,category';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_CLEAN, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_bold_fields';
$title = get_string('setting_bold_fields_title', 'mythtranscode');
$description = get_string('setting_bold_fields_description', 'mythtranscode');
$default = 'title';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_CLEAN, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_base_path';
$title = get_string('setting_base_path_title', 'mythtranscode');
$description = get_string('setting_base_path_description', 'mythtranscode');
$default = '/var/tv/recordings';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_CLEAN, 12);
$settings->add($setting);

$name = 'mod_mythtranscode_downloads';
$title = get_string('setting_downloads_title', 'mythtranscode');
$description = get_string('setting_downloads_description', 'mythtranscode');
$default = '1';
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$settings->add($setting);
