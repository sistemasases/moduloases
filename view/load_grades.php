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
 * Short description goes here
 *
 * @author     David Santiago Cortés
 * @package    block_ases
 * @copyright  2022 David Santiago Cortés <david.cortes@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
global $CFG;
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

require_once(__DIR__ . '/../managers/instance_management/instance_lib.php');
require_once('../managers/user_management/user_management_lib.php');
require_once('../managers/lib/lib.php');
require_once('../managers/lib/student_lib.php');
require_once(__DIR__ . '/../managers/validate_profile_action.php');
require_once(__DIR__ . '/../managers/menu_options.php');


require_once (__DIR__ . '/../classes/output/load_grades_page.php');
require_once (__DIR__ . '/../classes/output/renderer.php');

require_once (__DIR__ . '/../lib.php');

global $PAGE;
global $USER;


// Set up the page
$course_id = required_param('courseid', PARAM_INT);
$block_id = required_param('instanceid', PARAM_INT);

require_login($course_id, false);

if (!consult_instance($block_id)) {
    header("Location: instanceconfiguration.php?courseid=$course_id&instanceid=$block_id");
}

$contextcourse = context_course::instance($course_id);
$contextblock = context_block::instance($block_id);

$url = new moodle_url (
    "/blocks/ases/view/load_grades.php",
    array('courseid' => $course_id, 'instanceid' => $block_id
    )
);


$data = new stdClass();
$actions = authenticate_user_view($USER->id,$block_id);
$data = $actions;



$menu_option = create_menu_options($USER->id, $block_id, $course_id);
$data->menu = $menu_option;

// Navegación
$coursenode = $PAGE->navigation->find($course_id, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Carga de notas', $url, null, 'block', $block_id);
$coursenode->add_node($blocknode);


$page_title = 'Carga de notas';
$PAGE->set_url($url);
$PAGE->set_title($page_title);
$PAGE->set_heading($page_title);

$PAGE->requires->css('/blocks/ases/style/aaspect.min.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/load_grades.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);

$PAGE->requires->js_call_amd('block_ases/load_grades', 'init');

$OUTPUT = $PAGE->get_renderer('block_ases');

$load_grades_page = new \block_ases\output\load_grades_page($data);

echo $OUTPUT->header();
echo $OUTPUT->render($load_grades_page);
echo $OUTPUT->footer();
