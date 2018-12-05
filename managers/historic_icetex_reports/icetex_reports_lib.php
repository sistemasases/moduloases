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
function get_array_students_with_resolution($no_res_bool){
    global $DB;

    $array_historics = array();
    
    $sql_query = "SELECT row_number() over(), spp_students.id_ases_user, spp_students.cohorte, spp_students.num_doc, substring(spp_students.username from 0 for 8) AS codigo, 
                    spp_students.lastname, spp_students.firstname, spp_students.nombre_semestre, res_students.codigo_resolucion,
                    res_students.monto_estudiante, academic_students.fecha_cancel, academic_students.promedio_semestre, status_icetex.nombre_estado,
                    CASE WHEN (academic_students.fecha_cancel IS NULL AND academic_students.promedio_semestre IS NOT NULL)
                                THEN '-ACTIVO'
                        WHEN (academic_students.promedio_semestre IS NULL)
                                THEN '-INACTIVO'		
                    END AS program_status,
                    CASE WHEN (res_students.codigo_resolucion IS NULL)
                                THEN '---'
						WHEN (res_students.codigo_resolucion IS NOT NULL)
								THEN res_students.codigo_resolucion
                    END AS codigo_resolucion,
                    CASE WHEN ( (academic_students.fecha_cancel IS NULL AND academic_students.promedio_semestre IS NOT NULL) 
                                    AND status_icetex.nombre_estado LIKE '5%')
                                THEN '-ACTIVO'
                        WHEN (academic_students.promedio_semestre IS NULL OR status_icetex.nombre_estado LIKE '3%' 
                                    OR status_icetex.nombre_estado IS NULL)
                                THEN '-INACTIVO'
                    END AS est_ice_sra
                FROM
                (SELECT user_extended.id_ases_user, moodle_user.lastname, moodle_user.firstname, cohorts.idnumber, semestre.id AS id_semestre, semestre.nombre AS nombre_semestre, 
                    usuario.num_doc, moodle_user.username, substring(cohorts.idnumber from 0 for 5) AS cohorte
                FROM {cohort_members} AS members
                INNER JOIN {cohort} AS cohorts ON members.cohortid = cohorts.id
                INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = members.userid
                INNER JOIN {talentospilos_usuario} AS usuario ON usuario.id = user_extended.id_ases_user
                INNER JOIN {user} AS moodle_user ON moodle_user.id = user_extended.id_moodle_user
                CROSS JOIN {talentospilos_semestre} AS semestre 
                WHERE cohorts.idnumber LIKE 'SPP%' AND user_extended.tracking_status = 1) AS spp_students 

                LEFT JOIN 

                (SELECT res_student.id_estudiante, semestre.nombre, semestre.id AS id_semestre, res_icetex.id AS res_icetex, res_icetex.codigo_resolucion, res_student.monto_estudiante
                FROM {talentospilos_res_estudiante} AS res_student
                INNER JOIN {talentospilos_res_icetex} AS res_icetex ON res_icetex.id = res_student.id_resolucion
                INNER JOIN {talentospilos_semestre} AS semestre ON semestre.id = res_icetex.id_semestre
                ) AS res_students

                ON (spp_students.id_ases_user = res_students.id_estudiante AND spp_students.id_semestre = res_students.id_semestre)

                LEFT JOIN 

                (SELECT DISTINCT ON (academ.id_estudiante, academ.id_semestre) academ.id_estudiante, academ.id_semestre, academ.promedio_semestre,
                            to_timestamp(cancel.fecha_cancelacion) AS fecha_cancel
                FROM {talentospilos_history_academ} AS academ
                LEFT JOIN {talentospilos_history_cancel} cancel ON cancel.id_history = academ.id) AS academic_students

                ON (spp_students.id_ases_user = academic_students.id_estudiante AND spp_students.id_semestre = academic_students.id_semestre)
                
                LEFT JOIN
                
                (SELECT hist_stat.id_estudiante, est_icetex.nombre AS nombre_estado, hist_stat.id_semestre
                FROM {talentospilos_hist_est_ice} AS hist_stat 
                INNER JOIN {talentospilos_estados_icetex} AS est_icetex ON est_icetex.id = hist_stat.id_estado_icetex) AS status_icetex
                
                ON (spp_students.id_ases_user = status_icetex.id_estudiante AND spp_students.id_semestre = status_icetex.id_semestre)";
    
    if($no_res_bool === "true"){
        $students = $DB->get_records_sql($sql_query);
    }else{
        $sql_query .= " WHERE res_students.codigo_resolucion IS NOT NULL";
        $students = $DB->get_records_sql($sql_query);
    }    

    foreach ($students as $student) {
        $student->monto_estudiante = "$".number_format($student->monto_estudiante, 0, ',', '.');
        array_push($array_historics, $student);
    }

    return $array_historics;
}

