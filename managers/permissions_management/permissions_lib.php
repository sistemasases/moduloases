<?php

require_once(dirname(__FILE__). '/../../../../config.php');

/**
 * Función que obtiene los perfiles de la tabla {talentospilos_perfil}
 * @see get_profiles()
 * @return Array
 **/

function get_profiles(){
	global $DB;

    $sql_query = "SELECT * FROM {talentospilos_perfil}";
    return $DB->get_records_sql($sql_query);
	}

/**
 * Función que obtiene las acciones de la tabla {talentospilos_accion}
 * @see get_actions()
 * @return Array
 **/

function get_actions(){
	global $DB;

    $sql_query = "SELECT * FROM {talentospilos_accion} WHERE estado=1 order by nombre_accion asc";
    return $DB->get_records_sql($sql_query);
	}

function get_user_profiles(){

    global $DB;

    $sql_query = "SELECT usuario_perfil.id ,id_perfil,nombre_perfil,id_usuario,firstname,lastname,id_semestre, semestre.nombre
        FROM {talentospilos_usuario_perfil} as usuario_perfil
        INNER JOIN {talentospilos_perfil} as perfil ON usuario_perfil.id_perfil = perfil.id
        INNER JOIN {user} as usuario ON usuario_perfil.id_usuario = usuario.id
        INNER JOIN {talentospilos_semestre} as semestre ON usuario_perfil.id_semestre = semestre.id
        where usuario_perfil.estado=1";
    return $DB->get_records_sql($sql_query);
      
    }

/**
 * Función que retorna las acciones en el sistema 
 * @see get_actions_table()
 * @return Array
 **/

function get_actions_table()
{
    global $DB;
    $array = Array();
    $actions_array = get_actions();

    foreach ($actions_array as $action){
        $action->button = "<a id = \"delete_action\"  ><span  id=\"".$action->id."\" class=\"red glyphicon glyphicon-remove\"></span></a>";
        array_push($array, $action);
    }
    return $array;
}


function get_user_profile_table(){
    global $DB;
    $array = Array();
    $user_profiles =get_user_profiles();
    foreach ($user_profiles as $profile){
       $profile->button = "<a id = \"delete_user_profile\"  ><span  id=\"".$profile->id."\" class=\"red glyphicon glyphicon-remove\"></span></a>";
        array_push($array, $profile);
    }
    return $array;
}

/**
 * Función que retorna los perfiles en el sistema 
 * @see get_profiles_table()
 * @return Array
 **/

function get_profiles_table()
{
    global $DB;
    $array = Array();
    $profiles_array = get_profiles();

    foreach ($profiles_array as $profile){
        $profile->button = "<a id = \"delete_profiles\"  ><span  id=\"".$profile->id."\" class=\"red glyphicon glyphicon-remove\"></span></a>";
        array_push($array, $profile);
    }
    return $array;
}


/**
 * Función que obtiene de la tabla {talentos_perfil_accion} los perfiles que estan sujetos a alguna accion agrupados por el perfil
 * @see get_actions()
 * @return Array
 **/

function get_profiles_action(){
    global $DB;

    $sql_query = "select * from {talentospilos_perfil}";
    return $DB->get_records_sql($sql_query);
    }




/**
 * Función que obtiene la relacion action-perfil de la tabla {talentospilos_perfil_accion}
 * @see get_actions()
 * @return Array
 **/

function get_action_profile(){
    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_perfil_accion} WHERE habilitado=1";
    return $DB->get_records_sql($sql_query);
    }





/**
 * Función que retorna la relacion action-perfil en el sistema 
 * @see get_actions()
 * @return Array
 **/

function get_action_profile_table()
{
    global $DB;
    $array = Array();
    $profiles = get_profiles();
    $actions = count(get_actions());


    foreach ($profiles as $profile){
        
        $profile->button = "<a id = \"delete_action\"  ><span  id=\"".$profile->id."\" class=\"yellow glyphicon glyphicon-pencil\"></span></a>";
        array_push($array, $profile);
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

function delete_record($id,$source)
{
    global $DB;
    $record = new stdClass();
    $record->id = $id;
    $paso =1;
    try{
    if($source =='accion' or $source=='usuario_perfil'){
      $record->estado =false;
      $paso =$DB->update_record('talentospilos_'.$source, $record);

    }else if ($source =='perfil_accion'){
      $record->habilitado =false;
      $paso =$DB->update_record('talentospilos_'.$source, $record);
    }

    if($paso ==1){
    $msg =new stdClass();
    $msg->title ="Éxito";
    $msg->text = "Se eliminó satisfactoriamente el registro";
    $msg->type = "success";

    }else{

    $msg->title ="Error";
    $msg->text = "No se pudo eliminar el registro seleccionado";
    $msg->type = "error";

    }

    return $msg;
    }catch(Exception $ex){

    $msg->title ="Inconveniente !";
    $msg->text = $ex;
    $msg->type = "error";
    return  $msg;
    }
}



?>