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
 * @copyright  2019 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');

function get_array_students_men($period){
    global $DB;

    $array_students = array();

    $sql_query = "SELECT
                        usuario.id, est_academ.sem_nom, t_doc_ini.nombre AS tipo_documento_inicial, usuario.num_doc_ini, t_doc_act.nombre AS tipo_documento_actual,
                        usuario.num_doc, prog_academico.snies_prog AS pro_consec_uno, prog_academico.div_mun_programa,
                        mun_actual.codigodivipola AS div_ciu_actual, 	usuario.direccion_res, usuario.tel_res, usuario.celular, 
                        usuario.emailpilos, mun_familia.codigodivipola AS div_ciu_familia, 	usuario.dir_ini AS dir_familia, 
                        usuario.tel_ini AS tel_familia, usuario.tel_acudiente, est_icetex.nombre_estado, est_academ.prom_semestre, est_academ.prom_acumulado,
                        est_academ.json_materias, est_academ.cantidad_materias, est_academ.estimulo
                    FROM {talentospilos_usuario} AS usuario
                        INNER JOIN {talentospilos_user_extended} user_extended ON usuario.id = user_extended.id_ases_user
                        INNER JOIN {cohort_members} coh_members ON user_extended.id_moodle_user = coh_members.userid
                        INNER JOIN {cohort} cohort ON coh_members.cohortid = cohort.id
                        INNER JOIN {talentospilos_inst_cohorte} instancia_cohorte ON cohort.id = instancia_cohorte.id_cohorte 
                        INNER JOIN {talentospilos_tipo_documento} t_doc_ini ON usuario.tipo_doc_ini = t_doc_ini.id
                        INNER JOIN {talentospilos_tipo_documento} t_doc_act ON usuario.tipo_doc = t_doc_act.id
                        INNER JOIN {talentospilos_municipio} mun_actual ON usuario.id_ciudad_res = mun_actual.id
                        INNER JOIN {talentospilos_municipio} mun_familia ON usuario.id_ciudad_ini = mun_familia.id
                        INNER JOIN
                            (SELECT uext.id_ases_user AS usuarioid, prog.codigosnies AS snies_prog,
                                    municipio.codigodivipola AS div_mun_programa
                                FROM {talentospilos_user_extended} AS uext
                                INNER JOIN {talentospilos_programa} prog ON prog.id = uext.id_academic_program
                                INNER JOIN {talentospilos_sede} sede ON prog.id_sede = sede.id
                                INNER JOIN {talentospilos_municipio} municipio ON sede.id_ciudad = municipio.id
                            WHERE uext.tracking_status = 1) AS prog_academico			
                            ON usuario.id = prog_academico.usuarioid
                        INNER JOIN
                            (SELECT ases_user.id AS estudianteid, estados_icetex.nombre AS nombre_estado
                            FROM {talentospilos_usuario} AS ases_user
                            INNER JOIN {talentospilos_user_extended} user_extended ON ases_user.id = user_extended.id_ases_user
                            INNER JOIN {talentospilos_est_est_icetex} estud_icetex ON ases_user.id = estud_icetex.id_estudiante
                            INNER JOIN {talentospilos_estados_icetex} estados_icetex ON estud_icetex.id_estado_icetex = estados_icetex.id
                            WHERE user_extended.tracking_status = 1	AND estud_icetex.fecha = (SELECT Max(ice.fecha) FROM {talentospilos_est_est_icetex} AS ice
                                                                                            WHERE ice.id_estudiante = ases_user.id)
                            ) AS est_icetex
                            ON usuario.id = est_icetex.estudianteid
                        LEFT JOIN
                            (SELECT user_ext.id_ases_user AS id_academ, h_academico.promedio_semestre AS prom_semestre, h_academico.promedio_acumulado AS prom_acumulado, 
                                    ac_semestre.nombre AS sem_nom, h_academico.json_materias,
                                    CASE WHEN (json_array_length(h_academico.json_materias::json) IS NOT NULL)
                                                THEN json_array_length(h_academico.json_materias::json)
                                        WHEN (json_array_length(h_academico.json_materias::json) IS NULL)
                                                THEN 0
                                    END AS cantidad_materias,
                                    CASE WHEN (h_estim.puesto_ocupado IS NOT NULL)
                                                THEN 'S'
                                        WHEN (h_estim.puesto_ocupado IS NULL)
                                                THEN 'N'
                                    END AS estimulo	
                            FROM {talentospilos_user_extended} AS user_ext
                                LEFT JOIN {talentospilos_history_academ} h_academico ON h_academico.id_estudiante = user_ext.id_ases_user
                                LEFT JOIN {talentospilos_history_bajos} h_bajos ON h_bajos.id_history = h_academico.id
                                LEFT JOIN {talentospilos_history_cancel} h_cancel ON h_cancel.id_history = h_academico.id
                                LEFT JOIN {talentospilos_history_estim} h_estim ON h_estim.id_history = h_academico.id
                                INNER JOIN {talentospilos_semestre} ac_semestre ON ac_semestre.id = h_academico.id_semestre 
                            WHERE user_ext.tracking_status = 1	AND ac_semestre.nombre = '$period'
                            ) AS est_academ
                            ON usuario.id = est_academ.id_academ
                    WHERE user_extended.tracking_status = 1 AND instancia_cohorte.id_instancia = 450299 AND cohort.idnumber LIKE 'SPP%'";
    

    $students = $DB->get_records_sql($sql_query);

    foreach($students as $student) {
        process_student_subject_json($student);
        array_push($array_students, $student);
    }

    return $array_students;
}

function process_student_subject_json($student){
    $json_subjects = json_decode($student->json_materias);
    $student->materias_perdidas = 0;
    $student->materias_canceladas = 0;

    foreach($json_subjects as $materia) {
        if($materia->nota <= 3.0){
            $student->materias_perdidas += 1;
        }

        if($json_subjects->fecha_cancelacion_materia !== ""){
            $student->materias_canceladas += 1;
        }
    }
}