//print_r(get_array_students_with_resolution());

/**
 * Function that returns a string with the names of all spp cohorts
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

    $resolutions_options = "<select><option value=''></option> 
                                    <option value='---'>---</option>";

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

    $sql_query = "SELECT DISTINCT res_ice.id, res_ice.codigo_resolucion, semestre.nombre, 
                                res_ice.nota_credito, res_ice.monto_total, res_ice.cantidad_estudiantes 
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

    $array_active_res = array();

    $sql_query = "SELECT semestre.nombre AS semestre, Count(res_est.id) AS num_act_res, sum(res_est.monto_estudiante) AS monto_act_res
                    FROM {talentospilos_res_estudiante} AS res_est
                        INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                        INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre
                        INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                        INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                        INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                        WHERE cohortm.idnumber LIKE '$cohort%' AND uext.tracking_status = 1
                        AND ((res_est.id_estudiante, res_ice.id_semestre)

                            IN 
                            
                            (SELECT DISTINCT academ.id_estudiante, academ.id_semestre
                            FROM {talentospilos_history_academ} AS academ
                            INNER JOIN {talentospilos_semestre} AS semest ON semest.id = academ.id_semestre
                            INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = academ.id_estudiante                            
                            INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                            INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                            WHERE cohortm.idnumber LIKE '$cohort%' AND academ.promedio_semestre IS NOT NULL 
                            AND uext.tracking_status = 1))

                        AND ((res_est.id_estudiante, res_ice.id_semestre)
                        
                            IN 
                            
                            (SELECT DISTINCT hist_ice.id_estudiante, hist_ice.id_semestre
                            FROM {talentospilos_hist_est_ice} AS hist_ice
                            INNER JOIN {talentospilos_semestre} sem ON sem.id = hist_ice.id_semestre
                            INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = hist_ice.id_estudiante
                            INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                            INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                            WHERE cohortm.idnumber LIKE '$cohort%' AND hist_ice.id_estado_icetex = 5
                            AND uext.tracking_status = 1))
                        
                    GROUP BY semestre.nombre";

    $count = $DB->get_records_sql($sql_query);
    
    foreach($count as $record){
        $record->cohort = $cohort;
        $record->monto_act_res = "$".number_format($record->monto_act_res, 0, ',', '.');
        array_push($array_active_res, $record);
    }

    return $array_active_res;
}

//print_r(get_count_active_res_students('SPP2'));

/**
 * Function that returns an array with all the inactive students that belong to a resolution
 * 
 * @see get_count_inactive_res_students($cohort)
 * @param $cohort -> name of the cohort
 * @return array
 */
