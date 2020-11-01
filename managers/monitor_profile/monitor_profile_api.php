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
    $params = $input->params;

    if ($function == 'is_monitor_ps') {
        /**
         * [0] => code : student code.
         */
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

    // Cargar pestaña de conteo de fichas.
    } else if ($function == 'load_trackings_tab') {
        /**
         * [0] => monitor_code : monitor's username.
         * [1] => monitor_id : monitor's id.
         * [2] => instance_id : instance the monitor belongs to.
         */
        if (count($params) == 3) {
            $result = monitor_load_trackings_tab($params[0], $params[1], $params[2]); 

            if ($result) {
                echo json_encode(
                    array(
                        "status_code" => 1,
                        "message" => "",
                        "data_response" => $result,
                    )
                );
            } else {
                return_with_code(-6);
            }
        }
        else {
            return_with_code(-5);
        }
    } else if ($function == 'save_profile') {
        /**
         * [0] => form : Array with monitor's info to be saved.
         */
        if (count($params) == 1) {
            $result = monitor_save_profile($params[0]);

            if ($result) {
                echo json_encode(
                    array(
                        "status_code" => 1,
                        "message" => "",
                        "data_response" => $result,
                    )
                );
            }
            else{
                return_with_code(-6);
            }
        }
        else {
            return_with_code(-5);
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
