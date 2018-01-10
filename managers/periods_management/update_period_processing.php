<?php
	require_once(dirname(__FILE__).'/../../../../config.php'); 
	require_once('periods_lib.php');

	if(isset($_POST['id']) && isset($_POST['name']) && isset($_POST['beginning']) && isset($_POST['ending'])){
		global $DB;

		$semesterInfo = array($_POST['id'], $_POST['name'], $_POST['beginning'], $_POST['ending']);

		$success = update_semester($semesterInfo, $_POST['id']);

		if(!$success) {
		 	echo "Ocurrió un error al tratar de actualizar la información";

	   	}else {
		 	echo "El registro se actualizó con éxito";
	 	}



	}

