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
 *Backup reports
 *
 * @author     Juan Pablo Castro
 * @package    block_ases
 * @copyright  2018 Juan Pablo Castro <juan.castro.vasquez@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once ('../managers/validate_profile_action.php');
require_once ('../managers/menu_options.php');
require_once '../managers/user_management/user_lib.php';
require_once '../managers/discapacity_reports/discapacity_reports_lib.php';
require_once '../managers/instance_management/instance_lib.php';
require_once '../managers/dateValidator.php';
require_once '../managers/permissions_management/permissions_lib.php';


global $PAGE;

include("../classes/output/discapacity_reports_page.php");
include("../classes/output/renderer.php");
include '../lib.php';

// Variables for setup the page.
$title = "Reporte discapacidad e inclusión";
$pagetitle = $title;

$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
require_login($courseid, false);

// Set up the page.
if (!consult_instance($blockid)) {
    header("Location: instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock = context_block::instance($blockid);
// Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);
$url = new moodle_url("/blocks/ases/view/discapacity_reports.php", array('courseid' => $courseid, 'instanceid' => $blockid));


// Nav configuration
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Reporte discapacidad e inclusión', new moodle_url("/blocks/ases/view/discapacity_reports.php", array('courseid' => $courseid, 'instanceid' => $blockid)), null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);

//Creates a class with information that'll be send to template
$data = new stdClass;
$actions = authenticate_user_view($USER->id, $blockid);
$data = $actions;
$data->menu = $menu_option;

//Cantidad de estudiantes de la cohorte DISC2018B sin un registro de detalle de discapacidad
$data->cantidad_sin_detalle_disc = get_cant_sin_detalle();

//Listar estudiantes sin registro de detalle de discapacidad
$students_without_discapacity_data = '' ; 
if($data->cantidad_sin_detalle_disc == 0){
    $students_without_discapacity_data .= "No hay estudiantes sin detalle de discapacidad";
}else{
    $students_without_discapacity_data .= "<div class = 'row'> <div class = 'col-lg-4 col-md-4 col-sm-4 col-xs-4'><strong>Identificación</strong></div> ";
    $students_without_discapacity_data .= "<div class = 'col-lg-8 col-md-8 col-sm-8 col-xs-8'><strong>Nombre completo</strong></div>   </div>";
    $estudiantes_sin_datos_discapacidad = get_students_dd();
    foreach($estudiantes_sin_datos_discapacidad as $student){
        $name =  $student->firstname_student ." ". $student->lastname_student;
        $students_without_discapacity_data .= "<div class = 'row'> <div class = 'col-lg-4 col-md-4 col-sm-4 col-xs-4'>$student->num_doc</div>";
        $students_without_discapacity_data .= " <div class = 'col-lg-8 col-md-8 col-sm-8 col-xs-8'>$name</div> </div>";
    }
    
}

$data->students_without_discapacity_data = $students_without_discapacity_data;

//Cantidad de estudiantes de la cohorte DISC2018B sin un registro de datos económicos
$data->cantidad_sin_economics_data = get_cant_sin_economics_data();

//Listar estudiantes sin registro económico
$students_without_economics_data = '' ; 
if($data->cantidad_sin_economics_data == 0){
    $students_without_economics_data .= "No hay estudiantes sin datos económicos";
}else{
    $students_without_economics_data .= "<div class = 'row'> <div class = 'col-lg-4 col-md-4 col-sm-4 col-xs-4'><strong>Identificación</strong></div> ";
    $students_without_economics_data .= "<div class = 'col-lg-8 col-md-8 col-sm-8 col-xs-8'><strong>Nombre completo</strong></div>   </div>";
    $estudiantes_sin_datos_economicos = get_students_ed();
    foreach($estudiantes_sin_datos_economicos as $student){
        $name =  $student->firstname_student ." ". $student->lastname_student;
        $students_without_economics_data .= "<div class = 'row'> <div class = 'col-lg-4 col-md-4 col-sm-4 col-xs-4'>$student->num_doc</div>";
        $students_without_economics_data .= " <div class = 'col-lg-8 col-md-8 col-sm-8 col-xs-8'>$name</div> </div>";
    }
    
}

$data->students_without_economics_data = $students_without_economics_data;


//Cantidad de estudiantes de la cohorte DISC2018B sin un registro de datos de servicio de salud
$data->cantidad_sin_health_data = get_cant_sin_health_data();

//Listar estudiantes sin registro de servicio de salud
$students_without_health_data = '' ; 
if($data->cantidad_sin_health_data == 0){
    $students_without_health_data .= "No hay estudiantes sin datos económicos";
}else{
    $students_without_health_data .= "<div class = 'row'> <div class = 'col-lg-4 col-md-4 col-sm-4 col-xs-4'><strong>Identificación</strong></div> ";
    $students_without_health_data .= "<div class = 'col-lg-8 col-md-8 col-sm-8 col-xs-8'><strong>Nombre completo</strong></div>   </div>";
    $estudiantes_sin_datos_salud = get_students_hd();
    foreach($estudiantes_sin_datos_salud as $student){
        $name =  $student->firstname_student ." ". $student->lastname_student;
        $students_without_health_data .= "<div class = 'row'> <div class = 'col-lg-4 col-md-4 col-sm-4 col-xs-4'>$student->num_doc</div>";
        $students_without_health_data .= " <div class = 'col-lg-8 col-md-8 col-sm-8 col-xs-8'>$name</div> </div>";
    }
    
}

$data->students_without_health_data = $students_without_health_data;


$PAGE->requires->css('/blocks/ases/style/base_ases.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/discapacity_tab.css', true);
$PAGE->requires->css('/blocks/ases/style/creadorFormulario.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/beautify-json.css', true);

$paramReport = new stdClass();
$paramReport->table = $tableReport;
$PAGE->requires->js_call_amd('block_ases/discapacity_reports', 'init');


$output = $PAGE->get_renderer('block_ases');
$index_page = new \block_ases\output\discapacity_reports_page($data);

echo $output->header();
echo $output->render($index_page);
echo $output->footer();