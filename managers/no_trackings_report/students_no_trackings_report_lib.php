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


require_once(dirname(__FILE__). '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php';
//require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/periods_management/periods_lib.php'; 
require_once $CFG->dirroot.'/blocks/ases/managers/dphpforms/v2/dphpforms_lib.php';   
require_once $CFG->dirroot.'/blocks/ases/managers/monitor_assignments/monitor_assignments_lib.php'; 

// $semestre = get_current_semester();
// $idMaxSemester = $semestre->max;
// $monitorias = json_encode(monitor_assignments_get_monitors_students_relationship_by_instance_n_semester( 450299, $idMaxSemester ));
// echo $monitorias;

//get_array_students_without_trackings();
/**
 * Function that returns a list of the students with trackings on the current semester
 * 
 * @see get_array_students_with_trackings()
 * @return array
 */

function get_students_without_trackings(){      

    $semestre = get_current_semester();
    $idMaxSemester = $semestre->max;

    $monitorias = monitor_assignments_get_monitors_students_relationship_by_instance_n_semester( 450299, $idMaxSemester );
    
    $intervalSemester = get_semester_interval($idMaxSemester);
    
    $list_inicio = explode(" ", $intervalSemester->fecha_inicio);
    $list_fin = explode(" ", $intervalSemester->fecha_fin);
    
    $fecha_inicio = $list_inicio[0];
    $fecha_fin = $list_fin[0];

    $estudiantes = array();

    foreach ($monitorias as $monitoria){     
        
        $xQuery = new stdClass();
        $xQuery->form = "seguimiento_pares"; // Can be alias(String) or idntifier(Number)        
        $xQuery->filterFields = [
                                ["id_estudiante",[
                                    [$monitoria->id_estudiante, "="]                                
                                    
                                    ], false],
                                ["fecha",
                                    [[$fecha_inicio,">="],[$fecha_fin,"<="]]
                                    , false]                        
                        ];
        $xQuery->orderFields = [
                                ["fecha","DESC"]
                            ];

        $xQuery->orderByDatabaseRecordDate = false; // If true, orderField is ignored. DESC
        $xQuery->recordStatus = [ "!deleted" ];// options "deleted" or "!deleted", can be both. Empty = both.
        $xQuery->selectedFields = []; // RecordId and BatabaseRecordDate are selected by default.

        $countRegistersSeguimientos = dphpformsV2_find_n_count_records( $xQuery ); 
        
        
        //$xQuery = new stdClass();
        $xQuery->form = "inasistencia"; // Can be alias(String) or idntifier(Number)        
        $xQuery->filterFields = [
                                ["in_id_estudiante",[
                                    [$monitoria->id_estudiante, "="]                                
                                    
                                    ], false],
                                ["in_fecha",
                                    [[$fecha_inicio,">="],[$fecha_fin,"<="]]
                                    , false]                        
                        ];
        $xQuery->orderFields = [
                                ["in_fecha","DESC"]
                            ];

        $xQuery->orderByDatabaseRecordDate = false; // If true, orderField is ignored. DESC
        $xQuery->recordStatus = [ "!deleted" ];// options "deleted" or "!deleted", can be both. Empty = both.
        $xQuery->selectedFields = []; // RecordId and BatabaseRecordDate are selected by default.

        $countRegistersInasistencias = dphpformsV2_find_n_count_records( $xQuery );
        
        if($countRegistersSeguimientos == 0 && $countRegistersInasistencias == 0){
            $estudiante = (object) array ('id_estudiante' => $monitoria->id_estudiante, 'cantidad_seguimientos' => $countRegistersSeguimientos, 'cantidad_inasistencias' => $countRegistersInasistencias);
            array_push($estudiantes, $estudiante);
        }                
    }
    //echo json_encode($estudiantes);
    return json_encode($estudiantes);
}



/**
 * Function that returns a list of the students with monitor but without tracking
 * 
 * @see get_array_students_without_trackings()
 * @return array
 */
function get_array_students_without_trackings(){

    global $DB;   

    $sql_query = "SELECT usuario.id AS id, userm.username, usuario.num_doc AS cedula, userm.firstname, userm.lastname FROM {user} AS userm
    INNER JOIN {talentospilos_user_extended} as user_ext  ON user_ext.id_moodle_user= userm.id
    INNER JOIN  {talentospilos_usuario} AS usuario ON id_ases_user = usuario.id
    
    WHERE 
    usuario.id IN (";    
    
    $studentsWithoutTrackings = json_decode(get_students_without_trackings(), true);
    $additionalCondition = "";

    foreach($studentsWithoutTrackings as $tracking){                 
        $additionalCondition .="'". $tracking['id_estudiante']. "', ";
    }   

    $additionalCondition.= ")";
    
    $additionalCondition = str_replace("', )", "')", $additionalCondition);    

    $sql_query .= $additionalCondition;
    //echo $sql_query;
    
    $students = $DB->get_records_sql($sql_query);      
    
    // $students_to_return = array();

    // foreach($students as $student){
    //     array_push($students_to_return, $student);
    // }
    
    // return $students_to_return;
    
    $students_to_return = array();
    
    foreach($students as $student){
        
        $monitor_object = get_assigned_monitor($student->id);
        $trainee_object = get_assigned_pract($student->id);
        $professional_object = get_assigned_professional($student->id);


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

    return ($students_to_return);
}
