<?php

require_once dirname(__FILE__) . '/../../../../config.php';

global $DB;

function get_tabla_monitorias($course_id, $block_id){
    global $DB;
    $sql_query = "SELECT monitoria.id AS id, 
                        dia, 
                        hora, 
                        materia.nombre AS materia, 
                        user_m.firstname AS firstname_mon, 
                        user_m.lastname AS lastname_mon 
                FROM (({talentospilos_monitoria} monitoria
                INNER JOIN {user} user_m
                ON monitoria.monitor = user_m.id) 
                    INNER JOIN {talentospilos_mate_monitoria} materia
                    ON monitoria.materia = materia.id)
                WHERE monitoria.eliminado IS DISTINCT FROM 1";
    $result_query = $DB->get_records_sql($sql_query);
    $result_to_return = array();
    foreach($result_query as $result){
        $result->dia = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo")[$result->dia];
        $url = (new moodle_url("/blocks/ases/view/monitorias_academicas_detalle.php", array('courseid' => $course_id, 
                                                                                            'instanceid' => $block_id, 
                                                                                            'monitoriaid' => $result->id)))->out();
        $result->botones = "<a href='$url' class='dt-button buttons-print'>Ver sesiones programadas</a>
                            <a id='$result->id' class='dt-button buttons-print eliminar'>Eliminar</a>";
        array_push($result_to_return, $result);
    }
    // materia, dia, hora, monitor
    $columns = array();
    array_push($columns, array("title"=>"Materia", "name"=>"materia", "data"=>"materia", "width"=>"18%"));
    array_push($columns, array("title"=>"Dia", "name"=>"dia", "data"=>"dia", "width"=>"10%"));
    array_push($columns, array("title"=>"Hora", "name"=>"hora", "data"=>"hora", "width"=>"12%"));
    array_push($columns, array("title"=>"Nombre Monitor", "name"=>"firstname_mon", "data"=>"firstname_mon", "width"=>"18%"));
    array_push($columns, array("title"=>"Apellido Monitor", "name"=>"lastname_mon", "data"=>"lastname_mon", "width"=>"18%"));
    array_push($columns, array("title"=>"", "name"=>"botones", "data"=>"botones", "width"=>"24%", 
    "defaultContent"=>
    "<button class=\"dt-button buttons-print\">Ver sesiones programadas</button>
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

function anadir_monitoria($dia, $hora, $materia_id, $monitor_id, $programar, $programar_hasta){
        global $DB;
        $monitoria = new stdClass();
        $monitoria->dia = $dia;
        $monitoria->hora = $hora;
        $monitoria->monitor = $monitor_id;
        $monitoria->materia = $materia_id;
        $monitoria->eliminado = false;
        if($programar){
            $id = $DB->insert_record('talentospilos_monitoria', $monitoria, $returnid=true, $bulk=false);
            programar_sesiones($id, $dia, new DateTime(), formatear_fecha_legible_a_int($programar_hasta));
            return 1;
        } else {
            return $DB->insert_record('talentospilos_monitoria', $monitoria, $returnid=false, $bulk=false);
        }
         
}

function modificar_monitoria($dia, $hora, $materia_id, $monitor_id, $id_monitoria){
    global $DB;
    $sql = "SELECT * FROM {talentospilos_monitoria} WHERE id = '$id_monitoria'";
    $monitoria = $DB->get_record_sql($sql);
    if($monitoria){
        $monitoria->dia = $dia;
        $monitoria->hora = $hora;
        $monitoria->monitor = $monitor_id;
        $monitoria->materia = $materia_id;
        if($monitoria->id == 0){
            trigger_error('ASES Notificacion: actualizar monitoria en la BD con id 0');
            return -1;
        }
        return $DB->update_record('talentospilos_monitoria', $monitoria, $bulk=false);
    } 
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

function eliminar_sesion($id){
    global $DB;
    $sql = "SELECT * FROM {talentospilos_sesi_monitoria} WHERE id = '$id'";
    $sesion = $DB->get_record_sql($sql);
    if($sesion){
        $sesion->eliminado = true;
        if($sesion->id == 0){
            trigger_error('ASES Notificacion: actualizar monitoria en la BD con id 0');
            return -1;
        }
        $DB->update_record('talentospilos_sesi_monitoria', $sesion, $bulk=false);
        return 1;
    }else{
        return -1;
    }
}

function get_monitoria_by_id($id){
    global $DB;
    $sql = "SELECT monitoria.id AS id, 
                    dia AS dia_numero, 
                    hora, 
                    materia.nombre AS materia, 
                    user_m.firstname AS firstname_mon, 
                    user_m.lastname AS lastname_mon,
                    user_m.id AS monitor_id,
                    materia.id AS materia_id 
            FROM (({talentospilos_monitoria} monitoria
                    INNER JOIN {user} user_m
                    ON monitoria.monitor = user_m.id) 
            INNER JOIN {talentospilos_mate_monitoria} materia
            ON monitoria.materia = materia.id)
            WHERE monitoria.eliminado IS DISTINCT FROM 1 AND monitoria.id = '$id'";
    $result = $DB->get_record_sql($sql);
    $result->dia = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo")[$result->dia_numero];
    return $result;
}

function programar_sesiones($id_monitoria, $dia, $desde, $hasta){
    global $DB;
    // $hasta es un numero de formato Ymd
    // $desde es un objeto de clase DateTime
    // $dia es un int: 0 = Lunes, 1 = Martes, 2 = Miercoles, ...
    $dia_ingles = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')[$dia];
    $siguiente_dia = $desde;
    // mientras $siguiente_dia siga estando antes de $hasta, programar sesión
    while(intval($siguiente_dia->format("Ymd")) <= $hasta){
        $fecha =  intval($siguiente_dia->format("Ymd"));
        if(count($DB->get_records_sql("SELECT 1
                    FROM {talentospilos_sesi_monitoria} sesion 
                    WHERE 
                        sesion.id_monitoria = $id_monitoria 
                        AND sesion.eliminado IS DISTINCT FROM 1
                        AND sesion.fecha = $fecha")) == 0){ // si no existe una sesion ya programada
            // registrar sesion de monitoria
            $sesion = new stdClass();
            $sesion->id_monitoria = $id_monitoria;
            $sesion->fecha = $fecha;
            $sesion->eliminado = false;
            $DB->insert_record('talentospilos_sesi_monitoria', $sesion, $returnid=false, $bulk=false);
        }
        // correr $siguiente_dia hasta el proximo $dia (método modify de php hace la magia)
        $siguiente_dia->modify('next day');
        $siguiente_dia->modify($dia_ingles);
    }
}

function get_reporte_by_id($id){
    global $DB;
    $sql = "SELECT monitoria.id AS id, 
                    dia, 
                    hora, 
                    materia.nombre AS materia, 
                    user_m.firstname AS firstname_mon, 
                    user_m.lastname AS lastname_mon 
            FROM (({talentospilos_monitoria} monitoria
                    INNER JOIN {user} user_m
                    ON monitoria.monitor = user_m.id) 
            INNER JOIN {talentospilos_mate_monitoria} materia
            ON monitoria.materia = materia.id)
            WHERE monitoria.eliminado IS DISTINCT FROM 1";
    $result = $DB->get_records_sql($sql);
    $result->dia = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo")[$result->dia];
    return $DB->get_records_sql($id);
}


function get_tabla_sesiones($monitoria_id, $desde, $hasta){
    //$desde y $hasta deben ser int con formato de fecha int
    global $DB;
    $sql = "SELECT  sesion.id AS id, 
                    sesion.fecha AS fecha 
            FROM {talentospilos_sesi_monitoria} sesion 
            WHERE 
                sesion.id_monitoria = $monitoria_id 
                AND sesion.eliminado IS DISTINCT FROM 1
                AND sesion.fecha >= $desde
                AND sesion.fecha <= $hasta
            ORDER BY fecha ASC";
    $sesiones = $DB->get_records_sql($sql);
    $result_to_return = array();
    $ints_para_ordenar = array();
    foreach($sesiones as $result){
        array_push($ints_para_ordenar, intval($result->fecha));
        $result->fecha = formatear_fecha_int_a_legible($result->fecha);
        $result->botones = "<span style='float:right'><a id='$result->id' class='dt-button buttons-print estudiantes'>Ver estudiantes inscritos</a>
                            <a id='$result->id' class='dt-button buttons-print eliminar'>Cancelar sesión</a></span>";
        array_push($result_to_return, $result);
    }
    // fecha, botones
    $columns = array();
    array_push($columns, array("title"=>"Fecha", "name"=>"fecha", "data"=>"fecha", "width"=>"40%"));
    array_push($columns, array("title"=>"", "name"=>"botones", "data"=>"botones", "width"=>"60%"));

    $data_to_table = array(
        "ordering" => false,
        "data"=> $result_to_return,
        "columns" => $columns,
        "select" => "false",
        "fixedHeader"=> array(
            "header"=> true,
            "footer"=> true
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

function cargar_monitores($instancia){
    global $DB;
    $id_grupo = cargar_grupo_seleccionado()->id_number;
    $query = "  SELECT  user_m.id AS id,
                        user_m.firstname AS nombre, 
                        user_m.lastname AS apellido
                FROM    {user} user_m 
                    INNER JOIN {groups_members} miembro
                        ON user_m.id = miembro.userid
                WHERE miembro.groupid = $id_grupo";
    $result = $DB->get_records_sql($query);
    return $result;
}

function cargar_grupos($id_course){
    global $DB;
    $query = "  SELECT  group_m.id AS id,
                        group_m.name AS nombre 
                FROM    {groups} group_m
                        WHERE group_m.courseid = $id_course";
    $result = $DB->get_records_sql($query);
    return $result;
}

// Es necesario guardar la configuración (es decir, qué grupo es el de los monitores academicos)
// para no crear una tabla solo para eso, se acordó guardar esa entrada en la tabla talentospilos_instancia
// Esa entrada claramente no es una instancia. En el campo id_instancia tendrá el valor 9999999

function cargar_grupo_seleccionado(){
    global $DB;
    $ID_I = 9999999;
    $query = "  SELECT  *
                FROM    {talentospilos_instancia} i
                        WHERE i.id_instancia = $ID_I";

    $result = $DB->get_record_sql($query);
    
    if(! $result){
        $grupo = new stdClass();
        $grupo->id_instancia = $ID_I;
        $grupo->descripcion = "IGNORAR. Grupo seleccionado para monitores de monitorías academicas. Esta entrada corresponde a la funcionalidad de monitorías académicas, se usa para almacenar configuración.";
        $grupo->id_number = -1;
        if($DB->insert_record('talentospilos_instancia', $grupo, $returnid=true, $bulk=false)){
            return $grupo;
        }
    }
    return $result;
}

function actualizar_config($nuevo_grupo){
    global $DB;
    $grupo = cargar_grupo_seleccionado();
    if($grupo){
        $grupo->id_number = $nuevo_grupo;
        if($grupo->id == 0){
            trigger_error('ASES Notificacion: actualizar config monitorias academicas en la BD con id 0 '.serialize($grupo));
            return -1;
        }
        $DB->update_record('talentospilos_instancia', $grupo, $bulk=false);
        return 1;
    }else{
        return -1;
    }
}
// FORMATOS DE FECHA
// Fecha int (la que se guarda en la db): yyyymmdd
// Fecha legible (mostrada en el frontend): dd/MMM/yy
// Ej. 20201211 = 11/dic/20
// no hay validación de ningún tipo. El usuario NO DEBE ingresar las fechas manualmente, solo a través de un DatePicker, como el de jQuery. 
// para el DatePicker de jQuery, agregar esta opción: dateFormat: "dd/M/y"
function formatear_fecha_legible_a_int(string $fecha){
    $yyyy = "20".substr($fecha, 7, 2); // voy a suponer que esta función solo va a manejar fechas del siglo veinte, en caso tal de que no, estamos jodidos
    $mm = array_search(substr($fecha, 3, 3), array("ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic"), true) + 1;
    if($mm < 10) {
        $mm = "0".strval($mm);
    } else {
        $mm = strval($mm);
    }
    $dd = substr($fecha, 0, 2);
    return intval($yyyy.$mm.$dd);
}

function formatear_fecha_int_a_legible(int $fecha){
    $cadena = strval($fecha);
    $yy = substr($cadena, 2, 2);
    $MMM = array("ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic")[intval(substr($cadena, 4, 2))-1];
    $dd = substr($cadena, 6, 2);
    return $dd."/".$MMM."/".$yy;
}

function get_monitorias_para_inscribirse(){
    global $DB;
    $sql_query = "SELECT monitoria.id AS id, 
                        dia, 
                        hora, 
                        materia.nombre AS materia 
                FROM {talentospilos_monitoria} monitoria
                    INNER JOIN {talentospilos_mate_monitoria} materia
                    ON monitoria.materia = materia.id
                WHERE monitoria.eliminado IS DISTINCT FROM 1
                ORDER BY dia ASC";
   return $DB->get_records_sql($sql_query);
}

function get_proxima_sesion_de_monitoria($monitoria_id){
    global $DB;
    $hoy = (new DateTime())->format("Ymd");
    $sql = "SELECT  sesion.id AS id, 
                    sesion.id_monitoria AS monitoria,
                    sesion.fecha AS fecha 
            FROM {talentospilos_sesi_monitoria} sesion 
            WHERE 
            sesion.id_monitoria = $monitoria_id AND
             sesion.eliminado IS DISTINCT FROM 1
                AND sesion.fecha >= $hoy
            ORDER BY fecha ASC 
            LIMIT 2";

    $sesiones = array_values($DB->get_records_sql($sql));
    $proxima_sesion = $sesiones[0];
    if($sesiones[0]->fecha == $hoy){
        // MANEJO ESPECIAL CUANDO LA SESION ES EL MISMO DÍA QUE EL USUARIO SE VA REGISTRAR
        // El objetivo de este bloque de codigo es determinar si la hora final de la monitoría ya pasó. Si ya pasó, devolver la siguiente sesión programada

        $hora_final = array_slice(explode(' ',$DB->get_field('talentospilos_monitoria', 'hora', ['id' => $monitoria_id])), 3, 2);
        // Si la monitoría era en la mañana (AM) y en este instante ya estamos en la tarde (PM), retornar la siguiente sesion
        if($hora_final[1] == 'AM' && (new DateTime())->format("A") == 'PM') $proxima_sesion =  $sesiones[1];
        // Si la monitoría es en la tarde y todavía estamos en la mañana, retornar la sesión de hoy
        else if($hora_final[1] == 'PM' && (new DateTime())->format("A") == 'AM') $proxima_sesion =  $sesiones[0];
        // Si estamos en la misma jornada, comparar las horas como si fueran numeros. (ej, si la monitoría termina a las 4:00 y son las 2:03, se hace la comparación 400 > 203)
        else $proxima_sesion =  intval(str_replace(':','',$hora_final[0])) > intval((new DateTime())->format("gi")) ? $sesiones[0] : $sesiones[1];
        
    }
    $proxima_sesion->fecha_legible = formatear_fecha_int_a_legible($proxima_sesion->fecha);
    return $proxima_sesion;
}

function anadir_asistente_a_sesion_de_monitoria($sesion, $id_asistente, $asignatura_a_consultar, $tematica_a_consultar){
    global $DB;
    $asistente = new stdClass();
    $asistente->sesion = $sesion;
    // talvez me equivoqué nombrando este campo como "asistente". 
    // Este campo es una llave foranea a mdl_user, es el id del usuario de moodle que va a asistir a la monitoria
    $asistente->asistente = $id_asistente;
    $asistente->fecha_inscripcion = (new DateTime())->format("Ymd"); // hoy, en el formato custom especificado arriba
    $asistente->asignatura_a_consultar = $asignatura_a_consultar;
    $asistente->tematica_a_consultar = $tematica_a_consultar;
    $asistente->asiste = false;
    $asistente->eliminado = false;

    return $DB->insert_record('talentospilos_asis_monitoria', $asistente, $returnid=false, $bulk=false);    
}

function cargar_inscripciones_de_usuario($id_usuario){
    global $DB;
    $hoy = (new DateTime())->format("Ymd");
    $sql =    "SELECT
                   inscripcion.id AS id,
                   monitoria.dia AS dia_numero,
                   sesion.fecha AS fecha, 
                   monitoria.hora AS horario,
                   materia.nombre AS materia, 
                   monitor_encargado.firstname AS nombre_encargado, 
                   monitor_encargado.lastname AS apellido_encargado,
                   monitor_encargado.email AS correo_encargado,
                   inscripcion.asignatura_a_consultar AS asignatura,
                   inscripcion.tematica_a_consultar AS tematica
                FROM {talentospilos_asis_monitoria} inscripcion
                    INNER JOIN {talentospilos_sesi_monitoria} sesion
                    ON inscripcion.sesion = sesion.id
                        INNER JOIN {talentospilos_monitoria} monitoria
                        ON sesion.id_monitoria = monitoria.id
                            INNER JOIN {talentospilos_mate_monitoria} materia
                            ON monitoria.materia = materia.id
                                INNER JOIN {user} monitor_encargado
                                ON monitoria.monitor = monitor_encargado.id
                WHERE 
                    inscripcion.asistente = $id_usuario 
                    AND (inscripcion.eliminado IS DISTINCT FROM 1)
                    AND (sesion.eliminado IS DISTINCT FROM 1)
                    AND (monitoria.eliminado IS DISTINCT FROM 1)
                    AND (sesion.fecha >= $hoy)
                ORDER BY sesion.fecha ASC";
    
    $results = $DB->get_records_sql($sql);
    error_log(var_export($results, true));
    foreach($results as $result) {
        $result->dia = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo")[$result->dia_numero];
        $result->fecha = formatear_fecha_int_a_legible($result->fecha);
    }
    return $results;
}

function eliminar_asistencia($id){
    global $DB;
    $sql = "SELECT * FROM {talentospilos_asis_monitoria} WHERE id = '$id'";
    $asistencia = $DB->get_record_sql($sql);
    if($asistencia){
        $asistencia->eliminado = true;
        if($asistencia->id == 0){
            trigger_error('ASES Notificacion: actualizar monitoria en la BD con id 0');
            return -1;
        }
        $DB->update_record('talentospilos_asis_monitoria', $asistencia, $bulk=false);
        return 1;
    }else{
        return -1;
    }
}

function modificar_celular_de_usuario($id, $celular){
    global $DB;
    $user = $DB->get_record_sql("SELECT * FROM {user} WHERE id = $id");
    if($user){
        $user->phone1 = $celular;
        if($user->id == 0){
            trigger_error('ASES Notificacion: actualizar usuario en la BD con id 0');
            return -1;
        }
        //error_log(var_export($user, true));
        return $DB->update_record('user', $user, $bulk=false);
    } 
}

function cargar_asistentes_de_sesion($id){
    global $DB;
    // El programa/dependencia es un campo adicional agregado por la universidad a moodle, y está definido en la tabla mdl_user_info_field.
    // Al 27/03/2021 ese campo tiene id 1. Si por alguna razón llegara a cambiar, actualizar la siguiente variable:
    $id_field_programa = 1;
    $sql = "SELECT 
                inscripcion.id AS id,
                inscripcion.asiste AS asistencia,
                estudiante.idnumber AS codigo,
                estudiante.firstname AS nombres,
                estudiante.lastname AS apellidos,
                info_adicional.programa AS programa,
                estudiante.email AS correo,
                estudiante.phone1 AS celular,
                inscripcion.asignatura_a_consultar AS asignatura,
                inscripcion.tematica_a_consultar AS tematica
            FROM {talentospilos_asis_monitoria} inscripcion
                INNER JOIN {user} estudiante
                ON inscripcion.asistente = estudiante.id
                    INNER JOIN 
                        (SELECT userid, data AS programa FROM {user_info_data} 
                            WHERE fieldid = $id_field_programa) info_adicional
                    ON info_adicional.userid = estudiante.id
            WHERE 
                inscripcion.sesion = $id
                AND inscripcion.eliminado IS DISTINCT FROM 1";
    $results = array_values($DB->get_records_sql($sql));
    error_log(var_export($results, true));
    return $results;
}