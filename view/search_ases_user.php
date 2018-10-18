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
 * Create program view
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once __DIR__ . '/../../../config.php';

require_once(__DIR__ . '/../classes/mdl_forms/search_ases_user_form.php');
require_once(__DIR__ . '/../classes/AsesUser.php');
include "../classes/output/renderer.php";
include "../classes/output/progress_bar_component.php";
include '../managers/students_finalgrade_report/students_finalgrade_report_lib.php';

$pagetitle = 'Busqueda de usuarios ASES';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$next_url= optional_param('next_url', null, PARAM_PATH);

require_login($courseid, false);

$url = new moodle_url("/blocks/ases/view/search_ases_user.php",
    array(
        'courseid' => $courseid,
        'instanceid' => $blockid,
        'next_url'=>$next_url)
);
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);

$output = $PAGE->get_renderer('block_ases');

$search_ases_user_form = new search_ases_user_form($url);

echo $output->header();

if ($search_ases_user_form->is_submitted() && $search_ases_user_form->is_validated()) {
    $search_form_data = $search_ases_user_form->get_data();
    $user_exists = AsesUser::exists(array(
        AsesUser::NUMERO_DOCUMENTO=>$search_form_data->num_doc
    ));
    if( $user_exists ) {
        $ases_users = AsesUser::get_by(array(
            AsesUser::NUMERO_DOCUMENTO=>$search_form_data->num_doc
        ));
        //print_r($ases_users);
    } else {
        \core\notification::info('No existe ningun usuario que cumpla con las condiciones dadas');
    }

} else {

}
//$c_query = _select_instancias_cohorte($blockid)->compile();

//print_r($DB->get_records_sql($c_query->sql(), $c_query->params()));
print _select_ids_cursos_ases($blockid)->compile()->sql();

$c_query = _select_ids_cursos_ases($blockid)->compile();

print_r($DB->get_records_sql($c_query->sql(), $c_query->params()));

$search_ases_user_form->display();

echo $output->footer();

?>