<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Estrategia ASES
 *
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('permissions_lib.php');
require_once('permissions_functions.php');
require_once('../pilos_tracking/pilos_tracking_lib.php');

$msg = new stdClass();

global $USER;


if(isset($_POST['id'])&&isset($_POST['type'])&&isset($_POST['source'])&&$_POST['source']=='delete_record') {
 	echo json_encode(delete_record($_POST['id'],$_POST['type']));

}else if(isset($_POST['user'])&&isset($_POST['source'])&&$_POST['source']=='permissions_management'){
	echo json_encode(get_functions_by_role($_POST['user']));

}else if(isset($_POST['id'])&&isset($_POST['table'])&&$_POST['source']=='modify_register'&&isset($_POST['nombre'])&&isset($_POST['descripcion'])&&isset($_POST['funcionalidad'])){

	echo json_encode(modify_record($_POST['id'],$_POST['table'],$_POST['nombre'],$_POST['descripcion'],$_POST['funcionalidad']));

}else if(isset($_POST['user'])&&isset($_POST['source'])&&$_POST['source']=='get_info_permission'){
	$user=$USER->id;
    $user_role=get_id_rol_($user,$_POST['instance']);
    $accion = get_action_by_name($_POST['name_permission']);
    echo json_encode($is_permit=get_action_by_role($accion->id,$user_role));
}elseif($_POST['source']=='update_general_table'){
	//Gets connected user role
	$userrole = get_id_rol($USER->id,$_POST['instance']);
	$usernamerole= get_name_rol($userrole);

	echo json_encode(get_functions_actions($usernamerole));

}elseif($_POST['source']=='update_functionality_select'){
	$array = [];
	$function = get_functions();
	$functions_table = get_functions_select($function,"functions");
	$functions = get_functions_select($function,"functions_table");
	array_push($array, $functions_table);
	array_push($array, $functions);
	echo json_encode($array);

}elseif($_POST['source']=='update_role_select'){
	$roles = get_roles();
	$roles_table_user= get_roles_select($roles,"profiles_user");
	echo json_encode($roles_table_user);

}