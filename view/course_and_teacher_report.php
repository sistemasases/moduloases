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
 * Create program view
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once __DIR__ . '/../../../config.php';
include "../classes/output/renderer.php";
require_once (__DIR__.'/../managers/course_and_teacher_report/course_and_teacher_report_lib.php');
include '../classes/output/course_and_teacher_report_page.php';
include "../managers/menu_options.php";
$pagetitle = 'Reporte de curso y profesor';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
error_reporting(E_ALL);
require_login($courseid, false);
$actions = authenticate_user_view($USER->id, $blockid);
$url = new moodle_url("/blocks/ases/view/course_and_teacher_report.php",
    array(
        'courseid' => $courseid,
        'instanceid' => $blockid)
);

if (!isset($actions->course_and_teacher_report)) {
    redirect(new moodle_url('/'), "No tienes permiso para acceder a los reportes por profesor",1, \core\output\notification::NOTIFY_INFO);
}
// Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Reportes por docente',$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);


$PAGE->set_url($url);
$PAGE->set_title($pagetitle);

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/simple-sidebar.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.tableTools.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/NewCSSExport/buttons.dataTables.min.css', true);
// $PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/academic_reports_style.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/course_and_teacher_report.css', true);





$output = $PAGE->get_renderer('block_ases');
// Menu items are created
$data = new stdClass();
$menu_option = create_menu_options($USER->id, $blockid, $courseid);
$data->menu = $menu_option;

echo $output->header();

$course_and_teacher_report_table = get_datatable_for_course_and_teacher_report($blockid);
$data->table = $course_and_teacher_report_table;
$paramReport->dataTable = $course_and_teacher_report_table;
$paramReport->mdl_course_caller_id = $courseid;

$PAGE->requires->js_call_amd('block_ases/course_and_teacher_report', 'load_report', $paramReport);
$output = $PAGE->get_renderer('block_ases');

$course_and_teacher_report = new \block_ases\output\course_and_teacher_report_page($data);
echo $output->render($course_and_teacher_report);

echo $output->footer();

















?>