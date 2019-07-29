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
 * @author     Iader E. García Gómez
 * @author     Jorge Eduardo Mayor Fernández
 * @package    block_ases
 * @copyright  2018 Iader E. García <iadergg@gmail.com>
 * @copyright  2019 Jorge Eduardo Mayor <mayor.jorge@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../config.php');
require_once('../lib/student_lib.php');
require_once('geographic_lib.php');

date_default_timezone_set('America/Bogota');

$input = json_decode(file_get_contents("php://input"));

if(isset($input->func) && isset($input->params)) {

    //Saves the student's geographic information
    if ($input->func == 'save_geographic_info') {

        /**
         * id_ases: String
         * latitude: float
         * longitude: float
         * neighborhood: String
         * duration: int
         * distance: int
         * address: String
         * city: String
         * observaciones: String
         * vive_lejos: boolean
         * vive_zona_riesgo: boolean
         * nativo: boolean
         * nivel_riesgo: int
         */

        if (count($input->params) == 13) {

            $id_ases = $input->params[0];
            $latitude = $input->params[1];
            $longitude = $input->params[2];
            $neighborhood = $input->params[3];
            $duration = $input->params[4];
            $distance = $input->params[5];
            $address = $input->params[6];
            $city = $input->params[7];
            $observaciones = $input->params[8];
            $vive_lejos = $input->params[9];
            $vive_zona_riesgo = $input->params[10];
            $nativo = $input->params[11];
            $nivel_riesgo = $input->params[12];

            $nivel_riesgo = (int) $nivel_riesgo;

            if (is_string($id_ases) && is_float($latitude) && is_float($longitude) &&
                is_string($neighborhood) && is_int($duration) && is_int($distance) &&
                is_string($address) && is_string($city) && is_string($observaciones) &&
                is_bool($vive_lejos) && is_bool($vive_zona_riesgo) && is_bool($nativo) &&
                is_int($nivel_riesgo)) {

                $vive_lejos = ($vive_lejos) ? 1 : 0;
                $vive_zona_riesgo = ($vive_zona_riesgo) ? 1 : 0;
                $nativo = ($nativo) ? 1 : 0;

                $msg = new stdClass();

                $result_save_info = student_profile_save_geographic_info($id_ases, $latitude, $longitude, $neighborhood, $duration, $distance, $address, $city, $observaciones, $vive_lejos, $vive_zona_riesgo, $nativo, $nivel_riesgo);

                if ($result_save_info) {
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "title" => 'Éxito',
                            "message" => "La información geográfica ha sido guardada con éxito",
                            "type" => "success"
                        ));
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2, $input->params);
            }
        } else if(count($input->params) == 8) {

            $id_ases = $input->params[0];
            $latitude = $input->params[1];
            $longitude = $input->params[2];
            $neighborhood = $input->params[3];
            $duration = $input->params[4];
            $distance = $input->params[5];
            $address = $input->params[6];
            $city = $input->params[7];

            if(is_string($id_ases) && is_float($latitude) && is_float($longitude) &&
               is_string($neighborhood) && is_int($duration) && is_int($distance) &&
               is_string($address) && is_string($city)){

                $msg = new stdClass();

                $result_save_info = student_profile_save_geographic_info($id_ases, $latitude, $longitude, $neighborhood, $duration, $distance, $address, $city);

                if ($result_save_info) {
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => "La información geográfica ha sido guardada con éxito",
                        ));
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-2);
        }
    }
}

function return_with_code( $code, $input){

    switch( $code ){

        case -1:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "You are not allowed to access this resource.",
                    "data_response" => ""
                )
            );
            break;

        case -2:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Error in the scheme.",
                    "data_response" => $input
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
