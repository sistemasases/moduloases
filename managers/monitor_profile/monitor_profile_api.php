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
 * @author      David Santiago Cortés 
 * @package     block_ases
 * @copyright   2020 David S. Cortés <david.cortes@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once('monitor_profile_lib.php');

$input = json_decode(file_get_contents("php://input"));

if ( isset($input->function) && isset($input->params) ){

    $function = $input->function;

    if ($function == 'is_monitor_ps') {
        /**
         * [0] => code : student code.
         */
        $params = $input->params;
        if(count($params) == 1) {

            $code = $params[0];
            $result = monitor_is_monitor_ps($code);

            if ($result) {
                echo json_encode(
                    array(
                        "status_code" => 1,
                        "message" => "Is monitor",
                        "data_response" => $result,
                    )
                );
            } else {
                return_with_code(-5);
            }
        } else {
            return_with_code(-6);
        }
    } else {
        return_with_code(-2);
    }
} else {
    return_with_code(-1);
}
/**
 * Returns a message with an error code
 * @param int $code
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
                    "error_message" => "Función no definida.",
                    "data_response" => ""
                )
            );
            break;
        
        case -5:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Role not assigned.",
                    "data_response" => "" 
                )
            );
            break;

        case -6:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Cantidad de argumentos inválida.",
                    "data_response" => ""
                )
            );
            break;
    }
    die();
}
