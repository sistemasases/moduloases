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
 * Estrategia ASES
 *
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once ('../validate_profile_action.php');
require_once('tracking_functions.php');
require_once('../student_profile/studentprofile_lib.php');
require_once('../periods_management/periods_lib.php');
require_once ('../dphpforms/dphpforms_forms_core.php');
require_once ('../dphpforms/dphpforms_records_finder.php');
require_once ('../dphpforms/dphpforms_get_record.php');
require_once '../user_management/user_lib.php';

global $USER;

if(isset($_POST['type'])&&$_POST['type']=="getInfo"&&isset($_POST['instance'])) 
 {
    $datos=[];
    $datos["id"]=$USER->id;
    $datos["username"]=$USER->username;
    $datos["email"]=$USER->email;
    $datos["rol"]=get_id_rol_($USER->id,$_POST['instance']);
    $datos["name_rol"]=get_name_rol($datos["rol"]);

    echo json_encode($datos);
}

if(isset($_POST['type'])&&isset($_POST['instance'])&&$_POST['type']=="get_student_trackings"&&isset($_POST['student_code'])) 
 {
    // Student trackings (Seguimientos)

      $html_tracking_peer = "";



    $student_code = explode("-", $_POST['student_code']);

    $ases_student = get_ases_user_by_code($student_code[0]);
    $student_id = $ases_student->id;
    $array_peer_trackings_dphpforms = dphpforms_find_records('seguimiento_pares', 'seguimiento_pares_id_estudiante', $student_code[0], 'DESC');
    $array_peer_trackings_dphpforms = json_decode($array_peer_trackings_dphpforms);

    $array_detail_peer_trackings_dphpforms = array();
    $array_tracking_date = array();

    format_dates_trackings($array_detail_peer_trackings_dphpforms,$array_tracking_date,$array_peer_trackings_dphpforms);

    rsort($array_tracking_date);
    $seguimientos_ordenados=trackings_sorting($array_detail_peer_trackings_dphpforms,$array_tracking_date,$array_peer_trackings_dphpforms);

    $seguimientos_array = json_decode(json_encode($seguimientos_ordenados), true);
    $array_periodos = array();
    for ($x = 0; $x < count($seguimientos_array['index']); $x++) {
        array_push($array_periodos, $seguimientos_array[$seguimientos_array['index'][$x]]);
    }
    $record->peer_tracking_v2 = array(
        'index' => $seguimientos_array['index'],
        'periodos' => $array_periodos,
    );


    $html_tracking_peer = "";
    var_dump($record->peer_tracking_v2);
 
    if ($record->peer_tracking_v2['periodos'][0] != null) {

           foreach ($record->peer_tracking_v2['periodos'][0] as $tracking) {

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

        $html_tracking_peer .= $panel;

    } else {
        $html_tracking_peer .= "<div class='col-sm-12'><center><h4>No registra seguimientos</h4></center></div>";
    }




     echo json_encode($html_tracking_peer);



}



?>
