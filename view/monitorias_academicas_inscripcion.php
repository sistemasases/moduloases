<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

//require_once '../managers/instance_management/instance_lib.php';
//require_once('../managers/menu_options.php');
require_once '../managers/lib/lib.php';
//require_once('../managers/validate_profile_action.php');
require_once('../managers/asistencia_monitorias/asistencia_monitorias_lib.php');

include '../lib.php';
include "../classes/output/monitorias_academicas_detalle_page.php";
include "../classes/output/renderer.php";

global $PAGE;


//$course_id = required_param('courseid', PARAM_INT);
//$block_id = required_param('instanceid', PARAM_INT);
//$monitoria_id = required_param('monitoriaid', PARAM_INT);



//if (!consult_instance($block_id)) {
//    header("Location: instanceconfiguration.php?courseid=$course_id&instanceid=$block_id");
//}

//$contextcourse = context_course::instance($course_id);
//$contextblock = context_block::instance($block_id);
//$id_current_user = $USER->id;

$url = new moodle_url("/blocks/ases/view/monitorias_academicas_inscripcion.php");
                        //'monitoriaid' => $monitoria_id));

// courseid=34200&instanceid=525557


//$coursenode = $PAGE->navigation->find($course_id, navigation_node::TYPE_COURSE);
//$blocknode = navigation_node::create('Monitoría',$url, null, 'block', $block_id);
//$coursenode->add_node($blocknode);

$data = new stdClass();
//$menu_option = create_menu_options($id_current_user, $block_id, $course_id);
//$datos_monitoria = get_monitoria_by_id($monitoria_id);
//$data->menu = $menu_option;
//$data->materia = $datos_monitoria->materia;
//$data->monitor = $datos_monitoria->lastname_mon . " " . $datos_monitoria->firstname_mon;
//$data->horario = $datos_monitoria->dia . ", " . $datos_monitoria->hora;
//$data->materia_id = $datos_monitoria->materia_id;
//$data->monitor_id = $datos_monitoria->monitor_id;
//$data->dia = $datos_monitoria->dia_numero;
//$data->hora = $datos_monitoria->hora;
//$page_title = "Monitoría ".$datos_monitoria->materia." por ". $datos_monitoria->lastname_mon . " " . $datos_monitoria->firstname_mon;

$PAGE->set_url($url);
$PAGE->set_title("Monitorías académicas: Inscripción");
$PAGE->set_heading("Monitorías académicas: Inscripción");

$PAGE->set_context(context_system::instance());
require_login();
if(isguestuser()){
    redirect(new moodle_url('/login/index.php'), "Por favor ingrese con una cuenta válida para inscribirse en monitorías académicas.", 1);
}


$PAGE->requires->css('/blocks/ases/style/aaspect.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/buttons.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/jqueryui.css', true);

$PAGE->requires->css('/blocks/ases/style/monitorias_academicas.css', true);

//$PAGE->requires->js_call_amd('block_ases/monitorias_academicas_detalle','init');

//$params = new stdClass();
// inicialmente mostrar sesiones programadas desde hoy hasta infinito
//$params->sesiones = get_tabla_sesiones($monitoria_id, (new DateTime())->format("Ymd"), 99999999);
//$params->monitoria = $datos_monitoria;
//$PAGE->requires->js_call_amd('block_ases/monitorias_academicas_detalle','construir_tabla', $params);
//$PAGE->requires->js_call_amd('block_ases/monitorias_academicas_detalle','init_despues_de_tabla');

$output = $PAGE->get_renderer('block_ases');

// datos de contacto
$data->correo = $USER->email;
$numero_celular = $DB->get_record('user', array('id' => $USER->id), 'phone1')->phone1;
$USER->phone1 = $numero_celular;
$data->celular = $numero_celular ? $numero_celular : "SIN REGISTRAR";

// monitorias para inscribirse
$monitorias = get_monitorias_para_inscribirse();
$data->lunes = array_values(array_filter($monitorias,function($m){return $m->dia == 0;}));
$data->martes = array_values(array_filter($monitorias,function($m){return $m->dia == 1;}));
$data->miercoles = array_values(array_filter($monitorias,function($m){return $m->dia == 2;}));
$data->jueves = array_values(array_filter($monitorias,function($m){return $m->dia == 3;}));
$data->viernes = array_values(array_filter($monitorias,function($m){return $m->dia == 4;}));
$data->sabado = array_values(array_filter($monitorias,function($m){return $m->dia == 5;}));

// sesiones en las que el usuario está inscrito
$data->inscritas = array_values(cargar_inscripciones_de_usuario($USER->id));
$data->hay_inscritas = count($data->inscritas) > 0;

// asignaturas que el usuario tiene matriculadas
$data->asignaturas_matriculadas = array_values(get_asignaturas_matriculadas_por_usuario($USER->id));
$data->userid= $USER->id;

$PAGE->requires->js_call_amd('block_ases/monitorias_academicas_inscripcion','set_user', array($USER->id, $USER->email, $USER->phone1));
$PAGE->requires->js_call_amd('block_ases/monitorias_academicas_inscripcion','init');
$monitoria_page = new \block_ases\output\monitorias_academicas_inscripcion_page($data);

echo $output->header();
echo $output->render($monitoria_page);
echo $output->footer();