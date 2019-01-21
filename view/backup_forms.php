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
 *Backup reports
 *
 * @author     Juan Pablo Castro
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Juan Pablo Castro <juan.castro.vasquez@correounivalle.edu.co>
 * @copyright  2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/validate_profile_action.php');
require_once('../managers/menu_options.php');
require_once('../managers/user_management/user_lib.php');
require_once('../managers/dphpforms/dphpforms_dwarehouse_lib.php');
require_once('../managers/instance_management/instance_lib.php');
require_once('../managers/dateValidator.php');
require_once('../managers/permissions_management/permissions_lib.php');


global $PAGE;

include("../classes/output/backup_forms_page.php");
include("../classes/output/renderer.php");
include('../lib.php');

// Variables for setup the page.
$title = "Backup forms";
$pagetitle = $title;

$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
require_login($courseid, false);

// Set up the page.
if (!consult_instance($blockid)) {
    header("Location: instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock = context_block::instance($blockid);
// Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);
$url = new moodle_url("/blocks/ases/view/backup_forms.php", array('courseid' => $courseid, 'instanceid' => $blockid));


// Nav configuration
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Reporte formularios', new moodle_url("/blocks/ases/view/backup_forms.php", array('courseid' => $courseid, 'instanceid' => $blockid)), null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);

//Creates a class with information that'll be send to template
$data = new stdClass;
$actions = authenticate_user_view($USER->id, $blockid);
$data = $actions;
$data->menu = $menu_option;

//Options select table df_alias
$alias= get_df_alias();
$options_df_alias = '';

foreach($alias as $key){
    $options_df_alias .= "<option value='$key->id_pregunta'>$key->alias</option>";
}

$data->options_key_select = $options_df_alias;

$PAGE->requires->css('/blocks/ases/style/base_ases.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/creadorFormulario.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/beautify-json.css', true);
$PAGE->requires->css('/blocks/ases/style/backup_forms.css', true);

$paramReport = new stdClass();
$paramReport->table = $tableReport;
$PAGE->requires->js_call_amd('block_ases/dphpforms_backup_forms', 'init');
$PAGE->requires->js_call_amd('block_ases/dphpforms_form_renderer', 'init');


$output = $PAGE->get_renderer('block_ases');
$index_page = new \block_ases\output\backup_forms_page($data);

echo $output->header();
echo $output->render($index_page);
echo $output->footer();