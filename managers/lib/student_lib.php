<?php

require_once(dirname(__FILE__). '/../../../../config.php');

/**
 * Función que recupera los campos de usuario de la tabla {talentospilos_usuario}
 *
 * @see get_ases_user($id)
 * @param id_student --> id correspondiente a la tabla {talentospilos_usuario}
 * @return Array campos
 */
function get_ases_user($id){
    
    global $DB;
    
    $sql_query = "SELECT num_doc, tipo_doc, (now() - fecha_nac)/365 AS age, estado, estado_ases, direccion_res, tel_ini, tel_res, celular, emailpilos, acudiente, tel_acudiente, estado_ases, observacion  FROM {talentospilos_usuario} WHERE id = $id";
    $user = $DB->get_record_sql($sql_query);
    
    return $user;
}

/**
 * Función que recupera el ID de la tabla usuario de Moodle dado el ID de la tabla {talentospilos_usuario}
 *
 * @see get_id_user_moodle($id_student)
 * @param id_student --> id correspondiente a la tabla {talentospilos_usuario}
 * @return id moodle
 */
function get_id_user_moodle($id_student){
     
    global $DB;

    $sql_query = "SELECT id FROM {user_info_field} WHERE shortname = 'idtalentos'";
    $id_field = $DB->get_record_sql($sql_query)->id;
    
    $sql_query = "SELECT MAX(userid) AS userid FROM {user_info_data} WHERE fieldid = $id_field AND data = '$id_student'";
                  
    $id_user_moodle = $DB->get_record_sql($sql_query)->userid;

    return $id_user_moodle;
}

/**
 * Función que recupera el ID de la tabla usuario de Moodle dado el ID de la tabla {talentospilos_usuario}
 * SI ESTE SE ENCUENTRA ACTIVO
 *
 * @see get_id_user_moodle_active($id_student)
 * @param id_student --> id correspondiente a la tabla {talentospilos_usuario}
 * @return id moodle
 */
function get_id_user_moodle_active($id_student){
     
    global $DB;

    return $id_user_moodle;
}

/**
 * Función que recupera el usuario ASES dado el código de estudiante asociado al nombre de usuario de Moodle
 *
 * @see get_ases_user_by_code($code)
 * @param $username --> Código asociado al nombre de usuario de Moodle
 * @return Array --> Campos de la tabla talentos usuario asociados al nombre de usuario de Moodle ingresado
 */
function get_ases_user_by_code($code){
    
    global $DB;
    
    $sql_query = "SELECT MAX(id) as id_moodle FROM {user} WHERE username LIKE '".$code."%';";

    $id_moodle = $DB->get_record_sql($sql_query)->id_moodle;
    
    $id_ases = get_adds_fields_mi($id_moodle)->idtalentos;
    
    $sql_query = "SELECT *, (now() - fecha_nac)/365 AS age FROM {talentospilos_usuario} WHERE id =".$id_ases;
    
    $ases_user = $DB->get_record_sql($sql_query);
    
    return $ases_user;
}


