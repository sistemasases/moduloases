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
    $students_spt = array();
    $array_spt_spp = array();
    $final_array = array();

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
                    WHERE uextended.id_academic_program = res_est.id_programa 
                        AND substring(cohortm.idnumber from 0 for 4) = 'SPP'";

    $historics = $DB->get_records_sql($sql_query);

    $students_spt = get_student_resolution_spt();

    foreach ($historics as $historic) {
        $student_id = $historic->id_estudiante;
        $program_id = $historic->id_programa;
        $semester_id = $historic->id_semestre;

        $cancel_date = get_student_cancel_date($student_id, $program_id, $semester_id);

        if($cancel_date == false){
            $historic->fecha_cancel = "---";
            $historic->program_status = "ACTIVO";
        }else{
            $historic->fecha_cancel = date("Y-m-d", $cancel_date);
            $historic->program_status = "INACTIVO";
        }

        $historic->monto_estudiante = "$".number_format($historic->monto_estudiante, 0, ',', '.');
        array_push($array_historics, $historic);
    }

    $array_spt_spp = array_merge($array_historics, $students_spt);

    $students_no_res = get_active_no_res_students();

    $final_array = array_merge($array_spt_spp, $students_no_res);

    return $final_array;
}

//print_r(get_array_students_with_resolution());

function get_student_resolution_spt(){
    global $DB;

    $array_historics = array();    
    
    $sql_query = "SELECT res_est.id, substring(cohortm.idnumber from 0 for 5) AS cohorte, substring(userm.username from 0 for 8) AS codigo, 
                    usuario.num_doc, userm.firstname, userm.lastname, semestre.nombre, res.codigo_resolucion, monto_estudiante, res_est.id_estudiante, 
                    res.id_semestre, res_est.id_programa                    
                    FROM {talentospilos_res_estudiante} AS res_est
                    INNER JOIN {talentospilos_res_icetex} res ON res.id = res_est.id_resolucion
                    INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res.id_semestre 
                    INNER JOIN {talentospilos_usuario} usuario ON usuario.id = res_est.id_estudiante 
                    INNER JOIN {talentospilos_user_extended} uextended ON usuario.id = uextended.id_ases_user 
                    INNER JOIN {user} userm ON uextended.id_moodle_user = userm.id
                    INNER JOIN {cohort_members} co_mem ON userm.id = co_mem.userid
                    INNER JOIN {cohort} cohortm ON co_mem.cohortid = cohortm.id
                    WHERE res_est.id_programa = 1 AND substring(cohortm.idnumber from 0 for 4) = 'SPT'";

    $historics = $DB->get_records_sql($sql_query);

    foreach ($historics as $historic) {
        $student_id = $historic->id_estudiante;
        $program_id = $historic->id_programa;
        $semester_id = $historic->id_semestre;

        $cancel_date = get_student_cancel_date($student_id, $program_id, $semester_id);

        if($cancel_date == false){
            $historic->fecha_cancel = "---";
            $historic->program_status = "ACTIVO";
        }else{
            $historic->fecha_cancel = date("Y-m-d", $cancel_date);
            $historic->program_status = "INACTIVO";
        }

        $historic->monto_estudiante = "$".number_format($historic->monto_estudiante, 0, ',', '.');
        array_push($array_historics, $historic);
    }

    return $array_historics;
}

/**
 * Function that returns the date when an student quitted a program in the semester 
 * 
 * @see get_student_cancel_date($id_student, $id_program, $id_semester)
 * @param $id_student -> id of a student
 * @param $id_program -> id of a program
 * @param $id_semester -> id of the semester
 * @return array
 */
