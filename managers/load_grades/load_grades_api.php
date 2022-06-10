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
 * @author     David Santiago Cortés
 * @package    block_ases
 * @copyright  2022 David Santiago Cortés <david.cortes@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once('load_grades_lib.php');

$input = json_decode(file_get_contents("php://input"));

if (isset($input->function) && isset($input->params)) {
    $function = $input->function;
    $params = $input->params;


    if ($function == 'send_alerts') {
        /**
         * params[0]: Associative array with the student's grades.
         * params[1]: Instance_id
         */
        $result = send_alerts($params[0], $params[1]);

        if (is_array($result)) {

            echo json_encode(
                array(
                    "status_code" => 0,
                    "message" => "Enviadas ".$result['success'] . "/" . $result['total'] . " alertas a los practicantes y 
                                    profesionales a cargo.",
                    "data_response" => $result
                )
            );
        }
        else {
            return_with_code($result);
        }
    }
    else {
        return_with_code(-4);
    }
} else if (isset($_FILES)) {
    $result = load_csv($_FILES['file']);

    if ($result) {
        echo json_encode(
            array(
                "status_code" => 0,
                "message" => "Archivo cargado correctamente.",
                "data_response" => $result
            )
        );
    }
    else {
        return_with_code($result);
    }
} else {
    return_with_code(-3);
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
        case -7:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Algunos correos no pudieron enviarse.\n Debe ser solucionado por un desarrollador!",
                    "data_response" => ""
                )
            );
            break;
    }
    die();
}
