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

    $sql_query = "SELECT COUNT(spp_students.id_ases_user)
                    FROM
                    (SELECT user_extended.id_ases_user, moodle_user.lastname, moodle_user.firstname, cohorts.idnumber, semestre.id AS id_semestre, semestre.nombre AS nombre_semestre, 
                        usuario.num_doc, moodle_user.username, substring(cohorts.idnumber from 0 for 5) AS cohorte
                    FROM {cohort_members} AS members
                    INNER JOIN {cohort} AS cohorts ON members.cohortid = cohorts.id
                    INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = members.userid
                    INNER JOIN {talentospilos_usuario} AS usuario ON usuario.id = user_extended.id_ases_user
                    INNER JOIN {user} AS moodle_user ON moodle_user.id = user_extended.id_moodle_user
                    CROSS JOIN {talentospilos_semestre} AS semestre 
                    WHERE cohorts.idnumber LIKE 'SPP%') AS spp_students 

                    LEFT JOIN 

                    (SELECT res_student.id_estudiante, semestre.nombre, semestre.id AS id_semestre, res_icetex.id AS res_icetex, res_icetex.codigo_resolucion, res_student.monto_estudiante
                    FROM {talentospilos_res_estudiante} AS res_student
                    INNER JOIN {talentospilos_res_icetex} AS res_icetex ON res_icetex.id = res_student.id_resolucion
                    INNER JOIN {talentospilos_semestre} AS semestre ON semestre.id = res_icetex.id_semestre
                    ) AS res_students

                    ON (spp_students.id_ases_user = res_students.id_estudiante AND spp_students.id_semestre = res_students.id_semestre)

                    LEFT JOIN 

                    (SELECT academ.id_estudiante, academ.id_semestre, to_timestamp(cancel.fecha_cancelacion) AS fecha_cancel
                    FROM {talentospilos_history_academ} AS academ
                    INNER JOIN {talentospilos_history_cancel} cancel ON cancel.id_history = academ.id
                    ) AS cancel_students

                    ON (spp_students.id_ases_user = cancel_students.id_estudiante AND spp_students.id_semestre = cancel_students.id_semestre)

                    LEFT JOIN 

                    (SELECT academic.id_estudiante, academic.id_semestre, academic.promedio_semestre
                    FROM {talentospilos_history_academ} AS academic
                    ) AS academic_students

                    ON (spp_students.id_ases_user = academic_students.id_estudiante AND spp_students.id_semestre = academic_students.id_semestre)";
        
    
    // $sql_query = "SELECT row_number() over(), spp_students.id_ases_user, spp_students.cohorte, spp_students.num_doc, substring(spp_students.username from 0 for 8) AS codigo, 
    //                     spp_students.lastname, spp_students.firstname, spp_students.nombre_semestre, res_students.codigo_resolucion,
    //                     res_students.monto_estudiante, cancel_students.fecha_cancel, academic_students.promedio_semestre,
    //                     CASE WHEN (cancel_students.fecha_cancel IS NULL AND academic_students.promedio_semestre IS NOT NULL)
    //                                 THEN 'ACTIVO'
    //                         WHEN (cancel_students.fecha_cancel IS NULL AND res_students.codigo_resolucion IS NOT NULL)
    //                                 THEN 'ACTIVO'		
    //                         WHEN (cancel_students.fecha_cancel IS NOT NULL AND academic_students.promedio_semestre IS NULL)
    //                                 THEN 'INACTIVO'		
    //                     END AS program_status
    //                 FROM
    //                 (SELECT user_extended.id_ases_user, moodle_user.lastname, moodle_user.firstname, cohorts.idnumber, semestre.id AS id_semestre, semestre.nombre AS nombre_semestre, 
    //                     usuario.num_doc, moodle_user.username, substring(cohorts.idnumber from 0 for 5) AS cohorte
    //                 FROM {cohort_members} AS members
    //                 INNER JOIN {cohort} AS cohorts ON members.cohortid = cohorts.id
    //                 INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = members.userid
    //                 INNER JOIN {talentospilos_usuario} AS usuario ON usuario.id = user_extended.id_ases_user
    //                 INNER JOIN {user} AS moodle_user ON moodle_user.id = user_extended.id_moodle_user
    //                 CROSS JOIN {talentospilos_semestre} AS semestre 
    //                 WHERE cohorts.idnumber LIKE 'SPP%') AS spp_students 

    //                 LEFT JOIN 

    //                 (SELECT res_student.id_estudiante, semestre.nombre, semestre.id AS id_semestre, res_icetex.id AS res_icetex, res_icetex.codigo_resolucion, res_student.monto_estudiante
    //                 FROM {talentospilos_res_estudiante} AS res_student
    //                 INNER JOIN {talentospilos_res_icetex} AS res_icetex ON res_icetex.id = res_student.id_resolucion
    //                 INNER JOIN {talentospilos_semestre} AS semestre ON semestre.id = res_icetex.id_semestre
    //                 ) AS res_students

    //                 ON (spp_students.id_ases_user = res_students.id_estudiante AND spp_students.id_semestre = res_students.id_semestre)

    //                 LEFT JOIN 

    //                 (SELECT academ.id_estudiante, academ.id_semestre, to_timestamp(cancel.fecha_cancelacion) AS fecha_cancel
    //                 FROM {talentospilos_history_academ} AS academ
    //                 INNER JOIN {talentospilos_history_cancel} cancel ON cancel.id_history = academ.id
    //                 ) AS cancel_students

    //                 ON (spp_students.id_ases_user = cancel_students.id_estudiante AND spp_students.id_semestre = cancel_students.id_semestre)

    //                 LEFT JOIN 

    //                 (SELECT academic.id_estudiante, academic.id_semestre, academic.promedio_semestre
    //                 FROM {talentospilos_history_academ} AS academic
    //                 ) AS academic_students

    //                 ON (spp_students.id_ases_user = academic_students.id_estudiante AND spp_students.id_semestre = academic_students.id_semestre)";
    
    $students = $DB->get_records_sql($sql_query);
    print_r($students);
    $student = $DB->get_record_sql($sql_query);
    print_r($student);

    foreach ($students as $student) {
        $student->monto_estudiante = "$".number_format($student->monto_estudiante, 0, ',', '.');
        array_push($array_historics, $student);
    }

    return $array_historics;
}

