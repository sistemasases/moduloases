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
             (case when 
             (select count(*) from mdl_talentospilos_user_extended 
             where mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_usuario_outer.id) > 1
                        then 'SI'  else 'NO' end) as cambio_carrera,
       mdl_talentospilos_usuario_outer.id as mdl_talentospilos_usuario_id,
       username as codigo,
       firstname,
       lastname,
       mdl_talentospilos_semestre.nombre as mdl_talentospilos_semestre_nombre
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
     where mdl_talentospilos_inst_cohorte.id_instancia = $id_instance
    $cohort_sql_conditions
order by codigo desc
SQL;
    return $DB->get_records_sql($sql);
}


class ActiveSemestersReportField {
    public $codigo;
    public $nombre;
    public $talentos_usuario_id;
    public $num_doc;
    public $ases_user_id;
    public $cambio_carrera;
/**
 * @var array $semestres_activos Array of string than identify the active semesters of a student
 *  Example: ['2016A', '2016B' ...]
 */
    public $semestres_activos;
    public function __construct($codigo, $nombre, $talentos_usuario_id, $num_doc, $ases_user_id, $cambio_carrera = false, $semestres_activos = array())
    {
        $this->codigo = $codigo;
        $this->talentos_usuario_id = $talentos_usuario_id;
        $this->nombre = $nombre;
        $this->ases_user_id = $ases_user_id;
        $this->cambio_carrera = $cambio_carrera;
        $this->num_doc = $num_doc;
        $this->semestres_activos = $semestres_activos;
    }



    /**
     * Add active semester to current report
     * @param string $active_semester
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
       if(array_search($semester, $this->semestres_activos) === false) {
           return false;
       }
       return true;

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
        $cambio_carrera = $students_with_active_semester->cambio_carrera;
        $ases_user_id = $students_with_active_semester->mdl_talentospilos_usuario_id;
        if(!$include_current_semester && $nombre_semestre === $current_semester_name) {
            break;
        }
        if(array_key_exists($talentos_usuario_id, $active_semesters_report_fields)) {

            if( !($cancel_semester === $semester_is_canceled)) {
                /** @var  $active_semesters_report_fields[$talentos_usuario_id] ActiveSemestersReportField*/
                $active_semesters_report_fields[$talentos_usuario_id]->add_active_semester($nombre_semestre);
                /* If the current registry have other code and have active tracking status the code is updated  */

            }
            $active_semesters_report_fields[$talentos_usuario_id]->set_codigo($codigo, $tracking_status);

        } else {
            $nombre = $students_with_active_semester->firstname . ' ' . $students_with_active_semester->lastname;
            $active_semesters_report_field = new ActiveSemestersReportField($codigo, $nombre, $talentos_usuario_id,  $num_doc, $ases_user_id, $cambio_carrera);
            if( !($cancel_semester === $semester_is_canceled)) {
                $active_semesters_report_field->add_active_semester($nombre_semestre);
            }
            $active_semesters_report_fields[$talentos_usuario_id]  = $active_semesters_report_field;
        }
    }
    return $active_semesters_report_fields;

}