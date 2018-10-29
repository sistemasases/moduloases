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
//require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php';
//require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';
//require_once $CFG->dirroot.'/blocks/ases/managers/periods_management/periods_lib.php'; 


/**
 * Function that returns a list of the students with monitor but without tracking
 * 
 * @see get_array_students_wihtout_trackings()
 * @return array
 */
function get_array_students_without_trackings(){

    global $DB;
    
    $sql_query = "SELECT userm.id, userm.username FROM {user} AS userm
                INNER JOIN {talentospilos_user_extended} as user_ext  ON user_ext.id_moodle_user= userm.id
                INNER JOIN (SELECT *,id AS idtalentos FROM {talentospilos_usuario}) AS usuario ON id_ases_user = usuario.id    
                WHERE        
	            idtalentos IN (SELECT id_estudiante FROM {talentospilos_monitor_estud} WHERE id_semestre=8)    
	            AND 
                idtalentos NOT IN (SELECT id_estudiante FROM {talentospilos_seg_estudiante} AS seg_est
                                            INNER JOIN {talentospilos_seguimiento} AS seguimiento ON seguimiento.id = seg_est.id_seguimiento
                                            WHERE TO_TIMESTAMP(seguimiento.fecha) >= (SELECT fecha_inicio FROM {talentospilos_semestre} AS semestre WHERE id='8'))";

    $students = $DB->get_records_sql($sql_query);    
    $students_to_return = array();
    
    foreach($students as $student){
        array_push($students_to_return, $student);
    }

    return $students_to_return;
}
