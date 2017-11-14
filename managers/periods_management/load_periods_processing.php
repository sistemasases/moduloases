<?php
	
	require_once(dirname(__FILE__). '/../../../../config.php');
	require_once('periods_lib.php');

	if(isset($_POST['load']) && $_POST['load'] == 'loadSemester'){
		$columns = array();
		array_push($columns, array("title"=>"Código", "name"=>"id", "data"=>"id"));
		array_push($columns, array("title"=>"Nombre", "name"=>"nombre", "data"=>"nombre"));
		array_push($columns, array("title"=>"Fecha de Inicio", "name"=>"fecha_inicio", "data"=>"fecha_inicio"));
		array_push($columns, array("title"=>"Fecha de Finalización", "name"=>"fecha_fin", "data"=>"fecha_fin"));

		$data = array(
					"bsort" => false,
					"columns" => $columns,
					"data" => get_all_semesters_table(),
					"language" => 
                	 array(
                    	"search"=> "Buscar:",
                    	"oPaginate" => array(
                        	"sFirst"=>    "Primero",
                        	"sLast"=>     "Último",
                        	"sNext"=>     "Siguiente",
                        	"sPrevious"=> "Anterior"
                    		),
                		"sProcessing"=>     "Procesando...",
                		"sLengthMenu"=>     "Mostrar _MENU_ registros",
                    	"sZeroRecords"=>    "No se encontraron resultados",
                    	"sEmptyTable"=>     "Ningún dato disponible en esta tabla",
                    	"sInfo"=>           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    	"sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    	"sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
                    	"sInfoPostFix"=>    "",
                    	"sSearch"=>         "Buscar:",
                    	"sUrl"=>            "",
                    	"sInfoThousands"=>  ",",
                    	"sLoadingRecords"=> "Cargando...",
                 	),
					"order"=> array(0, "desc")

				);
			header('Content-Type: application/json');
		echo json_encode($data);
	}