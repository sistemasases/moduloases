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
 * Course and teacher report view
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/../managers/student/student_lib.php');
require_once (__DIR__ . '/../classes/Semestre.php');
require_once (__DIR__ . '/../managers/instance_management/instance_lib.php');
require_once (__DIR__ . '/../managers/menu_options.php');
require_once (__DIR__ . '/../classes/output/report_active_semesters_page.php');
$pagetitle = 'Adición de usuarios ASES a las cohortes';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
require_login($courseid, false);
$url = new moodle_url('/blocks/ases/view/report_active_semesters.php',
    array(
        'courseid' => $courseid,
        'instanceid' => $blockid
    ));
$output = $PAGE->get_renderer('block_ases');

$PAGE->requires->css('/blocks/ases/style/report_active_semesters.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);


// Navigation setup
$pagetitle = 'Reporte de semestres activos';
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create($pagetitle, $url, null, 'block', $blockid);
$coursenode->add_node($blocknode);

$PAGE->set_url($url);
$PAGE->set_title($pagetitle);

echo $output->header();
$data = new stdClass();
$data->cohorts_select = get_html_cohorts_select($blockid, false, 'cohorts', 'cohorts');
$menu_option = create_menu_options($USER->id, $blockid, $courseid);
$data->menu = $menu_option;
$report_active_semesters_page = new \block_ases\output\report_active_semesters_page($data);

echo $output->render($report_active_semesters_page);

$send_to_amd = new stdClass();
$send_to_amd->data = array('instance_id'=>$blockid);
$PAGE->requires->js_call_amd('block_ases/report_active_semesters', 'init', $send_to_amd);
echo $output->footer();