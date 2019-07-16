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
 * Ases block
 *
 * @author     Jeison Cardona G??mez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona G??mez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/user_management/user_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/ases_report/asesreport_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/dphpforms/v2/dphpforms_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/monitor_assignments/monitor_assignments_lib.php';

// Core cache
require_once $CFG->dirroot.'/blocks/ases/core/cache/cache.php';

/**
 * ..
 * @author Jeison Cardona G??mez. <jeison.cardona@correounivalle.edu.co>
 * @param String $username
 * @param int $semester_id
 * @param int $instance
 * @return Array 
 */

function pilos_tracking_get_tracking_count( $username, $semester_id, $instance, $is_monitor = false ){
    
    global $DB;

    $fecha_inicio = null;
    $fecha_fin = null;

    $interval = get_semester_interval($semester_id);
    if(!$interval){
        return -1;
    }
    $fecha_inicio = getdate(strtotime($interval->fecha_inicio));
    $fecha_fin = getdate(strtotime($interval->fecha_fin));

    $mon_tmp = $fecha_inicio["mon"];
    $day_tmp = $fecha_inicio["mday"];
    if( $mon_tmp < 10 ){
        $mon_tmp = "0" . $mon_tmp;
    }
    if( $day_tmp < 10 ){
        $day_tmp = "0" . $day_tmp;
    }

    $fecha_inicio_str = $fecha_inicio["year"]."-".$mon_tmp."-".$day_tmp;

    $mon_tmp = $fecha_fin["mon"];
    $day_tmp = $fecha_fin["mday"];
    if( $mon_tmp < 10 ){
        $mon_tmp = "0" . $mon_tmp;
    }
    if( $day_tmp < 10 ){
        $day_tmp = "0" . $day_tmp;
    }

    $fecha_fin_str = $fecha_fin["year"]."-".$mon_tmp."-".$day_tmp;

    $user = null;
    $user_id = null;
    $user_rol = null;

    //Get user_rol
    $sql_user = "SELECT id FROM {user} WHERE username = '$username'";
    $user = $DB->get_record_sql( $sql_user );

    if( $user ){
        $user_id = $user->id;

        $sql_rol = "SELECT * FROM {talentospilos_user_rol} AS user_rol
        INNER JOIN {talentospilos_rol} AS rol ON user_rol.id_rol = rol.id
        WHERE id_usuario = $user_id AND id_semestre = $semester_id AND id_instancia = $instance";

        $user_rol = $DB->get_record_sql( $sql_rol );
    }else{
        return -2;
    }

    if( $user_rol ){

        $sql_pro_prac = "SELECT user_rol.id_usuario, _user.firstname, _user.lastname, user_rol.id_semestre AS id_semestre, CONCAT(_user.firstname, ' ', _user.lastname) AS full_name, _user.username
        FROM {talentospilos_user_rol} AS user_rol 
        INNER JOIN {user} AS _user ON ( _user.id = user_rol.id_usuario ) 
        WHERE id_jefe = $user_id AND id_semestre = $semester_id AND estado = 1 AND id_instancia = $instance ORDER BY full_name ASC";

        $to_return = [];

        if( $user_rol->nombre_rol == "profesional_ps" ){

            //List of practicante_ps
            $list_users = $DB->get_records_sql( $sql_pro_prac );
            
            foreach( $list_users as $user ){

                $count = new stdClass();
                $count->username = $user->username;
                $count->count = pilos_tracking_general_get_count( $user->id_usuario, "practicante_ps", $fecha_inicio_str, $fecha_fin_str, $instance, $semester_id );
                
                array_push( $to_return, $count );
                
            }

            return $to_return;

        }else if( $user_rol->nombre_rol == "practicante_ps" ){

            //List of monitor_ps
            $list_users = $DB->get_records_sql( $sql_pro_prac );
            
            foreach( $list_users as $user ){

                $count = new stdClass();
                $count->username = $user->username;
                $count->count = pilos_tracking_general_get_count( $user->id_usuario, "monitor_ps", $fecha_inicio_str, $fecha_fin_str, $instance, $semester_id );
                
                array_push( $to_return, $count );
                
            }

            return $to_return;

        }else if( $user_rol->nombre_rol == "monitor_ps" ){

            $sql_mon_stud = "SELECT ME.id_estudiante AS id_ases_estudiante , U.username, ME.id_monitor
            FROM {talentospilos_monitor_estud} AS ME 
            INNER JOIN {user} AS U ON ME.id_monitor = U.id
            WHERE id_monitor = $user_id AND id_semestre = $semester_id AND id_instancia = $instance";

            //List of students
            $list_users = $DB->get_records_sql( $sql_mon_stud );
            
            foreach( $list_users as $user ){

                $count = new stdClass();
                $count->username = $user->id_ases_estudiante;
                $count->count = pilos_tracking_general_get_count( $user->id_ases_estudiante, "estudiante_t", $fecha_inicio_str, $fecha_fin_str, $instance, $semester_id );
                
                array_push( $to_return, $count );
                
            }

            return $to_return;

        }else{
            return -3;
        }

    }else{
        return -99;
    }

    return "";
}


