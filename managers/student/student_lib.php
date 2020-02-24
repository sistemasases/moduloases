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
namespace student_lib;
use function array_search;
require_once (__DIR__ . '/../../managers/periods_management/periods_lib.php');
/**
* Student lib
*
* @author     Luis Gerardo Manrique Cardona
* @package    block_ases
* @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

/**
 * Return the active semesters by all students in ASES
 *
 * For each student and semester, one row is returned.
 *
 * @param string|int $id_instance
 * ### Fields returned
 * - mdl_cohort_members_id
 * - mdl_talentospilos_usuario_id
 * - cancela: bool
 * - tracking_status,
 * - cambio_carrera: bool
 * - codigo -- Alias for mdl_user.username
 * - mdl_user.firstname
 * - mdl_talentospilos_history_academ_id
 * - mdl_user.lastname
 * - mdl_talentospilos_semestre_nombre -- Alias for mdl_talentospilos_semsetre.nombre
 *
 * ### Tables joined
 * - mdl_talentospilos_semestre
 * - mdl_talentospilos_user_extended
 * - mdl_user
 * - mdl_cohort
 * - mdl_cohort_members
 * @throws \dml_exception
 * @return array Key value array where the key is mdl_talentospilos_history_academ.id and the values are the founded
 *  registries
 */
function get_active_semesters_db($id_instance, $ases_cohort_id) {
    global $DB;
    $cohort_sql_conditions = '';
    $get_all_cohorts = false;
    $get_cohort_group = false;
    $cohort_prefix = \cohort_lib\get_cohort_name_prefix($ases_cohort_id);
    if(\cohort_lib\is_todos_cohort($ases_cohort_id)) {
        if($ases_cohort_id === \cohort_lib\TODOS_PREFIX) {
            $get_all_cohorts = true;
        } else {
            $get_cohort_group = true;
        }
    }
    if($get_all_cohorts) {
        $cohort_sql_conditions = '';
    }
    if($get_cohort_group) {
        $cohort_sql_conditions = " and mdl_cohort.idnumber like '$cohort_prefix%'";
    }
    if(!$get_cohort_group && !$get_all_cohorts) {
        $cohort_sql_conditions = "and mdl_cohort.idnumber = '$ases_cohort_id'";
    }

    $sql = <<<SQL
    select
           mdl_talentospilos_history_academ.id AS mdl_talentospilos_history_academ_id ,
           mdl_cohort_members.id as mdl_cohort_members_id,
           tracking_status,
           mdl_talentospilos_usuario_outer.num_doc, 
                  (case when mdl_talentospilos_history_academ.id   in (select id_history from mdl_talentospilos_history_cancel)
                            then 'SI' else 'NO' end) as cancela,
                  (select count(*) from mdl_talentospilos_user_extended 
                  where mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_usuario_outer.id) as num_carreras,
                  (case when program_status = 3 and id_programa != 1 then 'EGRESADO'  else 'NO' end) as egresado,
           mdl_talentospilos_usuario_outer.id as mdl_talentospilos_usuario_id,
           username as codigo,
           firstname,
           lastname,
           mdl_talentospilos_semestre.nombre as mdl_talentospilos_semestre_nombre,
           program_status,
           cod_univalle as programa
    from mdl_talentospilos_history_academ
        inner join mdl_talentospilos_semestre
          on mdl_talentospilos_semestre.id = mdl_talentospilos_history_academ.id_semestre
        inner join mdl_talentospilos_user_extended
          on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_history_academ.id_estudiante
        inner join mdl_user
          on mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
        inner join mdl_talentospilos_usuario as  mdl_talentospilos_usuario_outer
          on mdl_talentospilos_usuario_outer.id = mdl_talentospilos_user_extended.id_ases_user
        inner join mdl_cohort_members
          on mdl_cohort_members.userid = mdl_user.id
        inner join mdl_talentospilos_inst_cohorte
          on mdl_cohort_members.cohortid = mdl_talentospilos_inst_cohorte.id_cohorte
        inner join mdl_cohort
          on mdl_cohort.id = mdl_talentospilos_inst_cohorte.id_cohorte
        inner join mdl_talentospilos_programa
          on mdl_talentospilos_history_academ.id_programa = mdl_talentospilos_programa.id
    order by codigo desc
SQL;
    return $DB->get_records_sql($sql);
}

/*
 used tables:
mdl_talentospilos_history_academ
    | id | id_estudiante | id_semestre | id_programa | promedio semestre | promedio_acumulado | json_materias |

mdl_cohort_members
    | id | cohortid | userid | timeadded |

mdl_talentospilos_usuario
    | id | tipo_doc_ini | num_doc_ini | tipo_doc | num_doc | dir_ini | barrio_ini | id_ciudad_ini | tel_ini | direccion_res | barrio_res | id_ciudad_res | tel_res | celular | emailpilos | acudiente | tel_acudiente | fecha_nac | id_ciudad_nac | sexo | colegio | estamento | observacion | estado | grupo | id_discapacidad | ayuda_disc | estado_ases | id_pais | vive_con | hijos | id_cond_excepcion | id_estado_civil | id_identidad_gen | id_act_simultanea | id_economics_data | anio_ingreso | actividades_ocio_deporte | id_schema_json | json_detalle | puntaje_icfes | estrato | id_etnia |

mdl_talentospilos_history_cancel
    | id | id_history | fecha_cancelacion |

mdl_talentospilos_user_extended
    | id | id_moodle_user | id_ases_user | id_academic_program | tracking_status | program_status |

mdl_talentospilos_semestre
    | id | nombre | fecha_inicio | fecha_fin |

mdl_user
    | id| auth| confirmed| policyagreed| deleted| username| password| email| emailstop| icq| skype| yahoo| aim| msn| phone1| phone2| institution| department| address| city| country| theme| timezone| firstaccess| lastaccess| lastlogin| currentlogin| lastip| secret| picture| url| description| mailformat| maildigest| maildisplay| autosubscribe| trackforums| timemodified| trustbitmask| imagealt| mnethostid| firstname| lastname| lang| idnumber| descriptionformat| timecreated| suspended| lastnamephonetic| firstnamephonetic| middlename| alternatename| calendartype |

mdl_talentospilos_inst_cohorte
    | id | id_cohorte | id_instancia |

mdl_cohort
    | id | contextid | name | idnumber | description | descriptionformat | component | timecreated | timemodified | visible | theme |

mdl_talentospilos_programa
    | id | codigosnies | cod_univalle | nombre | id_sede | jornada | id_facultad

 */

