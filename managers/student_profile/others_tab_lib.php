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
 * @author     Juan Pablo Castro
 * @package    block_ases
 * @copyright  2018 Juan Pablo Castro <juan.castro.vasquez@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';


/**
 * Function save json decode in table usuario
 * @see save_detalle_discapacidad(json, id_ases)
 * @param $json  valid JSON 
 * @param $id_ases ID ASES user
 * @return boolean
 **/

function save_detalle_discapacidad($json, $id_ases){
    global $DB;

    $student = new stdClass ();
    // $sql_query = "SELECT * FROM {talentospilos_usuario} WHERE id = $id_ases";
    // $student=  $DB->get_records_sql($sql_query);
    $student->id = $id_ases;
    $student->json_detalle = $json;
    $result = $DB->update_record('talentospilos_usuario', $student);
    // $sql_query = "UPDATE {talentospilos_usuario} SET json_detalle = '$json' WHERE id = $id_ases";
    // $result =  $DB->execute($sql_query);

    // $student = $DB->get_records_sql($sql_query);
    // $student = (object) $student;
    // $student->json_detalle = $json;

    //$result = $DB->update_record('talentospilos_usuario', $student);
        return $result;

}


/**
 * Function save health_data in table talentospilos_health_data
 * @see save_academics_data(academics_data)
 * @param $academics_data  valid Object with academics data 
 * @return boolean
 **/

function save_academics_data($academics_data){
    global $DB;
   
    $result = $DB->insert_record("talentospilos_academics_data", $academics_data, true);
    return $result;

}


/**
 * Function update academics_data register in table talentospilos_academics_data switch user
 * @see update_academics_data(academics_data, id_ases)
 * @param $academics_data  valid Object with academics_data
 * @param $id_ases      user id
 * @return boolean
 **/

function update_academics_data($academics_data, $id_ases){
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_academics_data} WHERE id_ases_user = '$id_ases'";
    $register = $DB->get_record_sql($sql_query);
   
    $data = json_decode($academics_data);

    $register_health_data                               = new  stdClass();

    $register_academics_data->id                        = $register->id;
    $register_academics_data->id_ases_user              = $id_ases;
    $register_academics_data->resolucion_programa       = $data->current_resolution;
    $register_academics_data->creditos_totales          = $data->total_time;
    $register_academics_data->otras_instituciones       = json_encode($data->others_institutions);
    $register_academics_data->dificultades              = $data->academics_dificults;
    $register_academics_data->observaciones             = $data->academics_observations;
    $register_academics_data->titulo_academico_colegio  = $data->previous_academic_title;

    $result = $result = $DB->update_record('talentospilos_academics_data', $register_academics_data);
    return $result;

}

/**
 * Function save health_data in table talentospilos_health_data
 * @see save_health_data(economics_data)
 * @param $health_data  valid Object with health data 
 * @return boolean
 **/

function save_health_data($health_data){
    global $DB;
   
    $result = $DB->insert_record("talentospilos_health_data", $health_data, true);
    return $result;

}

/**
 * Function update health_data register in table talentospilos_health_data switch user
 * @see update_health_data(health_data, id_ases)
 * @param $health_data  valid Object with health data 
 * @param $id_ases      user id
 * @return boolean
 **/

function update_health_data($health_data, $id_ases){
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_health_data} WHERE id_ases_user = '$id_ases'";
    $register = $DB->get_record_sql($sql_query);
   
    $data = json_decode($health_data);

    $register_health_data                               = new  stdClass();
    $register_health_data->id                           = $register->id;;
    $register_health_data->regimen_salud                = json_encode($data[0]);
    $register_health_data->servicio_salud_vinculado     = json_encode($data[1]);
    $register_health_data->servicios_usados             = json_encode($data[2]);
    $register_health_data->id_ases_user                 = $id_ases;

    $result = $result = $DB->update_record('talentospilos_health_data', $register_health_data);
    return $result;

}



/**
 * Function save economics_data in table talentospilos_economics_data
 * @see save_economics_data(economics_data)
 * @param $economics_data  valid Object with economics data 
 * @return boolean
 **/

function save_economics_data($economics_data){
    global $DB;
   
    $result = $DB->insert_record("talentospilos_economics_data", $economics_data, true);
    return $result;

}

/**
 * Function update economics_data register in table talentospilos_economics_data switch user
 * @see update_economics_data(economics_data, id_ases)
 * @param $economics_data  valid Object with economics data 
 * @param $id_ases      user id
 * @return boolean
 **/

function update_economics_data($economics_data, $id_ases){
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_economics_data} WHERE id_ases_user = '$id_ases'";
     $register = $DB->get_record_sql($sql_query);
   
    $data = json_decode($economics_data);

    $register_economics_data                            = new  stdClass();
    $register_economics_data->id                        = $register->id;;
    $register_economics_data->estrato                   = $data[0]->val_input;
    $register_economics_data->prestacion_economica      = json_encode($data[1]);
    $register_economics_data->beca                      = json_encode($data[2]);
    $register_economics_data->ayuda_transporte          = json_encode($data[3]);
    $register_economics_data->ayuda_materiales          = json_encode($data[4]);
    $register_economics_data->solvencia_econo           = $data[5]->val_input;
    $register_economics_data->ocupacion_padres          = json_encode($data[6]);
    $register_economics_data->nivel_educ_padres         = json_encode($data[7]);
    $register_economics_data->situa_laboral_padres      = json_encode($data[8]);
    $register_economics_data->expectativas_laborales    = json_encode($data[9]);
    $register_economics_data->id_ases_user              = $id_ases;

    $result = $result = $DB->update_record('talentospilos_economics_data', $register_economics_data);
    return $result;

}




/**
 * Function return json schema switch id_schema
 * @see get_schema(id_schema)
 * @param $id_schema ID JSON SCHEMA
 * @return Object
 **/
function get_schema($id_schema){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_json_schema} WHERE id = $id_schema";
    return $DB->get_record_sql($sql_query);
}

?>