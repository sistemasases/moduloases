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

$course_and_teacher_api = new BaseAPI;
$course_and_teacher_api->get(':instance_id', function($data, array $args) {
    $r = new CourseAndTeacherReportView();
    $r->execute($data, $args);
});

$course_and_teacher_api->get('sql/courses_without_teacher', function() {
    echo '<pre>';
    print_r(get_reporte_cursos_sin_profesor(450299, '201808'));
    echo '</pre>';
});
/**
 * Betha: ases course and teacher summary
 */
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