/**
 * Función que recupera el estado ASES de un estudiante
 *
 * @see get_student_ases_status($id)
 * @param $id --> Código asociado al estudiante en la tabla talentospilos_usuario
 * @return Array --> Información del estado Ases del estudiante
 */

 function get_student_ases_status($id_student){
    global $DB;

    $sql_query = "SELECT MAX(id) FROM {talentospilos_est_estadoases} WHERE id_estudiante = $id_student";
    $id_ases_status = $DB->get_record_sql($sql_query)->max;

    $sql_query = "SELECT * FROM {talentospilos_est_estadoases} WHERE id = $id_ases_status";
    $id_status = $DB->get_record_sql($sql_query)->id_estado_ases;

    if($id_ases_status){
        $sql_query = "SELECT * FROM {talentospilos_estados_ases} WHERE  id = $id_status";
        $status_ases = $DB->get_record_sql($sql_query);
    }else{
        $status_ases = "NO REGISTRA";
    }    

    return $status_ases;
 }

 /**
 * Función que recupera el estado ICETEX de un estudiante
 *
 * @see get_student_icetex_status($id_student)
 * @param $id_student --> Código asociado al estudiante en la tabla talentospilos_usuario
 * @return Array --> Información del estado Ases del estudiante
 */

  function get_student_icetex_status($id_student){
    global $DB;

    $sql_query = "SELECT MAX(id) FROM {talentospilos_est_est_icetex} WHERE id_estudiante = $id_student";
    $id_icetex_status = $DB->get_record_sql($sql_query)->max;

    $sql_query = "SELECT * FROM {talentospilos_est_est_icetex} WHERE id = $id_icetex_status";
    $id_status = $DB->get_record_sql($sql_query)->id_estado_icetex;

    if($id_icetex_status){
        $sql_query = "SELECT * FROM {talentospilos_estados_icetex} WHERE  id = $id_status";
        $status_icetex = $DB->get_record_sql($sql_query);
    }else{
        $status_icetex = "NO REGISTRA";
    }    

    return $status_icetex;
 }

/**
 * Función que recupera los datos adicionales de un estudiante dado el ID de la tabla {user}
 *
 * @see get_adds_fields_mi($id_student)
 * @param id_student --> id correspondiente a la tabla {user}
 * @return stdClass --> campos adicionales del usuario moodle 
 */
 
function get_adds_fields_mi($id_student){
     
    global $DB;
    $sql_query = "SELECT field.shortname, data.data 
                  FROM {user_info_data} AS data INNER JOIN {user_info_field} AS field ON data.fieldid = field.id 
                  WHERE data.userid = $id_student";
    
    $result = $DB->get_records_sql($sql_query);
     
    $array_result = new stdClass();
    $array_result->idtalentos = $result['idtalentos']->data;
    $array_result->idprograma = $result['idprograma']->data;
    $array_result->estado = $result['estado']->data;
    
    return $array_result;
}

/**
 * Función que recupera los datos de un programa académico dado su ID
 *
 * @see get_program($id_program)
 * @param id --> id del programa
 * @return Array program
 */
function get_program($id){

    global $DB;

    $program = $DB->get_record_sql("SELECT * FROM  {talentospilos_programa} WHERE id=".$id.";"); 

    return $program;
}

/**
 * Función que recupera la facultad dado un id de facultad
 *
 * @see get_faculty($id)
 * @param id --> id correspondiente a la facultad
 * @return Array faculty
 */
function get_faculty($id){

    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_facultad} WHERE id=".$id;
    $result = $DB->get_record_sql($sql_query);

    return $result;
}

/**
 * Función que recupera la cohorte de un estudiante
 *
 * @see get_cohort_by_student($id_student)
 * @param $id_student --> id correspondiente a la facultad
 * @return Cohort Array
 */
function get_cohort_student($id_student){
    
    global $DB;

    $sql_query = "SELECT MAX(id) AS id FROM {cohort_members} WHERE userid = $id_student;";
    $id_cohort_member = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT cohortid FROM {cohort_members} WHERE id = $id_cohort_member";
    $id_cohort = $DB->get_record_sql($sql_query)->cohortid;
    
    $sql_query = "SELECT name, idnumber FROM {cohort} WHERE id = $id_cohort;";
    $cohort = $DB->get_record_sql($sql_query);
    
    return $cohort;
}

 /**
 * Función que retorna los nombres, apellidos y correo electrónico del monitor asignado a un estudiante, dado el ID del estudiante.
 *
 * @see get_assigned_monitor($id_student)
 * @parameters $id_student int ID relacionado en la tabla {talentospilos_usuario}
 * @return Array --> Contiene los nombres, apellidos y el email del monitor asignado a un estudiante.
 */
