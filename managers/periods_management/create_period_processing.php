<?php
	require_once(dirname(_FILE_).'/../../../../config.php'); 
	require_once('periods_lib.php');

	if(isset($_POST['op']) && isset($_POST['name']) && isset($_POST['beginning']) && isset($_POST['ending']) && $_POST['op'] == 'createSemester'){

		$create = create_semester($_POST['name'], $_POST['beginning'], $_POST['ending']);

		echo "El registro se realizó con éxito";	

	}