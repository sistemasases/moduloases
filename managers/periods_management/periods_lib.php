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
 * Estrategia ASES
 *
 * @deprecated
 * @author     Juan Pablo Moreno Muñoz
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2017 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @copyright  2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated
 */
/**
 * This lib is to not be used in any place.
 * Pleace @see core/periods/periods.php
 */

require_once(dirname(__FILE__). '/../../../../config.php');


 /**
 * Function that returns the current semester in a given interval
 * 
 * @see get_current_semester_byinterval($fecha_inicio,$fecha_fin)
 * @param $fecha_inicio ---> starting date
 * @param $fecha_fin ---> ending date
 * @return object that represents the semester within the given interval
 * @deprecated @see core_periods_get_all_periods()
 */

 function get_current_semester_byinterval($fecha_inicio,$fecha_fin){
    global $DB;

    $sql_query = "SELECT id  max, nombre FROM {talentospilos_semestre} WHERE fecha_inicio ='$fecha_inicio' and fecha_fin ='$fecha_fin' ";
    $current_semester = $DB->get_record_sql($sql_query);
    return $current_semester;
 }

 /**
 * Function that returns the current semester in a given approximate interval
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @param string $start_date With postgres fortmat YYYY-MM-DD
 * @param string $end_date With postgres fortmat YYYY-MM-DD
 * @return int $to_return id_semester
 * @deprecated 
 */

function periods_management_get_current_semester_by_apprx_interval( $start_date, $end_date ){
     
    global $DB;

    $sql = "SELECT id 
                    FROM {talentospilos_semestre} 
                    WHERE fecha_inicio <= '$start_date' 
                    AND fecha_fin >= '$end_date'";

    $to_return = $DB->get_record_sql( $sql );
    if( $to_return ){
        return $to_return->id;
    }else{
        return null;
    }

}

/**
 * Function that returns the current semester
 * ## Fields returned
 * - max: rename for semestre.id
 * - semestre.nombre
 * @deprecated 5.3 No longer used because its return does not comply with the no modification policy, please @see periods_get_current_semester().
 * @return object that represents the current semester
 */
 
// function get_current_semester(){
//     
//     global $DB;
//
//     $sql_query = "SELECT id AS max, nombre FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
//     $current_semester = $DB->get_record_sql($sql_query);
//     return $current_semester;
// }

/**
 * Función que retorna la fecha de inicio del semestre actual
 * @see get_current_semester()
 * @return cadena de texto que representa la fecha de inicio del semestre actual
 * @deprecated Refactorizar llamados a esta función.
 * @todo delete this function, no other calls to it.
 */
//function get_current_semester_start(){
//    global $DB;
//    $sql_query = "SELECT fecha_inicio AS fecha FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
//    $current_semester = $DB->get_record_sql($sql_query);
//    return $current_semester;
//}


 /**
 * Function that returns the current semester.
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>.
 * @return object that represents the current semester.
 * @return null if are no semesters registered.
 * @deprecated @see core_periods_get_current_period on core/periods/periods.php
 */
 
