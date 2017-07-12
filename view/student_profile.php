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
 * General Reports
 *
 * @author     Iader E. García G.
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/query.php');
require_once('../managers/lib/student_lib.php');
require_once('../managers/user_management/user_lib.php');
require_once('../managers/student_profile/geographic_lib.php');
require_once('../managers/dateValidator.php');
include('../lib.php');
global $PAGE;

include("../classes/output/student_profile_page.php");
include("../classes/output/renderer.php");

// Set up the page.
$title = "Ficha estudiante";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$student_id = optional_param('student_id', 0, PARAM_INT);

require_login($courseid, false);

// Set up the page.
if(!consultInstance($blockid)){
    header("Location: instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/student_profile.php",array('courseid' => $courseid, 'instanceid' => $blockid,'student_id'=>$student_id));

//set configura la navegacion

$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Reporte general',new moodle_url("/blocks/ases/view/index.php",array('courseid' => $courseid, 'instanceid' => $blockid)), null, 'block', $blockid);
$coursenode->add_node($blocknode);
$node = $blocknode->add('Ficha estudiante',new moodle_url("/blocks/ases/view/student_profile.php",array('courseid' => $courseid, 'instanceid' => $blockid,'student_id'=>$student_id)));
$blocknode->make_active();
$node->make_active();

//cargar info de la ficha

$record = 'data';
if ($student_id != 0){ 
    
    /**
     * Información para la cabecera de la ficha
     */
    $id_user_moodle = get_id_user_moodle($student_id);
    $user_moodle = get_moodle_user($id_user_moodle);
    $cohort = get_cohort_student($id_user_moodle);
    
    $ases_student = get_ases_user($student_id);
    
    $record = new stdClass;
    
    $array_aditional_fields = get_adds_fields_mi($id_user_moodle);
    $academic_program = get_program((int)$array_aditional_fields[1]->data);

    $faculty = get_faculty($academic_program->id_facultad);
    
    $record->id_user = $user_moodle->id;
    $record->firstname = $user_moodle->firstname;
    $record->lastname = $user_moodle->lastname;
    $record->email_moodle = $user_moodle->email_moodle;
    $record->code = $user_moodle->code;
    $record->age = substr($ases_student->age,0,2);
    $record->program = $academic_program->nombre;
    $record->faculty = $faculty->nombre;
    $record->cohort = $cohort->name;
    
    switch($ases_student->estado){
        case "ACTIVO":
            $record->icetex_status_active = "selected";
            break;
        case "APLAZADO":
            $record->icetex_status_postponed = "selected";
            break;
        case "EGRESADO":
            $record->icetex_status_graduate = "selected";
            break;
        case "RETIRADO":
            $record->icetex_status_retired = "selected";
            break;
        case "EMPTY":
            $record->icetex_status_empty = "selected";
            break;
    }

    switch($ases_student->estado_ases){
        case "ACTIVO":
            $record->ases_status_active = "selected";
            break;
        case "APLAZADO":
            $record->ases_status_postponed = "selected";
            break;
        case "EGRESADO":
            $record->ases_status_graduate = "selected";
            break;
        case "RETIRADO":
            $record->ases_status_retired = "selected";
            break;
        case "EMPTY":
            $record->ases_status_empty = "selected";
            break;
    }
    
    switch($ases_student->tipo_doc){
        case "T.I":
            $record->doc_type_ti = "selected";
            break;
        case "C.C":
            $record->doc_type_cc = "selected";
            break;
        case "C.R":
            $record->doc_type_cr = "selected";
            break;
        case "EMPTY":
            $record->doc_type_empty = "selected";
            break;
    }
    
    /**
     * Información para la ficha general
     */
    
    $record->res_address = $ases_student->direccion_res;
    $record->init_tel = $ases_student->tel_ini;
    $record->res_tel = $ases_student->tel_res;
    $record->cell_phone = $ases_student->celular;
    $record->emailpilos = $ases_student->emailpilos;
    $record->attendant = $ases_student->acudiente;
    $record->attendant_tel = $ases_student->tel_acudiente;
    $record->num_doc = $ases_student->num_doc;
    $record->observations = $ases_student->observacion;

    $monitor_object = get_assigned_monitor($student_id);
    $trainee_object = get_assigned_pract($student_id);
    $professional_object = get_assigned_professional($student_id);
    
    if($monitor_object){
        $record->monitor_fullname = "$monitor_object->firstname $monitor_object->lastname";
    }else{
        $record->monitor_fullname = "No registra";
    }
    
    if($trainee_object){
        $record->trainee_fullname = "$trainee_object->firstname $trainee_object->lastname";
    }else{
        $record->trainee_fullname = "No registra";
    }
    
    if($professional_object){
        $record->trainee_fullname = "$professional_object->firstname $professional_object->lastname";
    }else{
        $record->professional_fullname = "No registra";
    }
    
    /**
     * Información geográfica
     */
     
     $geographic_object = get_geographic_info($student_id);
     
     $record->latitude = $geographic_object->latitude;
     $record->longitude = $geographic_object->longitude;
     
     switch($geographic_object->risk){
        case 1:
            $record->geographic_class = 'div_low_risk';
            break;
        case 2:
            $record->geographic_class = 'div_medium_risk';
            break;
        case 3:
            $record->geographic_class = 'div_high_risk';
            break;
        default:
            $record->geographic_class = 'div_no_risk';
            break;
     }
    
    /**
     * Riesgos asociados al estudiante
     */
    
    $risk_object = get_risk_by_student($student_id); 
    
    switch($risk_object[familiar]->calificacion_riesgo){
        case 1:
            $record->familiar_class = 'div_low_risk';
            break;
        case 2:
            $record->familiar_class = 'div_medium_risk';
            break;
        case 3:
            $record->familiar_class = 'div_high_risk';
            break;
        default:
            $record->familiar_class = 'div_no_risk';
            break;
     }

    switch($risk_object[economico]->calificacion_riesgo){
        case 1:
            $record->economic_class = 'div_low_risk';
            break;
        case 2:
            $record->economic_class = 'div_medium_risk';
            break;
        case 3:
            $record->economic_class = 'div_high_risk';
            break;
        default:
            $record->economic_class = 'div_no_risk';
            break;
     }
     
     switch($risk_object[vida_universitaria]->calificacion_riesgo){
        case 1:
            $record->life_class = 'div_low_risk';
            break;
        case 2:
            $record->life_class = 'div_medium_risk';
            break;
        case 3:
            $record->life_class = 'div_high_risk';
            break;
        default:
            $record->life_class = 'div_no_risk';
            break;
     }
     
     switch($risk_object[academico]->calificacion_riesgo){
        case 1:
            $record->academic_class = 'div_low_risk';
            break;
        case 2:
            $record->academic_class = 'div_medium_risk';
            break;
        case 3:
            $record->academic_class = 'div_high_risk';
            break;
        default:
            $record->academic_class = 'div_no_risk';
            break;
     }
    
}

$PAGE->set_context($contextcourse);
$PAGE->set_context($contextblock);
$PAGE->set_url($url);
$PAGE->set_title($title);

// $PAGE->requires->js('/blocks/ases/js/edit_grades.js', true);

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
// $PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sugerenciaspilos.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);

// $PAGE->requires->js('/blocks/ases/js/jquery-2.2.4.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/jquery.dataTables.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/jquery.dataTables.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.jqueryui.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.bootstrap.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.bootstrap.js', true);
// $PAGE->requires->js('/blocks/ases/js/jquery.validate.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/bootstrap.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/bootstrap.js', true);
// $PAGE->requires->js('/blocks/ases/js/sweetalert2.js', true);
// $PAGE->requires->js('/blocks/ases/js/sweetalert-dev.js', true);

// $PAGE->requires->js('/blocks/ases/js/checkrole.js', true);
// $PAGE->requires->js('/blocks/ases/js/sugerenciaspilos.js', true);
// $PAGE->requires->js('/blocks/ases/js/attendance_profile.js', true);
// $PAGE->requires->js('/blocks/ases/js/main.js', true);
// $PAGE->requires->js('/blocks/ases/js/academic_profile.js', true);
// $PAGE->requires->js('/blocks/ases/js/update_profile.js', true);
// $PAGE->requires->js('/blocks/ases/js/talentos_profile.js', true);
// $PAGE->requires->js('/blocks/ases/js/search_profile.js', true);
// $PAGE->requires->js('/blocks/ases/js/d3.js', true);
// $PAGE->requires->js('/blocks/ases/js/d3.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/radarChart.js', true);

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
$student_profile_page = new \block_ases\output\student_profile_page($record);
echo $output->render($student_profile_page);
echo $output->footer();