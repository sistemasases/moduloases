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
require_once (__DIR__ . '/../../managers/course/course_lib.php');

$report_active_semesters_api = new BaseAPI;
$report_active_semesters_api->get(':cohort_id', function($data, array $args) {
    $r = new CourseAndTeacherReportView();
    $r->execute($data, $args);
});