//FUNCION NUEVA ACTIVOS-RESOLUCION
function get_active_res_students($semester, &$array_stu_act_res){
    global $DB;

    $sql_query = "SELECT res_est.id_estudiante, substring(moodle_user.username from 0 for 8) AS codigo, usu.num_doc, moodle_user.firstname, moodle_user.lastname, 
                res_ice.codigo_resolucion, res_est.monto_estudiante, substring(cohortm.idnumber from 0 for 5) as cohorte
                    FROM {talentospilos_res_estudiante} AS res_est
                        INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                        INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre
                        INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                        INNER JOIN {talentospilos_usuario} usu ON usu.id = res_est.id_estudiante
                        INNER JOIN {user} moodle_user ON moodle_user.id = uext.id_moodle_user
                        INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                        INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                        WHERE (substring(cohortm.idnumber from 0 for 4) = 'SPP')
                        AND semestre.nombre = '$semester'
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
                        WHERE (substring(cohortm.idnumber from 0 for 4) = 'SPP')
                        AND semestre.nombre = '$semester')";

    $students_act_res = $DB->get_records_sql($sql_query);

    foreach($students_act_res as $student){
        $student->nombre_semestre = $semester;
        $student->program_status = "ACTIVO";
        $student->fecha_cancel = "---";
        $student->monto_estudiante = "$".number_format($student->monto_estudiante, 0, ',', '.');

        array_push($array_stu_act_res, $student);
    }
}

//FUNCION NUEVA INACTIVOS-RESOLUCION
function get_inactive_res_students($semester, &$array_stu_inact_res){
    global $DB;

    $sql_query = "SELECT res_est.id, substring(moodle_user.username from 0 for 8) AS codigo, usu.num_doc,
                    moodle_user.firstname, moodle_user.lastname, res_ice.codigo_resolucion, res_est.monto_estudiante, cancel.fecha_cancelacion AS fecha_cancel,
                    substring(cohortm.idnumber from 0 for 5) AS cohorte
                                FROM {talentospilos_res_estudiante} AS res_est
                                    INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                                    INNER JOIN {talentospilos_history_academ} academ ON academ.id_estudiante = res_est.id_estudiante
                                    INNER JOIN {talentospilos_semestre} semestre  ON semestre.id = academ.id_semestre
                                    INNER JOIN {talentospilos_history_cancel} cancel ON cancel.id_history = academ.id
                                    INNER JOIN {talentospilos_usuario} usu ON usu.id = res_est.id_estudiante
                                    INNER JOIN {user} moodle_user ON moodle_user.id = uext.id_moodle_user
                                    INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                    WHERE (substring(cohortm.idnumber from 0 for 4) = 'SPP')
                                    AND semestre.nombre = '$semester'";

    $students_inact_res = $DB->get_records_sql($sql_query);

    foreach($students_inact_res as $student){
        $student->nombre_semestre = $semester;
        $student->program_status = "INACTIVO";
        $student->fecha_cancel = date("Y-m-d", $student->fecha_cancel);
        $student->monto_estudiante = "$".number_format($student->monto_estudiante, 0, ',', '.');

        array_push($array_stu_inact_res, $student);
    }
}

