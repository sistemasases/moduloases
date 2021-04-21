<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

require_once '../managers/instance_management/instance_lib.php';
require_once('../managers/menu_options.php');
require_once '../managers/lib/lib.php';
require_once('../managers/validate_profile_action.php');
require_once('../managers/asistencia_monitorias/asistencia_monitorias_lib.php');

include '../lib.php';
include "../classes/output/monitorias_academicas_page.php";
include "../classes/output/renderer.php";

global $PAGE;


$page_title = 'Monitorias académicas';
$course_id = required_param('courseid', PARAM_INT);
$block_id = required_param('instanceid', PARAM_INT);

require_login($course_id, false);

if (!consult_instance($block_id)) {
    header("Location: instanceconfiguration.php?courseid=$course_id&instanceid=$block_id");
}

$contextcourse = context_course::instance($course_id);
$contextblock = context_block::instance($block_id);
$id_current_user = $USER->id;

$url = new moodle_url("/blocks/ases/view/monitorias_academicas.php", array('courseid' => $course_id, 'instanceid' => $block_id));

$data = new stdClass();

$coursenode = $PAGE->navigation->find($course_id, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Monitorias',$url, null, 'block', $block_id);
$coursenode->add_node($blocknode);

$menu_option = create_menu_options($id_current_user, $block_id, $course_id);
$data->menu = $menu_option;

$PAGE->set_url($url);
$PAGE->set_title($page_title);
$PAGE->set_heading($page_title);


$PAGE->requires->css('/blocks/ases/style/aaspect.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/buttons.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/jqueryui.css', true);

$PAGE->requires->css('/blocks/ases/style/monitorias.css', true);

$PAGE->requires->js_call_amd('block_ases/monitorias_academicas','init');

$monitor_id = "";
$data->es_profesional = true;
$es_monitor = es_monitor($id_current_user);
if($es_monitor){ 
    $monitor_id = $id_current_user;
    $data->nombre_monitor = $USER->lastname." ".$USER->firstname;
    $data->es_profesional = false;
}

$monitorias = get_tabla_monitorias($course_id, $block_id, $monitor_id);
$params = new stdClass();
$params->table = $monitorias;
$PAGE->requires->js_call_amd('block_ases/monitorias_academicas','cargar_monitorias_default', $params);
$PAGE->requires->js_call_amd('block_ases/monitorias_academicas','continuar_setup_inicial', array($es_monitor));
$output = $PAGE->get_renderer('block_ases');
$monitorias_page = new \block_ases\output\monitorias_page($data);

echo $output->header();
echo $output->render($monitorias_page);
echo $output->footer();