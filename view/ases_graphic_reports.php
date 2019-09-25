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
 * @author     Joan Manuel Tovar Guzmán
 * @package    block_ases
 * @copyright  2019 Joan M. Tovar <joan.tovar@correounivalle.edu.co>
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
require_once(__DIR__.'/../managers/cohort/cohort_lib.php');
include('../lib.php');
global $PAGE;

include("../classes/output/ases_graphic_reports_page.php");
include("../classes/output/renderer.php");

// Set up the page.
$pagetitle = 'Reporte general gráfico ';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$id_current_user = $USER->id;

// Instance is consulted for its registration
if(!consult_instance($blockid)){
    header("Location: instance_configuration.php?courseid=$courseid&instanceid=$blockid");
    die();
}

require_login($courseid, false);




$cohorts_select = \cohort_lib\get_html_cohorts_select($blockid);

//se crean los elementos del menu
$menu_option = create_menu_options($id_current_user, $blockid, $courseid);

$risks = get_riesgos();
$risks_table='';


// Carga de riesgos
foreach($risks as $risk){
    $risks_table.='<div class="checkbox"><input type="checkbox" name="risk_fields[]" id="'.$risk->id.'" value="'.$risk->id.'" /> '.$risk->descripcion.'</div>';}


// Crea una clase con la información que se llevará al template.
$data = new stdClass();

// Evalua si el rol del usuario tiene permisos en esta view.
$actions = authenticate_user_view($id_current_user, $blockid);
$USER->actions = $actions;

foreach($actions as $act){
    $data->$act = $act;
}

$data->menu = $menu_option;
$data->risks_checks = $risks_table;

$data->cohorts_checks = $cohorts_select;
//$data->status_ases = $estados_ases;
$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/ases_graphic_reports.php",array('courseid' => $courseid, 'instanceid' => $blockid));

// ---------------------------------------------
// Carga por defecto de estudiantes relacionados
// ---------------------------------------------
$data_to_table = get_default_ases_report($blockid);
$params = new stdClass();
$params->table = $data_to_table;

// ---------------------------------------------
// Carga información resumen
// ---------------------------------------------
$data->summary_spp_cohorts = get_summary_group_cohorts('SPP', $blockid);
$data->summary_spe_cohorts = get_summary_group_cohorts('SPE', $blockid);
$data->summary_oa_cohorts = get_summary_group_cohorts('Otros', $blockid);
$data->summary_3740_cohorts = get_summary_group_cohorts('3740', $blockid);

// Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Reporte general gráfico ',$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);

$PAGE->requires->css('/blocks/ases/style/base_ases.css', true);
$PAGE->requires->css('/blocks/ases/style/ases_graphic_reports_style.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/buttons.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);

$PAGE->requires->js_call_amd('block_ases/ases_graphic_reports','init');

$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$output = $PAGE->get_renderer('block_ases');
$ases_graphic_reports_page = new \block_ases\output\ases_graphic_reports_page($data);

//echo $output->standard_head_html(); 
echo $output->header();
echo $output->render($ases_graphic_reports_page);
echo $output->footer();