function get_count_inactive_res_students($cohort){
    global $DB;

    $array_inactive_res = array();

    $sql_query = "SELECT semestre, sum(num_inact_res) AS num_inact_res, sum(monto_inact_res) AS monto_inact_res                            
                            FROM
                            
                            (SELECT semestre.nombre AS semestre, Count(DISTINCT res_est.id_estudiante) AS num_inact_res, sum(res_est.monto_estudiante) AS monto_inact_res
                                                FROM {talentospilos_res_estudiante} AS res_est
                                                    INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                                                    INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre
                                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                                                    INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                    WHERE cohortm.idnumber LIKE '$cohort%' AND uext.tracking_status = 1
                                                    AND ((res_est.id_estudiante, res_ice.id_semestre)
                            
                                                        NOT IN 
                                                        
                                                        (SELECT academ.id_estudiante, academ.id_semestre
                                                        FROM {talentospilos_history_academ} AS academ
                                                        INNER JOIN {talentospilos_semestre} AS semest ON semest.id = academ.id_semestre
                                                        INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = academ.id_estudiante                            
                                                        INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                                                        INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                        WHERE cohortm.idnumber LIKE '$cohort%' AND academ.promedio_semestre IS NOT NULL 
                                                        AND uext.tracking_status = 1))
                            
                                                    OR ((res_est.id_estudiante, res_ice.id_semestre)
                                                    
                                                    IN 
                                                    
                                                    (SELECT DISTINCT hist_ice.id_estudiante, hist_ice.id_semestre
                                                    FROM {talentospilos_hist_est_ice} AS hist_ice
                                                    INNER JOIN {talentospilos_semestre} sem ON sem.id = hist_ice.id_semestre
                                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = hist_ice.id_estudiante
                                                    INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                    WHERE cohortm.idnumber LIKE '$cohort%' AND hist_ice.id_estado_icetex != 5
                                                    AND uext.tracking_status = 1))
                                                    
                                                GROUP BY semestre.nombre
                                                
                                    UNION ALL
                                    
                            SELECT semestre.nombre AS semestre, Count(res_est.id) AS num_inact_res, sum(res_est.monto_estudiante) AS monto_inact_res
                                                FROM {talentospilos_res_estudiante} AS res_est
                                                    INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                                                    INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre
                                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                                                    INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                    WHERE cohortm.idnumber LIKE '$cohort%' AND uext.tracking_status = 1
                                                    AND ((res_est.id_estudiante, res_ice.id_semestre)
                            
                                                        IN 
                                                        
                                                        (SELECT DISTINCT academ.id_estudiante, academ.id_semestre
                                                        FROM {talentospilos_history_academ} AS academ
                                                        INNER JOIN {talentospilos_semestre} AS semest ON semest.id = academ.id_semestre
                                                        INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = academ.id_estudiante                            
                                                        INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                                                        INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                        WHERE cohortm.idnumber LIKE '$cohort%' AND academ.promedio_semestre IS NOT NULL 
                                                        AND uext.tracking_status = 1))
                            
                                                    AND ((res_est.id_estudiante, res_ice.id_semestre)
                                                    
                                                        NOT IN 
                                                        
                                                        (SELECT DISTINCT hist_ice.id_estudiante, hist_ice.id_semestre
                                                        FROM {talentospilos_hist_est_ice} AS hist_ice
                                                        INNER JOIN {talentospilos_semestre} sem ON sem.id = hist_ice.id_semestre
                                                        INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = hist_ice.id_estudiante
                                                        INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                                                        INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                        WHERE cohortm.idnumber LIKE '$cohort%' AND (hist_ice.id_estado_icetex = 5 OR hist_ice.id_estado_icetex = 3)
                                                        AND uext.tracking_status = 1))
                                                    
                                                GROUP BY semestre.nombre
                            ) inact_res
                            
                            GROUP BY semestre";

    $count = $DB->get_records_sql($sql_query);

    foreach($count as $record){
        $record->cohort = $cohort;
        $record->monto_inact_res = "$".number_format($record->monto_inact_res, 0, ',', '.');
        array_push($array_inactive_res, $record);
    }

    return $array_inactive_res;
}

//print_r(get_count_inactive_res_students('SPP2'));

/**
 * Function that returns an array with the number of students that are active and don't belong to a resolution
 * 
 * @see get_count_active_no_res_students($cohort, $semester_name)
 * @param $cohort -> name of the cohort
 * @param $semester_name -> name of the semester
 * @return array
 */
