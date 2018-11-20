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
 * Function save json decode in table usuario
 * @see save_detalle_discapacidad(json, id_ases)
 * @param $json  valid JSON 
 * @param $id_ases ID ASES user
 * @return boolean
 **/

function save_detalle_discapacidad($json, $id_ases){
    global $DB;

    $student = new stdClass ();
    // $sql_query = "SELECT * FROM {talentospilos_usuario} WHERE id = $id_ases";
    // $student=  $DB->get_records_sql($sql_query);
    $student->id = $id_ases;
    $student->json_detalle = $json;
    $result = $DB->update_record('talentospilos_usuario', $student);
    // $sql_query = "UPDATE {talentospilos_usuario} SET json_detalle = '$json' WHERE id = $id_ases";
    // $result =  $DB->execute($sql_query);

    // $student = $DB->get_records_sql($sql_query);
    // $student = (object) $student;
    // $student->json_detalle = $json;

    //$result = $DB->update_record('talentospilos_usuario', $student);
        return $result;

}


/**
 * Function return json schema switch id_schema
 * @see get_schema(id_schema)
 * @param $id_schema ID JSON SCHEMA
 * @return Object
 **/
function get_schema($id_schema){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_json_schema} WHERE id = $id_schema";
    return $DB->get_record_sql($sql_query);
}

?>