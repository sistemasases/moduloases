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
 * Student item grades report lib
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once (__DIR__ . '/../../../../config.php');
require_once ($CFG->dirroot . '/lib/grade/grade_item.php');
require_once ($CFG->dirroot . '/lib/datalib.php');
require_once ($CFG->dirroot . '/lib/grade/grade_grade.php');
require_once (__DIR__ . '/../jquery_datatable/jquery_datatable_lib.php');
require_once (__DIR__ . '/../../managers/periods_management/periods_lib.php');
require_once (__DIR__ . '/../../managers/course/course_lib.php');


use block_rss_client\output\item;
use core_analytics\course;
use function course_lib\getTeacherName;
use function course_lib\normalize_short_name;

/**
 * Class LosedAndAprovedItemGradesItemReport, model for objects returned at method get_losed_and_aproved_item_grades
 * @see get_losed_and_aproved_item_grades
 */
abstract class LosedAndAprovedItemGradesItemReport {
    public $cantidad_items_perdidos;
    public $cantidad_items_ganados;
    public $username;
    public $firstname;
    public $lastname;
    public $mdl_talentospilos_usuario_id;
    public $mdl_user_id;
    public $num_doc;
}

/**
 * Return counters of aproved or losed item grades agrouped  by student
 * @param $id_instancia string|int Instance id for filter the results
 * @param $semestre string Current semester representation (example: 201808, 201804)
 *  if none is given, return the current semester data
 * @returns LosedAndAprovedItemGradesItemReport
 */
function get_losed_and_aproved_item_grades($id_instancia, $semestre = null) {

    global $DB;

    if(!$semestre) {
        $semestre_object = get_current_semester();
        $sem = $semestre_object->nombre;
        $anio = substr($sem,0,4);

        if(substr($sem,4,1) == 'A'){
            $semestre = $anio.'02';
        }else if(substr($sem,4,1) == 'B'){
            $semestre = $anio.'08';
        }
    }

    $sql = <<<SQL
select  num_doc, count(*) filter(where not item_ganado) as cantidad_items_perdidos, count(*) filter (where item_ganado) as cantidad_items_ganados, username, mdl_user_id, mdl_talentospilos_usuario_id, firstname, lastname   from (
select
   distinct mdl_user.*, mdl_talentospilos_usuario.num_doc,
            case when (finalgrade < grademax * 0.6 or finalgrade is  null) then false else true end as item_ganado,
            mdl_user.id as mdl_user_id,
            mdl_talentospilos_usuario.id as mdl_talentospilos_usuario_id, finalgrade, grademax, mdl_grade_items.itemname, mdl_grade_items.id as item_id  ,
            (select count(*) from mdl_grade_grades as mdl_grade_grades_inner
                                    inner join mdl_grade_items as mdl_grade_items_inner
                                      on mdl_grade_grades_inner.itemid = mdl_grade_items_inner.id
             where mdl_grade_items_inner.courseid = mdl_course.id
               and mdl_grade_items_inner.id = mdl_grade_items.id
               and mdl_grade_grades.userid = mdl_user.id
               and mdl_grade_grades_inner.finalgrade is not null ) as calificaciones_item_todos_estudiantes
from mdl_talentospilos_usuario
      inner join mdl_talentospilos_user_extended
        on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_usuario.id
      inner join mdl_user
        on mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
    inner join mdl_cohort_members
    on mdl_cohort_members.userid = mdl_user.id
      inner join mdl_talentospilos_est_estadoases
        on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_est_estadoases.id_estudiante
      inner join mdl_talentospilos_estados_ases
        on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
      inner join mdl_grade_grades
        on mdl_grade_grades.userid = mdl_user.id
      inner join mdl_grade_items
        on mdl_grade_items.id = mdl_grade_grades.itemid
      inner join mdl_course
        on mdl_grade_items.courseid= mdl_course.id
    inner join mdl_talentospilos_inst_cohorte
    on mdl_talentospilos_inst_cohorte.id_cohorte = mdl_cohort_members.cohortid
where substring(mdl_course.shortname from 15 for 6) =  '$semestre'
and mdl_talentospilos_inst_cohorte.id_instancia = $id_instancia
 and mdl_grade_items.itemtype != 'category'
 AND  mdl_grade_items.itemtype != 'course'
 and mdl_talentospilos_user_extended.tracking_status = 1
 and mdl_talentospilos_estados_ases.nombre = 'seguimiento'
) as notas_estudiante
where notas_estudiante.calificaciones_item_todos_estudiantes > 0
group by (username, mdl_talentospilos_usuario_id, mdl_user_id, firstname, lastname, num_doc)
;
SQL;
    return $DB->get_records_sql($sql);

}

