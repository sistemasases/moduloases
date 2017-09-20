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
 
// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/instance_management/instance_lib.php');
require_once('../managers/grade_categories/grade_categories_lib.php');
include('../lib.php');
include("../classes/output/renderer.php");

global $PAGE;

$title = "LISTADO DOCENTES";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

//se consulta si la instancia ya está registrada
if(!consult_instance($blockid)){
    header("Location: /blocks/ases/view/instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/grade_categories.php",array('courseid' => $courseid, 'instanceid' => $blockid));

//Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create($title,$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

$PAGE->set_url($url);
$PAGE->set_title($title);

//$PAGE->requires->css('/blocks/ases/style/grade_categories.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
// $PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.tableTools.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/NewCSSExport/buttons.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.tableTools.css', true);
//$PAGE->requires->css('/theme/base/style/core.css',true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
// $PAGE->requires->js('/blocks/ases/js/grade_categories.js', true);
// $PAGE->requires->js('/blocks/ases/js/jquery-2.2.4.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/jquery.dataTables.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/jquery.dataTables.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.jqueryui.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.bootstrap.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.bootstrap.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.tableTools.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.tableTools.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/buttons.flash.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/buttons.html5.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/buttons.print.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/dataTables.buttons.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/jszip.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/pdfmake.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/vfs_fonts.js', true);
// // $PAGE->requires->js('/blocks/ases/js/attendance_table.js', true);
// $PAGE->requires->js('/blocks/ases/js/main.js', true);
// $PAGE->requires->js('/blocks/ases/js/checkrole.js', true);
// $PAGE->requires->js('/blocks/ases/js/bootstrap.js', true);
// $PAGE->requires->js('/blocks/ases/js/bootstrap.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/sweetalert-dev.js', true);
// $PAGE->requires->js('/blocks/ases/js/grade_categories.js', true);
$output = $PAGE->get_renderer('block_ases');


//Carga de informacion a mostrar
$courses = get_courses_pilos($blockid);

$htmlTable =  "<table id = 'teachers' cellspacing='0' width='100%' border>
                <thead> 
                    <tr>
                        <th style = 'border-right: none'> Nombre Completo </th>
                        <th style='border-left: none; width: 60px;'>  </th>
                    </tr>
                </thead><tbody>
                ";
$index = 0;
$courses_info = '';
foreach($courses as $profe => $cursos){
    $htmlTable.="<tr class='cerrado' id = 'profesor_$index' ><td style='border-right: none' id = 'profe_$index'>$profe</td><td style='border-left: none'><button style='width: 100%' class = 'desplegate'><i class='glyphicon glyphicon-chevron-left'></i></button></td></tr>";
    
    $courses_info.="<div style = 'padding-left: 15px' id = 'curso_$index'>";
    foreach ($cursos as $curso) {
        $id = $curso['id'];
        $name = $curso['nombre'];
        $shortname = $curso['shortname'];
        $courses_info.="<div class = 'ir_curso' id='$id'> <i class = 'glyphicon glyphicon-new-window'></i> $name $shortname </div>";
    }
    $courses_info.="</div>";
    $index = $index+1;
}

$htmlTable.='</tbody></table>';

$record = new stdClass;
$record->teachersTable = $htmlTable;
$record->courses_info = $courses_info;

echo $output->header();
//echo $output->standard_head_html(); 
$grade_categories_page = new \block_ases\output\grade_categories_page($record);
echo $output->render($grade_categories_page);
echo $output->footer();