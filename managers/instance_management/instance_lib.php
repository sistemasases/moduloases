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
 * Talentos Pilos
 *
 * @author     John Lourido 
 * @package    block_ases
 * @copyright  2017 JOhn Lourido <jhonkrave@gmail.com>
 * @copyright  2018 Iader E. García Gómez <iader.garcia@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once(__DIR__. "/../cohort/cohort_lib.php");


/**
 * Returns an instance given its id
 * 
 * @see consult_instance($id_instance)
 * @param $id_instance --> instance id
 * @return object representing the instance from {talentospilos_instancia} and {talentospilos_programa} tables (JOIN)
 */
function consult_instance($id_instance){
    global $DB;
    $sql_query = "SELECT *
                  FROM {talentospilos_instancia} 
                  WHERE id_instancia = ".$id_instance.";";
    $result_query = $DB->get_record_sql($sql_query);
    return $result_query;
}

/**
 * Insert a new instance
 * 
 * @see insert_instance($id_instance)
 * @param $id_instance --> instance id
 * @param integer Deprecated
 * @return boolean 
 */
function insert_instance($id_instance, $id_user){

    global $DB;

    $object_to_record = new stdClass();
    $object_to_record->id_instancia = $id_instance;
    //$object_to_record->id_administrador = $id_user; Deprecated, "id_administrador" doesn't exist at talentospilos_instancia;
    $object_to_record->descripcion = "";
    $object_to_record->id_number = time();

    $result_insertion = $DB->insert_record('talentospilos_instancia', $object_to_record, true);
    
    return $result_insertion;
}

/**
 * Función que retorna las cohortes del sistema que no han sido asignadas a alguna instancia
 * 
 * @see get_cohorts_without_assignment()
 * @return stdClass Array
 */

function get_cohorts_without_assignment($id_instance){
    global $DB;
    $sql_query = "SELECT id, idnumber, name 
                  FROM {cohort} 
                  WHERE id NOT IN 
                  (SELECT id_cohorte 
                   FROM {talentospilos_inst_cohorte} 
                   WHERE id_instancia = $id_instance)";
    $result = $DB->get_records_sql($sql_query);
    return $result;
}

/**
 * Función que asigna permisos sobre una funcionalidad determinada
 * 
 * @see assign_permissions()
 * @param role_name ---> nombre del rol
 * @param fun_name  ---> nombre de la funcionalidad
 * @return boolean
 */

function assign_permissions($role_name, $fun_name){
    global $DB;

    $result = true;

    $sql_query = "SELECT accion.id FROM {talentospilos_funcionalidad} AS funcionalidad
                                INNER JOIN {talentospilos_accion} AS accion 
                                ON funcionalidad.id = accion.id_funcionalidad";

    $actions = $DB->get_records_sql($sql_query);

    $sql_query = "SELECT id FROM {talentospilos_rol} AS role WHERE nombre_rol = '$role_name'";

    $id_role = $DB->get_record_sql($sql_query)->id;

    foreach($actions as $action){

        $object_record = new stdClass();
        $object_record->id_rol = $id_role;
        $object_record->id_accion = $action->id;

        $result_insert = $DB->insert_record('talentospilos_permisos_rol', $object_record, true);

        if(!$result_insert){
            $result = false;
            break;
        }
    }

    return $result;
}

/**
 * Función que valida si una cohorte ya fue asignada a una instancia
 * 
 * @see validate_cohort_instance()
 * @param id_cohort   ---> ID cohorte
 * @param id_instance  ---> ID instancia
 * @return boolean
 */

 function validate_cohort($id_cohort, $id_instance){

    global $DB;

    $sql_query = "SELECT count(*) AS count
                  FROM {talentospilos_inst_cohorte}
                  WHERE id_cohorte = $id_cohort AND id_instancia = $id_instance";
    
    $result_query = $DB->get_record_sql($sql_query);

    return $result_query;
 }



/**
 * Función que deshace la asignación de una cohorte sobre una instancia
 * 
 * @see unassign_cohort()
 * @param id_cohort   ---> idnumber cohorte
 * @param id_instance  ---> ID instancia
 * @return stdClass Array
 */

function unassign_cohort($idnumber_cohort, $id_instance){

    global $DB;

    $sql_query = "SELECT id
                  FROM {cohort}
                  WHERE idnumber = '$idnumber_cohort'";

    $id_cohort = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT id 
                  FROM {talentospilos_inst_cohorte}
                  WHERE id_cohorte = $id_cohort AND id_instancia = $id_instance";

    $id_register = $DB->get_record_sql($sql_query);

    $object_to_delete = array();
    $object_to_delete['id'] = $id_register->id;

    $result = $DB->delete_records('talentospilos_inst_cohorte', $object_to_delete);

    return $result;
}



/**
 * Función que recupera la información de una instancia determinada
 * 
 * @see get_info_instance()
 * @param id_instance  ---> ID instancia
 * @return stdClass Array
 */
function get_info_instance($instance_id){

    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_instancia} WHERE id_instancia = $instance_id";
    $result = $DB->get_record_sql($sql_query);

    return $result;
}

/**
 * Función que actualiza los parámetros de una instancia
 * 
 * @see update_info_instance()
 * @param id_instance  ---> ID instancia
 * @param idnumber  ---> Identificador de la instancia
 * @param description  ---> Descripción de la instancia
 * @return stdClass Array
 */
function update_info_instance($id_instance, $idnumber, $description){

    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_instancia} WHERE id_instancia = $id_instance";
    $result = $DB->get_record_sql($sql_query);

    $record = new stdClass();
    $record->id = $result->id;
    $record->id_instance = $id_instance;
    $record->id_number = $idnumber;
    $record->descripcion = $description;

    $result = $DB->update_record('talentospilos_instancia', $record);

    return $result;
}
