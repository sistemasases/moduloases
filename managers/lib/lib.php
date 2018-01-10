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

  

/**
* Funcion que retorna un select con los estudiantes asignados a un usuario de rol profesional, practicante, o monitor y "ROL NO PERMITIDO" si es otro rol.
*
* @param $id
* @return String-> $select
*/
function make_select_ficha($id){
    global $DB;
  
    $rol = get_role_ases($id);

    $asign = "<select name = 'asignados' id = 'asignados'><option>Seleccione un estudiante</option>";

    if($rol == 'profesional_ps'){
        $asign .= process_info_assigned_students(get_asigned_by_profesional($id));

    }elseif($rol == 'practicante_ps'){
        $asign .= process_info_assigned_students(get_asigned_by_practicante($id));

    }elseif($rol == 'monitor_ps'){
        $asign .= process_info_assigned_students(get_asigned_by_monitor($id));
    }else{
        $asign = "ROL NO PERMITIDO";
        return $asign;
    }
    $asign .= "</select>";
    return $asign;
  }

/**
* Funcion que retorna los estudiantes asignados a un usuario de rol monitor
*
* @param $id
* @return String-> $asign
*/

function get_asigned_by_monitor($id){
    global $DB;

    $query = "SELECT user_m.username, user_m.firstname, user_m.lastname
              FROM {user} user_m
              INNER JOIN {user_info_data} data ON data.userid = user_m.id
              INNER JOIN {user_info_field} field ON data.fieldid = field.id
              INNER JOIN {talentospilos_monitor_estud} mon_es ON data.data = CAST(mon_es.id_estudiante AS VARCHAR)
              WHERE mon_es.id_monitor = $id AND field.shortname = 'idtalentos'";

    $result = $DB->get_records_sql($query);
    
    return $result;
}

//print_r(get_asigned_by_monitor(76));

/**
* Funcion que retorna los estudiantes asignados a un usuario de rol practicante
*
* @param $id
* @return String-> $asign
*/

function get_asigned_by_practicante($id){
    global $DB;
    $query = "SELECT rol.id_usuario
              FROM {talentospilos_user_rol} rol
              WHERE rol.id_jefe = $id";

    $students = array();

    $result = $DB->get_records_sql($query);

    foreach($result as $id_mon){
        $students = array_merge($students, get_asigned_by_monitor($id_mon->id_usuario));
    }
    return $students;
}

//print_r(get_asigned_by_practicante(121));

/**
* Funcion que retorna los estudiantes asignados a un usuario de rol profesional
*
* @param $id
* @return String-> $asign
*/

function get_asigned_by_profesional($id){
    global $DB;
    $query = "SELECT rol.id_usuario
              FROM {talentospilos_user_rol} rol
              WHERE rol.id_jefe = $id";
    
    $students = array();

    $result = $DB->get_records_sql($query);

    foreach($result as $id_prac){
        $students = array_merge($students, get_asigned_by_practicante($id_prac->id_usuario));
    }
    return $students;
}

//print_r(get_asigned_by_profesional(122));


function process_info_assigned_students($array_students){
    $assign = "";

    foreach($array_students as $student){
        $assign .= "<option>$student->username $student->firstname $student->lastname </option>";
    }
    return $assign;

}



/**
* Función que retorna el semestre actual considerando la fecha actual
*
* @param void
* @return semester array object or zero if error
*/

function get_current_semester_today(){

    global $DB;
    
    date_default_timezone_set('America/Bogota');
    $today = time();

    $sql_query = "SELECT * FROM {talentospilos_semestre}";
    $array_periods = $DB->get_records_sql($sql_query);
    
    foreach($array_periods as $period){
        if(strtotime($period->fecha_inicio) < $today && strtotime($period->fecha_fin) > $today){
            return $period;
        }
    }

    return 0;
}  

