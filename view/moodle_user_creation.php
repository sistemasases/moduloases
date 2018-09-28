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
error_reporting(-1);
ini_set('display_errors', 'On');
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/ases_report/asesreport_lib.php');
require_once('../managers/permissions_management/permissions_lib.php');
require_once("../managers/lib/cohort_lib.php");
require_once("../managers/lib/student_lib.php");
require_once($CFG->dirroot.'/user/editlib.php');

require_once($CFG->dirroot.'/webservice/lib.php');

require_once('../managers/validate_profile_action.php');
require_once('../managers/menu_options.php');
require_once($CFG->dirroot.'/user/editadvanced_form.php');
include('../lib.php');
global $PAGE;

include("../classes/output/ases_user_creation_page.php");
include("../classes/output/renderer.php");

// Set up the page.
$pagetitle = 'Creacion de usuarios Moodle';
$courseid = required_param('courseid', PARAM_INT);
$user_name = optional_param('username', '', PARAM_TEXT);
$blockid = required_param('instanceid', PARAM_INT);
$url = new moodle_url("/blocks/ases/view/moodle_user_creation.php",array('courseid' => $courseid, 'instanceid' => $blockid));

$returnto = optional_param('returnto', $url, PARAM_TEXT);
$id_current_user = $USER->id;
$actions = authenticate_user_view($USER->id, $blockid);
if (!isset($actions->create_mdl_user)) {
    redirect(new moodle_url('/'), "No tienes permiso para acceder a la creación de usuarios moodle",1);
}
// Instance is consulted for its registration
if(!consult_instance($blockid)){
    header("Location: instance_configuration.php?courseid=$courseid&instanceid=$blockid");
}

require_login($courseid, false);


/* */
$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);


/*
Start search moodle user step
*/

/*
User standard

*/
$user = new stdClass();
$user->id = -1;
$user->auth = 'manual';
$user->confirmed = 1;
$user->deleted = 0;
$user->timezone = '99';
$user->username = $user_name;

$usercreated = false;

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


$usercontext = null;
$coursecontext = context_course::instance(SITEID);   // Course context.

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
$user->imagefile = $draftitemid;
$userform = new user_editadvanced_form(new moodle_url($PAGE->url, array('returnto' => $returnto)), array(
    'user' => $user));
//echo $output->standard_head_html(); 
echo $output->header();

if ($userform->is_cancelled()) {
    
} else if ($usernew = $userform->get_data()) {
    $usercreated = false;
    if (empty($usernew->auth)) {
        // User editing self.
        $authplugin = get_auth_plugin($user->auth);
        unset($usernew->auth); // Can not change/remove.
    } else {
        $authplugin = get_auth_plugin($usernew->auth);
    }
    $usernew = file_postupdate_standard_editor($usernew, 'description', $editoroptions, null, 'user', 'profile', null);

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
        $usercreated = true;
}
if ($usercreated) {
    html_writer::tag('h4', "Usuario creado con exito");
}


$userform->display();

echo $output->footer();
