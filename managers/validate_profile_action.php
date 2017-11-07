<?php
require_once (dirname(__FILE__) . '/../../../config.php');

global $user;


/*
* Función que retorna el rol de un usuario 
*
* @param $userid
* @param $instanceid
* @return Array
*/

function get_id_rol($userid,$blockid)
    {
    global $DB;
    $sql_query = "SELECT id_rol FROM {talentospilos_user_rol} WHERE id_usuario='$userid' AND id_instancia='$blockid'";
    $consulta = $DB->get_records_sql($sql_query);
    foreach($consulta as $tomarId)
        {
        $idretornar = $tomarId->id_rol;
        }

    return $idretornar;
    }


/* Función que retorna si un rol de usuario determinado puede hacer una acción
* @see role_is_able($role_id,$action_id)
* @param $role_id --> id del rol
* @param $action_id --> id de la acción
* @return boolean
*/

function role_is_able($role_id,$action_id)
    {
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_permisos_rol} where id_rol='$role_id' and id_accion='$action_id'";
    $consulta = $DB->get_record_sql($sql_query);
    if($consulta){
        return true;
    }else{
        return false;
    }
}

/* Función que retorna arreglo con las acciones que puede realizar un rol cuya funcionalidad es una especifica
* @see get_actions_by_role($id_functionality,$id_role)
* @param $id_functionality --> id del rol
* @param $id_role --> id de la acción
* @return Array
*/
function get_actions_by_role($id_functionality,$id_role){
    global $DB;
    $sql_query = "SELECT id_accion,nombre_accion  FROM {talentospilos_permisos_rol}  permisos INNER JOIN {talentospilos_accion}   accion ON permisos.id_accion = accion.id where id_funcionalidad='$id_functionality' and id_rol='$id_role' and estado=1";
    $consulta = $DB->get_records_sql($sql_query);
    return $consulta;
}
