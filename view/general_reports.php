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
 * Estrategia ASES
 *
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/instance_management/instance_lib.php');
include('../lib.php');
include("../classes/output/general_reports_page.php");
include("../classes/output/renderer.php");

global $PAGE;

// Variables for page setup
$title = "Reportes Generales";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

//se culta si la instancia ya está registrada
if(!consult_instance($blockid)){
    header("Location: /blocks/ases/view/instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/general_reports.php",array('courseid' => $courseid, 'instanceid' => $blockid));

//Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create($title,$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/simple-sidebar.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);


// $PAGE->requires->js('/blocks/ases/js/jquery-2.2.4.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/sweetalert-dev.js', true);
// $PAGE->requires->js('/blocks/ases/js/gr_grade_google.js', true);
// $PAGE->requires->js('/blocks/ases/js/main.js', true);
// $PAGE->requires->js('/blocks/ases/js/checkrole.js', true);
// $PAGE->requires->js('/blocks/ases/js/bootstrap.js', true);
// $PAGE->requires->js('/blocks/ases/js/bootstrap.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/jquery.dataTables.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/jquery.dataTables.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.jqueryui.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.bootstrap.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.bootstrap.js', true);

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
//echo $output->standard_head_html(); 
$prueba = new \block_ases\output\general_reports_page('Some text');
echo $output->render($prueba);
echo $output->footer();