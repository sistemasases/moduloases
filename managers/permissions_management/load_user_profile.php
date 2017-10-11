<?php
require_once('permissions_lib.php');
    $columns = array();
    array_push($columns, array("title"=>"Perfil", "name"=>"id_perfil", "data"=>"nombre_perfil"));
    array_push($columns, array("title"=>"Nombre usuario", "name"=>"id_usuario", "data"=>'firstname'));
    array_push($columns, array("title"=>"Apellido usuario", "name"=>"id_usuario", "data"=>'lastname'));
    array_push($columns, array("title"=>"Semestre", "name"=>"id_semestre", "data"=>'nombre'));
    array_push($columns, array("title"=>"Eliminar", "name"=>"button", "data"=>"button"));

        $data = array(
                "bsort" => false,
                "columns" => $columns,
                "data"=> get_user_profile_table(),
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



