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
 * @author     Cristian Duvan Machado Mosquera
 * @package    block_ases
 * @copyright  2022 Cristian DM <cristian.machado@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/ases_report/asesreport_lib.php');
require_once('../managers/instance_management/instance_lib.php');
require_once('../managers/student_profile/studentprofile_lib.php');
require_once('../managers/permissions_management/permissions_lib.php');
require_once('../managers/validate_profile_action.php');
require_once('../managers/menu_options.php');
require_once(__DIR__.'/../managers/cohort/cohort_lib.php');
require_once '../classes/AsesUser.php';
require_once '../managers/lib/student_lib.php';
include('../lib.php');
global $PAGE;
global $DB;

include("../classes/output/ases_nodos_imagen_page.php");
include("../classes/output/renderer.php");

// Set up the page.
$pagetitle = 'Nodos Graficos Libreria';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$id_current_user = $USER->id;

// Instance is consulted for its registration
if(!consult_instance($blockid)){
    header("Location: instance_configuration.php?courseid=$courseid&instanceid=$blockid");
    die();
}

require_login($courseid, false);



//se crean los elementos del menu
$menu_option = create_menu_options($id_current_user, $blockid, $courseid);


// Crea una clase con la información que se llevará al template.
$data = new stdClass();

// Evalua si el rol del usuario tiene permisos en esta view.
$actions = authenticate_user_view($id_current_user, $blockid);
$USER->actions = $actions;

foreach($actions as $act){
    $data->$act = $act;
}

$data->menu = $menu_option;


$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/ases_nodos_imagen.php",array('courseid' => $courseid, 'instanceid' => $blockid));


// Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Nodos Imagen',$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);


//consulta para obtener los nodos de la instancia
$sql_query_nodos = "SELECT DISTINCT tb_1.id,firstname , lastname , tb_2.id_usuario AS target , id_jefe AS source, tb_1.username FROM mdl_user AS tb_1
JOIN mdl_talentospilos_user_rol AS tb_2
ON tb_1.id = tb_2.id_usuario  AND tb_2.estado = 1
AND tb_2.id_semestre IN (SELECT MAX(id) FROM mdl_talentospilos_semestre)
WHERE (tb_2.id_jefe is not null IN (tb_2.id_rol = 4 OR tb_2.id_rol = 7) ) AND (
tb_2.id_rol = 4 OR tb_2.id_rol = 7 OR tb_2.id_rol = 3 OR tb_2.id_rol = 12)";

$nodos = $DB->get_records_sql($sql_query_nodos);

$sql_query_edges = "SELECT id_usuario AS target FROM mdl_talentospilos_user_rol
WHERE (id_rol = 3)  AND id_semestre IN (SELECT MAX(id) FROM mdl_talentospilos_semestre)";

$edges = $DB->get_records_sql($sql_query_edges);

$nodoid[] = array();

//recorrer el array de nodos para obtener los id de los nodos
foreach($nodos as $nodo) {
    $id_usuario_ases = get_ases_user_by_code($nodo->username);
    $nodoid[] = AsesUser::get_HTML_img_profile_image($contextblock->id, intval($id_usuario_ases->id));
}

$PAGE->requires->js_call_amd('block_ases/ases_nodos_imagen','init',array($nodos,$edges,$nodoid));

$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$output = $PAGE->get_renderer('block_ases');
$ases_nodos_imagen_page = new \block_ases\output\ases_nodos_imagen_page($data);

//echo $output->standard_head_html(); 
echo $output->header();
echo $output->render($ases_nodos_imagen_page);
echo $output->footer();
