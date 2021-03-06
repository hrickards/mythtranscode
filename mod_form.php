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
 * The main mythtranscode configuration form, used for creating and configuring
 * an instance of the module in a course
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_mythtranscode
 * @subpackage mythtranscode
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

// Load the (optional) javascript for the form. It just updates the choose recording
// field when a recording is chosen.
$course_id = optional_param('course', 0, PARAM_INT); // Course_module ID, or
$id  = optional_param('update', 0, PARAM_INT);  // mythtranscode instance ID.
if ($course_id) {
    $base_data = array('course' => $course_id);
} elseif ($id) {
    $base_data = array('id' => $id);
} else {
    error('You must specify a course_id ID or an instance ID');
}
$change_link = html_writer::link(
    new moodle_url('/mod/mythtranscode/choose.php', $base_data),
    get_string('change_programme', 'mythtranscode'),
    array('target' => '_blank', 'class' => 'mythtranscode_link')
);
$jsdata = array('change' => $change_link);
$jsmodule = array(
    'name' => 'mod_mythtranscode_form',
    'fullpath' => '/mod/mythtranscode/mod_form.js',
    'requires' => array('base', 'cookie', 'node')
);
$PAGE->requires->js_init_call('M.mod_mythtranscode_form.init', $jsdata, false, $jsmodule);

/**
 * Module instance settings form
 *
 * @copyright  2013 Harry Rickards <hrickards@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_mythtranscode_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        $course_id = optional_param('course', 0, PARAM_INT); // Course_module ID, or
        $id  = optional_param('update', 0, PARAM_INT);  // mythtranscode instance ID.
        if ($course_id) {
            $base_data = array('course' => $course_id);
        } elseif ($id) {
            $base_data = array('id' => $id);
        } else {
            error('You must specify a course_id ID or an instance ID');
        }

        // Remove any existing chosen programmes, if an existing instance isn't being edited
        $update = optional_param('update', -1, PARAM_INT); // Course_module ID, or
        if (!$id and $update != 0) {
            unset($_SESSION['basename']);
        }

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('mythtranscodename', 'mythtranscode'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'mythtranscodename', 'mythtranscode');

        // Adding the standard "intro" and "introformat" fields.
        $this->add_intro_editor();

        // Link to choose a television recording
        $link = html_writer::link(
            new moodle_url('/mod/mythtranscode/choose.php', $base_data),
            get_string('choose_programme', 'mythtranscode'),
            array('target' => '_blank', 'class' => 'mythtranscode_link')
        );
        $mform->addElement('static', 'choose_recording', 'Programme',
            html_writer::tag('div', $link, array('id'=>'mythtranscode_choose_recording')));

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();
        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    /**
     * Validates the form (actually just checks a programme has been chosen)
     */
    public function validation($data, $files) {
        // Get any other standard errors
        $errors = parent::validation($data, $files);

        // If a programme has not been chosen and an instance is not being edited
        $course_id = optional_param('course', 0, PARAM_INT); // Course_module ID, or
        $id  = optional_param('update', 0, PARAM_INT);  // mythtranscode instance ID.
        if (!(isset($_SESSION['basename'])) and !$id) {
            $errors['choose_recording'] = get_string('basename_not_found', 'mythtranscode');
        }

        return $errors;
    }
}
