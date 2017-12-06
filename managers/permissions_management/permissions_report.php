<?php
require_once('permissions_lib.php');
require_once('../pilos_tracking/pilos_tracking_lib.php');

$msg = new stdClass();

global $USER;


if(isset($_POST['id'])&&isset($_POST['source'])) {
 	echo json_encode(delete_record($_POST['id'],$_POST['source']));

}else if(isset($_POST['name'])){
    json_encode( get_action_profile());

}else if(isset($_POST['user'])&&isset($_POST['source'])&&$_POST['source']=='permissions_management'){
	echo json_encode(get_functions_by_role($_POST['user']));

}else if( $_POST['data']=='get_info_permission'){
	$user=$USER->id;
    $user_role=get_id_rol_($user,$_POST['instance']);
    $accion = get_action_by_name($_POST['name_permission']);
    echo json_encode($is_permit=get_action_by_role($accion->id,$user_role));



}
