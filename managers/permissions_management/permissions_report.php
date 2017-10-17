<?php
require_once('permissions_lib.php');

$msg = new stdClass();

if(isset($_POST['id'])&&isset($_POST['source'])) {
 	echo json_encode(delete_record($_POST['id'],$_POST['source']));

}else if(isset($_POST['name'])){
    json_encode( get_action_profile());

}else if(isset($_POST['user'])&&isset($_POST['source'])&&$_POST['source']=='permissions_management'){
	echo json_encode(get_functions_by_role($_POST['user']));

}
