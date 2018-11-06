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
 * Ases block
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/user_management/user_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/ases_report/asesreport_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/dphpforms/v2/dphpforms_lib.php';
/**
 * Función que renombra para clasificar la función get_professionals_by_instance en otras partes del plugin, el objetivo
 * es dibujar un mapa de seguimiento para saber de donde provienen las funciones
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array 
 */

function pilos_tracking_prof_prac_tracking_count( $username, $semester_id, $instance ){

    global $DB;

    $sql = "SELECT DISTINCT user_rol.id_usuario,user.firstname AS nombre, user.lastname AS apellido, user_rol.id_semestre AS semestre 
    FROM {talentospilos_user_rol} AS user_rol 
    INNER JOIN {user} AS user ON  ( user.id = user_rol.id_usuario) 
    WHERE id_jefe = '$id_profesional' AND id_semestre =$semester";
    return $DB->get_records_sql( $sql );
}


?>
