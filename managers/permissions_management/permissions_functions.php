<?php
require_once('permissions_lib.php');


/**
 * Función que obtiene un select con un array dado.
 * @see get_profiles_table()
 * @param $profiles --> array 
 * @return String
 **/
function get_profiles_select($profiles,$nombre_perfil){
	$table = "";
 	$table.='<select class="form-pilos" id="'.$nombre_perfil.'">';
    foreach($profiles as $profile){
            $table.='<option value="'.$profile->id.'">'.$profile->nombre_perfil.'</option>';
     }
    $table.='</select>';
    return $table;

}

/**
 * Función que obtiene un select con un array dado.
 * @see get_actions_table()
 * @param $actions --> array 
 * @return String
 **/
function get_actions_select($actions){
	$table = "";
 	$table.='<select class="form-pilos" id="actions">';
    foreach($actions as $action){
            $table.='<option value="'.$action->id.'">'.$action->nombre_accion.'</option>';
     }
    $table.='</select>';
    return $table;

}





?>
