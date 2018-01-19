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
 * Estrategia ASES
 *
 * @author     Juan Pablo Moreno Muñoz
 * @package    block_ases
 * @copyright  2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');

/**
 * Function that returns the resolution id given the number of the resolution
 * 
 * @see get_resolution_by_number($num_resolution)
 * @param $num_resolution -> number of the resolution to be found
 * @return Integer
 */
function get_resolution_by_number($num_resolution){

    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_res_icetex} WHERE codigo_resolucion = '$num_resolution'";
    $resolution_id = $DB->get_record_sql($sql_query)->id;

    return $resolution_id;
}

/**
 * Function that returns the id of an student given its identification
 * 
 * @see get_student_by_identification($identification)
 * @param $identification -> student's identification
 * @return Integer
 */
function get_student_by_identification($identification){

    global $DB;


    $sql_query = "SELECT id FROM {talentospilos_usuario} WHERE num_doc_ini = '$identification' OR num_doc = '$identification'";
    $student_id = $DB->get_record_sql($sql_query)->id;

    return $student_id;
}

//print_r(get_student_by_identification('97040114746'));


/**
 * Function that registers a new resolution given the number of the resolution, the date and the total amount
 * 
 * @see create_resolution($num_resolution, $date, $total_amount)
 * @param $num_resolution -> number of the new resolution
 * @param $date -> date of the new resolution
 * @param $total_amount -> total amount of money transfered
 * @return Integer
 */
function create_resolution($num_resolution, $date, $total_amount){

    global $DB;

    $newResolution = new stdClass();
    $newResolution->codigo_resolucion = $num_resolution;
    $newResolution->fecha_resolucion = strtotime($date);
    $newResolution->monto_total = $total_amount;

    $insert = $DB->insert_record('talentospilos_res_icetex', $newResolution, true);

    return $insert;

}

//print_r(create_resolution("0000000000", strtotime("2018-01-01"), 1000000));


function create_historic_icetex(){

    global $DB;

    $newHistoric = new stdClass();

}