<?php

require_once dirname(__FILE__) . '/../../../../config.php';

global $DB;

function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}

function get_default_monitorias(){
    return get_monitorias();
}

function get_monitorias(){
    global $DB;
    $sql_query = "SELECT monitoria.id AS id, 
                        dia, 
                        hora, 
                        materia.nombre AS materia, 
                        user_m.firstname AS firstname_mon, 
                        user_m.lastname AS lastname_mon 
                FROM (({talentospilos_monitoria} monitoria
                INNER JOIN ({talentospilos_monitores} monitores
                INNER JOIN {user} user_m
                ON monitores.id_moodle_user = user_m.id) 
                ON monitoria.monitor = monitores.id)
                    INNER JOIN {talentospilos_mate_monitoria} materia
                    ON monitoria.materia = materia.id)
                WHERE monitoria.eliminado IS DISTINCT FROM 1";
    $result_query = $DB->get_records_sql($sql_query);
    $result_to_return = array();
    foreach($result_query as $result){
        $result->dia = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo")[$result->dia];
        $result->botones = "<button id=\"".$result->id."\" class=\"dt-button buttons-print ver-sesiones\">Ver sesiones programadas</button>
                            <button id=\"".$result->id."\" class=\"dt-button buttons-print modificar\">Modificar</button>
                            <button id=\"".$result->id."\" class=\"dt-button buttons-print eliminar\">Eliminar</button>";
        array_push($result_to_return, $result);
    }
    // materia, dia, hora, monitor
    $columns = array();
    array_push($columns, array("title"=>"Materia", "name"=>"materia", "data"=>"materia", "width"=>"20%"));
    array_push($columns, array("title"=>"Dia", "name"=>"dia", "data"=>"dia", "width"=>"10%"));
    array_push($columns, array("title"=>"Hora", "name"=>"hora", "data"=>"hora", "width"=>"10%"));
    array_push($columns, array("title"=>"Nombre Monitor", "name"=>"firstname_mon", "data"=>"firstname_mon", "width"=>"17%"));
    array_push($columns, array("title"=>"Apellido Monitor", "name"=>"lastname_mon", "data"=>"lastname_mon", "width"=>"17%"));
    array_push($columns, array("title"=>"", "name"=>"botones", "data"=>"botones", "width"=>"25%", 
    "defaultContent"=>
    "<button class=\"dt-button buttons-print\">Ver sesiones programadas</button>
    <button class=\"dt-button buttons-print\">Modificar</button>
    <button class=\"dt-button buttons-print\">Eliminar</button>"));

    $data_to_table = array(
        "bsort" => false,
        "data"=> $result_to_return,
        "columns" => $columns,
        "select" => "false",
        "fixedHeader"=> array(
            "header"=> true,
            "footer"=> true
        ),
        "columnDefs" => array(
            array(
                "orderable" => false,
                "targets" => 'nosort'
            )
        ),
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
                "sEmptyTable"=>     "Ningún dato disponible",
                "sInfo"=>           "Mostrando del _START_ al _END_ de _TOTAL_",
                "sInfoEmpty"=>      " 0 registros",
                "sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix"=>    "",
                "sSearch"=>         "Buscar:",
                "sUrl"=>            "",
                "sInfoThousands"=>  ",",
                "sLoadingRecords"=> "Cargando...",
                "oAria"=> array(
                    "sSortAscending"=>  ": Ordenar ascendente",
                    "sSortDescending"=> ": Ordenar descendente"
                )
            ),
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

    return $data_to_table;
}


function cargar_materias(){
    global $DB;
    $sql_query = "SELECT    materia.id AS id,
                            materia.nombre AS nombre
                    FROM  {talentospilos_mate_monitoria} materia
                    WHERE materia.eliminado IS DISTINCT FROM 1";
    $result = $DB->get_records_sql($sql_query);
    return $result;
}

function cargar_monitores($instancia){
    global $DB;
    $sql_query = "SELECT    monitores.id AS id,
                            user_m.firstname AS nombre, 
                            user_m.lastname AS apellido
                    FROM    (({talentospilos_monitores} monitores
                                INNER JOIN {user} user_m ON monitores.id_moodle_user = user_m.id)
                            INNER JOIN {talentospilos_user_rol} rol ON user_m.id = rol.id_usuario)
                    WHERE   rol.estado = 1 AND rol.id_rol = 4 AND rol.id_instancia =".$instancia;
    $result = $DB->get_records_sql($sql_query);
    return $result;
}

function anadir_monitoria($dia, $hora, $materia_id, $monitor_id, $programar, $programar_hasta){
        global $DB;
        $monitoria = new stdClass();
        $monitoria->dia = $dia;
        $monitoria->hora = $hora;
        $monitoria->monitor = $monitor_id;
        $monitoria->materia = $materia_id;
        $monitoria->eliminado = false;

        return $DB->insert_record('talentospilos_monitoria', $monitoria, $returnid=false, $bulk=false);
}

function anadir_materia($materia){
    global $DB;
    $monitoria = new stdClass();
    $monitoria->nombre = $materia;
    $monitoria->eliminado = false;
    return $DB->insert_record('talentospilos_mate_monitoria', $monitoria, $returnid=true, $bulk=false);
}

function eliminar_materia($id){
    global $DB;
    $sql = "SELECT * FROM {talentospilos_mate_monitoria} WHERE id = '$id'";
    $materia = $DB->get_record_sql($sql);
    if($materia){
        $materia->eliminado = true;
        if($materia->id == 0){
            trigger_error('ASES Notificacion: actualizar materia monitoria en la BD con id 0');
            return -1;
        }
        $DB->update_record('talentospilos_mate_monitoria', $materia, $bulk=false);
        return 1;
    }else{
        return -1;
    }
}

function eliminar_monitoria($id){
    global $DB;
    $sql = "SELECT * FROM {talentospilos_monitoria} WHERE id = '$id'";
    $monitoria = $DB->get_record_sql($sql);
    if($monitoria){
        $monitoria->eliminado = true;
        if($monitoria->id == 0){
            trigger_error('ASES Notificacion: actualizar monitoria en la BD con id 0');
            return -1;
        }
        $DB->update_record('talentospilos_monitoria', $monitoria, $bulk=false);
        return 1;
    }else{
        return -1;
    }
}