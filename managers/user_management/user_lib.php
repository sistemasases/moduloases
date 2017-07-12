<?php

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