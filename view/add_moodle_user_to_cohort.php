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

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/cohort/lib.php');

require_once('../managers/ases_report/asesreport_lib.php');
require_once('../managers/permissions_management/permissions_lib.php');
require_once("../managers/lib/cohort_lib.php");
require_once("../managers/lib/student_lib.php");
require_once('../classes/mdl_forms/add_user_to_cohort_form.php');


require_once('../managers/validate_profile_action.php');
require_once('../managers/menu_options.php');


include('../lib.php');



$ases_context = cohort_lib::get_ases_context();
$manage_cohorts_url = cohort_lib::get_context_management_url();
// Set up the page.
$pagetitle = 'Creacion de usuarios ASES';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$id_current_user = $USER->id;

$url = new moodle_url('/blocks/ases/view/add_moodle_user_to_cohort.php', array('courseid' => $courseid, 'instanceid' => $blockid));
require_login($courseid, false);
$add_user_to_cohort_form = new add_user_to_cohort_form($url);

//Form processing and displaying is done here
if ($add_user_to_cohort_form->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    
} 
$output = $PAGE->get_renderer('block_ases');
echo $output->header();

if ($add_user_to_cohort_form->is_submitted() ) {

    $errors = $add_user_to_cohort_form->get_errors();
    if($errors['username']) {
        $url_create_moodle_user = new moodle_url('/blocks/ases/view/moodle_user_creation.php', 
        array('courseid' => $courseid, 'instanceid' => $blockid, 'username'=>$add_user_to_cohort_form->get_submitted_data()->username));
    
        $add_user_link =  html_writer::tag('p',
            "El usuario no existe en la base de datos moodle.     " . html_writer::tag('a', "Puede añadirlo a al campus virtual en el siguiente enlace",
        array('href' => $url_create_moodle_user)));
        \core\notification::info($add_user_link);
    }

}
if ($data = $add_user_to_cohort_form->get_data()) {
    $mdl_user = $DB->get_record('user', array('username' => $data->username));
    cohort_add_member( $data->cohort, $mdl_user->id);
    echo \core\notification::success(" El usuario $mdl_user->firstname $mdl_user->lastname ha sido añadido a la cohorte que ha seleccionado");

  //In this case you process validated data. $mform->get_data() returns data posted in form.
} 
$add_user_to_cohort_form->display();
echo $output->footer();
?>