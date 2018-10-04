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
 * Creación de usuarios ASES
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');
require_once('../managers/validate_profile_action.php');

require_once(__DIR__ . '/../classes/mdl_forms/ases_user_form.php');

$output = $PAGE->get_renderer('block_ases');

$add_ases_user_form = new ases_user_form();

echo $output->header();

if ($add_ases_user_form->is_validated()) {
    $ases_user = $add_ases_user_form->get_ases_user();
    $ases_user->save();
} else {
}
$add_ases_user_form->display();
echo $output->footer();


?>