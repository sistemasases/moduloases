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
 * ASES
 *
 * @author     Jeison Cardona Gómez.
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once __DIR__ . '/../../../config.php';
require_once $CFG->libdir . '/adminlib.php';

require_once('../managers/lib/lib.php');
require_once('../managers/instance_management/instance_lib.php');
require_once('../managers/menu_options.php');
include_once("../managers/dphpforms/dphpforms_reverse_filter.php");
include_once("../managers/dphpforms/dphpforms_form_updater.php");
require_once(__DIR__.'/../managers/cohort/cohort_lib.php');
include('../lib.php');


global $PAGE;
global $USER;

include "../classes/output/dphpforms_reports_page.php";
include "../classes/output/renderer.php";

$title = "Generador de reportes";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

$record = new stdClass();

require_login($courseid, false);

if (!consult_instance($blockid)) {
    header("Location: instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}
$cohorts_select = \cohort_lib\get_html_cohorts_select($blockid);
$contextcourse = context_course::instance($courseid);
$contextblock = context_block::instance($blockid);

$record->cohorts_checks = $cohorts_select;

$url = new moodle_url("/blocks/ases/view/dphpforms_form_builder.php", array('courseid' => $courseid, 'instanceid' => $blockid));

$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);

$rol = lib_get_rol_name_ases($USER->id, $blockid);

// dphpforms_form_updater.php->get_alias()
$record->alias_preguntas_globales = array_values(get_alias());
$preguntas_form1 = array_values(get_preguntas_form("seguimiento_pares"));
$preguntas_form2 = array_values(get_preguntas_form("inasistencia"));

$record->dphpforms_instance_id = $blockid;

$preguntas_form_short1 = array();
foreach( $preguntas_form1 as &$pregunta1 ){
    array_push( $preguntas_form_short1, array(
            'id' => $pregunta1->id,
            'enunciado' => $pregunta1->enunciado,
            'local_alias' => json_decode($pregunta1->atributos_campo)->local_alias
        ) 
    );
}

$preguntas_form_short2 = array();
foreach( $preguntas_form2 as &$pregunta2 ){
    array_push( $preguntas_form_short2, array(
            'id' => $pregunta2->id,
            'enunciado' => $pregunta2->enunciado,
            'local_alias' => json_decode($pregunta2->atributos_campo)->local_alias
        ) 
    );
}

$record->preguntas1 = json_encode( $preguntas_form_short1 );
$record->preguntas2 = json_encode( $preguntas_form_short2 );
$menu_option = create_menu_options($USER->id, $blockid, $courseid);
$record->menu = $menu_option;

$PAGE->set_context($contextcourse);
$PAGE->set_context($contextblock);
$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->requires->css('/blocks/ases/style/base_ases.css', true);
$PAGE->requires->css('/blocks/ases/style/jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/style/sugerenciaspilos.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/c3.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/buttons.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/style/dphpforms_reports.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);

$PAGE->requires->js_call_amd('block_ases/dphpforms_reports', 'init');

$PAGE->requires->js_call_amd('block_ases/ases_incident_system', 'init');

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
$dphpforms_reports_page = new \block_ases\output\dphpforms_reports_page($record);
echo $output->render($dphpforms_reports_page);
echo $output->footer();
