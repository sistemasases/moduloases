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
require_once (__DIR__ . '/../../../../config.php');

require_once (__DIR__ . '/report_active_semesters_lib.php');
require_once (__DIR__ . '/../../classes/API/BaseAPI.php');
require_once (__DIR__ . '/../../classes/DAO/BaseDAO.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$report_active_semesters_api = new BaseAPI();
$report_active_semesters_api->post('data_table', function($data, array $args) {
    $data = (object) $data;
    $r = get_report_active_semesters_datatable($data->instance_id, $data->cohort_id);

    print_r(json_encode($r));
});
$report_active_semesters_api->run();