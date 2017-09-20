<<<<<<< HEAD
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


=======
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


>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
