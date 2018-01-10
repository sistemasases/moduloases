<?php
require_once('asesreport_lib.php');

$columns = array();
$conditions = array(); // Condiciones para la consulta
$query_fields = array();
$risk_fields = array();
$academic_fields = array();

$name_columns = new stdClass();

$fields_format = array(
    'student_code'=>'username',
    'firstname'=>'firstname',
    'lastname'=>'lastname',
    'document_id'=>'num_doc',
    'email'=>'email',
    'cellphone'=>'celular',
    'address'=>'direccion_res',
    'program_code'=>'cod_univalle',
    'name_program'=>'programa.nombre',
    'faculty'=>'facultad.nombre'
);

$columns_format = array(
    'student_code'=>'Código estudiante',
    'firstname'=>'Nombre(s)',
    'lastname'=>'Apellido(s)',
    'document_id'=>'Número de documento',
    'email'=>'Correo electrónico',
    'cellphone'=>'Celular',
    'address'=>'Dirección residencia',
    'program_code'=>'Código programa',
    'name_program'=>'Programa académico',
    'faculty'=>'Facultad'
);

if(isset($_POST['conditions'])){
    foreach($_POST['conditions'] as $condition){
        array_push($conditions, $condition);
    }
}

if(isset($_POST['fields'])){
    foreach($_POST['fields'] as $field){
        array_push($query_fields, $fields_format[$field]);
        array_push($columns,  array("title"=>$columns_format[$field], "name"=>$columns_format[$field], "data"=>$columns_format[$field]));
    }
}

if(isset($_POST['academic_fields'])){
    foreach($_POST['academic_fields'] as $academic_field){
        array_push($academic_fields, $fields_format[$academic_field]);
        array_push($columns, array("title"=>$columns_format[$academic_field], "name"=>$columns_format[$academic_field], "data"=>$columns_format[$academic_field]));
    }
}

print_r($conditions);
print_r($query_fields);
print_r($academic_fields);
print_r($columns);

if(isset($_POST['instance_id'])){
    $counter = 0;
    
  
    
    if(isset($_POST['chk_risk'])){
        
        foreach($_POST['chk_risk'] as $chk_risk){
            $query_nombre = "SELECT * FROM {talentospilos_riesgos_ases} WHERE id =".$chk_risk;
            $nombre_riesgo = $DB->get_record_sql($query_nombre)->nombre;
            array_push($columns, array("title"=>'R.'.strtoupper(substr($nombre_riesgo, 0, 1)).substr($nombre_riesgo, 1, 2), "name"=>$nombre_riesgo, "data"=>$nombre_riesgo));
            array_push($riesgos_consulta, $chk_risk);
            $counter = $counter + 1;            
        }
    }

    $result = get_ases_report($query_fields, $conditions, $risk_fields, $academic_fields, $_POST['instance_id']);
    
    // $data = array(
    //             "bsort" => false,
    //             "data"=> $result->data,
    //             "columns" => $columns,
    //             "select" => "false",
    //             "language" => 
    //              array(
    //                 "search"=> "Buscar:",
    //                 "oPaginate" => array (
    //                     "sFirst"=>    "Primero",
    //                     "sLast"=>     "Último",
    //                     "sNext"=>     "Siguiente",
    //                     "sPrevious"=> "Anterior"
    //                 ),
    //                 "sProcessing"=>     "Procesando...",
    //                 "sLengthMenu"=>     "Mostrar _MENU_ registros",
    //                 "sZeroRecords"=>    "No se encontraron resultados",
    //                 "sEmptyTable"=>     "Ningún dato disponible en esta tabla",
    //                 "sInfo"=>           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    //                 "sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
    //                 "sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
    //                 "sInfoPostFix"=>    "",
    //                 "sSearch"=>         "Buscar:",
    //                 "sUrl"=>            "",
    //                 "sInfoThousands"=>  ",",
    //                 "sLoadingRecords"=> "Cargando...",
    //                 "oAria"=> array(
    //                     "sSortAscending"=>  ": Activar para ordenar la columna de manera ascendente",
    //                     "sSortDescending"=> ": Activar para ordenar la columna de manera descendente"
    //                 )
    //              ),
    //             "autoFill"=>"true",
    //             "dom"=> "lfrtBip",
    //             "buttons"=>array(
    //                             array("extend"=>"pdf", "message"=>"Generando PDF"),
    //                             "csv",
    //                             "excel"
    //                         )
    //     );
    // header('Content-Type: application/json');
    // $prueba = new stdClass;
    // if(isset($result->error)){
    //     $prueba->error = $result->error;
    //     echo json_encode($prueba);
    // }else{
    //     $prueba->data = $data;
    //     $prueba->columns = $result->columns;
    //     echo json_encode($prueba);
    // }
}
?>
