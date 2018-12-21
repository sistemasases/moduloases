<?php

require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/../classes/output/renderer.php');
require_once (__DIR__ . '/../classes/output/massive_upload_component.php');
$PAGE->set_title('Upload file');
$PAGE->requires->css('/blocks/ases/style/massive_upload.css');
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);

$PAGE->requires->js_call_amd('block_ases/massive_upload', 'init');
$output = $PAGE->get_renderer('block_ases');
echo $output->header();
\core\notification::info('Tip: pase con el mouse sobre los campos que tengan error para ver el detalle');
\core\notification::info('Tip: Si el archivo tiene errores, puede corregirlos y resubir el archivo, podra enviarlo de nuevo');
$course_and_teacher_report = new \block_ases\output\massive_upload_component($data);
echo $output->render($course_and_teacher_report);
echo $output->footer();
