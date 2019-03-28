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
require_once(__DIR__.'/../../classes/traits/from_std_object_or_array.php');
require_once(__DIR__.'/../lib/csv.php');


class MenReport {
    use from_std_object_or_array;
    public $anio;
    public $semestre;
    public $id_tipo_documento_actual;
    public $num_documento_actual;    
    public $id_tipo_documento_ingreso_spp;
    public $num_documento_ingreso_spp;
    public $id_convocatoria;
    public $ser_pilo_paga_profe;
    public $pro_consecutivo_uno;
    public $pro_consecutivo_dos;
    public $id_municipio_programa;
    public $id_estado_actual_ies;
    public $motivo_retiro_o_cancelacion;
    public $seguimiento;
    public $id_municipio_residencia;
    public $residencia_direccion_actual;
    public $residencia_telefono_actual;
    public $contacto_celular;
    public $contacto_mail;
    public $id_municipio_familia;
    public $residencia_direccion_familia;
    public $residencia_telefono_familia;
    public $contacto_celular_acudiente;
    public $tipo_discapacidad;
    public $ayudas_tecnicas_discapacidad;
    public $matricula_cursada;
    public $id_renovacion_icetex;
    public $num_asignaturas_inscritas;
    public $num_asignaturas_canceladas;
    public $num_asignaturas_no_aprobadas;
    public $promedio_academico_per_actual;
    public $promedio_academico_acumulado;
    public $reconocimiento;
    public $id_condicion_alerta;
    public $apoyo_financiero_icetex;
    public $apoyo_financiero_spp_profe_tec;
    public $apoyo_financiero_spp_profe_ing;
    public $apoyo_financiero_spp_profe_mov;
    public $apoyo_financiero_ies_transport;
    public $apoyo_financier_ies_materiales;
    public $apoyo_financiero_ies_alimentac;
    public $apoyo_financiero_ies_vivienda;
    public $apoyo_academico_ies;
    public $apoyo_total_financ_invertido;
    public $usu_id;
    public $sem_nom;
    public $num_fila;
    public $json_materias;  
}


function get_array_students_men($period){
    global $DB;

    $array_students = array();

    $sql_query = "SELECT row_number() over() AS num_fila,
                        usuario.id AS usu_id, est_academ.sem_nom, t_doc_ini.nombre AS id_tipo_documento_ingreso_spp, 
                        usuario.num_doc_ini AS num_documento_ingreso_spp, t_doc_act.nombre AS id_tipo_documento_actual,
                        usuario.num_doc AS num_documento_actual, prog_academico.snies_prog AS pro_consecutivo_uno, 
                        prog_academico.div_mun_programa AS id_municipio_programa, 
                        user_extended.program_status AS id_estado_actual_ies,
                        mun_actual.codigodivipola AS id_municipio_residencia, 
                        usuario.direccion_res AS residencia_direccion_actual, 
                        usuario.tel_res AS residencia_telefono_actual, usuario.celular AS contacto_celular, 
                        usuario.emailpilos AS contacto_mail, mun_familia.codigodivipola AS id_municipio_familia, 
                        usuario.dir_ini AS residencia_direccion_familia, usuario.tel_ini AS residencia_telefono_familia, 
                        usuario.tel_acudiente AS contacto_celular_acudiente, 
                        est_icetex.nombre_estado AS id_renovacion_icetex,
                        est_academ.cantidad_materias AS num_asignaturas_inscritas, 
                        est_academ.prom_semestre AS promedio_academico_per_actual, 
                        est_academ.prom_acumulado AS promedio_academico_acumulado,
                        est_academ.json_materias, est_academ.estimulo AS reconocimiento
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
        $semest = $student->sem_nom;
        $student->anio = substr($period, 0, 4);
        if(substr($period, 4, 1) == "A"){
            $student->semestre = 1;
        }elseif(substr($period, 4, 1) == "B"){
            $student->semestre = 2;
        }
        $student->id_convocatoria = "";
        $student->ser_pilo_paga_profe = "";
        $student->pro_consecutivo_dos = "";
        $student->motivo_retiro_o_cancelacion = "";
        $student->seguimiento = "";
        $student->tipo_discapacidad = "";
        $student->ayudas_tecnicas_discapacidad = "";
        $student->matricula_cursada = "";
        $student->id_condicion_alerta = "";
        $student->apoyo_financiero_icetex = "";
        $student->apoyo_financiero_spp_profe_tec = "";
        $student->apoyo_financiero_spp_profe_ing = "";
        $student->apoyo_financiero_spp_profe_mov = "";
        $student->apoyo_financiero_ies_transport = "";
        $student->apoyo_financier_ies_materiales = "";
        $student->apoyo_financiero_ies_alimentac = "";
        $student->apoyo_financiero_ies_vivienda = "";
        $student->apoyo_academico_ies = "";
        $student->apoyo_total_financ_invertido = "";
        
        process_student_subject_json($student);
        array_push($array_students, $student);
    }

    return $array_students;
}

//print_r(get_array_students_men('2017A'));


function process_student_subject_json($student){
    $student->num_asignaturas_no_aprobadas = 0;
    $student->num_asignaturas_canceladas = 0;
    if(isset($student->json_materias)){
        $json_subjects = json_decode($student->json_materias);    

        foreach($json_subjects as $materia) {
            if($materia->nota < 3.0){
                $student->num_asignaturas_no_aprobadas += 1;
            }

            if($materia->fecha_cancelacion_materia !== ""){
                $student->num_asignaturas_canceladas += 1;
            }
        }
    }    
}


function create_men_report_csv($period){

    $students_men = get_array_students_men($period);

    \csv\array_to_csv_download(
        MenReport::make_objects_from_std_objects_or_arrays($students_men),
        'Reporte Ministerio.csv',
        ',',
        ['json_materias', 'sem_nom', 'num_fila', 'usu_id']);
}
//create_men_report_csv('2017A');