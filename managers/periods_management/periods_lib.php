<?php

require_once(dirname(__FILE__). '/../../../../config.php');


 function get_current_semester_byinterval($fecha_inicio,$fecha_fin){
     
     global $DB;

     $sql_query = "SELECT id  max, nombre FROM {talentospilos_semestre} WHERE fecha_inicio ='$fecha_inicio' and fecha_fin ='$fecha_fin' ";
     $current_semester = $DB->get_record_sql($sql_query);
     return $current_semester;
 }

/**
 * Función que retorna el semestre actual 
 * 
 * @see get_current_semester()
 * @return cadena de texto que representa el semestre actual
 */
 
 function get_current_semester(){
     
     global $DB;

     $sql_query = "SELECT id AS max, nombre FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
     $current_semester = $DB->get_record_sql($sql_query);
     return $current_semester;
 }

 /**
 * Función que retorna el intervalo que define un semestre dado su ID 
 * 
 * @see get_semester_interval($id)
 * @param $id ---> id del semestre
 * @return cadena de texto que representa el semestre actual
 */
 
 function get_semester_interval($id){
     
     global $DB;

     $sql_query = "select * from mdl_talentospilos_semestre where id='$id'";
     $interval = $DB->get_record_sql($sql_query);
     return $interval;
 }


 /**
 * Función que retorna todos los semestres que se encuentran registrados en la base de datos
 * 
 * @see get_all_semesters()
 * @return arreglo que contiene todos los semestres que se han traído de la base de datos * 
 */

 function get_all_semesters(){
     global $DB;

     $sql_query = "SELECT id, nombre, fecha_inicio, fecha_fin FROM {talentospilos_semestre}";
     $all_semesters = $DB->get_records_sql($sql_query);          

     return $all_semesters;
     
 }

 //get_all_semesters();


 /**
 * Función que retorna un semestre dado un identificador
 * @see get_semester_by_id($idSemester)
 * @param $idSemester -> identificador del semestre
 * @return objeto que representa el semestre	 
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
 * Función que actualiza la información de un semestre
 * @see update_semester($semesterInfo, $idSemester)
 * @param $semesterInfo -> arreglo con la nueva información del semestre
 * @param $idSemester -> identificador del semestre
 * @return boolean
 */

 function update_semester($semesterInfo, $idSemester){

     global $DB;

     try{

          $semester = new stdClass();

          $semester->id = (int)$idSemester;
          $semester->nombre = $semesterInfo[1];
          $semester->fecha_inicio = $semesterInfo[2];
          $semester->fecha_fin = $semesterInfo[3];


          $update = $DB->update_record('talentospilos_semestre', $semester);


          return $update;
 
     }catch(Exception $e){
          return $e->getMessage();
     }


 }

 
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


 function create_semester($name, $beginning_date, $ending_date){

     global $DB;

     $newSemester = new stdClass;
     $newSemester->nombre = $name;
     $newSemester->fecha_inicio = $beginning_date;
     $newSemester->fecha_fin = $ending_date;

     $insert = $DB->insert_record('talentospilos_semestre', $newSemester, true);

     return $insert;


 }













