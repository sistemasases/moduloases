<?php

require_once(dirname(__FILE__). '/../../../../config.php');

/**
 * Función que recupera los campos de usuario de la tabla {user}
 *
 * @see get_moodle_user($id)
 * @param id_student --> id correspondiente a la tabla {user}
 * @return Array campos
 */
function get_moodle_user($id){
    
    global $DB;
    
    $sql_query = "SELECT SUBSTRING(username FROM 1 FOR 7) AS code, email AS email_moodle, firstname, lastname
                  FROM {user} WHERE id = $id";
                  
    $user = $DB->get_record_sql($sql_query);
    
    return $user;
}

/**
 * Función que recupera el id del jefe de un usuario
 *
 * @see get_boss_user($id_user)
 * @param $id_user --> id correspondiente a la tabla {user}
 * @return Array con los campos de usuario
 */
 
 function get_boss_user($id_user){
     
     global $DB;
     
     
     
 }
 
 /**
 * Función que actualiza el rol de un usuario en particular
 *
 * @see update_role_user($id_moodle_user, $id_role, $state, $id_semester, $username_boss){
 * @return Entero
 */
function update_role_user($username, $role, $idinstancia, $state = 1, $semester = null, $username_boss = null){
    
    global $DB;
    
    $sql_query = "SELECT id FROM {user} WHERE username='$username'";
    $id_user_moodle = $DB->get_record_sql($sql_query);
     
    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol='$role';";
    $id_role = $DB->get_record_sql($sql_query);
    
    $sql_query ="select max(id) as id from {talentospilos_semestre};";
    $id_semester = $DB->get_record_sql($sql_query);
    
    $array = new stdClass;
    $id_boss = null;
    if($username_boss != null){
        $sql_query = "SELECT * FROM {user} WHERE username='$username_boss'";
        $result = $DB->get_record_sql($sql_query);
        $id_boss =  $result->id;
    }
    
    $array->id_rol = $id_role->id;
    $array->id_usuario = $id_user_moodle->id;
    $array->estado = $state;
    $array->id_semestre = $id_semester->id;
    $array->id_jefe = $id_boss;
    $array->id_instancia = $idinstancia;
    
    $result = 0;
    
    if ($checkrole = checking_role($username, $idinstancia)){
        
        if ($checkrole->nombre_rol == 'monitor_ps'){
            $whereclause = "id_monitor = ".$id_user_moodle->id;
            $DB->delete_records_select('talentospilos_monitor_estud',$whereclause);
            
        }else if($checkrole->nombre_rol == 'profesional_ps'){ 
            $whereclause = "id_usuario = ".$id_user_moodle->id;
            $DB->delete_records_select('talentospilos_usuario_prof',$whereclause);
        } 
        
        
        $array->id = $checkrole->id;
        $update_record = $DB->update_record('talentospilos_user_rol', $array);
        if($update_record){
            $result = 3;
        }else{
            $result = 4;
        }
    }else{
        $insert_record = $DB->insert_record('talentospilos_user_rol', $array);
        if($insert_record){
            $result =1;
        }else{
            $result = 2;
        }
    }

    return $result;
}


/**
 * Función que revisa si un usuario tiene un rol asignado
 *
 * @see checking_role($username)
 * @return Boolean
 */
 
function checking_role($username, $idinstancia){
     
    global $DB;
     
    $sql_query = "SELECT id FROM {user} WHERE username = '$username'";
    $id_moodle_user = $DB->get_record_sql($sql_query);
    
    $semestre =  get_current_semester();
    
    $sql_query = "SELECT ur.id_rol as id_rol , r.nombre_rol as nombre_rol, ur.id as id, ur.id_usuario, ur.estado FROM {talentospilos_user_rol} ur INNER JOIN {talentospilos_rol} r ON r.id = ur.id_rol WHERE ur.id_usuario = ".$id_moodle_user->id." and ur.id_semestre = ".$semestre->max." and ur.id_instancia=".$idinstancia.";";
    $role_check = $DB->get_record_sql($sql_query); 
    
    return $role_check;
}