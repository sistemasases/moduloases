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
 * General Reports
 *
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');


global $PAGE;

include("../classes/output/upload_files_page.php");
include("../classes/output/renderer.php");
require_once('../managers/query.php');
// Set up the page.
$title = "Carga de archivos";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

//se culta si la instancia ya está registrada
if(!consultInstance($blockid)){
    header("Location: instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}



$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

require_capability('block/ases:configurateintance', $contextblock);


$url = new moodle_url("/blocks/ases/view/upload_files_form.php", array('courseid' => $courseid, 'instanceid' => $blockid));


//se configura la navegacion
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$node = $coursenode->add('Gestion de archivos',$url);
$node->make_active();

//Se configura la pagina

$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->set_heading($title);

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);

$PAGE->requires->js('/blocks/ases/js/jquery-2.2.4.min.js', true);
$PAGE->requires->js('/blocks/ases/js/bootstrap.js', true);
$PAGE->requires->js('/blocks/ases/js/bootstrap.min.js', true);
$PAGE->requires->js('/blocks/ases/js/sweetalert-dev.js', true);
// $PAGE->requires->js('/blocks/ases/js/checkrole.js', true);
$PAGE->requires->js('/blocks/ases/js/npm.js', true);
$PAGE->requires->js('/blocks/ases/js/main.js', true);
$PAGE->requires->js('/blocks/ases/js/upload.js', true);
$PAGE->requires->js('/blocks/ases/js/upload_files.js', true);

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
// echo $output->standard_head_html(); 
$upload_files_page = new \block_ases\output\upload_files_page('Some text');
echo $output->render($upload_files_page);
echo $output->footer();
