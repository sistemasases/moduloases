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
 * Monitor assignments API Views
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2019 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (__DIR__ . '/../../classes/API/BaseAPIView.php');
require_once (__DIR__ . '/monitor_assignments_lib.php');
require_once (__DIR__ . '/../lib/csv.php');
/**
 * Class MonitorAndStudentAndPracticantParams
 * @property $instance_id
 * @property $semester_name
 */
class  MonitorAndStudentAndPracticantParams {}

/**
 * Class MonitorAndStudentAndPracticantAPIView
 */
class MonitorAndStudentAndPracticantAPIView  extends  BaseAPIView {
    public const PARAM_INSTANCE_ID = 'instance_id';
    public const PARAM_SEMESTER_NAME = 'semester_name';
    function __construct()
    {
        parent::__construct();
        $this->response_type = BaseAPIView::CONTENT_TYPE_JSON;
    }

    function get_required_params(): array
    {
        return array(
            MonitorAndStudentAndPracticantAPIView::PARAM_INSTANCE_ID,
            MonitorAndStudentAndPracticantAPIView::PARAM_SEMESTER_NAME
        );
    }
    /**
     * @return \jquery_datatable\Datatable
     * @throws dml_exception
     */
    function send_response()
    {
        $objects = monitor_assignments_get_practicants_monitors_and_students(
            $this->params->instance_id,
            $this->params->semester_name
        );
        \csv\array_to_csv_download($objects);
    }
}