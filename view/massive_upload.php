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
 * Common view for the massive data upload process
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/../classes/output/renderer.php');
require_once (__DIR__ . '/../classes/output/massive_upload_component.php');
require_once (__DIR__ . '/../managers/menu_options.php');
require_once (__DIR__ . '/../managers/cohort/cohort_lib.php');
require_once (__DIR__ . '/../managers/mass_management/endpoints.php');


$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$selected_upload = optional_param('selected_upload', null, PARAM_TEXT);
$actions = authenticate_user_view($USER->id, $blockid);
if (!isset($actions->massive_upload)) {
    redirect(new moodle_url('/'), "No tienes permiso para acceder a la carga masiva (versión datatables)",1, \core\output\notification::NOTIFY_INFO);
}

$PAGE->set_title('Carga masiva datatables');
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);

$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/buttons.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/style/massive_upload.css');

$data_for_amd = array();
$data_for_amd['instance_id'] = $blockid;

$PAGE->requires->js_call_amd('block_ases/massive_upload', 'init', array('data'=>$data_for_amd));
$output = $PAGE->get_renderer('block_ases');

echo $output->header();
\core\notification::info('Tip: pase con el mouse sobre los campos que tengan error para ver el detalle');
\core\notification::info('Tip: Si el archivo tiene errores, puede corregirlos y resubir el archivo, podra enviarlo de nuevo');
$menu_option = create_menu_options($USER->id, $blockid, $courseid);



$data = array();
$data['menu'] = $menu_option;
if($selected_upload) {
    $data['selected_upload'] = $selected_upload;
}
$data['cohorts_select'] = \cohort_lib\get_html_cohorts_select($blockid, false, 'cohorts', 'cohorts');
$data['endpoints'] = \mass_management\endpoints\get_options();
$massive_upload_component = new \block_ases\output\massive_upload_component($data);
echo $output->render($massive_upload_component);
echo $output->footer();
