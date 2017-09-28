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

 /**
 * Funcion que evalua si un usuario es monitor o practicante y de ser asi retorna true.
 * 
 * @param $USER
 * @return bool  
 */
function isMonOrPract($USER){
    global $DB;
    
    $id = $USER->id;
    $query_role = "SELECT rol.nombre_rol  FROM {talentospilos_rol} rol INNER JOIN {talentospilos_user_rol} uRol ON rol.id = uRol.id_rol WHERE uRol.id_usuario = $id AND uRol.id_semestre = (SELECT max(id_semestre) FROM {talentospilos_user_rol})";
    $rol = $DB->get_record_sql($query_role)->nombre_rol;

    if($rol != "monitor_ps" && $rol != "practicante_ps"){
        return false;
    }else{
        return true;
    }
}


/**
* Funcion que retorna el rol en ases de un usuario dado el id de moodle.
*
* @param $id
* @return String-> $rol
*/
function get_role_ases($id){
    global $DB;
  
    $query_role = "SELECT rol.nombre_rol  FROM {talentospilos_rol} rol INNER JOIN {talentospilos_user_rol} uRol ON rol.id = uRol.id_rol WHERE uRol.id_usuario = $id AND uRol.id_semestre = (SELECT max(id_semestre) FROM {talentospilos_user_rol})";
    $rol = $DB->get_record_sql($query_role)->nombre_rol;
  
    return $rol;
  }
  