//Returns the student that does not have resolution
function get_active_no_res_students($semester, &$array_act_no_res){
    global $DB;
    
    $sql_query = "SELECT usu.id, substring(moodle_user.username from 0 for 8) AS codigo, usu.num_doc, moodle_user.firstname, 
                    moodle_user.lastname, substring(coh.idnumber from 0 for 5) AS cohorte 
                                FROM {talentospilos_usuario} AS usu
                                INNER JOIN {talentospilos_user_extended} uexten ON uexten.id_ases_user = usu.id
                                INNER JOIN {user} moodle_user ON moodle_user.id = uexten.id_moodle_user
                                INNER JOIN {cohort_members} coh_mem ON coh_mem.userid = uexten.id_moodle_user
                                INNER JOIN {cohort} coh ON coh.id = coh_mem.cohortid
                                WHERE substring(coh.idnumber from 0 for 4) = 'SPP'                    
                                AND usu.id
                                
                                NOT IN

                                (SELECT academ.id
                                FROM {talentospilos_usuario} AS academ
                                INNER JOIN {talentospilos_res_estudiante} res_est ON res_est.id_estudiante = academ.id
                                INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                                INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre
                                INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = academ.id
                                INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                                INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                                WHERE substring(cohortm.idnumber from 0 for 4) = 'SPP'   
                                AND semestre.nombre = '$semester')
                                
                                AND usu.id
                                
                                NOT IN 
                                
                                (SELECT academ.id_estudiante 
                                FROM {talentospilos_history_academ} academ
                                INNER JOIN {talentospilos_history_cancel} AS cancel ON cancel.id_history = academ.id
                                INNER JOIN {talentospilos_semestre} AS semes ON semes.id = academ.id_semestre
                                WHERE semes.nombre = '$semester')";

    $students_act_no_res = $DB->get_records_sql($sql_query);

    foreach($students_act_no_res as $student){
        $student->nombre_semestre = $semester;
        $student->fecha_cancel = "---";
        $student->program_status = "---"; 
        $student->monto_estudiante = "$0";
        $student->codigo_resolucion = "---";

        array_push($array_act_no_res, $student);
    }
}

//print_r(get_active_no_res_students());

function get_student_resolution_spt(){
    global $DB;

    $array_historics = array();    
    
    $sql_query = "SELECT res_est.id, substring(cohortm.idnumber from 0 for 5) AS cohorte, substring(userm.username from 0 for 8) AS codigo, 
                    usuario.num_doc, userm.firstname, userm.lastname, semestre.nombre AS nombre_semestre, res.codigo_resolucion, monto_estudiante, res_est.id_estudiante, 
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
function get_count_active_res_students($cohort, $semester_name){
    global $DB;

    $sql_query = "SELECT Count(res_est.id) AS num_act_res, sum(res_est.monto_estudiante) AS monto_act_res
                    FROM {talentospilos_res_estudiante} AS res_est
                        INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                        INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre
                        INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                        INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                        INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                        WHERE (substring(cohortm.idnumber from 0 for 5) = '$cohort' OR substring(cohortm.idnumber from 0 for 6) = '$cohort')
                        AND semestre.nombre = '$semester_name'
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
                        WHERE (substring(cohortm.idnumber from 0 for 5) = '$cohort' OR substring(cohortm.idnumber from 0 for 6) = '$cohort')
                        AND semestre.nombre = '$semester_name')";

    $count = $DB->get_record_sql($sql_query);

    $count->cohort = $cohort;
    $count->monto_act_res = "$".number_format($count->monto_act_res, 0, ',', '.');
    $count->semestre = $semester_name;    

    return $count;
}

//print_r(get_count_active_res_students('SPP1'));

/**
 * Function that returns an array with all the inactive students that belong to a resolution
 * 
 * @see get_count_inactive_res_students($cohort)
 * @param $cohort -> name of the cohort
 * @return array
 */
