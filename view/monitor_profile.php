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
 * @author     David S. Cortés
 * @package    block_ases
 * @copyright  2020 David S. Cortés david.cortes@correounivalle.edu.co
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

//require_once(__DIR__ . '/../core/module_loader.php');
require_once('../managers/instance_management/instance_lib.php');
require_once('../managers/lib/lib.php');
require_once('../managers/lib/student_lib.php');
require_once('../managers/validate_profile_action.php');
require_once('../managers/menu_options.php');

include "../lib.php";
include "../classes/output/monitor_profile_page.php";
include "../classes/output/renderer.php";


//module_loader('periods');

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
$id_current_user = $USER->id;

$url = new moodle_url("/blocks/ases/view/monitor_profile.php", array('courseid' => $course_id, 'instanceid' => $block_id));

// Clase con la información que se llevará al template.
$data = new stdClass();

$cohorts_select = \cohort_lib\get_html_cohorts_select($block_id);
$data->cohorts_select = $cohorts_select;

// Navegación
$coursenode = $PAGE->navigation->find($course_id, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Pérfil del monitor', $url, null, 'block', $block_id);
$coursenode->add_node($blocknode);

$menu_option = create_menu_options($id_current_user, $block_id, $course_id);
$data->menu = $menu_option;

$page_title = 'Pérfil del monitor';
$PAGE->set_url($url);
$PAGE->set_title($page_title);
$PAGE->set_heading($page_title);

$PAGE->requires->css('/blocks/ases/style/aaspect.min.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);


$output = $PAGE->get_renderer('block_ases');
$monitor_profile_page = new \block_ases\output\monitor_profile_page($data);

echo $output->header();
echo $output->render($monitor_profile_page);
echo $output->footer();
