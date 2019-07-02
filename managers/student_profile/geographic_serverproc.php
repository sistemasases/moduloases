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
 * @package    block_ases
 * @copyright  2018 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../config.php');
require_once('../lib/student_lib.php');
require_once('geographic_lib.php');

date_default_timezone_set('America/Bogota');


$data = json_decode(file_get_contents("php://input"));

if(isset($data)){
    if($data->func == 'load_geographic_info'){

        $id_ases = $data->id_ases;
        load_geographic_info($id_ases);

    }
    else if($data->func == 'save_geographic_info'){

        $id_ases = $data->id_ases;
        $latitude = $data->latitude;
        $longitude = $data->longitude;
        $neighborhood = $data->neighborhood;
        $geographic_risk = $data->geographic_risk;
        $duration = $data->duration;
        $distance = $data->distance;
        $address = $data->address;
        $city = $data->city;
        $observaciones = $data->observaciones;
        $vive_lejos = $data->vive_lejos;
        $vive_zona_riesgo = $data->vive_zona_riesgo;
        $nativo = $data->nativo;
        $nivel_riesgo = $data->nivel_riesgo;

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
};