function get_count_inactive_res_students($cohort, $semester_name){
    global $DB;

    $sql_query = "SELECT Count(res_est.id) AS num_inact_res, sum(res_est.monto_estudiante) AS monto_inact_res 
                    FROM {talentospilos_res_estudiante} AS res_est
                        INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = res_est.id_estudiante
                        INNER JOIN {talentospilos_history_academ} academ ON academ.id_estudiante = res_est.id_estudiante
                        INNER JOIN {talentospilos_semestre} semestre  ON semestre.id = academ.id_semestre
                        INNER JOIN {talentospilos_history_cancel} cancel ON cancel.id_history = academ.id
                        INNER JOIN {cohort_members} co_mem ON uext.id_moodle_user = co_mem.userid
                        INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                        WHERE (substring(cohortm.idnumber from 0 for 5) = '$cohort' OR substring(cohortm.idnumber from 0 for 6) = '$cohort')
                        AND semestre.nombre = '$semester_name'";

    $count = $DB->get_record_sql($sql_query);

    $count->cohort = $cohort;
    $count->monto_inact_res = "$".number_format($count->monto_inact_res, 0, ',', '.');
    $count->semestre = $semester_name;

    return $count;
}

//print_r(get_count_inactive_res_students('SPP1'));

/**
 * Function that returns an array with the number of students that are active and don't belong to a resolution
 * 
 * @see get_count_active_no_res_students($cohort)
 * @param $cohort -> name of the cohort
 * @return array
 */
function get_count_active_no_res_students($cohort, $semester_name){
    global $DB;

    $sql_query = "SELECT Count(usu.id) AS num_act_no_res
                    FROM {talentospilos_usuario} AS usu
                    INNER JOIN {talentospilos_user_extended} uexten ON uexten.id_ases_user = usu.id
                    INNER JOIN {cohort_members} coh_mem ON coh_mem.userid = uexten.id_moodle_user
                    INNER JOIN {cohort} cohorte ON cohorte.id = coh_mem.cohortid
                    WHERE (substring(cohorte.idnumber from 0 for 5) = '$cohort' OR substring(cohorte.idnumber from 0 for 6) = '$cohort') 
                    
                    AND usu.id
                    
                    NOT IN

                    (SELECT academ.id
                    FROM {talentospilos_usuario} AS academ
                    INNER JOIN {talentospilos_res_estudiante} res_est ON res_est.id_estudiante = academ.id
                    INNER JOIN {talentospilos_res_icetex} res_ice ON res_ice.id = res_est.id_resolucion
                    INNER JOIN {talentospilos_semestre} semestre ON semestre.id = res_ice.id_semestre
                    INNER JOIN {talentospilos_user_extended} uext ON uext.id_ases_user = academ.id
                    INNER JOIN {cohort_members} co_mem ON co_mem.userid = uext.id_moodle_user
                    INNER JOIN {cohort} cohortm ON cohortm.id = co_mem.cohortid
                    WHERE (substring(cohortm.idnumber from 0 for 5) = '$cohort' OR substring(cohortm.idnumber from 0 for 6) = '$cohort')   
                    AND semestre.nombre = '$semester_name')
                    
                    AND usu.id
                    
                    NOT IN 
                    
                    (SELECT academ.id_estudiante 
                    FROM {talentospilos_history_academ} academ
                    INNER JOIN {talentospilos_history_cancel} AS cancel ON cancel.id_history = academ.id
                    INNER JOIN {talentospilos_semestre} AS semes ON semes.id = academ.id_semestre
                    WHERE semes.nombre = '$semester_name')";    

    $count = $DB->get_record_sql($sql_query);

    $count->cohort = $cohort;
    $count->monto_act_no_res = "$0";
    $count->semestre = $semester_name;

    return $count;
}

//print_r(get_count_active_no_res_students('SPP2'));

function get_semesters_names(){
    global $DB;

    $array_semesters = array();

    $sql_query = "SELECT nombre FROM {talentospilos_semestre}";

    $semesters = $DB->get_records_sql($sql_query);

    foreach ($semesters as $semester) {
        array_push($array_semesters, $semester->nombre);
    }

    return $array_semesters;

}


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

    $semesters = get_semesters_names();
    foreach($semesters as $semester){
        $count_act_res = get_count_active_res_students($cohort, $semester);
        $count_inact_res = get_count_inactive_res_students($cohort, $semester);
        $count_act_no_res = get_count_active_no_res_students($cohort, $semester);

        array_push($array_act_res, $count_act_res);
        array_push($array_inact_res, $count_inact_res);
        array_push($array_act_no_res, $count_act_no_res);
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

//print_r(get_info_summary_report('SPP2'));

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
