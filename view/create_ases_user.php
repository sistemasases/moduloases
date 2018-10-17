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
 * Creación de usuarios ASES
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');
require_once('../managers/validate_profile_action.php');

require_once(__DIR__ .'/../classes/mdl_forms/ases_user_form.php');
require_once(__DIR__.'/../managers/user_creation_process/user_creation_process_lib.php');



$courseid = required_param('courseid', PARAM_INT);
require_login($courseid, false);
$blockid = required_param('instanceid', PARAM_INT);
$username = optional_param('username', '', PARAM_TEXT);
$continue = optional_param('continue', false, PARAM_BOOL);
$output = $PAGE->get_renderer('block_ases');

$pagetitle = 'Creacion de usuarios ASES';
$mdl_user = null;

if ( $username ) {
    $mdl_user = $DB->get_record('user', array('username'=>$username));
}

if( $mdl_user ) {
    \core\notification::info("Editando actualmente al usuario $mdl_user->firstname $mdl_user->lastname");
}

$actions = authenticate_user_view($USER->id, $blockid);
if ( !isset($actions->create_ases_user) ) {
    redirect(new moodle_url('/'), "No tienes permiso para acceder a la creación de usuario ASES",1);
}
$url = new moodle_url("/blocks/ases/view/create_ases_user.php",
    array(
        'courseid' => $courseid,
        'instanceid' => $blockid,
        'continue'=>$continue,
        'username'=>$username));
$PAGE->set_title($pagetitle);
$add_ases_user_form = new ases_user_form($url);

echo $output->header();

$ases_user_created = false;
if ($add_ases_user_form->is_validated()) {
    $ases_user = $add_ases_user_form->get_ases_user();
    if($ases_user->valid()) {
        $ases_user->save();
        \core\notification::success("Se ha creado el usuario número de documento '$ases_user->num_doc'");
        $ases_user_created = true;
    } else {
        /* @var AsesError $error*/
        foreach($ases_user->get_errors() as $error) {
            \core\notification::error($error->message);
        }
    }
}

if ( $continue && $ases_user_created ) {
    $url = \user_creation_process\generate_create_ases_update_user_extended_url($blockid, $courseid, $username, $ases_user->num_doc, true);
    redirect($url);
}
$add_ases_user_form->display();
echo $output->footer();


?>