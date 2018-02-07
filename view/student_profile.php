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
 * ASES
 *
 * @author     Iader E. García G.
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once __DIR__ . '/../../../config.php';
require_once $CFG->libdir . '/adminlib.php';
//require_once('../managers/query.php');

require_once '../managers/lib/student_lib.php';
require_once '../managers/lib/lib.php';
require_once '../managers/user_management/user_lib.php';
require_once '../managers/student_profile/geographic_lib.php';
require_once '../managers/student_profile/studentprofile_lib.php';
require_once '../managers/student_profile/academic_lib.php';
require_once '../managers/student_profile/student_graphic_dimension_risk.php';
require_once '../managers/instance_management/instance_lib.php';
require_once '../managers/dateValidator.php';
require_once '../managers/permissions_management/permissions_lib.php';
require_once '../managers/validate_profile_action.php';
require_once '../managers/menu_options.php';
require_once '../managers/dphpforms/dphpforms_forms_core.php';
require_once '../managers/dphpforms/dphpforms_records_finder.php';
require_once '../managers/dphpforms/dphpforms_get_record.php';
include '../lib.php';

global $PAGE;
global $USER;

include "../classes/output/student_profile_page.php";
include "../classes/output/renderer.php";

// Set up the page.
$title = "Ficha estudiante";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$student_code = optional_param('student_code', 0, PARAM_INT);

require_login($courseid, false);

