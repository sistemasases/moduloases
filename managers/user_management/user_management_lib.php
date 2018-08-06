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
require_once(dirname(__FILE__). '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/user_management/user_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/ases_report/asesreport_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';

function user_management_get_moodle_user( $user_id ){

    global $DB;

    if( !$user_id ){
        return null;
    }

    $sql = "SELECT U.id, U.firstname, U.lastname FROM {user} AS U WHERE U.id = $user_id";

    return $DB->get_record_sql( $sql );
}

function user_management_get_ases_user( $ases_id ){

    global $DB;

    if( !$ases_id ){
        return null;
    }

    $sql = "SELECT  id, firstname, lastname 
            FROM {user} WHERE id = (
                    SELECT id_moodle_user AS id_moodle 
                    FROM {talentospilos_user_extended} 
                    WHERE id_ases_user = $ases_id
                )";

    return $DB->get_record_sql( $sql );
}

function user_management_get_boss( $user_id ){

    global $DB;

    if( !$user_id ){
        return null;
    }

    $sql = "SELECT U.id, U.firstname, U.lastname FROM {user} AS U 
    INNER JOIN (
            SELECT id_jefe
            FROM {talentospilos_user_rol}
            WHERE id_semestre = ". get_current_semester()->max ." AND id_usuario = $user_id AND estado = 1
        ) AS MS 
    ON MS.id_jefe = U.id";

    return $DB->get_record_sql( $sql );
}

function user_management_get_student_monitor( $ases_id ){

    global $DB;

    if( !$ases_id ){
        return null;
    }

    $sql = "SELECT U.id, U.firstname, U.lastname 
    FROM {user} AS U 
    INNER JOIN (
            SELECT id_monitor
            FROM {talentospilos_monitor_estud}
            WHERE id_semestre = ". get_current_semester()->max ." AND id_estudiante = $ases_id 
        ) AS MS 
    ON MS.id_monitor = U.id";

    return $DB->get_record_sql( $sql );
}

function user_management_get_monitor_practicing( $user_id ){
    return user_management_get_boss( $user_id );
}

function user_management_get_practicing_prof( $user_id ){
    return user_management_get_boss( $user_id );
}

/**
 * Funcion that return student, monitor, practicing and professional related to student.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $ases_id
 * @return stdClass
 */
function user_management_get_stud_mon_prac_prof( $ases_id ){

    if( !$ases_id ){
        return null;
    }

    $student = null;
    $monitor = null;
    $pract = null;
    $prof = null;

    $student = user_management_get_ases_user( $ases_id );
    if( $student ){
        $student->id = $ases_id;
        $monitor =  user_management_get_student_monitor( $ases_id );
        if( $monitor ){
            $pract = user_management_get_monitor_practicing( $monitor->id );
            if( $pract ){
                $prof = user_management_get_practicing_prof( $pract->id );
            }
        }
    }

    $stud_mon_prac_prof = new stdClass();
    $stud_mon_prac_prof->student = $student;
    $stud_mon_prac_prof->monitor = $monitor;
    $stud_mon_prac_prof->practicing = $pract;
    $stud_mon_prac_prof->professional = $prof;

    return $stud_mon_prac_prof;
}

function user_management_get_crea_stud_mon_prac_prof( $ases_id, $created_by_id ){

    if( !$ases_id || !$created_by_id ){
        return null;
    }

    $to_return = user_management_get_stud_mon_prac_prof( $ases_id );
    $to_return->created_by = user_management_get_moodle_user( $created_by_id );

    return $to_return;
}



?>