function periods_get_current_semester(){
     
    global $DB;

    $sql_query = "SELECT id, nombre, fecha_inicio, fecha_fin 
    FROM {talentospilos_semestre} 
    WHERE id = (
        SELECT MAX(id) 
        FROM {talentospilos_semestre}
    )";
    
    $current_semester = $DB->get_record_sql($sql_query);
    return $current_semester;
}

 /**
 * Function that returns the interval that represents a semester by its ID
 * 
 * @see get_semester_interval($id)
 * @param $id ---> semester's id
 * @return object that represents the semester 
 * @deprecated
 */
 
 function get_semester_interval($id){
     
     global $DB;

     $sql_query = "select * from mdl_talentospilos_semestre where id='$id'";
     $interval = $DB->get_record_sql($sql_query);
     return $interval;
 }


 /**
 * Function that returns all registered semesters
 * 
 * @see get_all_semesters()
 * @return array that contains every semester registered on the DataBase
 * @deprecated @see core_periods_get_all_periods on core/periods/periods.php
 * @todo delete this function, no other calls to it.
 */

 function get_all_semesters(){
     global $DB;

     $sql_query = "SELECT id, nombre, fecha_inicio, fecha_fin FROM {talentospilos_semestre}";
     $all_semesters = $DB->get_records_sql($sql_query);   
     return $all_semesters;
     
 }

 //get_all_semesters();


 /**
 * Function that returns a semester given its id
 * 
 * @see get_semester_by_id($idSemester)
 * @param $idSemester -> semester's id
 * @return object that represents certain information about the specific semester
 * @deprecated @see core_periods_get_period_by_id on core/periods/periods.php
 * @todo delete.
 */


 function get_semester_by_id($idSemester){
     global $DB;

     $sql_query = "SELECT nombre, fecha_inicio, fecha_fin FROM {talentospilos_semestre} WHERE id = '$idSemester'";
     $info_semester = $DB->get_record_sql($sql_query);
     setlocale(LC_TIME, "es_CO");     
     $info_semester->fecha_inicio = strftime("%d %B %Y", strtotime($info_semester->fecha_inicio));
     $info_semester->fecha_fin = strftime("%d %B %Y", strtotime($info_semester->fecha_fin));


     return $info_semester;


 }

 /**
 * Function which updates the information of a semester
 * 
 * @see update_semester($semesterInfo, $idSemester)
 * @param $semesterInfo -> array with the new information of a semester
 * @param $idSemester -> semester's id
 * @return boolean true if it was updated, false it wasn't
 * @deprecated
 */

 function update_semester($semesterInfo, $idSemester){
	 
     global $DB;

     try{

          $semester = new stdClass();

          $semester->id = (int)$idSemester;
          $semester->nombre = $semesterInfo[1];
          $semester->fecha_inicio = $semesterInfo[2];
          $semester->fecha_fin = $semesterInfo[3];

          if($semester->id == 0){
            trigger_error('ASES Notificacion: actualizar semestre en la BD con id 0');
            return -1;
        }
          $update = $DB->update_record('talentospilos_semestre', $semester);


          return $update;
 
     }catch(Exception $e){
        return $e->getMessage();
     }


 }

 /**
 * Function that returns every semester, change its language and date-format to spanish 
 * 
 * @see get_all_semesters_table()
 * @return array
 * @deprecated
 * @todo delete, no more calls to this function.
 */
 
 function get_all_semesters_table(){
     global $DB;

     $array_semesters = array();

     $sql_query = "SELECT id, nombre, fecha_inicio, fecha_fin FROM {talentospilos_semestre}";
     $all_semesters = $DB->get_records_sql($sql_query);

     setlocale(LC_TIME, "es_CO");

     $length_array = count($all_semesters);

     foreach ($all_semesters as $semester) {
          $all_semesters[$semester->id]->fecha_inicio = strftime("%d %B %Y", strtotime($all_semesters[$semester->id]->fecha_inicio));
          $all_semesters[$semester->id]->fecha_fin = strftime("%d %B %Y", strtotime($all_semesters[$semester->id]->fecha_fin));                             
     }

     foreach ($all_semesters as $semester) {
          array_push($array_semesters, $semester);
     }

     return $array_semesters;

 }

/**
 * Function which creates a new semester
 * 
 * @see create_semester($name, $beginning_date, $ending_date)
 * @param $name -> name of the semester
 * @param $beginning_date -> semester's starting date
 * @param $ending_date -> semester's ending date
 * @return number
 * @deprecated @see core_periods_create_period
 */

 function create_semester($name, $beginning_date, $ending_date){
     global $DB;

     $newSemester = new stdClass;
     $newSemester->nombre = $name;
     $newSemester->fecha_inicio = $beginning_date;
     $newSemester->fecha_fin = $ending_date;

     $insert = $DB->insert_record('talentospilos_semestre', $newSemester, true);

     return $insert;
 }

 /**
  * Function that returns the semester id given its name
  * 
  * @see get_semester_id_by_name($semester_name)
  * @param $semester_name -> name of the semester to be found
  * @deprecated
  * @return Integer
  * @todo delete.
  */
 function get_semester_id_by_name($semester_name){
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_semestre} WHERE nombre = '$semester_name'";
    $result = $DB->get_record_sql($sql_query);

    if($result){

        $semester_id = $result->id;
        return $semester_id;

    }else{

        return false;

    }
}

 /**
 * Functions that returns all stored semesters.
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @return array
 * @deprecated @see core_periods_get_all_periods()
 * @todo delete.
 */

function periods_management_get_all_semesters(){
    global $DB;
    $sql = "SELECT * FROM {talentospilos_semestre}";

    return $DB->get_records_sql( $sql );

}
