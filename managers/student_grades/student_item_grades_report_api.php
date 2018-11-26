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
 * Student item grades report  public API
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
use function course_lib\get_courses_by_student;
require_once (__DIR__ . '/../../../../config.php');

require_once (__DIR__ . '/student_item_grades_report_lib.php');
require_once (__DIR__ . '/../../classes/API/BaseAPI.php');
require_once (__DIR__ . '/../../classes/API/BaseAPIView.php');

class StudentGradesInACourseSummary extends BaseAPIView {
    function __construct()
    {
        parent::__construct();
        $this->response_type = 'application/json';
    }

    function send_response()
    {

        return get_student_item_grades_sumary_report($this->args['student_id']);
    }
}

class StudentGradesInACourseSummaryByCourse extends BaseAPIView {
    function __construct()
    {
        parent::__construct();
        $this->response_type = 'application/json';
    }

    /**
     * @return mixed
     * @throws dml_exception
     */
    function send_response()
    {
        return get_student_item_grades_sumary_report_item($this->args['student_id'], $this->args['course_id']);
    }
}

$student_item_grades_report_api = new BaseAPI;
$student_item_grades_report_api->get(':instance_id', function($data, array $args) {

    echo json_encode(get_datatable_for_student_grades_report($args['instance_id']));
});
$student_item_grades_report_api->get('student_grades/item_summary_by_course/:student_id/:course_id', function($data, array $args) {
    $view = new StudentGradesInACourseSummaryByCourse();
    $view->execute($data, $args);
});
$student_item_grades_report_api->get('student_grades/item_summary/:student_id', function($data, array $args) {

    $view = new StudentGradesInACourseSummary();

    $view->execute($data, $args);
});
$student_item_grades_report_api->run();