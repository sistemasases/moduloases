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
 * Create program view
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../../config.php');
require_once('../managers/validate_profile_action.php');

require_once(__DIR__ . '/../classes/mdl_forms/program_form.php');


$pagetitle = 'Creacion de programas ASES';
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);
$actions = authenticate_user_view($USER->id, $blockid);
if (!isset($actions->create_program)) {
    redirect(new moodle_url('/'), "No tienes permiso para acceder a la creación de programas ASES",1);
}
$url = new moodle_url("/blocks/ases/view/create_program.php",array('courseid' => $courseid, 'instanceid' => $blockid));
$PAGE->set_title($pagetitle);

$output = $PAGE->get_renderer('block_ases');

$program_form = new program_form($url);

echo $output->header();

if ($program_form->is_submitted() && $program_form->is_validated()) {

    $program = $program_form->get_program();
    if ($program->valid()) {
        $program->save();
        \core\notification::success("Se ha almacenado correctamente el programa '$program->nombre'");
    } else {

        /* @var AsesError $error*/
        foreach($program->get_errors() as $error) {
            \core\notification::error($error->message);
        }
    }
} else {
    $errors = $program_form->get_errors();

    if($errors[BaseDAO::GENERIC_ERRORS_FIELD]) {
        \core\notification::error($errors[BaseDAO::GENERIC_ERRORS_FIELD]);
    }
}
$program_form->display();
echo $output->footer();


?>