class ActiveSemestersReportField {
    public $codigo;
    public $nombre;
    public $talentos_usuario_id;
    public $num_doc;
    public $ases_user_id;
    public $num_carreras;
    public $egresado;
/**
 * @var array $semestres_activos Array of string than identify the active semesters of a student
 *  Example: ['2016A', '2016B' ...]
 */
    public $semestres_activos = array();
    public function __construct($codigo, $nombre, $talentos_usuario_id, $num_doc, $ases_user_id, $egresado, $num_carreras = false)
    {
        $this->codigo = $codigo;
        $this->talentos_usuario_id = $talentos_usuario_id;
        $this->nombre = $nombre;
        $this->ases_user_id = $ases_user_id;
        $this->num_carreras = $num_carreras;
        $this->num_doc = $num_doc;
        $this->egresado = $egresado;
    }


    /**
     * Add active semester to current report
     * @param array $active_semester
     */
    public function add_active_semester($active_semester) {
        if(false === array_search($active_semester, $this->semestres_activos)){
            array_push($this->semestres_activos, $active_semester);
        }

    }
    public function set_codigo($codigo, $tracking_status = 1) {
        if( $tracking_status == 1 && $codigo != $this->codigo) {
            echo $this->codigo. ' '. $codigo . ' ' . $this->nombre;
            $this->codigo = $codigo;
        }
    }

    public function have_active_semester($semester): bool {
       foreach($this->semestres_activos as $semestre){
           if($semester === $semestre[0]) {
               return true;
           }
       }

       return false;

    }

    public function list_active_careers($semester): string{
        $lista = '';
        foreach($this->semestres_activos as $semestre){
            if($semester === $semestre[0]) {
                $lista.='SI - ';
                $lista.=$semestre[1];
                $lista.='<br>';
            }
        }
        return $lista;
    }

}

/**
 * Return the semesters with a list of all ASES students was active
 * @param $id_instance
 * @param $cohort_id
 * @param $include_current_semester bool If is true, the current semester is included in the graph
 * @return array Array of ActiveSemestersReportField
 * @throws \dml_exception
 */

function get_active_semesters($id_instance, $cohort_id, $include_current_semester = false) {
    $semester_is_canceled = 'SI';
    $current_semester = \get_current_semester();
    $current_semester_name = $current_semester->nombre;
    $active_semesters_report_fields = array();
    $students_with_active_semesters  = get_active_semesters_db($id_instance, $cohort_id);
    foreach ($students_with_active_semesters as $students_with_active_semester) {
        $talentos_usuario_id = $students_with_active_semester->mdl_talentospilos_usuario_id;
        $num_doc = $students_with_active_semester->num_doc;
        $nombre_semestre =  $students_with_active_semester->mdl_talentospilos_semestre_nombre;
        $tracking_status = $students_with_active_semester->tracking_status;
        $codigo = $students_with_active_semester->codigo;
        $cancel_semester = $students_with_active_semester->cancela;
        $num_carreras = $students_with_active_semester->num_carreras;
        $ases_user_id = $students_with_active_semester->mdl_talentospilos_usuario_id;
        $egresado = $students_with_active_semester->egresado;
        $programa = $students_with_active_semester->programa;
        $semestre = array($nombre_semestre, $programa);

        if(!$include_current_semester && $nombre_semestre === $current_semester_name) {
            continue;
        }
        if(array_key_exists($talentos_usuario_id, $active_semesters_report_fields)) {

            if( !($cancel_semester === $semester_is_canceled)) {
                /** @var  $active_semesters_report_fields[$talentos_usuario_id] ActiveSemestersReportField*/
                $active_semesters_report_fields[$talentos_usuario_id]->add_active_semester($semestre);
                /* If the current registry have other code and have active tracking status the code is updated  */

            }
            $active_semesters_report_fields[$talentos_usuario_id]->set_codigo($codigo, $tracking_status);

        } else {
            $nombre = $students_with_active_semester->firstname . ' ' . $students_with_active_semester->lastname;
            $active_semesters_report_field = new ActiveSemestersReportField($codigo, $nombre, $talentos_usuario_id,  $num_doc, $ases_user_id, $egresado, $num_carreras);
            if( !($cancel_semester === $semester_is_canceled)) {
                $active_semesters_report_field->add_active_semester($semestre);
            }
            $active_semesters_report_fields[$talentos_usuario_id]  = $active_semesters_report_field;
        }
    }
    return $active_semesters_report_fields;

}