<?php

require_once(dirname(__FILE__) . '/../../../../config.php');


//Funcionalidades.

/**
 * Función que obtiene las funcionalidades de la tabla {talentospilos_funcionalidades}
 * @see get_functions()
 * @return Array
 **/

function get_functions()
{
    global $DB;
    
    $sql_query = "SELECT * FROM {talentospilos_funcionalidad} ";
    return $DB->get_records_sql($sql_query);
}

/**
 * Función que retorna las funcionalidades con el campo eliminar en el sistema 
 * @see get_functions_table()
 * @return Array
 **/

function get_functions_table()
{
    global $DB;
    $array           = Array();
    $functions_array = get_functions();
    
    foreach ($functions_array as $function) {
        $function->button = "<a id = \"modify_function\"  ><span  id=\"" . $function->id . "\" class=\"red glyphicon glyphicon-pencil\"></span></a>";
        array_push($array, $function);
    }
    return $array;
}

/**
 * Función que obtiene las funcionalidades de la tabla {talentospilos_funcionalidades} por nombre
 * @see get_functions_by_name($name)
 * @param name --> nombre de la funcionalidad
 * @return Object
 **/

function get_functions_by_name($name)
{
    global $DB;
    
    
    $sql_query = "SELECT * FROM {talentospilos_funcionalidad} where  nombre_func='$name'";
    return $DB->get_record_sql($sql_query);
}



//Acciones.

/**
 * Función que obtiene las accion de la tabla {talentospilos_accion} por id
 * @see get_action_by_id($id)
 * @param id ---> id de acción
 * @return Object
 **/

function get_action_by_id($id)
{
    global $DB;
    
    $sql_query = "SELECT * FROM {talentospilos_accion} WHERE id ='$id'";
    return $DB->get_record_sql($sql_query);
}

/**
 * Función que obtiene las acciones de la tabla {talentospilos_accion}
 * @see get_actions()
 * @return Array
 **/

function get_actions()
{
    global $DB;
    
    $sql_query = "SELECT * FROM {talentospilos_accion} WHERE estado=1 order by nombre_accion asc";
    return $DB->get_records_sql($sql_query);
}

/**
 * Función que obtiene las acciones relacionadas a una funcionalidad de la tabla {talentospilos_accion}
 * @see get_actions()
 * @return Array
 **/

function get_actions_function($funcionalidad)
{
    global $DB;
    
    $sql_query = "SELECT * FROM {talentospilos_accion} WHERE estado=1 and id_funcionalidad=" . $funcionalidad;
    return $DB->get_records_sql($sql_query);
}

/**
 * Función que retorna si el id de la acción pertenece a una determinada funcionalidad {talentospilos_accion} y {talentospilos_funcionalidad}
 * @see is_action_in_functionality($id_action,$id_functionality)
 * @param $id_action --> id de la acción
 * @param $id_functionality --> id de la funcionalidad
 * @return boolean
 **/

function is_action_in_functionality($id_action,$id_functionality)
{
    global $DB;
    
    $sql_query = "SELECT * FROM {talentospilos_accion} where id_funcionalidad='$id_functionality' and id='$id_action'";
    $exist = $DB->get_record_sql($sql_query);
    if($exist){
        return true;
    }else{
        return false;
    }
}



/**
 * Función que retorna las acciones con el campo eliminar en el sistema 
 * @see get_actions_table()
 * @return Array
 **/

function get_actions_table()
{
    global $DB;
    $array         = Array();
    $actions_array = get_actions();
    
    foreach ($actions_array as $action) {
        $action->button = "<a id = \"delete_action\"  ><span  id=\"" . $action->id . "\" class=\"red glyphicon glyphicon-remove\"></span></a>";
        array_push($array, $action);
    }
    return $array;
}


//Rol.

/**
 * Función que obtiene los roles de la tabla {talentospilos_rol}
 * @see get_roles()
 * @return Array
 **/

function get_roles()
{
    global $DB;
    
    $sql_query = "SELECT * FROM {talentospilos_rol}";
    return $DB->get_records_sql($sql_query);
}

/**
 * Función que obtiene los registros de la tabla {talentospilos_permisos_rol} dado un rol 
 * @see  get_functions_by_role($id_role)
 * @param $id_role
 * @return Array
 **/

function get_functions_by_role($id_role)
{
    global $DB;
    
    $sql_query      = "SELECT * FROM {talentospilos_permisos_rol} where id_rol=" . $id_role;
    $consult        = $DB->get_records_sql($sql_query);
    $array_selected = array();
    foreach ($consult as $record) {
        array_push($array_selected, $record->id_accion);
    }
    
    return $array_selected;
}

/**
 * Función que retorna los roles con el campo eliminar existentes en el sistema 
 * @see get_roles_table()
 * @return Array
 **/

function get_roles_table()
{
    global $DB;
    $array       = Array();
    $roles_array = get_roles();
    
    foreach ($roles_array as $role) {
        $role->button = "<a id = \"delete_profiles\"  ><span  id=\"" . $role->id . "\" class=\"red glyphicon glyphicon-pencil\"></span></a>";
        array_push($array, $role);
    }
    return $array;
}



/**
 * Función cambia de estado a un registro de acciones, perfil 
 * @see delete_record($id,$source)
 * @param $id ---> id del registro a eliminar
 * @param $source --> string para identificar en que tabla se elimina el registro
 * @return Array
 **/

function delete_record($id, $source)
{
    global $DB;
    $record     = new stdClass();
    $record->id = $id;
    $paso       = 1;
    try {
        if ($source == 'accion' or $source == 'usuario_perfil') {
            $record->estado = false;
            $paso           = $DB->update_record('talentospilos_' . $source, $record);
            
        } else if ($source == 'perfil_accion') {
            $record->habilitado = false;
            $paso               = $DB->update_record('talentospilos_' . $source, $record);
        }
        
        if ($paso == 1) {
            $msg        = new stdClass();
            $msg->title = "Éxito";
            $msg->text  = "Se eliminó satisfactoriamente el registro";
            $msg->type  = "success";
            
        } else {
            
            $msg->title = "Error";
            $msg->text  = "No se pudo eliminar el registro seleccionado";
            $msg->type  = "error";
            
        }
        
        return $msg;
    }
    catch (Exception $ex) {
        
        $msg->title = "Inconveniente !";
        $msg->text  = $ex;
        $msg->type  = "error";
        return $msg;
    }
}



?>