function get_student_cancel_date($id_student, $id_program, $id_semester){    
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

/**
 * Function that returns a string with the names of all cohorts
 * 
 * @see get_all_cohort_names()
 * @return string
 */
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

/**
 * Function that returns a string with the names of all semesters
 * 
 * @see get_all_semesters_names()
 * @return string
 */
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

/**
 * Function that returns a string with the codes of all resolutions
 * 
 * @see get_all_resolutions_codes()
 * @return string
 */
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


function sum_amount_students_resolutions($id_resolution){
    global $DB;

    $sql_query = "SELECT sum(monto_estudiante) AS sum_am_res FROM {talentospilos_res_estudiante} 
                    WHERE id_resolucion = $id_resolution";

    $sum_res = $DB->get_record_sql($sql_query);

    if(!$sum_res->sum_am_res){
        return 0;
    }else{
        return $sum_res->sum_am_res;
    }
}

//print_r(sum_amount_students_resolutions(1));

/**
 * Functions that returns an array containing the resolutions for the report
 * 
 * @see get_resolutions_for_report
 * @return array
 */
function get_resolutions_for_report(){
    global $DB;

    $resolutions_array = array();

    $total_am_students = 0;
    $total_subtraction = 0;

    $sql_query = "SELECT DISTINCT res_ice.id, res_ice.codigo_resolucion, semestre.nombre, res_ice.nota_credito, res_ice.monto_total 
                    FROM mdl_talentospilos_res_icetex AS res_ice
                        INNER JOIN mdl_talentospilos_semestre semestre ON semestre.id = res_ice.id_semestre";

    $resolutions = $DB->get_records_sql($sql_query);
    
    foreach ($resolutions as $resolution) {
        if(is_null($resolution->nota_credito)){
            $resolution->nota_credito = "---";
        }
        
        $total_am_students = sum_amount_students_resolutions($resolution->id);
        $total_subtraction = $resolution->monto_total - $total_am_students;
        $resolution->monto_total = "$".number_format($resolution->monto_total, 0, ',', '.');
        $resolution->monto_sum_estudiantes = "$".number_format($total_am_students, 0, ',', '.');
        $resolution->monto_diferencia = "$".number_format($total_subtraction, 0, ',', '.');
        array_push($resolutions_array, $resolution);
    }

    return $resolutions_array;
}

//print_r(get_resolutions_for_report());

/**
 * Function that returns an array with the number of active students
 * 
 * @see get_count_active_res_students($cohort)
 * @param $cohort -> name of the cohort
 * @return array
 */
function get_count_active_res_students($cohort){
    global $DB;

    $array_count = array();

    $sql_query = "SELECT row_number() over(), Count(res_est.id) AS num_act_res, semestre.nombre AS semestre, sum(res_est.monto_estudiante) AS monto_act_res
                    FROM {talentospilos_res_estudiante} AS res_est
                        INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                        INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre
                        INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                        INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                        INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                        WHERE substring(cohortm.idnumber from 0 for 5) = '$cohort' OR substring(cohortm.idnumber from 0 for 6) = '$cohort'
                        AND res_est.id_estudiante                        
                        
                        NOT IN 
                        
                        (SELECT DISTINCT academ.id_estudiante 
                        FROM {talentospilos_res_estudiante} AS res_est
                        INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                        INNER JOIN {talentospilos_history_academ} academ ON academ.id_estudiante = res_est.id_estudiante
                        INNER JOIN {talentospilos_semestre} semestre  ON semestre.id = academ.id_semestre
                        INNER JOIN {talentospilos_history_cancel} cancel ON cancel.id_history = academ.id
                        INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                        INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                        WHERE substring(cohortm.idnumber from 0 for 5) = '$cohort' OR substring(cohortm.idnumber from 0 for 6) = '$cohort')
                        GROUP BY semestre.nombre";

    $counts = $DB->get_records_sql($sql_query);

    foreach($counts as $count){
        $count->cohort = $cohort;
        $count->monto_act_res = "$".number_format($count->monto_act_res, 0, ',', '.');
        array_push($array_count, $count);
    }

    return $array_count;
}

//print_r(get_count_active_res_students('SPP1'));

/**
 * Function that returns an array with all the inactive students that belong to a resolution
 * 
 * @see get_count_inactive_res_students($cohort)
 * @param $cohort -> name of the cohort
 * @return array
 */
function get_count_inactive_res_students($cohort){
    global $DB;

    $array_count = array();

    $sql_query = "SELECT row_number() over(), Count(res_est.id) AS num_inact_res, semestre.nombre AS semestre, sum(res_est.monto_estudiante) AS monto_inact_res 
                    FROM {talentospilos_res_estudiante} AS res_est
                        INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                        INNER JOIN {talentospilos_history_academ} academ ON academ.id_estudiante = res_est.id_estudiante
                        INNER JOIN {talentospilos_semestre} semestre  ON semestre.id = academ.id_semestre
                        INNER JOIN {talentospilos_history_cancel} cancel ON cancel.id_history = academ.id
                        INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                        INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                        WHERE substring(cohortm.idnumber from 0 for 5) = '$cohort' OR substring(cohortm.idnumber from 0 for 6) = '$cohort'
                        GROUP BY semestre.nombre";

    $counts = $DB->get_records_sql($sql_query);

    foreach($counts as $count){
        $count->cohort = $cohort;
        $count->monto_inact_res = "$".number_format($count->monto_inact_res, 0, ',', '.');
        array_push($array_count, $count);
    }

    return $array_count;
}

//print_r(get_count_inactive_res_students('SPP1'));

/**
 * Function that returns an array with the number of students that are active and don't belong to a resolution
 * 
 * @see get_count_active_no_res_students($cohort)
 * @param $cohort -> name of the cohort
 * @return array
 */
function get_count_active_no_res_students($cohort){
    global $DB;

    $array_count = array();

    $sql_query = "SELECT row_number() over(), Count(academ.id_estudiante) AS num_act_no_res, semestre.nombre AS semestre
                    FROM {talentospilos_history_academ} AS academ
                    INNER JOIN {talentospilos_semestre} semestre ON semestre.id = academ.id_semestre
                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = academ.id_estudiante
                    INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                    WHERE substring(cohortm.idnumber from 0 for 5) = '$cohort' OR substring(cohortm.idnumber from 0 for 6) = '$cohort'
                    AND  academ.id 
                    NOT IN 
                    (SELECT id_history FROM {talentospilos_history_cancel})
                    AND uext.id_ases_user    

                    NOT IN

                    (SELECT DISTINCT res_est.id_estudiante
                    FROM {talentospilos_res_estudiante} AS res_est
                    INNER JOIN {talentospilos_res_icetex} AS res_ice ON res_ice.id = res_est.id_resolucion
                    INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre)
                    GROUP BY semestre.nombre";

    $counts = $DB->get_records_sql($sql_query);

    foreach($counts as $count){
        $count->cohort = $cohort;
        $count->monto_act_no_res = "$0";
        array_push($array_count, $count);
    }

    return $array_count;
}

//print_r(get_count_active_no_res_students('SPP1'));

/**
 * Function that returns an array with all the necessary information for the summary report
 * 
 * @see get_info_summary_report($cohort)
 * @param $cohort -> name of the cohort
 * @return array
 */
function get_info_summary_report($cohort){

    $array_act_res = array();
    $array_inact_res = array();
    $array_act_no_res = array();

    $array_objects = array();

    $array_act_res = get_count_active_res_students($cohort);
    $array_inact_res = get_count_inactive_res_students($cohort);
    $array_act_no_res = get_count_active_no_res_students($cohort);

    if(count($array_act_res) > 0){
        foreach($array_act_res as $act_res){
            if(array_key_exists($act_res->semestre, $array_objects)){
                $array_objects[$act_res->semestre]->num_act_res = $act_res->num_act_res;
                $array_objects[$act_res->semestre]->monto_act_res = $act_res->monto_act_res;
                $array_objects[$act_res->semestre]->semestre = $act_res->semestre;
                $array_objects[$act_res->semestre]->cohort = $act_res->cohort;

            }else{
                $array_objects[$act_res->semestre] = new stdClass();
                $array_objects[$act_res->semestre]->num_act_res = $act_res->num_act_res;
                $array_objects[$act_res->semestre]->monto_act_res = $act_res->monto_act_res;
                $array_objects[$act_res->semestre]->semestre = $act_res->semestre;
                $array_objects[$act_res->semestre]->cohort = $act_res->cohort;
            }
        }
    }

    if(count($array_inact_res) > 0){
        foreach($array_inact_res as $inact_res){
            if(array_key_exists($inact_res->semestre, $array_objects)){
                $array_objects[$inact_res->semestre]->num_inact_res = $inact_res->num_inact_res;
                $array_objects[$inact_res->semestre]->monto_inact_res = $inact_res->monto_inact_res;
                $array_objects[$inact_res->semestre]->semestre = $inact_res->semestre;
                $array_objects[$inact_res->semestre]->cohort = $inact_res->cohort;              

            }else{
                $array_objects[$inact_res->semestre] = new stdClass();
                $array_objects[$inact_res->semestre]->num_inact_res = $inact_res->num_inact_res;
                $array_objects[$inact_res->semestre]->monto_inact_res = $inact_res->monto_inact_res;
                $array_objects[$inact_res->semestre]->semestre = $inact_res->semestre;
                $array_objects[$inact_res->semestre]->cohort = $inact_res->cohort;
            }
        }        
    }

    if(count($array_act_no_res) > 0){
        foreach($array_act_no_res as $act_no_res){
            if(array_key_exists($act_no_res->semestre, $array_objects)){
                $array_objects[$act_no_res->semestre]->num_act_no_res = $act_no_res->num_act_no_res;
                $array_objects[$act_no_res->semestre]->monto_act_no_res = $act_no_res->monto_act_no_res;
                $array_objects[$act_no_res->semestre]->semestre = $act_no_res->semestre;
                $array_objects[$act_no_res->semestre]->cohort = $act_no_res->cohort;

            }else{
                $array_objects[$act_no_res->semestre] = new stdClass();
                $array_objects[$act_no_res->semestre]->num_act_no_res = $act_no_res->num_act_no_res;
                $array_objects[$act_no_res->semestre]->monto_act_no_res = $act_no_res->monto_act_no_res; 
                $array_objects[$act_no_res->semestre]->semestre = $act_no_res->semestre;
                $array_objects[$act_no_res->semestre]->cohort = $act_no_res->cohort;
            }
        }        
    }

    $array_final = array();

    foreach($array_objects as $object){

        if(!isset($object->num_act_res)){
            $object->num_act_res = "---";
        }

        if(!isset($object->monto_act_res)){
            $object->monto_act_res = "---";
        }

        if(!isset($object->num_inact_res)){
            $object->num_inact_res = "---";
        }

        if(!isset($object->monto_inact_res)){
            $object->monto_inact_res = "---";
        }

        if(!isset($object->num_act_no_res)){
            $object->num_act_no_res = "---";
        }

        if(!isset($object->monto_act_no_res)){
            $object->monto_act_no_res = "---";
        }

        array_push($array_final, $object);
    }

    return $array_final;
}

//print_r(get_info_summary_report('SPP1'));

//Returns the student that does not have resolution
function get_active_no_res_students(){
    global $DB;
    
    $students = array();
    $final_students = array();

    $sql_query = "SELECT id_estudiante
                    FROM {talentospilos_history_academ} AS academ
                    WHERE academ.id 
                    NOT IN 
                    (SELECT id_history FROM {talentospilos_history_cancel})                    
                    EXCEPT                     
                    SELECT id_estudiante
                    FROM {talentospilos_res_estudiante}";

    $results = $DB->get_records_sql($sql_query);

    foreach($results as $result){
        $student = get_info_student($result->id_estudiante);
        $final_students = array_merge($students, $student);
    }

    return $final_students;
}

//print_r(get_active_no_res_students());


/**
 * Function that returns an array containing the information of an student
 * 
 * @see get_info_student($student_id)
 * @param $student_id -> id of an student
 * @return array
 */
function get_info_student($student_id){
    global $DB;

    $info_students = array();

    $sql_query = "SELECT academ.id, substring(cohortm.idnumber from 0 for 5) AS cohorte, 
                        substring(userm.username from 0 for 8) AS codigo, 
                        usuario.num_doc, userm.firstname, userm.lastname, semestre.nombre, 
                        academ.id_estudiante, academ.id_semestre, academ.id_programa
                    FROM {talentospilos_history_academ} AS academ
                        INNER JOIN {talentospilos_semestre} semestre ON semestre.id = academ.id_semestre 
                        INNER JOIN {talentospilos_usuario} usuario ON usuario.id = academ.id_estudiante 
                        INNER JOIN {talentospilos_user_extended} uextended ON usuario.id = uextended.id_ases_user 
                        INNER JOIN {user} userm ON uextended.id_moodle_user = userm.id
                        INNER JOIN {cohort_members} co_mem ON userm.id = co_mem.userid
                        INNER JOIN {cohort} cohortm ON co_mem.cohortid = cohortm.id
                        WHERE uextended.id_academic_program = academ.id_programa 
                                AND substring(cohortm.idnumber from 0 for 4) = 'SPP'
                                AND academ.id_estudiante = '$student_id'";

    $students = $DB->get_records_sql($sql_query);
    
    foreach($students as $student){
        $student->codigo_resolucion = "---";
        $student->monto_estudiante = "$0";
        $student->fecha_cancel = "---";
        $student->program_status = "ACTIVO";

        array_push($info_students, $student);
    }

    return $info_students;
} 
