<?php
	require_once(dirname(__FILE__).'/../../../../config.php');
	require_once('periods_lib.php');

	if(isset($_POST['dat'])){

		$info_semester = get_semester_by_id($_POST['dat']);

		echo json_encode($info_semester);

	}else{

		$object = new stdClass();
		$object->error = "Error al consultar la base de datos. El semestre " .$_POST['dat']. " no se encuentra registrado en la base de datos";
		echo json_encode($object);
	}
