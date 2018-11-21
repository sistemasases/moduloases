<?php

require_once (__DIR__ . '/../../classes/API/BaseAPIView.php');
require_once (__DIR__ . '/../../classes/grades/ases_grade_report_grader.php');

/**
 * Class _params Dummy class for this api params
 * @property string $course_id
 * @property string $instance_id
 * @property string $course_caller_id Id of the course where the report is generated
 * @property int $page
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
class _params {}


/**
 * Class GradesAPI
 * @property _params $params
 */
class GradeTableView extends BaseAPIView {
    public function get_required_params(): array {
        return array (
            'course_id',
            'course_caller_id',
            'instance_id'
        );
    }
    public function __construct()
    {
        parent::__construct();
        $this->response_type = 'text/html';

    }

    public function send_response()
    {
        $sortitemid = 0;
        $report = new ases_grade_report_grader(
            $this->params->course_id,
            $this->params->course_caller_id,
            $this->params->instance_id,
            $this->params->page? $this->params->page: null,
            $sortitemid);

        return $report->get_grade_table();
    }
}