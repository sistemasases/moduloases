<?php

require_once (__DIR__ . '/../../classes/API/BaseAPIView.php');
require_once (__DIR__ . '/course_and_teacher_report_lib.php');

/**
 * Class _params Dummy class for this api params
 * @property string $instance_id
 * @property string $course_id Course from which the request is being made
 */

class _params {}
/**
 * Class GradesAPI
 * @property _params $params
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