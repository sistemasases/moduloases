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

            if(is_array($params) && is_string($option1) && is_string($option2) &&
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