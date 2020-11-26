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
                        "status_code" => 0,
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
    } else if ($function == 'load_tabs') {
        /**
         * [0] => monitor_id : monitor's id.
         * [1] => instance_id : instance the monitor belongs to.
         * [2] => tab_name : Specific tab to load.
         */
        if (count($params) == 3) {
            if (is_string($params[2]) && is_numeric($params[0]) && is_numeric($params[1])) {
                $tab_name = $params[2];
                
                switch($tab_name) {

                    case 'history_boss':
                        $result = monitor_load_bosses_tab($params[0], $params[1]);
                        break;

                    default:
                        return_with_code(-2);
                } 

                if ($result != null) {
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "",
                            "data_response" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-3);
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
                        "status_code" => 0,
                        "message" => "Información guardada con éxito",
                        "data_response" => $result,
                    )
                );
            }
            else{
                return_with_code(-1);
            }
        }
        else {
            return_with_code(-5);
        }
    
    } else if ($function == 'tracking_count') {
        /**
         * [0] => Moodle's monitor user id.
         * [1] => Instance ID
         * [2] => Period ID
         */
        if (count($params) == 3) {

            if (is_numeric($params[0]) && is_numeric($params[1]) && is_numeric($params[2])) {
               $result = monitor_get_tracking_count($params[0], $params[1], $params[2]); 

               if ($result) {
                   echo json_encode(
                       array(
                            "status_code" => 0,
                            "message" => "",
                            "data_response" => $result,
                       )
                   );
               } else {
                   return_with_code(-6);
               }
            } else {
                return_with_code(-3);
            }
        
        } else {
            return_with_code(-6);
        }
    }  else {
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
        case -3:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "El tipo de los argumentos es inválido.",
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