function get_count_active_no_res_students($cohort, $semester_name){
    global $DB;

    $sql_query = "SELECT Count(usuarios.student_id) AS num_act_no_res FROM					
                            (SELECT ases_user.id AS student_id     
                                FROM {cohort} AS cohort                                
                                INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                                INNER JOIN {user} AS moodle_user ON moodle_user.id = cohort_member.userid
                                INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = moodle_user.id
                                INNER JOIN {talentospilos_usuario} AS ases_user ON ases_user.id = user_extended.id_ases_user
                                WHERE user_extended.tracking_status = 1 AND cohort.idnumber LIKE '$cohort%') AS usuarios
                            
                            WHERE (usuarios.student_id

                                    NOT IN

                                    (SELECT res_est.id_estudiante
                                    FROM {talentospilos_res_estudiante} AS res_est
                                    INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                                    INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre
                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                                    INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                    WHERE cohortm.idnumber LIKE '$cohort%' AND uext.tracking_status = 1 
                                    AND semestre.nombre = '$semester_name'))
                                    
                                    AND (usuarios.student_id
                                    
                                    IN 
                                    
                                    (SELECT DISTINCT academ.id_estudiante
                                    FROM {talentospilos_history_academ} AS academ
                                    INNER JOIN {talentospilos_semestre} AS semest ON semest.id = academ.id_semestre
                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = academ.id_estudiante                            
                                    INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                    WHERE cohortm.idnumber LIKE '$cohort%' AND academ.promedio_semestre IS NOT NULL 
                                    AND uext.tracking_status = 1 AND semest.nombre = '$semester_name'))
                                    
                                    AND (usuarios.student_id
                                    
                                    IN

                                    (SELECT DISTINCT hist_ice.id_estudiante
                                    FROM {talentospilos_hist_est_ice} AS hist_ice
                                    INNER JOIN {talentospilos_semestre} sem ON sem.id = hist_ice.id_semestre
                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = hist_ice.id_estudiante
                                    INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                    WHERE cohortm.idnumber LIKE '$cohort%' AND hist_ice.id_estado_icetex = 5
                                    AND uext.tracking_status = 1 AND sem.nombre = '$semester_name'))";

    $count = $DB->get_record_sql($sql_query);

    $amount_no_res = $count->num_act_no_res * 6000000;
    $count->cohort = $cohort;
    $count->monto_act_no_res = "$".number_format($amount_no_res, 0, ',', '.');;
    $count->semestre = $semester_name;

    return $count;
}

//print_r(get_count_active_no_res_students('SPP2', '2018B'));

