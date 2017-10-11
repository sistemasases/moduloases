<?php
require_once('permissions_lib.php');
    $columns = array();
    $rows = array();

    array_push($columns, array("title"=>"Perfiles", "name"=>"nombre_perfil", "data"=>"nombre_perfil"));

    $actions =get_actions();
    foreach($actions as $action){
      array_push($columns, array("title"=>$action->nombre_accion, "name"=>$action->id, "data"=>$action->id));
    }


    array_push($columns, array("title"=>"Modificar", "name"=>"button", "data"=>"button"));


        $data = array(
                "bsort" => false,
                "columns" => $columns,
                "rows" =>$rows,
                "data"=> get_action_profile_table(),
                "language" => 
                 array(
                    "search"=> "Buscar:",
                    "oPaginate" => array (
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
                 "order"=> array(0, "desc" )
        );
    header('Content-Type: application/json');


echo json_encode($data); 



