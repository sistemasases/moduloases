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
 * Ases block
 * @author     Edgar Mauricio Ceron Florez
 * @package    block_ases
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/permissions_management/permissions_functions.php');
include('../lib.php');
global $PAGE;

include("../classes/output/create_action_page.php");
require_once('../managers/user_management/user_lib.php');
include("../classes/output/renderer.php");
//require_once('../managers/query.php');


// Set up the page.
$title = "Crear accion";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);


//Obtiene los perfiles.
$profiles = get_profiles();
$profiles_table= get_profiles_select($profiles,"profiles_prof");
$profiles_table_user= get_profiles_select($profiles,"profiles_user");

//obtiene acciones 
$actions = get_actions();
$actions_table = get_actions_select($actions);

//obtiene funcionalidades
$function = get_functions();
$functions_table = get_functions_select($function,"functions");

$general_table  = get_functions_actions();


//obtiene usuarios 
$courseusers = get_course_usersby_id($courseid);
$table_courseuseres='<select class="form-pilos" id="users">';
$table_courseuseres.='<option value=""> ---------------------------------------</option>';
foreach ($courseusers as $courseuser) {
    $table_courseuseres.='<option value="'.$courseuser->codigo.'">'.$courseuser->codigo.' - '.$courseuser->nombre.' '.$courseuser->apellido.'</option>';
}
$table_courseuseres.="</select>";



$data = 'data';    
$data = new stdClass;
$data->profiles_table =$profiles_table;
$data->actions_table = $actions_table;
$data->table_courseuseres=$table_courseuseres;
$data->profiles_table_user=$profiles_table_user;
$data->functions_table =$functions_table;
$data->general_table=$general_table;



$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);
$url = new moodle_url("/blocks/ases/view/create_action.php",array('courseid' => $courseid, 'instanceid' => $blockid));

//Configuracion de la navegacion
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Crear accion',$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();


$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/add_fields.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);



$PAGE->requires->js('/blocks/ases/js/npm.js', true);
$PAGE->requires->js('/blocks/ases/js/checkrole.js', true);
$PAGE->requires->js_call_amd('block_ases/permissionsmanagement_main','init');

//$PAGE->requires->js('/blocks/ases/js/create_action.js', true);

$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->set_heading($title);

$output = $PAGE->get_renderer('block_ases');

echo $output->header();

$permisos_rol_page = new \block_ases\output\create_action_page($data);
echo $output->render($permisos_rol_page);
echo $output->footer();