function get_count_inactive_no_res_students($cohort, $semester_name){
    global $DB;

    $sql_query = "SELECT sum(num_inact_no_res) AS num_inact_no_res
                        FROM
                        (SELECT Count(usuarios.student_id) AS num_inact_no_res FROM					
                                            (SELECT ases_user.id AS student_id                                                    	     
                                                FROM {cohort} AS cohort
                                                INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                                                INNER JOIN {user} AS moodle_user ON moodle_user.id = cohort_member.userid
                                                INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = moodle_user.id
                                                INNER JOIN {talentospilos_usuario} AS ases_user ON ases_user.id = user_extended.id_ases_user
                                                INNER JOIN {talentospilos_estad_programa} AS program_statuses ON program_statuses.id = user_extended.program_status
                                                WHERE user_extended.tracking_status = 1
                                                        AND cohort.idnumber LIKE '$cohort%') AS usuarios
                                            
                                            WHERE (usuarios.student_id

                                                    NOT IN

                                                    (SELECT academ.id
                                                    FROM {talentospilos_usuario} AS academ
                                                    INNER JOIN {talentospilos_res_estudiante} res_est ON res_est.id_estudiante = academ.id
                                                    INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                                                    INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre
                                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = academ.id
                                                    INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                    WHERE cohortm.idnumber LIKE '$cohort%' AND uext.tracking_status = 1
                                                    AND semestre.nombre = '$semester_name'))
                                                    
                                                    AND 
                                                    
                                                    (
                                                    
                                                    (usuarios.student_id
                                                    
                                                    NOT IN 
                                                    
                                                    (SELECT DISTINCT academ.id_estudiante
                                                    FROM {talentospilos_history_academ} AS academ
                                                    INNER JOIN {talentospilos_semestre} AS semest ON semest.id = academ.id_semestre
                                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = academ.id_estudiante                            
                                                    INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                    WHERE cohortm.idnumber LIKE '$cohort%' AND academ.promedio_semestre IS NOT NULL 
                                                    AND semest.nombre = '$semester_name' AND uext.tracking_status = 1))
                                                    
                                                    OR (usuarios.student_id
                                                    
                                                    IN

                                                    (SELECT DISTINCT hist_ice.id_estudiante
                                                    FROM {talentospilos_hist_est_ice} AS hist_ice
                                                    INNER JOIN {talentospilos_semestre} sem ON sem.id = hist_ice.id_semestre
                                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = hist_ice.id_estudiante
                                                    INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                    WHERE cohortm.idnumber LIKE '$cohort%' AND hist_ice.id_estado_icetex != 5
                                                    AND uext.tracking_status = 1 AND sem.nombre = '$semester_name'))
                                                    
                                                    )
                                                    
                            UNION ALL
                                        
                        SELECT Count(usuarios.student_id) AS num_inact_no_res FROM					
                                            (SELECT ases_user.id AS student_id     
                                                FROM {cohort} AS cohort                                
                                                INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                                                INNER JOIN {user} AS moodle_user ON moodle_user.id = cohort_member.userid
                                                INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = moodle_user.id
                                                INNER JOIN {talentospilos_usuario} AS ases_user ON ases_user.id = user_extended.id_ases_user
                                                WHERE user_extended.tracking_status = 1 AND cohort.idnumber LIKE '$cohort%') AS usuarios
                                            
                                            WHERE (usuarios.student_id

                                                    NOT IN

                                                    (SELECT res_est.id_estudiante
                                                    FROM {talentospilos_res_estudiante} AS res_est
                                                    INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                                                    INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre
                                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                                                    INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                    WHERE cohortm.idnumber LIKE '$cohort%' AND uext.tracking_status = 1 
                                                    AND semestre.nombre = '$semester_name'))
                                                    
                                                    AND (usuarios.student_id
                                                    
                                                    IN 
                                                    
                                                    (SELECT DISTINCT academ.id_estudiante
                                                    FROM {talentospilos_history_academ} AS academ
                                                    INNER JOIN {talentospilos_semestre} AS semest ON semest.id = academ.id_semestre
                                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = academ.id_estudiante                            
                                                    INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                    WHERE cohortm.idnumber LIKE '$cohort%' AND academ.promedio_semestre IS NOT NULL 
                                                    AND uext.tracking_status = 1 AND semest.nombre = '$semester_name'))
                                                    
                                                    AND (usuarios.student_id
                                                    
                                                    NOT IN

                                                    (SELECT DISTINCT hist_ice.id_estudiante
                                                    FROM {talentospilos_hist_est_ice} AS hist_ice
                                                    INNER JOIN {talentospilos_semestre} sem ON sem.id = hist_ice.id_semestre
                                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = hist_ice.id_estudiante
                                                    INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                                    WHERE cohortm.idnumber LIKE '$cohort%' AND (hist_ice.id_estado_icetex = 5 OR hist_ice.id_estado_icetex = 3) 
                                                    AND uext.tracking_status = 1 AND sem.nombre = '$semester_name'))
                                                    
                        ) inact_no_res";

    $count = $DB->get_record_sql($sql_query);

    $amount_no_res = $count->num_inact_no_res * 6000000; 
    $count->cohort = $cohort;
    $count->monto_inact_no_res = "$".number_format($amount_no_res, 0, ',', '.');
    $count->semestre = $semester_name;

    return $count;
}

//print_r(get_count_inactive_no_res_students('SPP2', '2017A'));

function get_semester_from_cohort($cohort){
    global $DB;

    $sql_query = "SELECT substring(idnumber from 5 for 5) AS semestre FROM {cohort}
                    WHERE idnumber LIKE '$cohort%'";

    $record = $DB->get_record_sql($sql_query);

    return $record->semestre;
}

