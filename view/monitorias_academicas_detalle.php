<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

require_once '../managers/instance_management/instance_lib.php';
require_once('../managers/menu_options.php');
require_once '../managers/lib/lib.php';
require_once('../managers/validate_profile_action.php');
require_once('../managers/asistencia_monitorias/asistencia_monitorias_lib.php');

include '../lib.php';
include "../classes/output/monitorias_academicas_detalle_page.php";
include "../classes/output/renderer.php";

global $PAGE;


$course_id = required_param('courseid', PARAM_INT);
$block_id = required_param('instanceid', PARAM_INT);
$monitoria_id = required_param('monitoriaid', PARAM_INT);

require_login($course_id, false);

if (!consult_instance($block_id)) {
    header("Location: instanceconfiguration.php?courseid=$course_id&instanceid=$block_id");
}

$contextcourse = context_course::instance($course_id);
$contextblock = context_block::instance($block_id);
$id_current_user = $USER->id;

$url = new moodle_url("/blocks/ases/view/monitorias_academicas_detalle.php", 
                        array('courseid' => $course_id, 'instanceid' => $block_id, 'monitoriaid' => $monitoria_id));



$coursenode = $PAGE->navigation->find($course_id, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Monitoría',$url, null, 'block', $block_id);
$coursenode->add_node($blocknode);

$data = new stdClass();
$menu_option = create_menu_options($id_current_user, $block_id, $course_id);
$datos_monitoria = get_monitoria_by_id($monitoria_id);
$data->menu = $menu_option;
$data->materia = $datos_monitoria->materia;
$data->monitor = $datos_monitoria->lastname_mon . " " . $datos_monitoria->firstname_mon;
$data->horario = $datos_monitoria->dia . ", " . $datos_monitoria->hora;
$data->materia_id = $datos_monitoria->materia_id;
$data->monitor_id = $datos_monitoria->monitor_id;
$data->dia = $datos_monitoria->dia_numero;
$data->hora = $datos_monitoria->hora;
$page_title = "Monitoría ".$datos_monitoria->materia." por ". $datos_monitoria->lastname_mon . " " . $datos_monitoria->firstname_mon;

$PAGE->set_url($url);
$PAGE->set_title($page_title);
$PAGE->set_heading($page_title);

$PAGE->requires->css('/blocks/ases/style/aaspect.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/style/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/buttons.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/jqueryui.css', true);

$PAGE->requires->css('/blocks/ases/style/monitorias.css', true);

$PAGE->requires->js_call_amd('block_ases/monitorias_academicas_detalle','init');

$params = new stdClass();
// inicialmente mostrar sesiones programadas desde hoy hasta infinito
$params->sesiones = get_tabla_sesiones($monitoria_id, (new DateTime())->format("Ymd"), 99999999);
$params->monitoria = $datos_monitoria;
$PAGE->requires->js_call_amd('block_ases/monitorias_academicas_detalle','construir_tabla', $params);
$PAGE->requires->js_call_amd('block_ases/monitorias_academicas_detalle','init_despues_de_tabla');
$output = $PAGE->get_renderer('block_ases');
$monitoria_page = new \block_ases\output\monitoria_page($data);

echo $output->header();
echo $output->render($monitoria_page);
echo $output->footer();