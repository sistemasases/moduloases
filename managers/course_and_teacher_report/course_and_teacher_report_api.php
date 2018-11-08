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
 * Course and teacher extern API
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (__DIR__ . '/../../../../config.php');

require_once (__DIR__ . '/course_and_teacher_report_api_views.php');
require_once (__DIR__ . '/course_and_teacher_report_lib.php');
require_once (__DIR__ . '/../../classes/API/BaseAPI.php');
require_once (__DIR__ . '/../../classes/DAO/BaseDAO.php');
require_once (__DIR__ . '/../../managers/course/course_lib.php');
require_once(__DIR__ . '/../../vendor/autoload.php');
use function Latitude\QueryBuilder\{literal, criteria, alias, on, field};
error_reporting(E_ALL);
ini_set('display_errors', '1');
$course_and_teacher_api = new BaseAPI;
$course_and_teacher_api->get(':instance_id', function($data, array $args) {
    $r = new CourseAndTeacherReportView();
    $r->execute($data, $args);
});

function _select_count_ases_courses($id_instancia = 450299, $semestre = '201808') {
    return BaseDAO::get_factory()
    ->select(literal('count(*)'))
    ->from(
        subquery(
            _select_ases_courses('201808', 450299)
                ->innerJoin(
                    alias('',''),
                    on('','')
                )
                ->andWhere(field('estados_ases.nombre')->eq('seguimiento'))
                ->andWhere(field('inst_cohorte.id_instancia')->eq($id_instancia))
                ->andWhere(field('user_extended.tracking_status')->eq(1))
                ->andWhere(field('mdl_role_assignments.roleid')->eq(5))
                ->andWhere(criteria("substring(mdl_course.shortname from 15 for 6) = %s", $semestre))
                ->addColumns('mdl_course.id'),
            'some'

        ));
}
<<<SQL

where mdl_grade_items.itemtype != 'category'
  AND mdl_grade_items.itemtype != 'course'
and mdl_grade_grades.finalgrade is not null
SQL;

function _select_count_courses_with_at_least_one_item_calif($id_instancia = 450299, $semestre= '201808') {
    return BaseDAO::get_factory()
        ->select(literal(' count(mdl_course_id)'))
        ->from(
            subquery(
                _select_ases_courses('201808', 450299)
                    ->innerJoin(
                        alias('{grade_items}', 'mdl_grade_items'),
                        on('mdl_grade_items.courseid', 'mdl_course.id'))
                    ->innerJoin(
                        alias('{grade_grades}','mdl_grade_grades'),
                        on('mdl_grade_grades.itemid','mdl_grade_items.id'))
                    ->where(field('mdl_grade_items.itemtype')->notEq('course'))
                    ->andWhere(field('mdl_grade_items.itemtype')->notEq('category'))
                    ->andWhere(field('mdl_grade_grades.finalgrade')->isNotNull())


                    ->andWhere(field('estados_ases.nombre')->eq('seguimiento'))
                    ->andWhere(field('inst_cohorte.id_instancia')->eq($id_instancia))
                    ->andWhere(field('user_extended.tracking_status')->eq(1))
                    ->andWhere(field('mdl_role_assignments.roleid')->eq(5))
                    ->andWhere(criteria("substring(mdl_course.shortname from 15 for 6) = %s", $semestre))
                    ->addColumns(alias('mdl_course.id', 'mdl_course_id')),
                'some'

            ));
}

function _select_count_courses_with_at_least_one_item($id_instancia = 450299, $semestre= '201808') {
    return BaseDAO::get_factory()
        ->select(literal(' count(mdl_course_id)'))
        ->from(
            subquery(
                _select_ases_courses('201808', 450299)
                    ->innerJoin(
                        alias('{grade_items}', 'mdl_grade_items'),
                        on('mdl_grade_items.courseid', 'mdl_course.id'))
                    ->where(field('mdl_grade_items.itemtype')->notEq('course'))
                    ->andWhere(field('mdl_grade_items.itemtype')->notEq('category'))


                    ->andWhere(field('estados_ases.nombre')->eq('seguimiento'))
                    ->andWhere(field('inst_cohorte.id_instancia')->eq($id_instancia))
                    ->andWhere(field('user_extended.tracking_status')->eq(1))
                    ->andWhere(field('mdl_role_assignments.roleid')->eq(5))
                    ->andWhere(criteria("substring(mdl_course.shortname from 15 for 6) = %s", $semestre))
                    ->addColumns(alias('mdl_course.id', 'mdl_course_id')),
                'some'

            ));
}
$course_and_teacher_api->get('sql/courses_without_teacher', function() {
    echo '<pre>';
    print_r(get_reporte_cursos_sin_profesor(450299, '201808'));
    echo '</pre>';
});

$course_and_teacher_api->get('sql/ases_courses', function() {
    global $DB;

    $return = new stdClass();
    $query = _select_count_ases_courses()->compile();
    $records = $DB->get_records_sql($query->sql(), $query->params());
    $cantidad_cursos_un_ases = array_values($records)[0]->count;
    $return->cantidad_cursos_con_almenos_un_ases = $cantidad_cursos_un_ases ;
    $query_ = _select_count_courses_with_at_least_one_item_calif()->compile();
    $records_ = $DB->get_records_sql($query_->sql(), $query_->params());
    $cantidad_cursos_almenos_un_item_calif = array_values($records_)[0]->count;
    $return->cantidad_cursos_almenos_un_item_calif = $cantidad_cursos_almenos_un_item_calif ;

    $query__ = _select_count_courses_with_at_least_one_item()->compile();
    $records = $DB->get_records_sql($query__->sql(), $query__->params());
    $cantidad_cursos_almenos_un_item = array_values($records)[0]->count;
    $return->cantidad_cursos_almenos_un_item = $cantidad_cursos_almenos_un_item ;

    print_r(json_encode($return));
});

$course_and_teacher_api->run();