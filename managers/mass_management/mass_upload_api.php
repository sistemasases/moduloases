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
 * Common API endpoint for the massive data upload process
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../classes/ExternInfo/EstadoAsesEIManager.php');
require_once(__DIR__ . '/../../classes/ExternInfo/condicion_exepcion/CondicionExcepcionEIManager.php');
require_once(__DIR__ . '/../../classes/ExternInfo/historial_academico/HistorialAcademicoEIManager.php');

require_once(__DIR__ . '/../../classes/API/BaseAPI.php');
require_once(__DIR__ . '/../../managers/mass_management/endpoints.php');
$api = new BaseAPI();
$ases_user_endpoint = \mass_management\endpoints\UPLOAD_ASES_USERS;
$cond_exepcion_endpoint = \mass_management\endpoints\UPDATE_COND_EXEPCION;
$history_academic = \mass_management\endpoints\UPDATE_ACADEMIC_HISTORY;
$api->post("$ases_user_endpoint/:cohort_id/:instance_id/:save", function($args, $data) {
    $save = $data['save'] === 'true';
    //sprint_r($data['save']);
    $estado_ases_csv_manager = new EstadoAsesEIManager($data['cohort_id'], $data['instance_id'], $save);
    $estado_ases_csv_manager->execute();
});
$api->post("$cond_exepcion_endpoint/:cohort_id/:instance_id/:save", function($args, $data) {
    $cond_excepcion_manager = new CondicionExcepcionEIManager();
    $cond_excepcion_manager->execute();
});
$api->post("$history_academic/:cohort_id/:instance_id/:save", function($args, $data) {
    $history_academ_manager = new HistorialAcademicoEIManager();
    $history_academ_manager->execute();
});
$api->run();

?>