//print_r(get_semester_from_cohort('SPP2'));

function get_id_semester_from_name($sem_name){
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_semestre} WHERE nombre = '$sem_name'";

    $record = $DB->get_record_sql($sql_query);

    return $record->id;
}

//print_r(get_id_semester_from_name(get_semester_from_cohort('SPP2')));

function get_semesters_names($semester_id){
    global $DB;

    $array_semesters = array();

    $sql_query = "SELECT nombre FROM {talentospilos_semestre} WHERE id >= $semester_id";

    $semesters = $DB->get_records_sql($sql_query);

    foreach ($semesters as $semester) {
        array_push($array_semesters, $semester->nombre);
    }

    return $array_semesters;
}

//print_r(get_semesters_names(get_id_semester_from_name(get_semester_from_cohort('SPP3'))));

/**
 * Function that returns an array with all the necessary information for the summary report
 * 
 * @see get_info_summary_report($cohort)
 * @param $cohort -> name of the cohort
 * @return array
 */
function get_info_summary_report($cohort){

    $array_act_res = get_count_active_res_students($cohort);
    $array_inact_res = get_count_inactive_res_students($cohort);
    $array_act_no_res = array();
    $array_inact_no_res = array();

    $array_objects = array();

    $semes_name = get_semester_from_cohort($cohort);
    $id_semester = get_id_semester_from_name($semes_name);
    $semesters = get_semesters_names($id_semester);

    foreach($semesters as $semester){
        $count_act_no_res = get_count_active_no_res_students($cohort, $semester);
        $count_inact_no_res = get_count_inactive_no_res_students($cohort, $semester);

        array_push($array_act_no_res, $count_act_no_res);
        array_push($array_inact_no_res, $count_inact_no_res);
    }

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

    if(count($array_inact_no_res) > 0){
        foreach($array_inact_no_res as $inact_no_res){
            if(array_key_exists($inact_no_res->semestre, $array_objects)){
                $array_objects[$inact_no_res->semestre]->num_inact_no_res = $inact_no_res->num_inact_no_res;
                $array_objects[$inact_no_res->semestre]->monto_inact_no_res = $inact_no_res->monto_inact_no_res;
                $array_objects[$inact_no_res->semestre]->semestre = $inact_no_res->semestre;
                $array_objects[$inact_no_res->semestre]->cohort = $inact_no_res->cohort;

            }else{
                $array_objects[$inact_no_res->semestre] = new stdClass();
                $array_objects[$inact_no_res->semestre]->num_inact_no_res = $inact_no_res->num_inact_no_res;
                $array_objects[$inact_no_res->semestre]->monto_inact_no_res = $inact_no_res->monto_inact_no_res;
                $array_objects[$inact_no_res->semestre]->semestre = $inact_no_res->semestre;
                $array_objects[$inact_no_res->semestre]->cohort = $inact_no_res->cohort;
            }
        }
    }

    ksort($array_objects);

    $array_final = array();

    foreach($array_objects as $object){

        if(!isset($object->num_act_res)){
            $object->num_act_res = 0;
        }

        if(!isset($object->monto_act_res)){
            $object->monto_act_res = "$0";
        }

        if(!isset($object->num_inact_res)){
            $object->num_inact_res = 0;
        }

        if(!isset($object->monto_inact_res)){
            $object->monto_inact_res = "$0";
        }

        if(!isset($object->num_act_no_res)){
            $object->num_act_no_res = 0;
        }

        if(!isset($object->monto_act_no_res)){
            $object->monto_act_no_res = "$0";
        }

        if(!isset($object->num_inact_no_res)){
            $object->num_inact_no_res = 0;
        }

        if(!isset($object->monto_inact_no_res)){
            $object->monto_inact_no_res = "$0";
        }

        $object->total_students = $object->num_act_res + $object->num_inact_res 
                                    + $object->num_act_no_res + $object->num_inact_no_res;

        array_push($array_final, $object);
    }
    
    return $array_final;
}

//print_r(get_info_summary_report('SPP2'));
