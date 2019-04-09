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
 * ASES
 *
 * @author     Jeison Cardona Gómez.
 * @package    block_ases
 * @copyright  2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once __DIR__ . '/../../../config.php';
require_once $CFG->libdir . '/adminlib.php';

require_once('../managers/lib/lib.php');
require_once('../managers/menu_options.php');
require_once('../managers/instance_management/instance_lib.php');
include('../lib.php');


global $PAGE;
global $USER;

include "../classes/output/plugin_status_page.php";
include "../classes/output/renderer.php";

$title = "Estado";
$courseid = required_param('courseid', PARAM_INT);
$instanceid = required_param('instanceid', PARAM_INT);

$record = new stdClass();

require_login($courseid, false);
if (!consult_instance($instanceid)) {
    header("Location: instanceconfiguration.php?courseid=$courseid&instanceid=$instanceid");
}

$contextcourse = context_course::instance($courseid);
$contextblock = context_block::instance($instanceid);

$url = new moodle_url("/blocks/ases/view/dashboard.php", array('courseid' => $courseid, 'instanceid' => $instanceid));

$rol = lib_get_rol_name_ases($USER->id, $instanceid);
$menu_option = create_menu_options($USER->id, $instanceid, $courseid);
$record->view_menu = $menu_option;
$record->view_title = "Estado";

$PAGE->set_context($contextcourse);
$PAGE->set_context($contextblock);
$PAGE->set_url($url);
$PAGE->set_title($title);

// Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Estado',$url, null, 'block', $instanceid);
$coursenode->add_node($blocknode);


$PAGE->requires->css('/blocks/ases/style/base_ases.css', true);
$PAGE->requires->css('/blocks/ases/style/aaspect.min.css', true);
$PAGE->requires->css('/blocks/ases/style/_view.component.css', true);

$PAGE->requires->js_call_amd('block_ases/ases_incident_system', 'init');

$output = $PAGE->get_renderer('block_ases');


$plugin_status_page = new \block_ases\output\plugin_status_page($record);

echo $output->header();
echo $output->render($plugin_status_page);
echo $output->footer();
