<?php

require_once(dirname(__FILE__). '/../../../../config.php');

/**
 * Función que recupera los programas académicos almacenados en la tabla talentospilos_programa
 *
 * @see load_programs()
 * @param void
 * @return Array --> Arreglo con los programas académicos (id, codigo_snies, codigo_univalle, nombre, id_sede, jornada, id_facultad)
 */
function load_programs(){
    
    global $DB;
    
    $sql_query = "SELECT * FROM {talentospilos_programa}";
    $array_programs = $DB->get_records_sql($sql_query);
    
    return $array_programs;
}

/**
 * Función que recupera los programas académicos almacenados en la tabla talentospilos_programa que 
 * corresponden a la sede CALI
 *
 * @see load_programs_cali()
 * @param void
 * @return Array --> Arreglo con los programas académicos (id, codigo_snies, codigo_univalle, nombre, id_sede, jornada, id_facultad)
 */
function load_programs_cali(){
    
    global $DB;
    
    $sql_query = "SELECT id FROM {talentospilos_sede} WHERE nombre = 'CALI'";
    $id_cali = $DB->get_record_sql($sql_query)->id;
    
    $sql_query = "SELECT * FROM {talentospilos_programa} WHERE id_sede = $id_cali ORDER BY nombre";
    $array_programs = $DB->get_records_sql($sql_query);
    
    return $array_programs;
}

/**
 * Función que busca un usuario Moodle dado el username
 *
 * @see search_user()
 * @param void
 * @return Array --> Arreglo con los datos del usuario Moodle consultado
 */
 function search_user($username){
     
    global $DB;
    
    $sql_query = "SELECT * FROM {user} WHERE username = $username";
    $array_user = $DB->get_record_sql($sql_query);
    
    return $array_user;
 }