function get_user_grade_grades_with_items($user_id, $course_id = null, $only_graded_items = true) {
    global $DB;
    $sql = <<<SQL
    SELECT *, mdl_grade_grades.id as grade_id, mdl_grade_items.id as item_id FROM mdl_grade_grades
    inner join mdl_grade_items
    on mdl_grade_grades.itemid = mdl_grade_items.id
    where mdl_grade_grades.userid = $user_id
    and mdl_grade_items.itemtype != 'category'
    AND  mdl_grade_items.itemtype != 'course'
SQL;
    if($course_id) {
        $sql.="and mdl_grade_items.courseid = $course_id";
    }
    if ($only_graded_items) {
        $sql.=<<<SQL
    and mdl_grade_items.id in 
    (select distinct mdl_grade_items_inner.id from mdl_grade_items as mdl_grade_items_inner
    inner join mdl_grade_grades
        on mdl_grade_grades.itemid = mdl_grade_items_inner.id
    where mdl_grade_items_inner.courseid = mdl_grade_items.courseid
    and finalgrade is not null)
SQL;
    }
    return $DB->get_records_sql($sql);
}

abstract class student_item_grades extends grade_grade {

}

/**
 * Class student_item_grades_with_items
 * @property $grade_id
 * @property $item_id
 * @property $multfactor
 * @property $itemname
 */
abstract class student_item_grades_with_items extends student_item_grades {

}

/**
 * Return an array of objects than contains the join of the objects grade item and grade grade
 * @see mdl_grade_grades
 * @see mdl_grade_items
 * @param $student_id int Moodle user id
 * @param $course_id int Moodle course id
 * @param $only_graded_items boolean Include only items graded by teacher or all items in a course
 * @return array Array of student_item_grades_with_items objects
 */
function get_student_item_grades_by_course($student_id, $course_id=null, $only_graded_items=true) {
    $user_grades_with_items = get_user_grade_grades_with_items($student_id, $course_id, $only_graded_items);
    return $user_grades_with_items;
}

/**
 * Class than represent an item in student item grades summary report
 */
class ReportStudentItemGradesSummaryItem {
    /**
     * Course code in format SEDE-CODE-GROUP
     * @var $codigo_asignatura string
     */
    public $codigo_asignatura;
    /**
     * Course fullname
     * @see mdl_course.fullname
     * @var $nombre_asignatura string
     */
    public $nombre_asignatura;
    /**
     * @var $nombre_profesor string Teacher name
     */
    public $nombre_profesor;
    /**
     * @var $notas string Summary of grades of the current student in a current course
     *  Example: "Exam(20%): 4.5 - Quiz(10%): 3.0
     */
    public $notas;
    /**
     * Course final grade if is calculated
     * @var $nota_final Course final grade
     */
    public $nota_final;
    public function  __construct()
    {
        $this->notas = '';
    }
}

/**
 * Return a summary of the student grade in a single readable string,
 * @param $item student_item_grades_with_items
 * @param $include_null_finalgraes bool If is true, and the grade has null finalgrade empty string is returned
 * @return string Example: "Exam(70%): 5.0"" or empty string if include null final grade is false and final grade is false
 */
function normalize_grade($item, $decimal_places = 1): string {
    $finalgrade =  number_format((float) $item->finalgrade, $decimal_places);
    $formated_final_grade = $finalgrade == 0? 0 : $finalgrade;
    $formated_mult_factor = number_format((float) $item->multfactor * 100, 0);
    $formated_mult_factor_str = $formated_mult_factor == 100? '' : "($formated_mult_factor)";
    $first_word_item_name = explode(' ', $item->itemname)[0];
    $normalized_grade="$first_word_item_name$formated_mult_factor_str: $formated_final_grade";
    return $normalized_grade;
}
/**
 * Return a summary of the student grades in a single readable string
 * @param $item_grades array Array of student_item_grades_with_items
 * @param $separator string Separator between grades summary
 * @return string Example: "Exam(20%): 4.5 - Quiz(10%): 3.0 - Exam(70%): 5.0"
 */
