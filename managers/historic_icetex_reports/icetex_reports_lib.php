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
                    semestre.nombre, res.codigo_resolucion, monto_estudiante, res_est.id_estudiante, res.id_semestre, res_est.id_programa
                    FROM {talentospilos_res_estudiante} AS res_est
                    INNER JOIN {talentospilos_res_icetex} res ON res.id = res_est.id_resolucion
                    INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res.id_semestre 
                    INNER JOIN {talentospilos_usuario} usuario ON usuario.id = res_est.id_estudiante 
                    INNER JOIN {talentospilos_user_extended} uextended ON usuario.id = uextended.id_ases_user 
                    INNER JOIN {user} userm ON uextended.id_moodle_user = userm.id
                    INNER JOIN {cohort_members} co_mem ON userm.id = co_mem.userid
                    INNER JOIN {cohort} cohortm ON co_mem.cohortid = cohortm.id
                    WHERE uextended.id_academic_program = res_est.id_programa AND substring(cohortm.idnumber from 0 for 4) = 'SPP'";

    $historics = $DB->get_records_sql($sql_query);

    foreach ($historics as $historic) {
        $student_id = $historic->id_estudiante;
        $program_id = $historic->id_programa;
        $semester_id = $historic->id_semestre;

        $cancel_date = get_array_students_with_cancel($student_id, $program_id, $semester_id);

        if($cancel_date == false){
            $historic->fecha_cancel = "---";
            $historic->program_status = "ACTIVO";
        }else{
            $historic->fecha_cancel = date("Y-m-d", $cancel_date);
            $historic->program_status = "INACTIVO";
        }

        array_push($array_historics, $historic);
    }

    return $array_historics;
}

function get_array_students_with_cancel($id_student, $id_program, $id_semester){    
    global $DB;

    $sql_query = "SELECT cancel.fecha_cancelacion FROM {talentospilos_history_academ} AS academ
    INNER JOIN {talentospilos_history_cancel} cancel ON academ.id = cancel.id_history 
    WHERE academ.id_estudiante = $id_student AND academ.id_semestre = $id_semester AND academ.id_programa = $id_program";

    $result = $DB->get_record_sql($sql_query);

    if($result == false){
        return false;
    }else{
        $fecha_cancel = $result->fecha_cancelacion;
        return $fecha_cancel;
    }    
}

function get_all_cohort_names(){
    global $DB;

    $cohorts_options = "<select><option value=''></option>";

    $sql_query = "SELECT substring(idnumber from 0 for 5) AS cohort_name FROM {cohort} 
                    WHERE substring(idnumber from 0 for 4) = 'SPP'";

    $cohorts = $DB->get_records_sql($sql_query);

    foreach($cohorts as $cohort){
        $cohorts_options.= "<option value='$cohort->cohort_name'>$cohort->cohort_name</option>";
    }

    $cohorts_options .= "</select>";

    return $cohorts_options;
}


function get_all_semesters_names(){
    global $DB;

    $semesters_options = "<select><option value=''></option>";

    $sql_query = "SELECT nombre FROM {talentospilos_semestre}";

    $semesters = $DB->get_records_sql($sql_query);

    foreach($semesters as $semester){

        $semesters_options .= "<option value='$semester->nombre'>$semester->nombre</option>";
    }

    $semesters_options .= "</option>";

    return $semesters_options;
}


function get_all_resolutions_codes(){
    global $DB;

    $resolutions_options = "<select><option value=''></option>";

    $sql_query = "SELECT codigo_resolucion FROM {talentospilos_res_icetex}";

    $resolutions = $DB->get_records_sql($sql_query);

    foreach($resolutions as $resolution){

        $resolutions_options .= "<option value='$resolution->codigo_resolucion'> $resolution->codigo_resolucion</option>";
    }

    $resolutions_options .= "</select>";

    return $resolutions_options;

}


function get_resolutions_for_report(){
    global $DB;

    $resolutions_array = array();

    $sql_query = "SELECT DISTINCT res_ice.id, res_ice.codigo_resolucion, semestre.nombre, res_ice.monto_total 
                    FROM mdl_talentospilos_res_icetex AS res_ice
                        INNER JOIN mdl_talentospilos_semestre semestre ON semestre.id = res_ice.id_semestre";

    $resolutions = $DB->get_records_sql($sql_query);

    foreach ($resolutions as $resolution) {
        array_push($resolutions_array, $resolution);
    }

    return $resolutions_array;
}


function get_active_res_students($cohort){
    global $DB;

$sql_query = "SELECT Count(res_est.id) AS numero FROM {talentospilos_res_estudiante} AS res_est
                INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                WHERE substring(cohortm.idnumber from 0 for 5) = '$cohort' OR substring(cohortm.idnumber from 0 for 6) = '$cohort'";

$count = $DB->get_record_sql($sql_query)->numero;

return $count;

}

//print_r(get_active_res_students("SPP1"));

function get_active_no_res_students(){

}
