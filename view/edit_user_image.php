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
 * General Reports
 *
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_once("$CFG->libdir/formslib.php");

require_once('../managers/instance_management/instance_lib.php');
require_once ('../managers/validate_profile_action.php');
require_once ('../managers/menu_options.php');
require_once('../classes/output/edit_user_image_page.php');
global $PAGE;

class user_edit_form_ extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
 
        $mform = $this->_form; // Don't forget the underscore! 
        $mform->addElement('text', 'email', get_string('email')); // Add elements to your form
        $mform->setType('email', PARAM_NOTAGS);                   //Set type of element
        $mform->setDefault('email', 'Please enter email');        //Default value

        $mform->addElement('filepicker', 'userfile', 'Nueva imágen de perfil'); // Add elements to your form
        $mform->setType('email', PARAM_NOTAGS);                   //Set type of element
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
 
include("../classes/output/upload_files_page.php");
include("../classes/output/renderer.php");
require_once('../managers/query.php');

// Set up the page.
$title     = "Edición de imágene de usuario";
$pagetitle = $title;
$courseid  = required_param('courseid', PARAM_INT);
$blockid   = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

$contextcourse = context_course::instance($courseid);
$contextblock  = context_block::instance($blockid);
$url           = new moodle_url("/blocks/ases/view/edit_user_image.php", array(
    'courseid' => $courseid,
    'instanceid' => $blockid
));

// Instance is consulted for its registration
if (!consult_instance($blockid)) {
    header("Location: instance_configuration.php?courseid=$courseid&instanceid=$blockid");
}

// Menu items are created

// Creates a class with information that'll be send to template
$data = 'data';
$data = new stdClass;


//Nav configuration
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$node       = $coursenode->add('Gestion de archivos', $url);
$node->make_active();

//Page set up

$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->set_heading($title);

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->js_call_amd('block_ases/uploaddata_main', 'init');

$output = $PAGE->get_renderer('block_ases');

echo $output->header();  
$user_edit_form = new user_edit_form_();
echo $user_edit_form->render();
echo $output->footer();