function normalize_grades($item_grades, $separator=' - '): string {

    $normalized_grades = array_map(
        function ($item) {
            return normalize_grade($item);
        },
        $item_grades);
    $normalized_grades_str = implode($separator, $normalized_grades);
    return $normalized_grades_str;
}

/**
 * Return individual element for the report student grades summary
 *
 * @param $student_id
 * @param $course_id
 * @return mixed
 * @throws dml_exception
 */
function get_student_item_grades_sumary_report_item($student_id, $course_id ) {
    $course = get_course($course_id, false);
    $report_item = new ReportStudentItemGradesSummaryItem();
    $report_item->nombre_asignatura = $course->fullname;
    $report_item->nombre_profesor = \course_lib\getTeacherName($course_id);
    $items_and_grades = get_student_item_grades_by_course($student_id, $course_id);
    $report_item->codigo_asignatura = \course_lib\normalize_short_name($course->shortname);
    $report_item->notas = normalize_grades($items_and_grades);
    $report_item->nota_final = \course_lib\get_finalgrade_by_student_and_course($student_id, $course_id);
    return $report_item;

}

function get_student_item_grades_sumary_report($student_id, $semestre = null) {
    $report_items = array();

    if(!$semestre) {
        $semestre_object = get_current_semester();
        $sem = $semestre_object->nombre;
        $anio = substr($sem,0,4);

        if(substr($sem,4,1) == 'A'){
            $semestre = $anio.'02';
        }else if(substr($sem,4,1) == 'B'){
            $semestre = $anio.'08';
        }
    }

    $student_courses = \course_lib\get_courses_with_grades($student_id, $semestre);

    foreach($student_courses as $student_course) {

        array_push($report_items, get_student_item_grades_sumary_report_item($student_id, $student_course->id_course));
    }
    return $report_items;
}


/**
 * Return a datatable formated as array with all information needed for item grades agrouped by students
 * @param string $instance_id
 * @return array Datatable with indexs: {bsort, columns, data, language, order}
 */
function get_datatable_for_student_grades_report($instance_id) {

    $common_language_config = \jquery_datatable\get_datatable_common_language_config();
    $columns = array();
    $data = array_values(get_losed_and_aproved_item_grades($instance_id));
    $columna_items_perdidos =  array(
        "title"=>"# Items perdidos",
        "name"=>"cantidad_items_perdidos",
        "data"=>"cantidad_items_perdidos",
        "description"=>"Sumatoria de los items perdidos entre todos los cursos de el estudiante");
    $columnn_detail_data= \jquery_datatable\get_datatable_class_column();
    $columnn_detail_data['description'] = 'Mostrar información referente a los cursos que el estudiante tiene matriculados en el presente semestre, cursos que almenos tienen una nota registrada';
    array_push($columns, $columnn_detail_data);
    array_push($columns, array(
        "title"=>"Número de documento",
        "name"=>'num_doc',
        "data"=>"num_doc",
        "description"=>"Número de docuento de el estudiante"));
    array_push($columns, array(
        "title"=>"Apellidos",
        "name"=>"lastname",
        "data"=>"lastname"));


    array_push($columns, array(
        "title"=>"Nombres",
        "name"=>"firstname",
        "data"=>"firstname"));

    array_push($columns, $columna_items_perdidos);

    array_push($columns, array(
        "title"=>"# Items ganados",
        "name"=>"cantidad_items_ganados",
        "data"=>"cantidad_items_ganados",
        'description'=>'Sumantoria de los items ganados entre todos los cursos de el estudiante'));

    $columna_items_perdidos_index = array_search($columna_items_perdidos, $columns);
    $data_table = array(
        "bsort" => false,
        "columns" => $columns,
        "data" => $data,
        "language" => $common_language_config,
        "order"=> array($columna_items_perdidos_index, "desc")
    );
    return $data_table;
}

