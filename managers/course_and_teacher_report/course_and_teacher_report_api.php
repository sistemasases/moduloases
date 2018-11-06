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

require_once (__DIR__ . '/../../managers/lib/route.php');
require_once (__DIR__ . '/../../classes/AsesUser.php');
require_once (__DIR__ . '/../../classes/API/BaseAPIView.php');
require_once (__DIR__ . '/../../classes/API/BaseAPI.php');
require_once (__DIR__ . '/../../classes/Errors/Factories/FieldValidationErrorFactory.php');
require_once (__DIR__ . '/../../classes/grades/ases_grade_report_grader.php');



/**
 * Class _params Dummy class for this api params
 * @property string $course_id
 * @property string $instance_id
 * @property string $course_caller_id Id of the course where the report is generated
 * @property int $page
 */

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
$course_and_teacher_api = new BaseAPI;

$course_and_teacher_api->post('grade_table', function($data) {
    $r = new GradeTableView();
    $r->execute($data);
});
$course_and_teacher_api->run();

