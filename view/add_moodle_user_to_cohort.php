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
require_once("../managers/user_creation_process/user_creation_process_lib.php");


require_once('../managers/validate_profile_action.php');
require_once('../managers/menu_options.php');

include('../lib.php');
/**
 * View lib
 * @throws coding_exception
 */
$PAGE->requires->css('/blocks/ases/style/progress_bar_component.css');
function multiple_steps() {
    global $PAGE, $output;
    $PAGE->requires->js_call_amd('block_ases/progress_bar_component', 'init');
    $template_data = new stdClass();
    $template_data->items = \user_creation_process\get_steps(\user_creation_process\ADD_USER_TO_COHORT);
    $student_profile_page = new \block_ases\output\progress_bar_component($template_data);
    echo $output->render($student_profile_page);
}

/**
 * End view lib
 */


$pagetitle = 'Adición de usuarios ASES a las cohortes';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$username = optional_param('username', '', PARAM_TEXT);
$continue = optional_param('continue', false, PARAM_BOOL);
$user_created = optional_param('user_created', false, PARAM_BOOL);

require_login($courseid, false);
$actions = authenticate_user_view($USER->id, $blockid);
if (!isset($actions->add_moodle_user_to_cohort)) {
    redirect(new moodle_url('/'), "No tienes permiso para adicionar un usuario moodle a una cohorte ASES",1);
}

$ases_context = cohort_lib::get_ases_context();
$manage_cohorts_url = cohort_lib::get_context_management_url();
// Set up the page.
$id_current_user = $USER->id;

$url = new moodle_url('/blocks/ases/view/add_moodle_user_to_cohort.php',
    array(
        'courseid' => $courseid,
        'instanceid' => $blockid,
        'continue'=>$continue,
        'user_created'=>$user_created
    ));

$add_user_to_cohort_form = new add_user_to_cohort_form($url,  array('username'=>$username));

if ($add_user_to_cohort_form->is_cancelled()) {

    
} 
$output = $PAGE->get_renderer('block_ases');


$user = $DB->get_record('user', array('username' => $username));

if ($add_user_to_cohort_form->is_submitted() && !$add_user_to_cohort_form->is_validated() ) {

    $errors = $add_user_to_cohort_form->get_errors();
    $data = $add_user_to_cohort_form->get_submitted_data();

    if($errors['username']) {
        /* Si existe un error en username, pero esta bien escrito*/
        if(valid_moodle_username($data->username)) {
            $url_create_moodle_user = \user_creation_process\generate_create_moodle_user_url($blockid, $courseid, $data->username, 'true');

            $add_user_link = html_writer::tag('p',
                "El usuario no existe en la base de datos moodle.     " . html_writer::tag('a', "Puede añadirlo a al Campus Virtual en el siguiente enlace",
                    array('href' => $url_create_moodle_user)));
            /* Si el nombre de usuario moodle es correcto pero no existe, mostrar el enlace para añadirlo */

            \core\notification::info($add_user_link);
        }
    }

}
$usuario_aniadido = false;
/* Si el form es valido añadir el usuario dado a la cohorte seleccionada */
if ($data = $add_user_to_cohort_form->get_data()) {
    $mdl_user = $DB->get_record('user', array('username' => $data->username));
    cohort_add_member( $data->cohort, $mdl_user->id);
    echo \core\notification::success(" El usuario $mdl_user->firstname $mdl_user->lastname ha sido añadido a la cohorte que ha seleccionado");
    $usuario_aniadido = true;

}
if ( $user_created ) {
    \core\notification::success('Se ha creado el usuario moodle');
}
if( $continue && $usuario_aniadido ){

    $url = \user_creation_process\generate_create_ases_user_url($blockid, $courseid, $data->username,true);
    \core\notification::add(" El usuario $mdl_user->firstname $mdl_user->lastname ha sido añadido a la cohorte que ha seleccionado", \core\output\notification::NOTIFY_INFO);
    redirect($url, null, 3000000);
}

if ( $user ) {
    \core\notification::info("Editando actualmente al usuario $user->firstname $user->lastname");
}
echo $output->header();

if ( $continue ) {
    multiple_steps();
}


$add_user_to_cohort_form->display();
echo $output->footer();
?>