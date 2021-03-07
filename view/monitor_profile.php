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
 * Short description goes here
 *
 * @author     David S. Cortés
 * @package    block_ases
 * @copyright  2020 David S. Cortés david.cortes@correounivalle.edu.co
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_once(__DIR__ . '/../core/module_loader.php'); // Please don't remove
require_once('../managers/instance_management/instance_lib.php');
require_once('../managers/user_management/user_management_lib.php');
require_once('../managers/lib/lib.php');
require_once('../managers/lib/student_lib.php');
require_once('../managers/validate_profile_action.php');
require_once('../managers/menu_options.php');
require_once('../managers/monitor_assignments/monitor_assignments_lib.php');
require_once('../managers/monitor_profile/monitor_profile_lib.php');
require_once('../classes/AsesUser.php');
require_once('../classes/Sede.php');

include "../lib.php";
include "../classes/output/monitor_profile_page.php";
include "../classes/output/renderer.php";


module_loader('periods');

global $PAGE;
global $USER;

// Set up the page
$course_id = required_param('courseid', PARAM_INT);
$block_id = required_param('instanceid', PARAM_INT);
$monitor_code = (string)optional_param('monitor_code', 0, PARAM_TEXT);

require_login($course_id, false);

if (!consult_instance($block_id)) {
    header("Location: instanceconfiguration.php?courseid=$course_id&instanceid=$block_id");
}

$contextcourse = context_course::instance($course_id);
$contextblock = context_block::instance($block_id);
$id_current_user = $USER->id;


$rol = lib_get_rol_name_ases($id_current_user, $block_id);
$url = new moodle_url("/blocks/ases/view/monitor_profile.php", array('courseid' => $course_id, 'instanceid' => $block_id, 'monitor_code' => $monitor_code));

// Clase con la información que se llevará al template.
$data = new stdClass();
$actions = authenticate_user_view($USER->id, $block_id);
$data = $actions;

if ($rol == 'sistemas') {
    $data->not_sistemas = false;
} else {
    $user = (object) [
        'id' => $id_current_user,
        'fullname' => $USER->firstname . " " . $USER->lastname,
        'username' => $USER->username,
    ];
    $data->user_logged = $user;
}


$cohorts_select = \cohort_lib\get_html_cohorts_select($block_id);
$data->cohorts_select = $cohorts_select;

$menu_option = create_menu_options($id_current_user, $block_id, $course_id);
$data->menu = $menu_option;

// Navegación
$coursenode = $PAGE->navigation->find($course_id, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Pérfil del monitor', $url, null, 'block', $block_id);
$coursenode->add_node($blocknode);

if ($monitor_code != 0){
    // Recolección de la información básica del monitor.
    $monitor = search_user($monitor_code);
    $monitor_info = get_monitor($monitor->id);
    $data->id_moodle = $monitor->id;
    $data->email = $monitor->email;
    $data->fullname = $monitor->username . " " . $monitor->firstname . " " . $monitor->lastname;
    //$data->select = make_select_monitors(get_all_monitors($block_id), $data->fullname);
    $data->phone1 = $monitor_info->telefono1;
    $data->phone2 = $monitor_info->telefono2;
    $data->num_doc = $monitor_info->num_doc; 
    $data->pdf_cuenta_banco = $monitor_info->pdf_cuenta_banco;

    $data->pdf_acuerdo_conf = $monitor_info->pdf_acuerdo_conf;
    $data->pdf_doc = $monitor_info->pdf_doc;
    $data->pdf_d10 = $monitor_info->pdf_d10;
    $data->email_alter = $monitor_info->email_alternativo;
    $data->profile_image =  get_mon_HTML_profile_img($contextblock->id, $monitor->id );
    $data->select_periods = make_select_active_periods($monitor->id, $block_id);
    $data->jefe = "No registra";
    $data->jefe_pract = "No registra";

    $estado = monitor_is_active($monitor->id, $block_id);
    if ($estado) {
        $data->activo=true;
        $mon_pract = user_management_get_boss($monitor->id, $block_id, core_periods_get_current_period()->id); 
	    $jefe = user_management_get_boss($mon_pract->id, $block_id, core_periods_get_current_period()->id); 

        if (isset($jefe->id)) {
            $nombre = $jefe->firstname ." ". $jefe->lastname;
            $data->jefe = $nombre; 
            $data->jefe_pract = $mon_pract->firstname . " ". $mon_pract->lastname;
        } 

    } else {
        $data->inactivo=true;
    }
    
    // Nombre del plan actual
    $data->plan = "No registra programa académico";
    if ($monitor_info->id_programa > 0) {
        $program_obj = get_program($monitor_info->id_programa);
        $program_sede = Sede::get_one_by(array(Sede::ID=>$program_obj->id_sede)); 
        $data->plan = ($program_obj->nombre) . ' - ' . ($program_sede->nombre);
    } 

    // Verificar prefijo http
    foreach ($data as $key => $value) {
        if (substr($key,0, 3) === 'pdf') {
            if (preg_match('/(^http:\/\/)|/(^https:\/\/)', $value) == 0) {
                $data->$key = 'https://' . $value;
            }
        } 
    }
    
    switch ($rol) {
        
    case "sistemas":
        $data->select = make_select_monitors(get_all_monitors($block_id), $monitor );
        break;
    case "practicante_ps":
        $data->select = make_select_monitors( get_all_monitors_pract($block_id, $user->id), $monitor );
        break;
    case "profesional_ps":
        $data->select = make_select_monitors( get_all_monitors_prof($block_id, $user->id), $monitor );
        break;
    case "monitor_ps":
        //$data->select = make_select_monitors( get_monitor($user->id) );
        $monitor_code = $user->username;
    }


} else {
    switch ($rol) {
        
    case "sistemas":
        $data->select = make_select_monitors(get_all_monitors($block_id));
        break;
    case "practicante_ps":
        $data->select = make_select_monitors( get_all_monitors_pract($block_id, $user->id) );
        break;
    case "profesional_ps":
        $data->select = make_select_monitors( get_all_monitors_prof($block_id, $user->id) );
        break;
    case "monitor_ps":
        //$data->select = make_select_monitors( get_monitor($user->id) );
        $monitor_code = $user->username;
    }
    $monitor_code = -1;
}

$page_title = 'Pérfil del monitor';
$PAGE->set_url($url);
$PAGE->set_title($page_title);
$PAGE->set_heading($page_title);

$PAGE->requires->css('/blocks/ases/style/aaspect.min.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/monitor_profile.css', true);
$PAGE->requires->css('/blocks/ases/style/monitor_trackings_tab.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/style/switch.css', true);

$PAGE->requires->js_call_amd('block_ases/monitor_profile', 'init');

$OUTPUT = $PAGE->get_renderer('block_ases');


$monitor_profile_page = new \block_ases\output\monitor_profile_page($data);

echo $OUTPUT->header();
echo $OUTPUT->render($monitor_profile_page);
echo $OUTPUT->footer();