function get_assigned_monitor($id_student){
     
    global $DB;
    
    $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =".$id_student.";";
    $id_monitor = $DB->get_record_sql($sql_query)->id_monitor;
    
    if($id_monitor){
        $sql_query = "SELECT id, firstname, lastname, email FROM {user} WHERE id = ".$id_monitor;
        $monitor_object = $DB->get_record_sql($sql_query);
    }else{
        $monitor_object = array();
    }

    return $monitor_object;
}

/**
* Función que retorna los nombres, apellidos y correo electrónico del practicante asignado a un estudiante, dado el ID del estudiante.
*
* @see get_assigned_pract($id_student)
* @parameters $id_student int Id relacionado en la tabla {talentospilos_usuario}
* @return Array --> Contiene los nombres, apellidos y el email del practicante asignado a un estudiante.
*/
function get_assigned_pract($id_student){
     
     global $DB;
     
     $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =".$id_student.";";
     $id_monitor = $DB->get_record_sql($sql_query)->id_monitor;
     
     if($id_monitor){
         $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_monitor.";";
         $id_trainee = $DB->get_record_sql($sql_query)->id_jefe; 

         if($id_trainee){
            $sql_query = "SELECT id, firstname, lastname, email FROM {user} WHERE id = ".$id_trainee;
            $trainee_object = $DB->get_record_sql($sql_query);
         }else{
            $trainee_object = array();
         }
     }else{
         $trainee_object = array();
     }
    
     return $trainee_object;
}

/**
 * Función que retorna los nombres, apellidos y correo electrónico del profesional asignado a un estudiante, dado el ID del estudiante.
 *
 * @see get_assigned_professional($id_student)
 * @param$id_student int Id relacionado en la tabla {talentospilos_usuario}
 * @return Array --> Contiene los nombres, apellidos y el email del profesional asignado a un estudiante
 */
function get_assigned_professional($id_student){
     
     global $DB;
     
     $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =".$id_student.";";
     $id_monitor = $DB->get_record_sql($sql_query)->id_monitor;
     
     if($id_monitor){

         $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_monitor.";";
         $id_trainee = $DB->get_record_sql($sql_query)->id_jefe; 
         
         if($id_trainee){
            $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_trainee.";";
            $id_professional = $DB->get_record_sql($sql_query)->id_jefe;

            if($id_professional){
                $sql_query = "SELECT id, firstname, lastname, email FROM {user} WHERE id = ".$id_professional.";";
                $professional_object = $DB->get_record_sql($sql_query);    
            }else{
                $professional_object = array();   
            }            
         }else{
            $professional_object = array();   
         }
     }else{
         $professional_object = array();
     }
     
     return $professional_object;
 }
 
 /**
 * Función que retorna un arreglo de los riesgos asociados a un estudiante dado el ID del usuario de la tabla {talentospilos_usuario}
 *
 * @see get_risk_by_student($id_student)
 * @param $id_student int Id relacionado en la tabla {talentospilos_usuario}
 * @return Array --> Contiene los nombres, apellidos y el email del profesional asignado a un estudiante
 */
 
function get_risk_by_student($id_student){
    
    global $DB;

    $sql_query = "SELECT riesgo.nombre, r_usuario.calificacion_riesgo
                  FROM {talentospilos_riesg_usuario} AS r_usuario INNER JOIN {talentospilos_riesgos_ases} AS riesgo ON r_usuario.id_riesgo = riesgo.id 
                  WHERE r_usuario.id_usuario = $id_student AND riesgo.nombre <> 'geografico'";
                  
    $array_risk = $DB->get_records_sql($sql_query);
    
    return $array_risk;
}

 /**
 * Función que un objeto USER de moodle dado un id
 *
 * @see get_full_user($id)
 * @parameters $id int Id relacionado en la tabla {user}
 * @return Object --> Contiene toda la informacion de un usuario en la tabla {user}
 */
 
function get_full_user($id){
    global $DB;
    
    $sql_query = "SELECT * FROM {user} WHERE id= ".$id;
    $user = $DB->get_record_sql($sql_query);
    
    return $user;
}