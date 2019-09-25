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
 * Course and teacher extern API Views
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once (__DIR__ . '/../../classes/API/BaseAPIView.php');
require_once (__DIR__ . '/course_and_teacher_report_lib.php');


/**
 * Class GradesAPI
 */
class CourseAndTeacherReportView extends BaseAPIView {

    public function __construct()
    {
        parent::__construct();
        $this->response_type = 'application/json';
    }

    public function send_response()
    {
        $course_and_teacher_report_table = get_datatable_for_course_and_teacher_report($this->args['instance_id']);
        return $course_and_teacher_report_table;
    }
}