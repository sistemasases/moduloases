<?php

require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/../classes/output/renderer.php');
require_once (__DIR__ . '/../classes/output/massive_upload_component.php');
require_once (__DIR__ . '/../managers/menu_options.php');
require_once (__DIR__ . '/../managers/cohort/cohort_lib.php');


$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);


$PAGE->set_title('Upload file');
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/buttons.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/massive_upload.css');

$data_for_amd = array();
$data_for_amd['id_curso'] = $courseid;

$PAGE->requires->js_call_amd('block_ases/massive_upload', 'init', array('data'=>$data_for_amd));
$output = $PAGE->get_renderer('block_ases');

echo $output->header();
\core\notification::info('Tip: pase con el mouse sobre los campos que tengan error para ver el detalle');
\core\notification::info('Tip: Si el archivo tiene errores, puede corregirlos y resubir el archivo, podra enviarlo de nuevo');
$menu_option = create_menu_options($USER->id, $blockid, $courseid);


$data = array();
$data['menu'] = $menu_option;
$data['cohorts_select'] = \cohort_lib\get_html_cohorts_select($blockid, false, 'cohorts', 'cohorts');

$massive_upload_component = new \block_ases\output\massive_upload_component($data);
echo $output->render($massive_upload_component);
echo $output->footer();
