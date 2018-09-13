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
 * Paso a paso para la creacion de un usuario ases, la cual esta echa para ser una 
 * secuencia de acciones las cuales estan orientadas a validar los datos 
 * y por ultimo insertar el usuario en la base de datos
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
error_reporting(-1);
ini_set('display_errors', 'On');
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/ases_report/asesreport_lib.php');
require_once('../managers/permissions_management/permissions_lib.php');
require_once("../managers/lib/cohort_lib.php");
require_once("../managers/lib/student_lib.php");
require_once('../classes/mdl_forms/SearchMoodleUser.php');
require_once('../managers/validate_profile_action.php');
require_once('../managers/menu_options.php');
require_once("../classes/mdl_forms/UserImageForm.php");
include('../lib.php');
global $PAGE;

include("../classes/output/ases_user_creation_page.php");
include("../classes/output/renderer.php");

// Set up the page.
$pagetitle = 'Creacion de usuarios ASES';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$id_current_user = $USER->id;

// Instance is consulted for its registration
if(!consult_instance($blockid)){
    header("Location: instance_configuration.php?courseid=$courseid&instanceid=$blockid");
    die();
}

require_login($courseid, false);

/*
Control flow vars
*/

$user_exists = false;

/* */
$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/ases_user_creation.php",array('courseid' => $courseid, 'instanceid' => $blockid));
$search_moodle_user = new search_moodle_user($url);

/*
Start search moodle user step
*/
$edit_user_image = new user_image_edit_form();

//Form processing and displaying is done here
if ($search_moodle_user->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    
} else if ($data = $search_moodle_user->get_data()) {
    $user_exists = $DB->record_exists('user', array('username' => $data->username));
    
  //In this case you process validated data. $mform->get_data() returns data posted in form.
} 
//Form processing and displaying is done here
if ($edit_user_image->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    
} else if ($data = $edit_user_image->get_data()) {
    if(!$DB->record_exists('user', array('username' => $data->username))){
       
    }
    
  //In this case you process validated data. $mform->get_data() returns data posted in form.
} 
$PAGE->requires->css('/blocks/ases/style/ases_report_style.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/buttons.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);

$PAGE->requires->js_call_amd('block_ases/ases_report_main','init');
$PAGE->requires->js_call_amd('block_ases/ases_report_main','load_defaults_students', $params);
$PAGE->requires->js_call_amd('block_ases/fix_hidden_fieldset', 'init');
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$output = $PAGE->get_renderer('block_ases');
$ases_user_creation = new \block_ases\output\ases_user_creation($data);

//echo $output->standard_head_html(); 
echo $output->header();
if (!$user_exists) {
    echo html_writer::tag('h1', " El usuario no existe en la base de datos moodle");
} else {
    // If the user with code given have one ases user, return std object, if have more than one return an array of registries
    $ases_students = get_ases_users_by_mdl_username_prefix($search_moodle_user->get_data()->username);
    print_r($ases_students);
    echo html_writer::tag('h3', " El usuario moodle es valido");
    if (is_array($ases_students) && count($ases_students)>1) {
        echo html_writer::tag('h', "El usuario tiene mas de un registro en ASES");
    } else if (! $ases_students) {
        echo html_writer::tag('h', "El usuario no tiene registros en ASES");
    }
    echo html_writer::tag('h3', 
        html_writer::tag('a', "Puede añadirlo a la cohorte en el siguiente enlace",
        array('href' => $CFG->wwwroot+'https://campusvirtual.univalle.edu.co/moodle/cohort/index.php?contextid=934651')));
}
//echo $output->render($ases_user_creation);    
$search_moodle_user->display();
echo $output->footer();
