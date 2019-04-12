<?php

require_once('asesreport_lib.php');

if(isset($_POST['type'])&&$_POST['type']=="sexo"&&isset($_POST['cohort'])&&isset($_POST['ases_status'])&&isset($_POST['icetex_status'])&&isset($_POST['program_status'])&&isset($_POST['instance_id'])){
    
    $cohorte =  $_POST['cohort'];
    $ases_status = $_POST['ases_status'];
    $icetex_status = $_POST['icetex_status'];
    $program_status = $_POST['program_status'];
    $instance_id = $_POST['instance_id'];    
    $result = getGraficSex($cohorte, $ases_status, $icetex_status, $program_status, $instance_id);

    $columns = array();

    array_push($columns, array("title"=>"Sexo", "name"=>"sexo", "data"=>"nombre"));
    array_push($columns, array("title"=>"Cantidad", "name"=>"cantidad", "data"=>"cantidad"));

    $data = get_general_table_graphic($columns, $result);

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
    array_push($columns, array("title"=>"Cantidad", "name"=>"cantidad", "data"=>"cantidad"));

    $data = get_general_table_graphic($columns, $result);

    echo json_encode($data);
    
} 

if(isset($_POST['type'])&&$_POST['type']=="facultad"&&isset($_POST['cohort'])&&isset($_POST['ases_status'])&&isset($_POST['icetex_status'])&&isset($_POST['program_status'])&&isset($_POST['instance_id'])){
    
    $cohorte =  $_POST['cohort'];
    $ases_status = $_POST['ases_status'];
    $icetex_status = $_POST['icetex_status'];
    $program_status = $_POST['program_status'];
    $instance_id = $_POST['instance_id'];    
    $result = getGraficFacultad($cohorte, $ases_status, $icetex_status, $program_status, $instance_id);

    $columns = array();

    array_push($columns, array("title"=>"Facultad", "name"=>"nombre", "data"=>"nombre"));
    array_push($columns, array("title"=>"Cantidad", "name"=>"cantidad", "data"=>"cantidad"));

    $data = get_general_table_graphic($columns, $result);

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