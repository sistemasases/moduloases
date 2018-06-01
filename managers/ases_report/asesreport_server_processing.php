<?php
require_once('asesreport_lib.php');

$columns = array();
$conditions = array(); // Condiciones para la consulta
$query_fields = array();
$risk_fields = array();
$academic_fields = array();
$statuses_array = array();
$assignment_fields = array();

$name_columns = new stdClass();

$fields_format = array(
    'student_code'=>'user_moodle.username',
    'firstname'=>'user_moodle.firstname',
    'lastname'=>'user_moodle.lastname',
    'document_id'=>'tp_user.num_doc',
    'cohort'=>'all_students_cht.cohorts_student',
    'email'=>'tp_user.emailpilos',
    'cellphone'=>'tp_user.celular',
    'address'=>'tp_user.direccion_res',
    'program_code'=>'acad_program.cod_univalle AS cod_univalle',
    'name_program'=>'acad_program.nombre AS nombre_programa',
    'faculty'=>'faculty.nombre AS nombre_facultad',
    'average'=>'academic_history.promedio_acumulado AS promedio_acumulado',
    'academic_stimuli'=>'estim_query.estimulos AS estimulos',
    'low_academic_performance'=>'bajos_query.bajos AS bajos',
    'ases_status'=>'query_status_ases.estado_ases',
    'icetex_status'=>'query_icetex_status.estado_icetex',
    'academic_program_status'=>'user_extended.program_status',
    'professional'=>'assignments_query.professional',
    'training'=>'assignments_query.trainer',
    'monitor'=>'assignments_query.monitor'
);

$columns_format = array(
    'student_code'=>'Código estudiante',
    'firstname'=>'Nombre(s)',
    'lastname'=>'Apellido(s)',
    'document_id'=>'Número de documento',
    'cohort'=>'Cohorte',
    'email'=>'Correo electrónico',
    'cellphone'=>'Celular',
    'address'=>'Dirección residencia',
    'program_code'=>'Código programa',
    'name_program'=>'Programa académico',
    'faculty'=>'Facultad',
    'average'=>'Promedio acumulado',
    'academic_stimuli'=>'Estimulos',
    'low_academic_performance'=>'Bajos rendimientos',
    'ases_status'=>'Estado ASES',
    'icetex_status'=>'Estado ICETEX',
    'academic_program_status'=>'Estado programa',
    'professional'=>'Profesional',
    'training'=>'Practicante',
    'monitor'=>'Monitor'
);

if(isset($_POST['conditions'])){
    foreach($_POST['conditions'] as $condition){
        array_push($conditions, $condition);
    }
}

if(isset($_POST['fields'])){
    foreach($_POST['fields'] as $field){
        array_push($query_fields, $fields_format[$field]);
        array_push($columns,  array("title"=>$columns_format[$field], "name"=>explode('.', $fields_format[$field])[1], "data"=>explode('.', $fields_format[$field])[1]));
    }
}

if(isset($_POST['academic_fields'])){
    foreach($_POST['academic_fields'] as $academic_field){
        array_push($academic_fields, $fields_format[$academic_field]);
        array_push($columns, array("title"=>$columns_format[$academic_field], "name"=>explode(' ', $fields_format[$academic_field])[2], "data"=>explode(' ', $fields_format[$academic_field])[2]));
    }
}

if(isset($_POST['risk_fields'])){
    $select='<br/><select class="select_risk"><option value=""></option><option value="N.R.">N.R.</option><option value="Bajo">Bajo</option><option value="Medio">Medio</option>
          <option value="Alto">Alto</option></select>';

    foreach($_POST['risk_fields'] as $risk_field){
    
        $query_name = "SELECT * FROM {talentospilos_riesgos_ases} WHERE id =".$risk_field;
        $risk_name = $DB->get_record_sql($query_name)->nombre;
        array_push($columns, array("title"=>'R.'.strtoupper(substr($risk_name, 0, 1)).substr($risk_name, 1, 2).$select, "name"=>$risk_name, "data"=>$risk_name));
        array_push($risk_fields, $risk_field);
    }
}

if(isset($_POST['status_fields'])){
    foreach($_POST['status_fields'] as $status_field){
        array_push($statuses_array, $fields_format[$status_field]);
        array_push($columns, array("title"=>$columns_format[$status_field], "name"=>explode('.', $fields_format[$status_field])[1], "data"=>explode('.', $fields_format[$status_field])[1]));
    }
}

if(isset($_POST['assignment_fields']) && isset($_POST['instance_id'])){
    foreach($_POST['assignment_fields'] as $assignment_field){

        if($assignment_field == 'professional'){
            $professionals = get_professionals_by_instance($_POST['instance_id']);
            $option = "<option>";
            foreach($professionals as $professional){
                $option .= $professional->fullname;
                $option .= "</option>";
            }
        }elseif($assignment_field == 'training'){
            $practs = get_practicing_by_instance($_POST['instance_id']);
            $option = "<option>";
            foreach($practs as $pract){
                $option .= $pract->fullname;
                $option .= "</option>";
            }
        }elseif($assignment_field == 'monitor'){
            $monitors = get_monitors_by_instance($_POST['instance_id']);
            $option = "<option>";
            foreach($monitors as $monitor){
                $option .= $monitor->fullname;
                $option .= "</option>";
            }
        }

        $filter_select = "<br><select class='filter_assignments'><option></option>$option</select>";
        array_push($assignment_fields, $fields_format[$assignment_field]);
        array_push($columns, array("title"=>$columns_format[$assignment_field].$filter_select, "name"=>explode('.', $fields_format[$assignment_field])[1], "data"=>explode('.', $fields_format[$assignment_field])[1]));
    }
}

if(isset($_POST['instance_id'])){
    $counter = 0;
    
    $result = get_ases_report($query_fields, $conditions, $risk_fields, $academic_fields, $statuses_array, $assignment_fields, $_POST['instance_id']);

    $data = array(
                "bsort" => false,
                "data"=> $result,
                "columns" => $columns,
                "select" => "false",
                "fixedHeader"=> array(
                    "header"=> true,
                    "footer"=> true
                ),
                "scrollX" => true,
                "scrollCollapse" => true,
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
                        "oAria"=> array(
                            "sSortAscending"=>  ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending"=> ": Activar para ordenar la columna de manera descendente"
                        )
                    ),
                "autoFill"=>"true",
                "dom"=> "lifrtpB",
                "tableTools"=>array(
                    "sSwfPath"=>"../../style/swf/flashExport.swf"
                ),
                "buttons"=>array(
                            
                            array(
                                "extend" => "print",
                                "text" => 'Imprimir',

                            ), 
                            array(
                                "extend" => "csv",
                                "text" => 'CSV',
                            ),
                            array(
                                "extend" => "excel",
                                "text" => 'Excel',
                                "className" => 'buttons-excel',
                                "filename" => 'Export excel',
                                "extension" => '.xls'
                            ),
                        )
        );

    header('Content-Type: application/json');

    echo json_encode($data);
}
?>