// Set up the page.
if (!consult_instance($blockid)) {
    header("Location: instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock = context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/student_profile.php", array('courseid' => $courseid, 'instanceid' => $blockid, 'student_code' => $student_code));

// Nav configuration

$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Reporte general', new moodle_url("/blocks/ases/view/ases_report.php", array('courseid' => $courseid, 'instanceid' => $blockid)), null, 'block', $blockid);
$coursenode->add_node($blocknode);
$node = $blocknode->add('Ficha estudiante', new moodle_url("/blocks/ases/view/student_profile.php", array('courseid' => $courseid, 'instanceid' => $blockid, 'student_code' => $student_code)));
$blocknode->make_active();
$node->make_active();

// Load information of student's file
// Initialize context variable
$record = new stdClass;
$actions = authenticate_user_view($USER->id, $blockid);
$record = $actions;

$rol = get_role_ases($USER->id);

if ($student_code != 0) {

    $ases_student = get_ases_user_by_code($student_code);

    $student_id = $ases_student->id;

    $student_status_ases = get_student_ases_status($student_id);
    $student_status_icetex = get_student_icetex_status($student_id);

    // Loading available states

    $ases_status_array = get_status_ases();
    $icetex_status_array = get_status_icetex();

    $html_status_ases = "<option value=''>NO REGISTRA</option>";

    foreach ($ases_status_array as $ases_status) {

        if ($ases_status->nombre == $student_status_ases->nombre) {
            $html_status_ases .= "<option value='" . $ases_status->id . "' selected>" . $ases_status->nombre . "</option>";
        } else {
            $html_status_ases .= "<option value='" . $ases_status->id . "'>" . $ases_status->nombre . "</option>";
        }
    }

    $html_status_icetex = "<option value=''>NO REGISTRA</option>";

    foreach ($icetex_status_array as $icetex_status) {
        if ($icetex_status->nombre == $student_status_icetex->nombre) {
            $html_status_icetex .= "<option value='" . $icetex_status->id . "' selected>" . $icetex_status->nombre . "</option>";
        } else {
            $html_status_icetex .= "<option value='" . $icetex_status->id . "'>" . $icetex_status->nombre . "</option>";
        }
    }

    $record->status_ases = $html_status_ases;
    $record->status_icetex = $html_status_icetex;

    // Student information to display on file header (ficha)

    $id_user_moodle = get_id_user_moodle($ases_student->id);

    $user_moodle = get_moodle_user($id_user_moodle);

    $cohort = get_cohort_student($id_user_moodle);

    $array_aditional_fields = get_adds_fields_mi($id_user_moodle);

    $academic_program = get_program((int) $array_aditional_fields->idprograma);

    $faculty = get_faculty($academic_program->id_facultad);

    // Evaluates if user role has permissions assigned on this view

    $record->id_moodle = $id_user_moodle;
    $record->id_ases = $student_id;
    $record->firstname = $user_moodle->firstname;
    $record->lastname = $user_moodle->lastname;
    $record->email_moodle = $user_moodle->email_moodle;
    $record->age = substr($ases_student->age, 0, 2);
    $record->program = $academic_program->nombre;
    $record->faculty = $faculty->nombre;
    $record->cohort = $cohort->name;

    switch ($ases_student->tipo_doc) {
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

    // General file (ficha general) information

    $record->res_address = $ases_student->direccion_res;
    $record->init_tel = $ases_student->tel_ini;
    $record->res_tel = $ases_student->tel_res;
    $record->cell_phone = $ases_student->celular;
    $record->emailpilos = $ases_student->emailpilos;
    $record->attendant = $ases_student->acudiente;
    $record->attendant_tel = $ases_student->tel_acudiente;
    $record->num_doc = $ases_student->num_doc;
    $record->observations = $ases_student->observacion;

    $monitor_object = new stdClass();
    $trainee_object = new stdClass();
    $professional_object = new stdClass();

    $record->id_dphpforms_creado_por = $USER->id;

    if (get_assigned_monitor($student_id)) {
        $monitor_object = get_assigned_monitor($student_id);
    }
    if (get_assigned_pract($student_id)) {
        $trainee_object = get_assigned_pract($student_id);
    }
    if (get_assigned_professional($student_id)) {
        $professional_object = get_assigned_professional($student_id);
    }

    if ($monitor_object) {
        $record->monitor_fullname = "$monitor_object->firstname $monitor_object->lastname";
        $record->id_dphpforms_monitor = '-1';
    } else {
        $record->monitor_fullname = "NO REGISTRA";
    }

    if ($trainee_object) {
        $record->trainee_fullname = "$trainee_object->firstname $trainee_object->lastname";
    } else {
        $record->trainee_fullname = "NO REGISTRA";
    }

    if ($professional_object) {
        $record->professional_fullname = "$professional_object->firstname $professional_object->lastname";
    } else {
        $record->professional_fullname = "NO REGISTRA";
    }

    // Geographic information

    $geographic_tab_html = file_get_contents('../templates/geographic_tab.html');
    $record->geographic_tab = $geographic_tab_html;

    $geographic_object = load_geographic_info($student_id);

    $neighborhoods_array = get_neighborhoods();

    $neighborhoods = "<option>No registra</option>";

    for ($i = 1; $i <= count($neighborhoods_array); $i++) {
        if ($neighborhoods_array[$i]->id == (int) $geographic_object->barrio) {
            $neighborhoods .= "<option value='" . $neighborhoods_array[$i]->id . "' selected>" . $neighborhoods_array[$i]->nombre . "</option>";
        } else {
            $neighborhoods .= "<option value='" . $neighborhoods_array[$i]->id . "'>" . $neighborhoods_array[$i]->nombre . "</option>";
        }
    }

    $level_risk_array = array('Bajo', 'Medio', 'Alto');

    $select_geographic_risk = "<option>No registra</option>";
    for ($i = 0; $i < 3; $i++) {
        $value = $i + 1;
        if ($i + 1 == (int) $geographic_object->risk) {
            $select_geographic_risk .= "<option value='$value' selected>" . $level_risk_array[$i] . "</option>";
        } else {
            $select_geographic_risk .= "<option value='$value'>" . $level_risk_array[$i] . "</option>";
        }
    }

    $record->select_geographic_risk = $select_geographic_risk;

    $record->select_neighborhoods = $neighborhoods;

    $geographic_object = get_geographic_info($student_id);

    $record->latitude = $geographic_object->latitude;
    $record->longitude = $geographic_object->longitude;

    switch ($geographic_object->risk) {
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

    // Students risks

    $risk_object = get_risk_by_student($student_id);

    $record->individual_risk = $risk_object['individual']->calificacion_riesgo;
    $record->familiar_risk = $risk_object['familiar']->calificacion_riesgo;
    $record->academic_risk = $risk_object['academico']->calificacion_riesgo;
    $record->life_risk = $risk_object['vida_universitaria']->calificacion_riesgo;
    $record->economic_risk = $risk_object['economico']->calificacion_riesgo;

    switch ($risk_object['individual']->calificacion_riesgo) {
        case 1:
            $record->individual_class = 'div_low_risk';
            break;
        case 2:
            $record->individual_class = 'div_medium_risk';
            break;
        case 3:
            $record->individual_class = 'div_high_risk';
            break;
        default:
            $record->individual_class = 'div_no_risk';
            break;
    }

    switch ($risk_object['familiar']->calificacion_riesgo) {
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

    switch ($risk_object['economico']->calificacion_riesgo) {
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

    switch ($risk_object['vida_universitaria']->calificacion_riesgo) {
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

    switch ($risk_object['academico']->calificacion_riesgo) {
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

    if ($rol == 'sistemas') {
        $record->code = "<input type='text' class='tip' id='codigo' value='$student_code' size='12' maxlength='12' required>";
    } else {
        $select = make_select_ficha($USER->id);
        $record->code = $select;
    }

    // Loading academic information

    //Current data
    //weighted average
    $promedio = get_promedio_ponderado($student_id, $academic_program->id);
    $record->promedio = $promedio;

    //num bajos
    $bajos = get_bajos_rendimientos($student_id, $academic_program->id);
    $record->bajos = $bajos;

    // //num estimulos
    $estimulos = get_estimulos($student_id, $academic_program->id);
    $record->estimulos = $estimulos;

    //Current semester
    $html_academic_table = get_grades_courses_student_last_semester($id_user_moodle);
    $record->academic_semester_act = $html_academic_table;

    //historic academic
    $html_historic_academic = get_historic_academic_by_student($student_id);
    $record->historic_academic = $html_historic_academic;

    // Student trackings (Seguimientos)

    $html_tracking_peer = "";
    $array_peer_trackings = get_tracking_group_by_semester($student_id, 'PARES', null, $blockid);

    $array_peer_trackings_dphpforms = dphpforms_find_records('seguimiento_pares', 'seguimiento_pares_id_estudiante', $student_code, 'DESC');
    $array_peer_trackings_dphpforms = json_decode($array_peer_trackings_dphpforms);
    $array_detail_peer_trackings_dphpforms = array();
    foreach ($array_peer_trackings_dphpforms->results as &$peer_trackings_dphpforms) {
        array_push($array_detail_peer_trackings_dphpforms, json_decode(dphpforms_get_record($peer_trackings_dphpforms->id_registro, 'fecha')));
    }

    $array_tracking_date = array();
    foreach ($array_detail_peer_trackings_dphpforms as &$peer_tracking) {
        foreach ($peer_tracking->record->campos as &$tracking) {
            if ($tracking->local_alias == 'fecha') {
                array_push($array_tracking_date, strtotime($tracking->respuesta));
            }
        }
    }

    rsort($array_tracking_date);

    $seguimientos_ordenados = new stdClass();
    $seguimientos_ordenados->index = array();
    //Inicio de ordenamiento
    $periodo_a = [1, 2, 3, 4, 5, 6, 7];
    //periodo_b es el resto de meses;
    for ($x = 0; $x < count($array_tracking_date); $x++) {
        $string_date = $array_tracking_date[$x];
        $array_tracking_date[$x] = getdate($array_tracking_date[$x]);
        if (property_exists($seguimientos_ordenados, $array_tracking_date[$x]['year'])) {
            if (in_array($array_tracking_date[$x]['mon'], $periodo_a)) {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->per_a, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }

                            }
                        }
                    }
                }
            } else {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->per_b, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }

                            }
                        }
                    }
                }
            }
        } else {
            array_push($seguimientos_ordenados->index, $array_tracking_date[$x]['year']);
            $seguimientos_ordenados->$array_tracking_date[$x]['year']->year = $array_tracking_date[$x]['year'];
            $seguimientos_ordenados->$array_tracking_date[$x]['year']->per_a = array();
            $seguimientos_ordenados->$array_tracking_date[$x]['year']->per_b = array();
            if (in_array($array_tracking_date[$x]['mon'], $periodo_a)) {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->per_a, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }

                            }
                        }
                    }
                }
            } else {
                for ($y = 0; $y < count($array_detail_peer_trackings_dphpforms); $y++) {
                    if ($array_detail_peer_trackings_dphpforms[$y]) {
                        foreach ($array_detail_peer_trackings_dphpforms[$y]->record->campos as &$tracking) {
                            if ($tracking->local_alias == 'fecha') {
                                if (strtotime($tracking->respuesta) == $string_date) {
                                    array_push($seguimientos_ordenados->$array_tracking_date[$x]['year']->per_b, $array_detail_peer_trackings_dphpforms[$y]);
                                    $array_detail_peer_trackings_dphpforms[$y] = null;
                                    break;
                                }

                            }
                        }
                    }
                }
            }
        }
    }
    //Fin de ordenamiento

    //echo json_encode($seguimientos_ordenados);
    $seguimientos_array = json_decode(json_encode($seguimientos_ordenados), true);
    $array_periodos = array();
    for ($x = 0; $x < count($seguimientos_array['index']); $x++) {
        array_push($array_periodos, $seguimientos_array[$seguimientos_array['index'][$x]]);
    }
    $record->peer_tracking_v2 = array(
        'index' => $seguimientos_array['index'],
        'periodos' => $array_periodos,
    );
    //print_r(json_encode($record->peer_tracking_v2));

    $enum_risk = array();
    array_push($enum_risk, "");
    array_push($enum_risk, "Bajo");
    array_push($enum_risk, "Medio");
    array_push($enum_risk, "Alto");

    //END V2

    if ($array_peer_trackings != null) {

        $panel = "<div class='panel-group' id='accordion_semesters'>";

        foreach ($array_peer_trackings->semesters_segumientos as $array_semester) {

            $panel .= "<div class='panel panel-default'>";
            $panel .= "<a data-toggle='collapse' class='collapsed' data-parent='#accordion_semesters' style='text-decoration:none' href='#semester" . $array_semester->id_semester . "'>";
            $panel .= "<div class='panel-heading heading_semester_tracking'>";
            $panel .= "<h4 class='panel-title'>";
            $panel .= "$array_semester->name_semester";
            $panel .= "<span class='glyphicon glyphicon-chevron-left'></span>";
            $panel .= "</h4>"; //End panel-title
            $panel .= "</div>"; //End panel-heading
            $panel .= "</a>";

            $panel .= "<div id='semester$array_semester->id_semester' class='panel-collapse collapse in'>";
            $panel .= "<div class='panel-body'>";

            // $panel .= "<div class=\"container well col-md-12\">";
            // $panel .= "<div class=\"container-fluid col-md-10\" name=\"info\">";
            // $panel .= "<div class=\"row\">";

            $panel .= "<div class='panel-group' id='accordion_trackings_semester'>";

            foreach ($array_semester->result as $tracking) {

                $monitor_object = get_moodle_user($tracking->id_monitor);

                // Date format (Formato de fecha)
                $date = date_parse_from_format('d-m-Y', $tracking->fecha);
                $months = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-heading'>";
                $panel .= "<h4 class='panel-title'>";

                $panel .= "<a data-toggle='collapse' data-parent='#accordion_trackings_semester' href='#" . $tracking->id_seg . "'>";
                $panel .= " Registro " . $months[(int) $date["month"] - 1] . "-" . $date["day"] . "-" . $date["year"] . "</a>";

                $panel .= "</h4>"; // h4 div panel-title
                $panel .= "</div>"; // End div panel-heading

                $panel .= "<div id='$tracking->id_seg' class='panel-collapse collapse'>";
                $panel .= "<div class='panel-body'>";

                // Date, Place, time  (Fecha, lugar, hora)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-3'>";
                $panel .= "<b>Fecha:</b>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-6'>";
                $panel .= "<b>Lugar:</b>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-3'>";
                $panel .= "<b>Hora:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-3'>";
                $panel .= "<span class='date_tracking_peer'>" . $date["month"] . "-" . $date["day"] . "-" . $date["year"] . "</span>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-6'>";
                $panel .= "<span class='place_tracking_peer'>" . $tracking->lugar . "</span>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-3'>";
                $panel .= "<span class='init_time_tracking_peer'>" . $tracking->hora_ini . "</span> - <span class='ending_time_tracking_peer'>" . $tracking->hora_fin . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Created by (Creado por)

                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Creado por: </b>";
                $panel .= $monitor_object->firstname . " " . $monitor_object->lastname;
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Subject (Tema)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Tema:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<span class='topic_tracking_peer'>" . $tracking->tema . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Objectives (Objetivos)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Objetivos:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<span class='objectives_tracking_peer'>" . $tracking->objetivos . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                if ($tracking->individual != "") {

                    if ($tracking->individual_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->individual_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->individual_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Individual:</b><br>";
                    $panel .= "<span class='individual_tracking_peer'>$tracking->individual</span><br><br>";
                    $panel .= "<b>Riesgo individual: </b>";
                    $panel .= "<span class='ind_risk_tracking_peer'>" . $enum_risk[(int) $tracking->individual_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->familiar_desc != "") {

                    if ($tracking->familiar_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->familiar_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->familiar_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Familiar:</b><br>";
                    $panel .= "<span class='familiar_tracking_peer'>$tracking->familiar_desc</span><br><br>";
                    $panel .= "<b>Riesgo familiar: </b>";
                    $panel .= "<span class='fam_risk_tracking_peer'>" . $enum_risk[(int) $tracking->familiar_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->academico != "") {

                    if ($tracking->academico_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->academico_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->academico_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Académico:</b><br>";
                    $panel .= "<span class='academico_tracking_peer'>$tracking->academico</span><br><br>";
                    $panel .= "<b>Riesgo académico: </b>";
                    $panel .= "<span class='aca_risk_tracking_peer'>" . $enum_risk[(int) $tracking->academico_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->economico != "") {

                    if ($tracking->economico_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->economico_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->economico_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Económico:</b><br>";
                    $panel .= "<span class='economico_tracking_peer'>$tracking->economico</span><br><br>";
                    $panel .= "<b>Riesgo económico: </b>";
                    $panel .= "<span class='econ_risk_tracking_peer'>" . $enum_risk[(int) $tracking->economico_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                if ($tracking->vida_uni != "") {

                    if ($tracking->vida_uni_riesgo == '1') {
                        $panel .= "<div class='panel panel-default riesgo_bajo'>";
                    } else if ($tracking->vida_uni_riesgo == '2') {
                        $panel .= "<div class='panel panel-default riesgo_medio'>";
                    } else if ($tracking->vida_uni_riesgo == '3') {
                        $panel .= "<div class='panel panel-default riesgo_alto'>";
                    } else {
                        $panel .= "<div class='panel panel-default'>";
                    }

                    $panel .= "<div class='panel-body'>";
                    $panel .= "<div class='col-sm-12'>";
                    $panel .= "<b>Vida universitaria:</b><br>";
                    $panel .= "<span class='lifeu_tracking_peer'>$tracking->vida_uni</span><br><br>";
                    $panel .= "<b>Riesgo vida universitaria: </b>";
                    $panel .= "<span class='lifeu_risk_tracking_peer'>" . $enum_risk[(int) $tracking->vida_uni_riesgo] . "</span><br><br>";
                    $panel .= "</div>"; // End div col-sm-12
                    $panel .= "</div>"; // End panel-body
                    $panel .= "</div>"; // End div panel panel-default
                }

                // Observations (observaciones)
                $panel .= "<div class='panel panel-default'>";
                $panel .= "<div class='panel-body'>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<b>Observaciones:</b>";
                $panel .= "</div>";

                $panel .= "<div class='col-sm-12'>";
                $panel .= "<span class='observations_tracking_peer'>" . $tracking->observaciones . "</span>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body
                $panel .= "</div>"; // End div panel panel-default

                // Edit and delete buttons
                $panel .= "<div class='row'>";
                $panel .= "<div class='col-sm-4 row-buttons-tracking'>";
                $panel .= "<button type='button' class='btn-primary edit_peer_tracking' id='edit_tracking_" . $tracking->id_seg . "'>Editar seguimiento</button>";
                $panel .= "</div>";
                $panel .= "<div class='col-sm-3 col-sm-offset-5 row-buttons-tracking'>";
                $panel .= "<button type='button' class='btn-danger delete_peer_tracking col-sm-10' id='delete_tracking_peer_" . $tracking->id_seg . "'>";
                $panel .= "Borrar <span class='glyphicon glyphicon-trash'></span>";
                $panel .= "</button>";
                $panel .= "</div>";
                $panel .= "</div>";

                $panel .= "</div>"; // End panel-body tracking
                $panel .= "</div>"; // End div panel-collapse tracking
                $panel .= "</div>"; // End div panel-default
            }

            $panel .= "</div>"; // End panel accordion_trackings_semester

            $panel .= "</div>"; // End panel-body
            $panel .= "</div>"; // End panel-collapse

            $panel .= "</div>"; //End panel panel-default
        }

        $panel .= "</div>"; //End panel group accordion_semesters

        $html_tracking_peer .= $panel;

    } else {
        $html_tracking_peer .= "<div class='col-sm-12'><center><h4>No registra seguimientos</h4></center></div>";
    }

    $record->peer_tracking = $html_tracking_peer;

    // Loading desertion reasons or studies postponement

    $reasons_dropout = get_reasons_dropout();

    $html_select_reasons = "<option value='' id='no_reason_option'>Seleccione el motivo</option>";

    foreach ($reasons_dropout as $reason) {
        $html_select_reasons .= "<option value=" . $reason->id . ">";
        $html_select_reasons .= $reason->descripcion;
        $html_select_reasons .= "</option>";
    }

    $record->reasons_options = $html_select_reasons;

    // Getting data for risks graphs

    $periodoactual = getPeriodoActual();
    $idEstudiante = $student_id;
    // Mustache doesn't allow advanced conditional control, information detachment occurs here
    $seguimientosEstudianteIndividual = obtenerDatosSeguimientoFormateados($idEstudiante, 'individual', $periodoactual);
    $seguimientosEstudianteFamiliar = obtenerDatosSeguimientoFormateados($idEstudiante, 'familiar', $periodoactual);
    $seguimientosEstudianteAcademico = obtenerDatosSeguimientoFormateados($idEstudiante, 'academico', $periodoactual);
    $seguimientosEstudianteEconomicor = obtenerDatosSeguimientoFormateados($idEstudiante, 'economico', $periodoactual);
    $seguimientosVidaUniversitaria = obtenerDatosSeguimientoFormateados($idEstudiante, 'vida_universitaria', $periodoactual);

    $record->nombrePeriodoSeguimiento = $periodoactual['nombre_periodo'];
    $record->datosSeguimientoEstudianteIndividual = $seguimientosEstudianteIndividual;
    $record->datosSeguimientoEstudianteFamiliar = $seguimientosEstudianteFamiliar;
    $record->datosSeguimientoEstudianteAcademico = $seguimientosEstudianteAcademico;
    $record->datosSeguimientoEstudianteEconomico = $seguimientosEstudianteEconomicor;
    $record->datosSeguimientoEstudianteVidaUniversitaria = $seguimientosVidaUniversitaria;

// End of data obtaining for risks graphs

    //Pruebas
    $record->form_seguimientos = null;
    $record->form_seguimientos = dphpforms_render_recorder('seguimiento_pares', $rol);
    if ($record->form_seguimientos == '') {
        $record->form_seguimientos = "<strong><h3>Oops!: No se ha encontrado un formulario con el alias <code>seguimiento_pares</code></h3></strong>";
    }

} else {

    $student_id = -1;
    if ($rol == 'sistemas') {
        $record->code = "<input type='text' class='tip' id='codigo' value=' ' size='12' maxlength='12' required>";
    } else {
        $select = make_select_ficha($USER->id);
        $record->code = $select;
    }
}

//Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);

$record->menu = $menu_option;

$PAGE->set_context($contextcourse);
$PAGE->set_context($contextblock);
$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->requires->css('/blocks/ases/style/jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/style/sugerenciaspilos.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/c3.css', true);
$PAGE->requires->css('/blocks/ases/style/student_profile_risk_graph.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
//Pendiente para cambiar el idioma del nombre del archivo junto con la estructura de
//su nombramiento.
$PAGE->requires->css('/blocks/ases/style/creadorFormulario.css', true);

$PAGE->requires->js_call_amd('block_ases/student_profile_main', 'init');
$PAGE->requires->js_call_amd('block_ases/geographic_main', 'init');
$PAGE->requires->js_call_amd('block_ases/dphpforms_form_renderer', 'init');
$PAGE->requires->js_call_amd('block_ases/academic_profile_main', 'init');

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
$student_profile_page = new \block_ases\output\student_profile_page($record);
echo $output->render($student_profile_page);
echo $output->footer();
