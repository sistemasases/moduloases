<?php

require_once(dirname(__FILE__). '/../../../../config.php');

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