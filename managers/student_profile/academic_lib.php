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
 * Ases block
 *
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2018 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once dirname(__FILE__) . '/../../../../config.php';
require_once $CFG->dirroot . '/grade/querylib.php';
require_once $CFG->dirroot . '/grade/report/user/lib.php';
require_once $CFG->dirroot . '/grade/lib.php';

/**
 * Returns an html wiht courses and grades for a student in the last semester
 *
 * @see get_grades_courses_student_last_semester($id_student)
 * @param $id_student --> id from {user}
 * @return string html courses
 */

function get_grades_courses_student_last_semester($id_student)
{

    global $DB;

    $query_semestre = "SELECT nombre FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
    $sem = $DB->get_record_sql($query_semestre)->nombre;

    $año = substr($sem, 0, 4);

    if (substr($sem, 4, 1) == 'A') {
        $last_semester = $año . '02';
    } else if (substr($sem, 4, 1) == 'B') {
        $last_semester = $año . '08';
    }

    $courses = get_courses_by_student($id_student, $last_semester);

    $html_courses = make_html_courses($courses);
    return $html_courses;
}
//print_r(get_grades_courses_student_last_semester(144));

/**
 * Process info of a courses array and make an html collapsable
 *
 * @see make_html_courses($courses)
 * @param $courses --> array() of stdClass object representing courses and grades for single student
 * @return string --> hmtl text with the info
 */

function make_html_courses($courses)
{
    $html = '';

    foreach ($courses as $course) {

        $html .= "<div class='panel panel-default'>
                    <div class='panel-heading' id = 'academic'>
                        <h4 class='panel-title'>
                        <a data-toggle='collapse' data-parent='#accordion_academic' href='#course_$course->id_course' aria-expanded='false' aria-controls='$course->id_course'>
                            $course->fullname 
                        </a>
                        </h4>
                    </div>
                    <div id = 'course_$course->id_course' class='panel-collapse collapse'>
                        <div class = 'panel-body'>
                            $course->descriptions
                        </div>
                    </div>
                  </div>";

    }

    return $html;
}

/**
 * Return courses and grades for a student in the last semester
 *
 * @see get_courses_by_student($id_student, $last_semester)
 * @param $id_student --> student id
 * @param $last_semester --> last semester string identifier
 * @return array --> filled with stdClass objects representing courses and grades for single student
 */

function get_courses_by_student($id_student, $last_semester)
{

    global $DB;

    $query = "SELECT DISTINCT curso.id as id_course,
			                curso.fullname,
			                curso.shortname,
			                to_timestamp(curso.timecreated)::DATE AS time_created
			FROM {course} curso
			INNER JOIN {enrol} role ON curso.id = role.courseid
			INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
			WHERE enrols.userid = $id_student AND SUBSTRING(curso.shortname FROM 15 FOR 6) = '$last_semester'
            ORDER BY time_created DESC";

    $result_query = $DB->get_records_sql($query);
    $courses_array = array();
    foreach ($result_query as $result) {
        $result->grade = number_format(grade_get_course_grade($id_student, $result->id_course)->grade, 2);
        $result->descriptions = getCoursegradelib($result->id_course, $id_student);
        array_push($courses_array, $result);
    }

    return $courses_array;

}

//print_r(get_courses_by_student(144,false));

function getCoursegradelib($courseid, $userid)
{
    $context = context_course::instance($courseid);

    $gpr = new grade_plugin_return(array('type' => 'report', 'plugin' => 'user', 'courseid' => $courseid, 'userid' => $userid));
    $report = new grade_report_user($courseid, $gpr, $context, $userid);
    reduce_table($report);

    if ($report->fill_table()) {
        return $report->print_table(true);
    }
    return null;
}

/**
 * Reduces course information to display
 *
 * @param &$report --> report object containing information to reduce, such as percentage, range, feedback and contributiontocoursetotal.
 * @return null
 */
function reduce_table(&$report)
{
    $report->showpercentage = false;
    $report->showrange = false;
    $report->showfeedback = false;
    $report->showcontributiontocoursetotal = false;
    $report->setup_table();
}
