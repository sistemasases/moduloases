<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Estrategia ASES
 *
 * @author     Joan Manuel Tovar Guzmán
 * @package    block_ases
 * @copyright  2018 Joan Manuel Tovar Guzmán <joan.tovar@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once( dirname(__FILE__). '/../../../../config.php' );
require_once( $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php' );
require_once( $CFG->dirroot.'/blocks/ases/managers/periods_management/periods_lib.php' ); 
require_once( $CFG->dirroot.'/blocks/ases/managers/dphpforms/v2/dphpforms_lib.php' );   
require_once( $CFG->dirroot.'/blocks/ases/managers/monitor_assignments/monitor_assignments_lib.php' ); 
require_once( $CFG->dirroot.'/blocks/ases/managers/user_management/user_management_lib.php' ); 

/**
 * 
 */

function students_no_trackings_get_students_count_trackings( $instance_id ){      

    $id_semester = get_current_semester()->max;
    $interval_semester = get_semester_interval($id_semester);
    
    $list_inicio = explode(" ", $interval_semester->fecha_inicio);
    $list_fin = explode(" ", $interval_semester->fecha_fin);
    
    $start_date = $list_inicio[0];
    $end_date = $list_fin[0];
    
    $xQuery = new stdClass();
    $xQuery->form = "seguimiento_pares"; 
    $xQuery->filterFields = [
                                ["id_estudiante",[["%%", "LIKE"]], false],
                                ["fecha",[[$start_date,">="],[$end_date,"<="]], false],
                                ["id_instancia",[[$instance_id, "="]], false]                             
                            ];
    $xQuery->orderFields = [["fecha","DESC"]];
    $xQuery->orderByDatabaseRecordDate = false; 
    $xQuery->recordStatus = [ "!deleted" ];
    $xQuery->selectedFields = []; 

    $trackings = dphpformsV2_find_records( $xQuery );
    $count = [];

    foreach( $trackings as $key => $tracking ){

        if( !array_key_exists ( $tracking['id_estudiante'] , $count ) ){
            $count[ $tracking['id_estudiante'] ] = 1;
        }else{
            $count[ $tracking['id_estudiante'] ]++;
        }

    }

    $xQuery = new stdClass();
    $xQuery->form = "inasistencia"; 
    $xQuery->filterFields = [
                                ["in_id_estudiante",[["%%", "LIKE"]], false],
                                ["in_fecha",[[$start_date,">="],[$end_date,"<="]], false],
                                ["in_id_instancia",[[$instance_id, "="]],false]                         
                            ];
    $xQuery->orderFields = [["in_fecha","DESC"]];
    $xQuery->orderByDatabaseRecordDate = false; 
    $xQuery->recordStatus = [ "!deleted" ];
    $xQuery->selectedFields = []; 

    $trackings = dphpformsV2_find_records( $xQuery );

    foreach( $trackings as $key => $tracking ){

        if( !array_key_exists ( $tracking['in_id_estudiante'] , $count ) ){
            $count[ $tracking['in_id_estudiante'] ] = 1;
        }else{
            $count[ $tracking['in_id_estudiante'] ]++;
        }

    }

    return $count;
}

/**
 * Function that returns a list of the students with pair trackings on the current semester
 * 
 * @see get_array_students_with_trackings()
 * @return array
 */

function get_students_with_trackings( $instance_id ){      

    $semestre = get_current_semester();
    $idMaxSemester = $semestre->max;   
    $intervalSemester = get_semester_interval($idMaxSemester);
    
    $list_inicio = explode(" ", $intervalSemester->fecha_inicio);
    $list_fin = explode(" ", $intervalSemester->fecha_fin);
    
    $fecha_inicio = $list_inicio[0];
    $fecha_fin = $list_fin[0];
    
    $xQuery = new stdClass();
    $xQuery->form = "seguimiento_pares"; // Can be alias(String) or idntifier(Number)        
    $xQuery->filterFields = [
                            ["id_estudiante",
                                [["%%", "LIKE"]], 
                                false],
                            ["fecha",
                                [[$fecha_inicio,">="],[$fecha_fin,"<="]], 
                                false],
                            ["id_instancia",
                                [[$instance_id, "="]],
                                false
                            ]                        
                    ];
    $xQuery->orderFields = [
                            ["fecha","DESC"]
                        ];

    $xQuery->orderByDatabaseRecordDate = false; // If true, orderField is ignored. DESC
    $xQuery->recordStatus = [ "!deleted" ];// options "deleted" or "!deleted", can be both. Empty = both.
    $xQuery->selectedFields = []; // RecordId and BatabaseRecordDate are selected by default.

    $seguimientos = dphpformsV2_find_records( $xQuery ); 
    return json_encode($seguimientos);
}


/**
 * Function that returns a list of the students with non attendance trackings on the current semester
 * 
 * @see get_array_students_with_trackings()
 * @return array
 */

function get_students_with_non_attendance_trackings( $instance_id ){ 

    $semestre = get_current_semester();
    $idMaxSemester = $semestre->max;   
    $intervalSemester = get_semester_interval($idMaxSemester);
    
    $list_inicio = explode(" ", $intervalSemester->fecha_inicio);
    $list_fin = explode(" ", $intervalSemester->fecha_fin);
    
    $fecha_inicio = $list_inicio[0];
    $fecha_fin = $list_fin[0];


    $xQuery = new stdClass();
    $xQuery->form = "inasistencia"; // Can be alias(String) or idntifier(Number)        
    $xQuery->filterFields = [
                            ["in_id_estudiante",[
                                ["%%", "LIKE"]                                
                                
                                ], false],
                            ["in_fecha",
                                [[$fecha_inicio,">="],[$fecha_fin,"<="]]
                                , false],
                            ["in_id_instancia",
                                [[$instance_id, "="]],
                                false
                            ]                                    
                    ];
    $xQuery->orderFields = [
                            ["in_fecha","DESC"]
                        ];

    $xQuery->orderByDatabaseRecordDate = false; // If true, orderField is ignored. DESC
    $xQuery->recordStatus = [ "!deleted" ];// options "deleted" or "!deleted", can be both. Empty = both.
    $xQuery->selectedFields = []; // RecordId and BatabaseRecordDate are selected by default.

    $inasistencias = dphpformsV2_find_records( $xQuery );
    return json_encode($inasistencias);

}


/**
 * Function that returns a list of the students with monitor but without tracking
 * 
 * @see get_array_students_without_trackings()
 * @return array
 */
function get_array_students_with_trackings_count( $instance_id ){

    global $DB;   

    $semestre = get_current_semester();
    $idMaxSemester = $semestre->max;
    $monitorias = monitor_assignments_get_monitors_students_relationship_by_instance_n_semester( $instance_id, $idMaxSemester );

    $sql_query = "SELECT usuario.id AS id, userm.username, usuario.num_doc AS cedula, userm.firstname, userm.lastname FROM {user} AS userm
    INNER JOIN {talentospilos_user_extended} as user_ext  ON user_ext.id_moodle_user= userm.id
    INNER JOIN  {talentospilos_usuario} AS usuario ON id_ases_user = usuario.id";    
    
    //Condition to get the students who don't have pair trackings on the current semester


    $studentsWithTrackings = json_decode(get_students_with_trackings( $instance_id ), true);    

    $tracked_students_condition = " WHERE 
    usuario.id NOT IN (";    

    foreach($studentsWithTrackings as $tracking){                 
        $tracked_students_condition .="'". $tracking['id_estudiante']. "', ";
    }   

    $tracked_students_condition.= ")";    
    $tracked_students_condition = str_replace("', )", "')", $tracked_students_condition);    
    $sql_query .= $tracked_students_condition;   
    

    //Condition to get the students who don't have non attendance trackings on the current semester    
    
    $studentsAttendanceTrackings = json_decode(get_students_with_non_attendance_trackings( $instance_id ), true);

    if(count($studentsAttendanceTrackings) != 0){

        $tracked_att_students_condition = " AND usuario.id NOT IN (";    

        foreach($studentsAttendanceTrackings as $attTracking){                 
            $tracked_att_students_condition .="'". $attTracking['in_id_estudiante']. "', ";
        }   

        $tracked_att_students_condition.= ")";    
        $tracked_att_students_condition = str_replace("', )", "') ", $tracked_att_students_condition);    
        $sql_query .= $tracked_att_students_condition;        
    }
    
    //Condition to get the students who do have a monitor assigned on the current semester
    $monitorias_condition = " AND usuario.id IN (";

    foreach($monitorias as $monitoria){                 
        $monitorias_condition .="'". $monitoria->id_estudiante . "', ";
    }   

    $monitorias_condition.= ")";    
    $monitorias_condition = str_replace("', )", "')", $monitorias_condition);    
    $sql_query .= $monitorias_condition;

    $sql_query .= " AND tracking_status = 1 ";
    
    $students = $DB->get_records_sql($sql_query);      
    //The monitor, trainee and professional of each student is added to the report

    $students_to_return = array();
    
    foreach($students as $student){
        
        //$monitor_object = get_assigned_monitor($student->id);
        $tracking_team =  user_management_get_stud_mon_prac_prof( $student->id, $instance_id, $idMaxSemester );
        $monitor_object = $tracking_team->monitor;
        $trainee_object = $tracking_team->practicing;
        $professional_object = $tracking_team->professional;
       
        $student->cantidad_fichas = 0;


        if ($monitor_object) {
            $student->monitor_fullname = "$monitor_object->firstname $monitor_object->lastname";
            $student->id_dphpforms_monitor = '-1';
        } else {
            $record->monitor_fullname = "NO REGISTRA";
        }
    
        if ($trainee_object) {
            $student->trainee_fullname = "$trainee_object->firstname $trainee_object->lastname";
        } else {
            $student->trainee_fullname = "NO REGISTRA";
        }
        
        if ($professional_object) {
            $student->professional_fullname = "$professional_object->firstname $professional_object->lastname";
        } else {
            $student->professional_fullname = "NO REGISTRA";
        }


        array_push($students_to_return, $student);
    }

    // users with trackings
    $student_count_trackings = students_no_trackings_get_students_count_trackings( $instance_id );

    $sql_query = "SELECT usuario.id AS id, userm.username, usuario.num_doc AS cedula, userm.firstname, userm.lastname, user_ext.tracking_status FROM {user} AS userm
    INNER JOIN {talentospilos_user_extended} as user_ext  ON user_ext.id_moodle_user= userm.id
    INNER JOIN  {talentospilos_usuario} AS usuario ON id_ases_user = usuario.id";   

    $where = " WHERE ";
    
    foreach( $student_count_trackings as $key => $student ){
         
        $where .= "usuario.id = " . $key;
        if( next( $student_count_trackings ) ){
            $where .= " OR ";
        }

    }

    $sql_query .= $where;
    $sql_query .= " AND tracking_status = 1";

    $sql_query = "SELECT id, username, cedula, firstname, lastname FROM ( $sql_query ) AS mq WHERE mq.tracking_status = 1";

    $students_with_trackings = $DB->get_records_sql( $sql_query );

    foreach($students_with_trackings as $student){
        

        $with_monitor = false;

        foreach( $monitorias as $key => $monitoria  ){
            if( $monitoria->id_estudiante == $student->id ){
                $with_monitor = true;
                break;
            }
        };

        if( !$with_monitor ){
            continue;
        }

        $tracking_team =  user_management_get_stud_mon_prac_prof( $student->id, $instance_id, $idMaxSemester );
        $monitor_object = $tracking_team->monitor;
        $trainee_object = $tracking_team->practicing;
        $professional_object = $tracking_team->professional;

        $student->cantidad_fichas = $student_count_trackings[ $student->id ];

        if ($monitor_object) {
            $student->monitor_fullname = "$monitor_object->firstname $monitor_object->lastname";
            $student->id_dphpforms_monitor = '-1';
        } else {
            $record->monitor_fullname = "NO REGISTRA";
        }
    
        if ($trainee_object) {
            $student->trainee_fullname = "$trainee_object->firstname $trainee_object->lastname";
        } else {
            $student->trainee_fullname = "NO REGISTRA";
        }
        
        if ($professional_object) {
            $student->professional_fullname = "$professional_object->firstname $professional_object->lastname";
        } else {
            $student->professional_fullname = "NO REGISTRA";
        }


        array_push($students_to_return, $student);
    }
    return $students_to_return;
}

@students_no_trackings_generate_datatable( 450299 );

function students_no_trackings_generate_datatable( $instance_id ){

    $columns = array();

    $data = get_array_students_with_trackings_count( $instance_id );        
    $monitores = array();
    $practicantes = array();
    $profesionales = array();

    foreach($data as $record){               
        array_push($monitores, $record->monitor_fullname);
        array_push($practicantes, $record->trainee_fullname);
        array_push($profesionales, $record->professional_fullname);
    }

    $monitores = array_unique($monitores);
    $practicantes = array_unique($practicantes);
    $profesionales = array_unique($profesionales);

    $monitores_options = "<select><option value=''></option> 
                             <option value='---'>---</option>";
    $practicantes_options = "<select><option value=''></option> 
                             <option value='---'>---</option>";
    $profesionales_options = "<select><option value=''></option> 
                             <option value='---'>---</option>";
        
    foreach($monitores as $monitor){
        $monitores_options .= "<option value='$monitor'> $monitor</option>";
    }

    foreach($practicantes as $practicante){
        $practicantes_options .= "<option value='$practicante'> $practicante</option>";
    }
    foreach($profesionales as $profesional){
        $profesionales_options .= "<option value='$profesional'> $profesional</option>";
    }

        
    array_push($columns, array("title"=>"Código estudiante", "name"=>"codigo", "data"=>"username"));
    array_push($columns, array("title"=>"Cédula", "name"=>"cedula", "data"=>"cedula")); 
    array_push($columns, array("title"=>"Nombres", "name"=>"firstname", "data"=>"firstname"));
    array_push($columns, array("title"=>"Apellidos", "name"=>"lastname", "data"=>"lastname"));              
    array_push($columns, array("title"=>"Cantidad de fichas", "name"=>"cantidad_seguimientos", "data"=>"cantidad_fichas"));
    array_push($columns, array("title"=>"Monitor".$monitores_options, "name"=>"monitor_fullname", "data"=>"monitor_fullname"));
    array_push($columns, array("title"=>"Practicante".$practicantes_options, "name"=>"trainee_fullname", "data"=>"trainee_fullname"));
    array_push($columns, array("title"=>"Profesional".$profesionales_options, "name"=>"professional_fullname", "data"=>"professional_fullname"));

    $data = array(
        "bsort" => false,
        "columns" => $columns,
        "data" => $data,
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
        "order"=> array(0, "desc"),
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

    return $data;

}