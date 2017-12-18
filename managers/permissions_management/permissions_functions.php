<?php
require_once('permissions_lib.php');


/**
 * Función que crea variables dinamicamente deacuerdo al nombre de las acciones 
 que involucran una funcionalidad especifica.

 * @see create_variablebyname($validation)
 * @return void
 **/

 function create_variablebyname($validation){

    $array_variable =[];

    foreach ($validation as $key => $value) {
        
        ${$value->nombre_accion} = true;
        
        $name        = $value->nombre_accion;
        $data->$name = $name;
        array_push($array_variable,$name);
        array_push($array_variable,$data->$name);
    }

    return $array_variable;

 }


/**
 * Función que obtiene las funcionalidades y sus acciones asociadas
 * @see get_functions_actions()
 * @return String
 **/

function get_functions_actions($rol){
    $table = "";
    $functions = get_functions();


    foreach($functions as $function){
        $table .=' <div class="col-lg-3 col-md-3"><fieldset id="'.$function->id.'"><legend>'.$function->nombre_func.'</legend>';
        $actions = get_actions_function($function->id);
        foreach($actions as $action){
            if($rol=='sistemas' && $function->nombre_func=='create_action'){

            }else{
           $table.='<input type="checkbox" name="actions[]" "="" value="'.$action->id.'">'.$action->nombre_accion.'</br>';
            }
        }
        $table.='</div>';

    }
    return $table;
}



/**
 * Función que obtiene un select con un array dado.
 * @see get_roles_select($roles,$nombre_rol)
 * @param $roles --> array 
 * @param $nombre_rol --> nombre que se designara al select
 * @return String
 **/
function get_roles_select($roles,$nombre_rol){
    $table = "";
    $table.='<select class="form-pilos" id="'.$nombre_rol.'">';
    $table.='<option></option>';
    foreach($roles as $role){
            $table.='<option value="'.$role->id.'">'.$role->nombre_rol.'</option>';
     }
    $table.='</select>';
    return $table;

}


/**
 * Función que obtiene un select con un array dado.
 * @see get_functions_table()
 * @param $functions --> array 
 * @return String
 **/
function get_functions_select($functions,$nombre_function){
    $table = "";
    $table.='<select class="form-pilos" id="'.$nombre_function.'">';
    foreach($functions as $function){
            $table.='<option value="'.$function->id.'">'.$function->nombre_func.'</option>';
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