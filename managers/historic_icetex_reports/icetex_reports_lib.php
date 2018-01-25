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
 * @author     Juan Pablo Moreno Muñoz
 * @package    block_ases
 * @copyright  2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');

/**
 * Function that returns an array with the students that belong to an ICETEX resolution
 * 
 * @see get_array_students_with_resolution()
 * @return array
 */
function get_array_students_with_resolution(){
    global $DB;

    $array_historics = array();

    $sql_query = "SELECT res_est.id, substring(cohortm.idnumber from 0 for 5) AS cohorte, substring(userm.username from 0 for 8) AS codigo, usuario.num_doc, userm.firstname, userm.lastname, 
                    semestre.nombre, res.codigo_resolucion, monto_estudiante, uextended.program_status
                    FROM mdl_talentospilos_res_estudiante AS res_est
                    INNER JOIN mdl_talentospilos_res_icetex res ON res.id = res_est.id_resolucion
                    INNER JOIN mdl_talentospilos_semestre semestre ON semestre.id = res_est.id_semestre 
                    INNER JOIN mdl_talentospilos_usuario usuario ON usuario.id = res_est.id_estudiante 
                    INNER JOIN mdl_talentospilos_user_extended uextended ON usuario.id = uextended.id_ases_user 
                    INNER JOIN mdl_user userm ON uextended.id_moodle_user = userm.id
                    INNER JOIN mdl_cohort_members co_mem ON userm.id = co_mem.userid
                    INNER JOIN mdl_cohort cohortm ON co_mem.cohortid = cohortm.id
                    WHERE uextended.id_academic_program = res_est.id_programa";

    $historics = $DB->get_records_sql($sql_query);

    foreach ($historics as $historic) {
        $id_es
        $id_pr
        $id_se

        array_push($array_historics, $historic);
    }

    return $array_historics;
}

function get_array_students_with_cancel($id_student, $id_program, $id_semester){    
    global $DB;

    $sql_query = "SELECT cancel.fecha_cancelacion FROM mdl_talentospilos_history_academ AS academ
    INNER JOIN mdl_talentospilos_history_cancel cancel ON academ.id = cancel.id_history 
    WHERE academ.id_estudiante = $id_student AND academ.id_semestre = $id_semester AND academ.id_programa = $id_program";

    $cancel_date = $DB->get_record_sql($sql_query)->fecha_cancelacion;

    foreach($students as $student){
        array_push($cancel_students, $student);
    }

    return $cancel_students;
}
