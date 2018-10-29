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
 * Creación de usuarios de moodle desde el bloque ases
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

require_once('../managers/ases_report/asesreport_lib.php');
require_once('../managers/permissions_management/permissions_lib.php');
require_once("../managers/lib/cohort_lib.php");
require_once("../managers/lib/student_lib.php");
require_once("../managers/user_creation_process/user_creation_process_lib.php");

require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/webservice/lib.php');
require_once($CFG->libdir.'/formslib.php');

require_once('../managers/validate_profile_action.php');
require_once('../managers/menu_options.php');
include('../lib.php');

require_once(__DIR__.'/../classes/mdl_forms/moodle_user_creation_ases_version.php');



global $PAGE;

// Set up the page.

$pagetitle = 'Creacion de usuarios Moodle';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$continue = optional_param('continue', false, PARAM_BOOL);

$user_name = optional_param('username', '', PARAM_TEXT);
$url = new moodle_url("/blocks/ases/view/moodle_user_creation.php",array('courseid' => $courseid, 'instanceid' => $blockid, 'continue'=>$continue));


$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$returnto = optional_param('returnto', $url, PARAM_TEXT);
$id_current_user = $USER->id;
$actions = authenticate_user_view($USER->id, $blockid);
require_login($courseid, false);

if (!isset($actions->create_mdl_user)) {
    redirect(new moodle_url('/'), "No tienes permiso para acceder a la creación de usuarios moodle",1);
}
// Instance is consulted for its registration
if(!consult_instance($blockid)){
    header("Location: instance_configuration.php?courseid=$courseid&instanceid=$blockid");
}
$PAGE->requires->css('/blocks/ases/style/progress_bar_component.css');

/**
 * View lib
 * @throws coding_exception
 */
/**
 * If moodle user creation actually is part of all ASES user creation procces, we need
 * progress_bar component
 * @throws coding_exception
 */
function multiple_steps() {
    global $PAGE, $output;
    $PAGE->requires->js_call_amd('block_ases/progress_bar_component', 'init');
    $template_data = new stdClass();
    $template_data->items = \user_creation_process\get_steps(\user_creation_process\CREATE_MOODLE_USER);
    $student_profile_page = new \block_ases\output\progress_bar_component($template_data);
    echo $output->render($student_profile_page);
}

/**
 * Because we are using the moodle functions for create users and algo a form than inherit of moodle user form
 * we need have prepare with anticipation an user to create the form,
 * @see moodle/user/edit.php
 * @return stdClass
 * @throws dml_exception
 */
function prepare_user(): stdClass {
    global $user_name;
    $user = new stdClass();
    $user->id = -1;
    $user->auth = 'manual';
    $user->confirmed = 1;
    $user->deleted = 0;
    $user->timezone = '99';
    $user->username = $user_name;
// Load user preferences.
    useredit_load_preferences($user);
// Load custom profile fields data.
    profile_load_data($user);
// User interests.
    $user->interests = core_tag_tag::get_item_tags_array('core', 'user', $id);

    $coursecontext = context_system::instance();   // SYSTEM context.
// This is a new user, we don't want to add files here.
    $editoroptions = array(
        'maxfiles' => 0,
        'maxbytes' => 0,
        'trusttext' => false,
        'forcehttps' => false,
        'context' => $coursecontext
    );

    $filemanageroptions = array('maxbytes'       => $CFG->maxbytes,
        'subdirs'        => 0,
        'maxfiles'       => 1,
        'accepted_types' => 'web_image');
    $filemanagercontext = $editoroptions['context'];
    file_prepare_draft_area($draftitemid, $filemanagercontext->id, 'user', 'newicon', 0, $filemanageroptions);
    $draftitemid = 0;
    $user->imagefile = $draftitemid;
    return $user;
}
/**
 * Because we are using the moodle functions for create users and algo a form than inherit of moodle user form
 * we need have post proccess user arrive from form before save this
 * @see moodle/user/edit.php
 * @return stdClass
 * @throws dml_exception
 */
function post_process_user( $usernew ) {
    $coursecontext = context_system::instance();   // SYSTEM context.
// This is a new user, we don't want to add files here.
    $editoroptions = array(
        'maxfiles' => 0,
        'maxbytes' => 0,
        'trusttext' => false,
        'forcehttps' => false,
        'context' => $coursecontext
    );
    $usernew = file_postupdate_standard_editor($usernew, 'description', $editoroptions, null, 'user', 'profile', null);
    return $usernew;
}

/**
 * End view lib
 */

/* */
$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);


/*
Start search moodle user step
*/



$usercreated = false;



$output = $PAGE->get_renderer('block_ases');

$user = prepare_user();
$userform = new moodle_user_creation_ases_version(new moodle_url($PAGE->url, array('returnto' => $returnto)), array(
    'user' => $user));
echo $output->header();

if ($userform->is_cancelled()) {

} else if ($usernew = $userform->get_data()) {
    $usernew = post_process_user($usernew);
    if (empty($usernew->auth)) {
        // User editing self.
        $authplugin = get_auth_plugin($user->auth);
        unset($usernew->auth); // Can not change/remove.
    } else {
        $authplugin = get_auth_plugin($usernew->auth);
    }


    unset($usernew->id);
    $createpassword = !empty($usernew->createpassword);
    unset($usernew->createpassword);
    $usernew->mnethostid = $CFG->mnet_localhost_id; // Always local user.
    $usernew->confirmed  = 1;
    $usernew->timecreated = time();
    if ($authplugin->is_internal()) {
        if ($createpassword or empty($usernew->newpassword)) {
            $usernew->password = '';
        } else {
            $usernew->password = hash_internal_user_password($usernew->newpassword);
        }
    } else {
        $usernew->password = AUTH_PASSWORD_NOT_CACHED;
    }
    $usernew->id = user_create_user($usernew, false, false);
    if (!$authplugin->is_internal() and $authplugin->can_change_password() and !empty($usernew->newpassword)) {
        if (!$authplugin->user_update_password($usernew, $usernew->newpassword)) {
            // Do not stop here, we need to finish user creation.
            debugging(get_string('cannotupdatepasswordonextauth', '', '', $usernew->auth), DEBUG_NONE);
        }
    }
    if ( $usernew->id ) {
        $usercreated = true;
    }
} elseif ($userform->is_submitted() && !$userform->is_validated()){
    \core\notification::error('Se han encontrado errores al procesar');
}

if ( $continue && $usercreated) {
    $url = \user_creation_process\generate_add_user_to_cohort_url($blockid, $courseid, $usernew->username, true, true);
    redirect($url);
}
if ($usercreated) {
    \core\notification::success("Usuario creado con exito");
}


if ( $continue ) {
    multiple_steps();
}


$userform->display();


echo $output->footer();
echo
    /** @lang  HTML */
<<<TAG

<script>

/*For some reason, the button fieldset is hidden by default, this code fix this*/
            $(document).ready(function(){
                $('fieldset').removeClass('hidden');
            });
 </script>

TAG;
