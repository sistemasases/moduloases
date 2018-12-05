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
require_once $CFG->dirroot.'/blocks/ases/managers/periods_management/periods_lib.php'; 

/**
 * Function that returns the resolution id given the number of the resolution
 * 
 * @see get_resolution_id_by_number($num_resolution)
 * @param $num_resolution -> number of the resolution to be found
 * @return integer|boolean
 */
function get_resolution_id_by_number($num_resolution){

    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_res_icetex} WHERE codigo_resolucion = '$num_resolution'";
    $result = $DB->get_record_sql($sql_query);

    if($result){

        $resolution_id = $result->id;

        return $resolution_id;

    }else{

        return false;
    }
}

/**
 * Function that returns the ases id of an student given its identification
 * 
 * @see get_student_id_by_identification($identification)
 * @param $identification -> student's identification
 * @return integer|boolean 
 */
function get_student_id_by_identification($identification){

    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_usuario} WHERE num_doc_ini = '$identification' OR num_doc = '$identification'";
    $result = $DB->get_record_sql($sql_query);

    if($result){

        $student_id = $result->id;

        return $student_id;

    }else{

        return false;
    }    
}

//print_r(get_student_by_identification('97040114746'));


/**
 * Function that registers a new resolution given the number of the resolution, the date and the total amount
 * 
 * @see create_resolution($num_resolution, $date, $total_amount)
 * @param $num_resolution -> number of the new resolution
 * @param $date -> date of the new resolution
 * @param $total_amount -> total amount of money transfered
 * @return integer
 */
function create_resolution($num_resolution, $semester_id, $date, $total_amount){

    global $DB;

    $result = validate_resolution_register($num_resolution);

    if(!$result) {
        $newResolution = new stdClass();
        $newResolution->codigo_resolucion = $num_resolution;
        $newResolution->id_semestre = $semester_id;
        $newResolution->fecha_resolucion = strtotime($date);
        $newResolution->monto_total = $total_amount;

        $insert = $DB->insert_record('talentospilos_res_icetex', $newResolution, true);

        return $insert;

    }else{        
        return $result;
    }   

}

//print_r(create_resolution("0000000000", strtotime("2018-01-01"), 1000000));

function validate_resolution_register($num_resolution){
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_res_icetex} WHERE codigo_resolucion = '$num_resolution'";

    $id_resolution = $DB->get_record_sql($sql_query)->id;

    return $id_resolution;
}

//print_r(validate_resolution_register('10618482'));

/**
 * Function that creates a historic register given the student identification, the number of the resolution, the name of the semester and the amount of money per student
 * 
 * @see create_historic_icetex($student_identification, $num_resolution, $name_semester, $amount)
 * @param $student_identification -> the student's identification number (not academic id)
 * @param $num_resolution -> number of the resolution 
 * @param $name_semester -> name of the semester 
 * @param $amount -> amount of money per student
 * @return integer
 */
function create_historic_icetex($student_id, $program_id, $resolution_id, $amount){

    global $DB;

    $newHistoric = new stdClass();
    $newHistoric->id_estudiante = $student_id;
    $newHistoric->id_resolucion = $resolution_id;
    $newHistoric->id_programa = $program_id;
    $newHistoric->monto_estudiante = $amount;

    $insert = $DB->insert_record('talentospilos_res_estudiante', $newHistoric, true);

    return $insert;
}

/**
 * Function that updates the field 'nota_credito' of a resolution register in the database
 * 
 * @see update_resolution_credit_note($id_resolution, $credit_note)
 * @param $id_resolution -> id of a resolution
 * @param $credit_note -> value that represents the credi note of a resolution
 * @return boolean
 */
function update_resolution_credit_note($id_resolution, $credit_note){
    global $DB;

    $upd_cred_note = false;
    
    $object_resolution = new stdClass();
    $object_resolution->id = $id_resolution;
    $object_resolution->nota_credito = $credit_note;

    $update = $DB->update_record('talentospilos_res_icetex', $object_resolution);

    if($update){
        $upd_cred_note = true;
    }else{
        $upd_cred_note = false;
    }

    return $upd_cred_note;
}

//print_r(update_resolution_credit_note(10, 'Hey'));

function update_resolution_amount_students($id_resolution, $amount_students){
    global $DB;

    $upd_am_stud = false;
    
    $object_resolution = new stdClass();
    $object_resolution->id = $id_resolution;
    $object_resolution->cantidad_estudiantes = $amount_students;

    $update = $DB->update_record('talentospilos_res_icetex', $object_resolution);

    if($update){
        $upd_am_stud = true;
    }else{
        $upd_am_stud = false;
    }

    return $upd_am_stud;
}

/**
 * Returns the id of the icetex_status given its name
 * 
 * @see get_id_icetex_status($icetex_status)
 * @param $icetex_status -> name of the icetex status
 * @return integer||boolean
 */
function get_id_icetex_status($icetex_status){
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_estados_icetex} WHERE nombre = '$icetex_status'";

    $id_icetex_status = $DB->get_record_sql($sql_query);

    if(!$id_icetex_status){
        return false;
    }else{
        return $id_icetex_status->id;
    }
}

/**
 * validate a register in table talentospilos_hist_est_ice
 *
 * @see validate_historic_status_register($id_student, $id_semester, $icetex_status)
 * @param $id_student --> id from table talentospilos_usuario
 * @param $id_semester --> id from table talentospilos_semestre
 * @param $icetex_status --> name of the icetex status
 * @return Object|boolean
 */
function validate_historic_status_register($id_student, $id_semester, $id_icetex_status)
{
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_hist_est_ice} WHERE id_estudiante = $id_student AND id_semestre = $id_semester 
                                                                    AND id_estado_icetex = $id_icetex_status";
    return $DB->get_record_sql($sql_query);

}

/**
 * Updates or inserts a register in the talentospilos_hist_est_ice table
 * 
 * @see update_historic_icetex_status($student_id, $semester_id, $icetex_status)
 * @param $student_id -> id of the student
 * @param $semester_id -> id of the semester
 * @param $icetex_status -> name of the icetex status
 * @return integer||boolean
 */
function update_historic_icetex_status($student_id, $semester_id, $id_icetex_status){
    global $DB;

    //validate existence
    $result = validate_historic_status_register($student_id, $semester_id, $id_icetex_status);
    $object_historic = new StdClass;

    if(!$result){
        //INSERTION

        $object_historic->id_estudiante = $student_id;
        $object_historic->id_semestre = $semester_id;
        $object_historic->id_estado_icetex = $id_icetex_status;

        $insert = $DB->insert_record('talentospilos_hist_est_ice', $object_historic, true);

        if ($insert) {
            return $insert;
        } else {
            return false;
        }

    }else{
        //UPDATE
        $id_historic = $result->id;

        $object_historic->id = $id_historic;
        $object_historic->id_estudiante = $student_id;
        $object_historic->id_semestre = $semester_id;
        $object_historic->id_estado_icetex = $id_icetex_status;

        $update = $DB->update_record('talentospilos_hist_est_ice', $object_historic);

        if ($update) {
            return $id_historic;
        } else {
            return false;
        }
    }    
}