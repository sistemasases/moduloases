<?php

require_once('asesreport_lib.php');

if(isset($_POST['type'])&&$_POST['type']=="sexo"&&isset($_POST['cohort'])){
    
    $cohorte =  $_POST['cohort'];
    $data = getGraficSex($cohorte);
    echo json_encode($data);
    
} 



if(isset($_POST['type'])&&$_POST['type']=="edad"&&isset($_POST['cohort'])){
    
    $cohorte =  $_POST['cohort'];
    $data = getGraficAge($cohorte);
    echo json_encode($data);
    
} 

if(isset($_POST['type'])&&$_POST['type']=="programa"&&isset($_POST['cohort'])&&isset($_POST['ases_status'])&&isset($_POST['icetex_status'])&&isset($_POST['program_status'])&&isset($_POST['instance_id'])){
    
    $cohorte =  $_POST['cohort'];
    $ases_status = $_POST['ases_status'];
    $icetex_status = $_POST['icetex_status'];
    $program_status = $_POST['program_status'];
    $instance_id = $_POST['instance_id'];    
    $result = getGraficPrograma($cohorte, $ases_status, $icetex_status, $program_status, $instance_id);

    $columns = array();

    array_push($columns, array("title"=>"Programa", "name"=>"nombre", "data"=>"nombre"));
    array_push($columns, array("title"=>"Cantidad", "name"=>"count", "data"=>"count"));


    $data = array(
        "bsort" => false,
        "columns" => $columns,
        "data" => $result,
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
        "order"=> array(0, "asc"),
        "dom"=>'lifrtpB',

        "buttons"=>array(
            array(
                "extend"=>'print',
                "text"=>'Imprimir'
            ),
            array(
                "extend"=>'csvHtml5',
                "text"=>'CSV'
            ),
            array(
                "extend" => "excel",
                "text" => 'Excel',
                "className" => 'buttons-excel',
                "filename" => 'Export excel',
                "extension" => '.xls'
            )
        )

    );    

    echo json_encode($data);
    
} 

if(isset($_POST['type'])&&$_POST['type']=="facultad"&&isset($_POST['cohort'])){
    
    $cohorte =  $_POST['cohort'];
    $data = getGraficFacultad($cohorte);

    echo json_encode($data);
    
} 

if(isset($_POST['type'])&&$_POST['type']=="estado"&&isset($_POST['cohort'])){
    
    $cohorte =  $_POST['cohort'];
    $data = getGraficEstado($cohorte);

    echo json_encode($data);
    
} 
//user_info_field
//user_info_data
//tabla programa
//tabla facultad

// select * from prefix_talentospilos_programa  
// inner join prefix_talentospilos_facultad on 
// (prefix_talentospilos_programa.id_facultad=prefix_talentospilos_facultad.id);

?>