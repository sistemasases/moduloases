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
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/user_management/user_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/ases_report/asesreport_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/dphpforms/v2/dphpforms_lib.php';

/**
 * ..
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param String $username
 * @param int $semester_id
 * @param int $instance
 * @return Array 
 */

function pilos_tracking_get_tracking_count( $username, $semester_id, $instance ){
    
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


    //Get user_rol
    $sql_user = "SELECT id FROM {user} WHERE username = '$username'";
    $user = $DB->get_record_sql( $sql_user );
    
    $user_id = null;

    if( $user ){
        $user_id = $user->id;
    }else{
        return -2;
    }

    $sql_rol = "SELECT * FROM {talentospilos_user_rol} AS user_rol
    INNER JOIN {talentospilos_rol} AS rol ON user_rol.id_rol = rol.id
    WHERE id_usuario = $user_id AND id_semestre = $semester_id AND id_instancia = $instance";

    $user_rol = $DB->get_record_sql( $sql_rol );

    if( $user_rol ){

        $sql_pro_prac = "SELECT user_rol.id_usuario, _user.firstname, _user.lastname, user_rol.id_semestre AS id_semestre, CONCAT(_user.firstname, _user.lastname) AS full_name, _user.username
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
                $count->count = pilos_tracking_general_get_count( $user->id_usuario, "practicante_ps", $fecha_inicio_str, $fecha_fin_str );
                
                array_push( $to_return, $count );
                
            }

            return $to_return;

        }else if( $user_rol->nombre_rol == "practicante_ps" ){

            //List of monitor_ps
            $list_users = $DB->get_records_sql( $sql_pro_prac );
            
            foreach( $list_users as $user ){

                $count = new stdClass();
                $count->username = $user->username;
                $count->count = pilos_tracking_general_get_count( $user->id_usuario, "monitor_ps", $fecha_inicio_str, $fecha_fin_str );
                
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


function pilos_tracking_general_get_count( $user_id, $rol, $fecha_inicio_str, $fecha_fin_str ){
    
    $nombre_campo = "";
 
    if( $rol == "profesional_ps" ){
        $nombre_campo = "id_profesional";
    }else if( $rol == "practicante_ps" ){
        $nombre_campo = "id_practicante";
    }else if( $rol == "monitor_ps" ){
        $nombre_campo = "id_monitor";
    }

    $xQuery = new stdClass();
    $xQuery->form = "seguimiento_pares";
    $xQuery->filterFields = [["fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
                             ["revisado_profesional",[["%%","LIKE"]], false],
                             ["revisado_practicante",[["%%","LIKE"]], false],
                             [$nombre_campo,[[$user_id,"="]], false]
                            ];
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
    $xQuery->filterFields = [["in_fecha",[[$fecha_inicio_str,">="],[$fecha_fin_str,"<="]], false],
                             ["in_revisado_profesional",[["%%","LIKE"]], false],
                             ["in_revisado_practicante",[["%%","LIKE"]], false],
                             ["in_$nombre_campo",[[$user_id,"="]], false]
                            ];
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

    return $count;
}

?>
