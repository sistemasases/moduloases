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
 * View dedicated to perform communications between dependencies.
 *
 * @author     Jorge Eduardo Mayor Fernández
 * @package    block_ases
 * @copyright  2020 Jorge Eduardo Mayor <mayor.jorge@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

require_once '../managers/instance_management/instance_lib.php';
require_once '../managers/lib/lib.php';
require_once('../managers/validate_profile_action.php');

include '../lib.php';
include "../classes/output/communications_page.php";
include "../classes/output/renderer.php";

global $PAGE;

$page_title = "Comunicaciones";
$course_id = required_param('courseid', PARAM_INT);
$block_id = required_param('instanceid', PARAM_INT);

require_login($course_id, false);

// Set up the page.
if (!consult_instance($block_id)) {
    header("Location: instanceconfiguration.php?courseid=$course_id&instanceid=$block_id");
}

$contextcourse = context_course::instance($course_id);
$contextblock = context_block::instance($block_id);

$url = new moodle_url("/blocks/ases/view/communications.php", array('courseid' => $course_id, 'instanceid' => $block_id));

// Crea una clase con la información que se llevará al template.
$data = new stdClass();


// Navigation setup
$coursenode = $PAGE->navigation->find($course_id, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Comunicaciones',$url, null, 'block', $block_id);
$coursenode->add_node($blocknode);


$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$PAGE->requires->css('/blocks/ases/style/aaspect.min.css', true);

$PAGE->requires->js_call_amd('block_ases/communications', 'init');

$output = $PAGE->get_renderer('block_ases');
$communications_page = new \block_ases\output\communications_page($data);

echo $output->header();
echo $output->render($communications_page);
echo $output->footer();