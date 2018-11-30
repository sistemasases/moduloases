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
 * @author     Juan Pablo Castro
 * @package    block_ases
 * @copyright  2018 Juan Pablo Castro <juan.castro.vasquez@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';


/**
 * Function load report of json_detalle in talentospilos_usuario
 * @see get_list_discapacity_reports()
 * @return array
 **/

function get_list_discapacity_reports(){
    global $DB;
    $array_detalles = array();
 
    $sql = "SELECT  usuario.num_doc AS num_doc_act, _user.firstname AS name_estudiante, _user.lastname AS lastname_estudiante, usuario.json_detalle AS detalle_disc
	                FROM {talentospilos_usuario} AS usuario
		                INNER JOIN   {talentospilos_user_extended}  AS user_extended ON user_extended.id_ases_user = usuario.id
			                INNER JOIN {user} AS _user ON _user.id = user_extended.id_moodle_user
                                WHERE usuario.json_detalle IS NOT NULL;";
                                
    $results = $DB->get_records_sql($sql);

    foreach ($results as $record) {
        array_push($array_detalles, $record);
    }
   return $array_detalles;

}

?>