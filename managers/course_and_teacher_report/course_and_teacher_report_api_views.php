<?php

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