function pilos_tracking_general_get_count( $user_id, $rol, $fecha_inicio_str, $fecha_fin_str, $instance, $semester_id ){
    
    $count['revisado_profesional'] = 0;
    $count['not_revisado_profesional'] = 0;
    $count['total_profesional'] = 0;
    $count['revisado_practicante'] = 0;
    $count['not_revisado_practicante'] = 0;
    $count['total_practicante'] = 0;

    $count['in_revisado_profesional'] = 0;
    $count['in_not_revisado_profesional'] = 0;
    $count['in_total_profesional'] = 0;
    $count['in_revisado_practicante'] = 0;
    $count['in_not_revisado_practicante'] = 0;
    $count['in_total_practicante'] = 0;
    
    //I_ID_ = Instance id
    //M_ID_ = Moodle id
    //A_ID_ = ASES id
    $cache_prefix = "TRACKING_COUNT_I_ID_".$instance."_" . ( ( ($rol == "profesional_ps") || ($rol == "practicante_ps") || ($rol == "monitor_ps") ) ? "M_ID" : "A_ID" ) . "_" ;
    
    if( core_cache_is_supported() ){
        
        try {
            
            $value = json_decode(core_cache_get_value( $cache_prefix . $user_id ));
            
            $count['revisado_profesional'] = $value->revisado_profesional;
            $count['not_revisado_profesional'] = $value->not_revisado_profesional;
            $count['total_profesional'] = $value->total_profesional;
            $count['revisado_practicante'] = $value->revisado_practicante;
            $count['not_revisado_practicante'] = $value->not_revisado_practicante;
            $count['total_practicante'] = $value->total_practicante;

            $count['in_revisado_profesional'] = $value->in_revisado_profesional;
            $count['in_not_revisado_profesional'] = $value->in_not_revisado_profesional;
            $count['in_total_profesional'] = $value->in_total_profesional;
            $count['in_revisado_practicante'] = $value->in_revisado_practicante;
            $count['in_not_revisado_practicante'] = $value->in_not_revisado_practicante;
            $count['in_total_practicante'] = $value->in_total_practicante;
            
            return $count;
            
        } catch (Exception $exc) {}
            
    }
    
    $student_list_ids = [];
    $xquery_seguimiento_pares_filterFields = [
        ["fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
        ["revisado_profesional",[["%%","LIKE"]], false],
        ["revisado_practicante",[["%%","LIKE"]], false]
    ];
    $xquery_inasistencia_filterFields = [
        ["in_fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
        ["in_revisado_profesional",[["%%","LIKE"]], false],
        ["in_revisado_practicante",[["%%","LIKE"]], false]
    ];

    if( $rol == "profesional_ps" ){
        
        $practicant_from_profesional = monitor_assignments_get_practicants_from_professional( $instance, $user_id , $semester_id );
        foreach( $practicant_from_profesional as $key__ => $pract ){
            $monitors_from_practicant = monitor_assignments_get_monitors_from_practicant( $instance, $pract->id , $semester_id );
            foreach( $monitors_from_practicant as $key_ => $monitor ){
                $students_from_monitor = monitor_assignments_get_students_from_monitor( $instance, $monitor->id , $semester_id );
                foreach( $students_from_monitor as $key => $student ){
                    array_push( $student_list_ids, $student );
                }
            }
        }
        
        if( count($student_list_ids) == 0 ){
            return $count;
        }

        //Pares
        foreach( $student_list_ids as $key => $student ){
            array_push( $xquery_seguimiento_pares_filterFields, ["id_estudiante", [[ $student->id, "=" ]], false ] );
        }
        array_push( $xquery_seguimiento_pares_filterFields, ["id_profesional",[["%%","LIKE"]], false] );

        //Inasistencia
        foreach( $student_list_ids as $key => $student ){
            array_push( $xquery_inasistencia_filterFields, ["in_id_estudiante", [[ $student->id, "=" ]], false ] );
        }
        array_push( $xquery_inasistencia_filterFields, ["in_id_profesional",[["%%","LIKE"]], false] );
        
    }else if( $rol == "practicante_ps" ){
        
        $monitors_from_practicant = monitor_assignments_get_monitors_from_practicant( $instance, $user_id , $semester_id );
        foreach( $monitors_from_practicant as $key_ => $monitor ){
            $students_from_monitor = monitor_assignments_get_students_from_monitor( $instance, $monitor->id , $semester_id );
            foreach( $students_from_monitor as $key => $student ){
                array_push( $student_list_ids, $student );
            }
        }
        
        if( count($student_list_ids) == 0 ){
            return $count;
        }

        //Pares
        foreach( $student_list_ids as $key => $student ){
            array_push( $xquery_seguimiento_pares_filterFields, ["id_estudiante", [[ $student->id, "=" ]], false ] );
        }
        array_push( $xquery_seguimiento_pares_filterFields, ["id_practicante",[["%%","LIKE"]], false] );

        //Inasistencia
        foreach( $student_list_ids as $key => $student ){
            array_push( $xquery_inasistencia_filterFields, ["in_id_estudiante", [[ $student->id, "=" ]], false ] );
        }
        array_push( $xquery_inasistencia_filterFields, ["in_id_practicante",[["%%","LIKE"]], false] );

    }else if( $rol == "monitor_ps" ){

        $students_from_monitor = monitor_assignments_get_students_from_monitor( $instance, $user_id , $semester_id );
        
        if( count($students_from_monitor) == 0 ){
            return $count;
        }
        
        //Pares
        foreach( $students_from_monitor as $key => $student ){
            array_push( $xquery_seguimiento_pares_filterFields, ["id_estudiante", [[ $student->id, "=" ]], false ] );
        }
        array_push( $xquery_seguimiento_pares_filterFields, ["id_monitor",[["%%","LIKE"]], false] );
        //Inasistencia
        foreach( $students_from_monitor as $key => $student ){
            array_push( $xquery_inasistencia_filterFields, ["in_id_estudiante", [[ $student->id, "=" ]], false ] );
        }
        array_push( $xquery_inasistencia_filterFields, ["in_id_monitor",[["%%","LIKE"]], false] );

    }else if( $rol == "estudiante_t" ){

        array_push( $xquery_seguimiento_pares_filterFields, ["id_estudiante",[[$user_id,"="]], false] );
        array_push( $xquery_inasistencia_filterFields, ["in_id_estudiante",[[$user_id,"="]], false] );

    }

    $xQuery = new stdClass();
    $xQuery->form = "seguimiento_pares";
    $xQuery->filterFields = $xquery_seguimiento_pares_filterFields;
    $xQuery->orderFields = [["fecha","DESC"]];
    $xQuery->orderByDatabaseRecordDate = true; 
    $xQuery->recordStatus = [ "!deleted" ];
    $xQuery->selectedFields = []; 

    $trackings = dphpformsV2_find_records( $xQuery );

    $rev_pro = 0;
    $not_rev_pro = 0;
    $rev_prac = 0;
    $not_rev_prac = 0;

    foreach( $trackings as $track ){
        if( $track["revisado_profesional"] == 0 ){
            $rev_pro++;
        }else{
            $not_rev_pro++;
        }
        if( $track["revisado_practicante"] == 0 ){
            $rev_prac++;
        }else{
            $not_rev_prac++;
        }
    }

    $xQuery = new stdClass();
    $xQuery->form = "inasistencia";
    $xQuery->filterFields = $xquery_inasistencia_filterFields;
    $xQuery->orderFields = [["in_fecha","DESC"]];
    $xQuery->orderByDatabaseRecordDate = true; 
    $xQuery->recordStatus = [ "!deleted" ];
    $xQuery->selectedFields = []; 

    $in_trackings = dphpformsV2_find_records( $xQuery );

    $in_rev_pro = 0;
    $in_not_rev_pro = 0;
    $in_rev_prac = 0;
    $in_not_rev_prac = 0;

    foreach( $in_trackings as $track ){
        if( $track["in_revisado_profesional"] == 0 ){
            $in_rev_pro++;
        }else{
            $in_not_rev_pro++;
        }
        if( $track["in_revisado_practicante"] == 0 ){
            $in_rev_prac++;
        }else{
            $in_not_rev_prac++;
        }
    }    

    $count['revisado_profesional'] = $rev_pro;
    $count['not_revisado_profesional'] = $not_rev_pro;
    $count['total_profesional'] = $rev_pro + $not_rev_pro;
    $count['revisado_practicante'] = $rev_prac;
    $count['not_revisado_practicante'] = $not_rev_prac;
    $count['total_practicante'] = $rev_prac + $not_rev_prac;

    $count['in_revisado_profesional'] = $in_rev_pro;
    $count['in_not_revisado_profesional'] = $in_not_rev_pro;
    $count['in_total_profesional'] = $in_rev_pro + $in_not_rev_pro;
    $count['in_revisado_practicante'] = $in_rev_prac;
    $count['in_not_revisado_practicante'] = $in_not_rev_prac;
    $count['in_total_practicante'] = $in_rev_prac + $in_not_rev_prac;
    
    if( core_cache_is_supported() ){
        
        try {
            
            $value = new stdClass();
            
            $value->revisado_profesional = $count['revisado_profesional'];
            $value->not_revisado_profesional = $count['not_revisado_profesional'];
            $value->total_profesional = $count['total_profesional'];
            $value->revisado_practicante = $count['revisado_practicante'];
            $value->not_revisado_practicante = $count['not_revisado_practicante'];
            $value->total_practicante = $count['total_practicante'];

            $value->in_revisado_profesional = $count['in_revisado_profesional'];
            $value->in_not_revisado_profesional = $count['in_not_revisado_profesional'];
            $value->in_total_profesional = $count['in_total_profesional'];
            $value->in_revisado_practicante = $count['in_revisado_practicante'];
            $value->in_not_revisado_practicante = $count['in_not_revisado_practicante'];
            $value->in_total_practicante = $count['in_total_practicante'];
            
            core_cache_put_value( $cache_prefix . $user_id, json_encode($value), "tracking_count", time() + (60*60*12) );
            
        } catch (Exception $exc) {}
            
    }

    return $count;
}

?>
