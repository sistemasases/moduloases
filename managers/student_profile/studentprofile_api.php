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
 * @copyright  2019 Jorge Eduardo Mayor <mayor.jorge@correounivalle.edu.co>
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
         * [1] => tab_name: string
         * [2] => id_block: string
         */
        $params = $input->params;
        if(count($params) == 3) {

            $id_ases = $params[0];
            $tab_name = $params[1];
            $id_block = $params[2];

            if(is_string($id_ases) && is_string($tab_name) && is_string($id_block)) {

                switch($tab_name){
                    case 'socioed':
                        $result = student_profile_load_socioed_tab($id_ases, $id_block);
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
                            "message" => $tab_name." information",
                            "data_response" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2, $params);
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
                        "type" => $msg->status,
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
         * [1] => id_new_status: string
         */
        $params = $input->params;

        if(count($params) == 2){

            $id_ases = $params[0];
            $id_new_status = $params[1];

            if(is_string($id_ases ) && is_string($id_new_status)){

                $result = save_status_icetex($id_new_status, $id_ases);

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

                echo json_encode($result);
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'update_ases_status_without_traking'){

        /**
         * [0] => current_status: string
         * [1] => new_status: string
         * [2] => instance_id: string
         * [3] => student_code: string
         * [4] => id_reason_dropout: string
         * [5] => observation: string
         */
        $params = $input->params;

        if(count($params) == 6){

            $current_status = $params[0];
            $new_status = $params[1];
            $instance_id = $params[2];
            $student_code = $params[3];
            $id_reason_dropout = $params[4];
            $observation = $params[5];

            if(is_string($current_status) && is_string($new_status) && is_string($instance_id) &&
                is_string($student_code) && is_string($id_reason_dropout) && is_string($observation)) {

                if (trim($id_reason_dropout) != "" && trim($observation) != "") {

                    $result_save_dropout = save_reason_dropout_ases($student_code, $id_reason_dropout, $observation);
                    $result = update_status_ases($current_status, $new_status, $instance_id, $student_code, $id_reason_dropout);

                    if ($result && $result_save_dropout) {
                        echo json_encode(
                            Array (
                                "status_code" => 0,
                                "title" => 'Éxito',
                                "message" => 'Estado actualizado con éxito.',
                                "type" => 'success'
                            )
                        );
                    } else {
                        return_with_code(-7);
                    }
                } else {
                    return_with_code(-8);
                }
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'update_ases_status_on_traking') {

        /**
         * [0] => current_status: string
         * [1] => new_status: string
         * [2] => instance_id: string
         * [3] => student_code: string
         */
        $params = $input->params;

        if(count($params) == 4){

            $current_status = $params[0];
            $new_status = $params[1];
            $instance_id = $params[2];
            $student_code = $params[3];

            if(is_string($current_status) && is_string($new_status) && is_string($instance_id) &&
                is_string($student_code)) {

                $result = update_status_ases($current_status, $new_status, $instance_id, $student_code);

                if ($result) {
                    echo json_encode(
                        Array (
                            "status_code" => 0,
                            "title" => 'Éxito',
                            "message" => 'Estado actualizado con éxito.',
                            "type" => 'success'
                        )
                    );
                } else {
                    return_with_code(-7);
                }
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
    } else if($function == 'update_tracking_status'){

        /**
         * [0] => id_ases_student: string
         * [1] => id_academic_program: string
         */
        $params = $input->params;

        if(count($params) == 2){

            $id_ases_student = $params[0];
            $id_academic_program = $params[1];

            if(is_string($id_ases_student) && is_string($id_academic_program)) {

                $result = update_tracking_status($id_ases_student, $id_academic_program);

                if($result){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "title" => "Éxito",
                            "message" => "El campo se ha actualizado con éxito",
                            "type" => "success"
                        )
                    );
                } else {
                    return_with_code(-9);
                }
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else if($function == 'update_user_image'){

        /**
         * [0] => id_moodle: string
         * [1] => image_file:
         */
        $params = $input->params;

        if(count($params) == 2){

            $id_moodle = $params[0];
            $image_file = $params[1];

            if(is_string($id_moodle) && isset($image_file)) {

                update_user_image_profile($user->id, 0);

                print_r($id_moodle);
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } else {
        return_with_code(-4);
    }
} else {
    return_with_code(-1);
}

/**
 * @method return_with_code
 * Returns a message with the code of the error.
 * reserved codes: -1, -2, -3, -4, -5, -6, -7, -8, -9 -99.
 * @param $code
 */
function return_with_code($code, $params = null){

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
                    "data_response" => $params
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

        case -7:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "title" => "Error",
                    "error_message" => "Error al realizar registro.",
                    "type" => "error",
                    "data_response" => ""
                )
            );
            break;

        case -8:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "title" => "Error",
                    "error_message" => "Todos los campos son obligatorios",
                    "type" => "error",
                    "data_response" => ""
                )
            );
            break;

        case -9:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "title" => "Error",
                    "message" => "Error al actualizar el campo",
                    "type" => "error",
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