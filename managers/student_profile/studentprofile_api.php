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
 * @author     Jorge Eduardo Mayor Fernández
 * @package    block_ases
 * @copyright  2019 Jorge E. Mayor <mayor.jorge@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once('../lib/student_lib.php');
require_once('../student_profile/studentprofile_lib.php');

date_default_timezone_set('America/Bogota');

$input = json_decode(file_get_contents("php://input"));

if(isset($input->func) && isset($input->params)) {

    $function = $input->func;

    //Loads tabs of student profile
    if($function == 'load_tabs') {

        /**
         * [0] => id_ases: string
         * [1] => tab_to_load: string
         */
        if(count($input->params) == 2) {

            $id_ases = $input->params[0];
            $tab_to_load = $input->params[1];

            if(is_string($id_ases)) {

                switch($tab_to_load){
                    case 'socioed':
                        $result = student_profile_load_socioed_tab($id_ases);
                        break;
                    case 'academic':
                        $result = student_profile_load_academic_tab($id_ases);
                        break;
                    case 'geographic':
                        $result = student_profile_load_geographic_tab($id_ases);
                        break;
                    case 'others':
                        $result = student_profile_load_tracing_others_tab($id_ases);
                        break;
                    default:
                        return_with_code(-3);
                        break;
                }

                if($result != null){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "Geographic information",
                            "data_response" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'save_profile'){

        /**
         * [0] => form: array
         * [1] => option1: string
         * [2] => option2: string
         * [3] => live_with: string (json)
         */
        $params = $input->params;

        if(count($params) == 4){

            $form = $params[0];
            $option1 = $params[1];
            $option2 = $params[2];
            $live_with = $params[3];

            if(is_array($form) && is_string($option1) && is_string($option2) &&
               is_string($live_with)) {
                $msg = save_profile($form, $option1, $option2, $live_with);
                echo json_encode(
                    array(
                        "status_code" => 0,
                        "title" => $msg->title,
                        "status" => $msg->status,
                        "message" => $msg->msg
                    )
                );
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'save_icetex_status') {

        /**
         * [0] => id_ases: string
         * [1] => new_status:
         * [2] => id_reason:
         * [3] => observations:
         */

        $params = $input->params;
        if(count($params) == 4){

            $id_ases = $params[0];
            $new_status = $params[1];
            $id_reason = $params[2];
            $observations = params[3];

            if(is_string($id_ases ) && is_string($new_status)){

                $id_reason=(isset($id_reason))?$id_reason:null;
                $observations=(isset($observations))?$observations:null;

                $result = save_status_icetex($new_status, $id_ases, $id_reason, $observations);

                echo json_encode(
                    array(
                        "status_code" => 0,
                        "title" => $result->title,
                        "type" => $result->type,
                        "message" => $result->msg
                    )
                );
            }else{
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'update_status_program') {

        /**
         * [0] => id_moodle: string
         * [1] => id_program: string
         * [2] => status_program: string
         */
        $params = $input->params;

        if(count($params) == 3){

            $id_moodle = $params[0];
            $id_program = $params[1];
            $status_program = $params[2];

            if(is_string($id_moodle) && is_string($id_program) && is_string($status_program)) {

                $result = update_status_program($id_program, $status_program, $id_moodle);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "title" => 'Éxito',
                            "status" => 'success',
                            "message" => 'Estado del programa actualizado con éxito.'
                        )
                    );
                } else {
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "title" => 'Error',
                            "status" => 'error',
                            "message" => 'Error al guardar estado en la base de datos.'
                        )
                    );
                }
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'update_status_ases'){

    } else if($function == 'save_tracking_peer'){

        /**
         * [0] => tracking_peer:
         * [1] => id_monitor:
         * [2] => created:
         * [3] => fecha:
         * [4] => lugar:
         * [5] => h_ini:
         * [6] => m_ini:
         * [7] => h_fin:
         * [8] => m_fin:
         * [9] => tema:
         * [10] => objetivos:
         * [11] => individual:
         * [12] => riesgo_ind:
         * [13] => familiar:
         * [14] => riesgo_familiar:
         * [15] => academico:
         * [16] => riesgo_aca:
         * [17] => economico:
         * [18] => riesgo_econom:
         * [19] => vida_uni:
         * [20] => riesgo_uni:
         * [21] => observaciones:
         * [22] => id_ases: string
         * [23] => id_instance:
         */
        $params = $input->params;

        if(count($params) == 3){

            $id_tracking_peer = $params[0];
            $id_monitor = $params[1]; //Pendiente - $USER->id
            $created = $params[2]; //Pendiente - tiempo
            $fecha = $params[3];
            $lugar = $params[4];
            $h_ini = $params[5];
            $m_ini = $params[6];
            $h_fin = $params[7];
            $m_fin = $params[8];
            $tema = $params[9];
            $objetivos = $params[10];
            $individual = $params[11];
            $riesgo_ind = $params[12];
            $familiar = $params[13];
            $riesgo_familiar = $params[14];
            $academico = $params[15];
            $riesgo_aca = $params[16];
            $economico = $params[17];
            $riesgo_econom = $params[18];
            $vida_uni = $params[19];
            $riesgo_uni = $params[20];
            $observaciones = $params[21];
            $id_ases = $parms[22];
            $id_instance = $params[23];

            if(is_string($id_moodle) && is_string($id_program) && is_string($status_program)) {

                $tracking_object = new stdClass();
                $tracking_object->id = (int)$id_tracking_peer;
                $tracking_object->id_monitor = $id_monitor;
                $tracking_object->created = $created;
                $tracking_object->fecha = strtotime($fecha);
                $tracking_object->lugar = $lugar;
                $tracking_object->hora_ini = $h_ini.":".$m_ini;
                $tracking_object->hora_fin = $h_fin.":".$m_fin;
                $tracking_object->tema = $tema;
                $tracking_object->objetivos = $objetivos;
                $tracking_object->individual = $individual;
                $tracking_object->individual_riesgo = $riesgo_ind;
                $tracking_object->familiar_desc = $familiar;
                $tracking_object->familiar_riesgo = $riesgo_familiar;
                $tracking_object->academico = $academico;
                $tracking_object->academico_riesgo = $riesgo_aca;
                $tracking_object->economico = $economico;
                $tracking_object->economico_riesgo = $riesgo_econom;
                $tracking_object->vida_uni = $vida_uni;
                $tracking_object->vida_uni_riesgo = $riesgo_uni;
                $tracking_object->observaciones = $observaciones;
                $tracking_object->id_estudiante_ases = $id_ases;
                $tracking_object->id_instancia = $id_instance;
                $tracking_object->tipo = "PARES";
                $tracking_object->status = 1;
                $tracking_object->revisado_profesional = 0;
                $tracking_object->revisado_practicante = 0;

                $result_saving = save_tracking_peer($tracking_object);

                echo json_encode($result_saving);
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'delete_tracking_peer'){

        /**
         * [0] => id_tracking: string
         */
        $params = $input->params;

        if(count($params) == 1){

            $id_tracking = $params[0];

            if(is_string($id_tracking)) {
                $result = delete_tracking_peer((int)$id_tracking_peer);

                echo json_encode(
                    array(
                        "status_code" => 0,
                        "title" => $result->title,
                        "status" => $result->type,
                        "msg" => $result->msg
                    )
                );
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'is_student'){

        /**
         * [0] => code_student: string
         */
        $params = $input->params;

        if(count($params) == 1){

            $code_student = $params[0];

            if(is_string($code_student)) {

                $msg = validate_student($code_student);

                echo json_encode($msg);
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'send_email'){

    } else if($function == 'update_tracking_status'){

    } else if($function == 'update_user_image'){

    } else {
        return_with_code(-4);
    }
} else {
    return_with_code(-1);
}

/**
 * @method return_with_code
 * Returns a message with the code of the error.
 * reserved codes: -1, -2, -3, -4, -5, -6, -99.
 * @param $code
 */
function return_with_code($code){

    switch( $code ){

        case -1:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Error en el servidor.",
                    "data_response" => ""
                )
            );
            break;

        case -2:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Error in the scheme.",
                    "data_response" => ""
                )
            );
            break;

        case -3:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Invalid values in the parameters.",
                    "data_response" => ""
                )
            );
            break;

        case -4:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Function not defined.",
                    "data_response" => ""
                )
            );
            break;

        case -5:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Duplicate.",
                    "data_response" => ""
                )
            );
            break;

        case -6:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Wrong quantity of parameters in input.",
                    "data_response" => ""
                )
            );
            break;

        case -99:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "critical error.",
                    "data_response" => ""
                )
            );
            break;
    }
    die();
}