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
 * Creación de usuarios extendidos
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');
require_once('../managers/validate_profile_action.php');

require_once(__DIR__ . '/../classes/AsesUserExtended.php');
require_once(__DIR__ . '/../classes/mdl_forms/ases_user_extended_form.php');
require_once(__DIR__.'/../managers/user_creation_process/user_creation_process_lib.php');

$pagetitle = 'Crear usuario extendido ASES';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$username = optional_param('username', null, PARAM_TEXT);
$continue = optional_param('continue', false, PARAM_BOOL);


require_login($courseid, false);
$actions = authenticate_user_view($USER->id, $blockid);
if ( !isset($actions->create_user_extended )) {
    redirect(new moodle_url('/'), "No tienes permiso para adicionar un usuario extendido ASES",1);
}
$mdl_user = null;

if( $username ){
    $mdl_user = $DB->get_record('user', array('username'=>$username));
}

if( $mdl_user ) {
    \core\notification::info("Editando actualmente al usuario $mdl_user->firstname $mdl_user->lastname");

}


$output = $PAGE->get_renderer('block_ases');
echo $output->header();

$user_extended_form = new ases_user_extended_form( new moodle_url(\user_creation_process\CREATE_UPDATE_USER_URL,
    array(
       'instanceid'=>$blockid,
       'courseid'=>$courseid,
       'username'=>$username,
        'continue'=>$continue
    )));

$user_extended_created = false;

if($data = $user_extended_form->get_data()) {
    $ases_user_extended = $user_extended_form->get_ases_user_extended();
    $user_extended_created = $ases_user_extended->save();
    if($user_extended_created){
        \core\notification::success('Se ha guardado el registro correctamente');
    }
}

if ( $continue && $user_extended_created ) {
    $url = \user_creation_process\generate_add_user_to_cohort_url($blockid, $courseid, null, true);
    \core\notification::info(html_writer::link($url, 'Para añadir otro usuario de click aquí'));
}

$user_extended_form->display();
echo $output->footer();


?>