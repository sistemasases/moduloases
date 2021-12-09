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
 * @author     David S. Cortés
 * @package    block_ases
 * @copyright  2021 David Santiago Cortés<david.cortes@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Note: DO NOT use this as a guide to write an API for libs
 * not intended to use files.
 */

require_once(__DIR__ . '/mrm_monitor_estud.php');

$input = $_POST;

if ( isset($input['function']) && isset($_FILES['file']) ){
    $function = $input['function'];

    if ($function == "mrm_monitor_estud.php") {
        /**
         * [0] => instance id
         */
        if (count($input) == 2) {
            $instance_id = $input['idinstancia'];

            $result = upload_assignations_file($_FILES['file'], $instance_id);

            if ($result) {
                echo json_encode( $result );
            } else {
                return_with_code(-4);
            }
        }
        else {
            return_with_code(-3);
        }
    }
    else {
        return_with_code(-2);
    }
}
else {
    return_with_code(-1);
}


/**
 * Returns a message with an error code
 * @param int $code
 */
function return_with_code($code) {
    switch($code) {
        case -1:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Cuerpo de solicitud incompleto.",
                )
            );
            break;
        case -2:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Función desconocida"
                )
            );
            break;

        case -3:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Se esperaba un número de argumentos distinto."
                )
            );
            break;

        case -4:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Error en el servidor."
                )
            );
            break;
    }
}
