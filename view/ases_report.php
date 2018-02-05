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
 *
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/ases_report/asesreport_lib.php');
require_once('../managers/instance_management/instance_lib.php');
require_once('../managers/student_profile/studentprofile_lib.php');
require_once('../managers/permissions_management/permissions_lib.php');
require_once('../managers/validate_profile_action.php');
require_once('../managers/menu_options.php');

include('../lib.php');
global $PAGE;

include("../classes/output/ases_report_page.php");
include("../classes/output/renderer.php");

// Set up the page.
$pagetitle = 'Reporte general';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$id_current_user = $USER->id;


require_login($courseid, false);
$riesgos = get_riesgos();
$cohortes = get_cohortes();

//se crean los elementos del menu
$menu_option = create_menu_options($id_current_user, $blockid, $courseid);


$tabla_riesgos='';
$tabla_cohortes='';
foreach($riesgos as $riesgo){
    $tabla_riesgos.='<div class="checkbox"><input type="checkbox" name="risk_fields[]" id="'.$riesgo->id.'" value="'.$riesgo->id.'" /> '.$riesgo->descripcion.'</div>';}
    
$tabla_cohortes.='<option value="TODOS">TODOS</option>';
foreach ($cohortes as $cohorte) {
    $tabla_cohortes.='<option value="'.$cohorte->idnumber.'">'.$cohorte->name.'</option>';
}

$estados_ases = "";
$estados_ases = "<option value='TODOS'>TODOS</option>";
$ases_status_array = get_status_ases();

foreach($ases_status_array as $ases_status){
	$estados_ases .= "<option value='".$ases_status->id."'>".$ases_status->nombre."</option>";
}


// Crea una clase con la información que se llevará al template.
$data = new stdClass();

// Evalua si el rol del usuario tiene permisos en esta view.
$actions = authenticate_user_view($id_current_user, $blockid);

$USER->actions = $actions;

foreach($actions as $act){
    $data->$act = $act;
}

$data->menu = $menu_option;
$data->risks_checks = $tabla_riesgos;
$data->cohorts_checks = $tabla_cohortes;
$data->status_ases = $estados_ases;
$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/ases_report.php",array('courseid' => $courseid, 'instanceid' => $blockid));
//$url =  $CFG->wwwroot."/blocks/ases/view/index.php?courseid=".$courseid."&instanceid=".$blockid;

// Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Reporte general',$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);

// $blocknode->make_active();
// $node->make_active();

//Instance is consulted for its registration
if(!consult_instance($blockid)){
    header("Location: instance_configuration.php?courseid=$courseid&instanceid=$blockid");
}

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
// $PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.tableTools.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/NewCSSExport/buttons.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);


$PAGE->requires->js_call_amd('block_ases/ases_report_main','init');
//$PAGE->requires->js_call_amd('block_ases/ases_report_graphics','init');

$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$output = $PAGE->get_renderer('block_ases');
$ases_report_page = new \block_ases\output\ases_report_page($data);

//echo $output->standard_head_html(); 
echo $output->header();
echo $output->render($ases_report_page);
echo $output->footer();
