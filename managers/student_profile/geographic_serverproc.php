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

if(isset($input->func) $$ isset($input->params)){

    //Get **
    
    if($input->func == 'load_geographic_info'){

        /**
         * id_ases: int
         * latitude: float
         * longitude: float
         * neighborhood: String
         * geographic_risk: int
         * duration: int
         * distance: int
         * address: String
         * city: String
         * observaciones: String
         * vive_lejos: int
         * vive_zona_riesgo: int
         * nativo: int
         * nivel_riesgo: int
         */

        if( count( $input->params ) == 14 ){

            //Order of params
            /**
             * id_ases [0]
             * latitude [1]
             * longitude [2]
             * neighborhood [3]
             * geographic_risk [4]
             * duration [5]
             * distance [6]
             * address [7]
             * city [8]
             * observaciones [9]
             * vive_lejos [10]
             * vive_zona_riesgo [11]
             * nativo [12]
             * nivel_riesgo [13]
             */

             if(is_string($input->params[0]) && is_float($input->params[1]) && is_float($input->params[2]) &&
                is_string($input->params[3]) && is_int($input->params[4]) && is_int($input->params[5]) &&
                is_int($input->params[6]) && is_string($input->params[7]) && is_string($input->params[8]) &&
                is_string($input->params[9]) &&  is_int($input->params[10]) && is_int($input->params[11]) &&
                is_int($input->params[12]) && is_int($input->params[13])){

                    $id_ases = $input->id_ases;
                    $result = load_geographic_info($id_ases);

                    if($result){
                        $msg->title = 'Éxito';
                        $msg->text = "La información geográfica ha sido cargada con éxito";
                        $msg->type = "success";
                        $msg->info = $result;
                    }
                    else{
                        $msg->title = 'Error';
                        $msg->text = "La información geográfica no ha sido cargada. Inténtalo nuevamente.";
                        $msg->type = "error";
                    }
                    echo json_encode($msg);

                }else {

                }

        }

        

    }
    else if($input->func == 'save_geographic_info'){

        $id_ases = $input->id_ases;
        $latitude = $input->latitude;
        $longitude = $input->longitude;
        $neighborhood = $input->neighborhood;
        $geographic_risk = $input->geographic_risk;
        $duration = $input->duration;
        $distance = $input->distance;
        $address = $input->address;
        $city = $input->city;
        $observaciones = $input->observaciones;
        $vive_lejos = $input->vive_lejos;
        $vive_zona_riesgo = $input->vive_zona_riesgo;
        $nativo = $input->nativo;
        $nivel_riesgo = $input->nivel_riesgo;

        $nativo = (isset($nativo))?$nativo:-1;
        $vive_lejos = ($vive_lejos)?1:0;
        $vive_zona_riesgo = ($vive_zona_riesgo)?1:0;
        $nivel_riesgo = (isset($nivel_riesgo))?$nivel_riesgo:-1;

        $msg = new stdClass();

        $result_save_info = save_geographic_info($id_ases, $latitude, $longitude, $neighborhood, $geographic_risk, $duration, $distance, $address, $city, $observaciones, $vive_lejos, $vive_zona_riesgo, $nativo, $nivel_riesgo);

        if($result_save_info){
            $msg->title = 'Éxito';
            $msg->text = "La información geográfica ha sido guardada con éxito";
            $msg->type = "success";
        }
        else{
            $msg->title = 'Error';
            $msg->text = "La información geográfica no ha sido guardada. Inténtalo nuevamente.";
            $msg->type = "error";
        }
        echo json_encode($msg);
    }
}

function return_with_code( $code ){
        
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
