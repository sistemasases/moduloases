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
 
    $sql = "SELECT  talentospilos_usuario.num_doc AS num_doc_act, _user.firstname AS name_estudiante, _user.lastname AS lastname_estudiante, talentospilos_usuario.json_detalle AS detalle_disc
                FROM mdl_cohort AS cohort 
                        INNER JOIN mdl_cohort_members AS cohort_members ON cohort_members.cohortid = cohort.id
                            INNER JOIN mdl_user AS _user ON _user.id = cohort_members.userid
                                INNER JOIN mdl_talentospilos_user_extended AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 AND talentospilos_user_extended.id_moodle_user = _user.id
                                     INNER JOIN mdl_talentospilos_usuario AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user AND talentospilos_usuario.json_detalle IS NOT NULL
                            WHERE cohort.idnumber = 'DISC2018B'";
                                
    $results = $DB->get_records_sql($sql);

    foreach ($results as $record) {
        array_push($array_detalles, $record);
    }
   return $array_detalles;

}

/**
 * Function get count users without json_detalle in talentospilos_usuario who are members of DISC2018B cohort
 * @see get_cant_sin_detalle()
 * @return int
 **/

 function get_cant_sin_detalle(){
    global $DB;

    $sql = "SELECT COUNT(*) AS cant FROM (SELECT  talentospilos_usuario.num_doc FROM {cohort} AS cohort 
    INNER JOIN {cohort_members} AS cohort_members ON cohort_members.cohortid = cohort.id
        INNER JOIN {user} AS _user ON _user.id = cohort_members.userid
            INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 AND talentospilos_user_extended.id_moodle_user = _user.id
                INNER JOIN {talentospilos_usuario} AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user AND talentospilos_usuario.json_detalle IS NULL
                    WHERE cohort.idnumber = 'DISC2018B') AS total";

    $result = $DB->get_record_sql($sql);
    return $result->cant;
  
 }

 /**
 * Function get users without json_detalle  who are members of DISC2018B cohort
 * @see get_students_dd()
 * @return array
 **/

function get_students_dd(){
    global $DB;

    $sql = "SELECT  talentospilos_usuario.num_doc , _user.firstname AS firstname_student, _user.lastname AS lastname_student FROM {cohort} AS cohort 
    INNER JOIN {cohort_members} AS cohort_members ON cohort_members.cohortid = cohort.id
        INNER JOIN {user} AS _user ON _user.id = cohort_members.userid
            INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 AND talentospilos_user_extended.id_moodle_user = _user.id
                INNER JOIN {talentospilos_usuario} AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user AND talentospilos_usuario.json_detalle IS NULL
                    WHERE cohort.idnumber = 'DISC2018B'";

    $result = $DB->get_records_sql($sql);
    return $result;
  
 }

 /**
 * Function get count users without economics data  who are members of DISC2018B cohort
 * @see get_cant_sin_economics_data()
 * @return int
 **/

function get_cant_sin_economics_data(){
    global $DB;

    $sql = "SELECT COUNT(*) AS cant FROM (SELECT  talentospilos_usuario.num_doc
    FROM {cohort} AS cohort 
        INNER JOIN {cohort_members} AS cohort_members ON cohort_members.cohortid = cohort.id
            INNER JOIN {user} AS _user ON _user.id = cohort_members.userid
            INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 AND 	talentospilos_user_extended.id_moodle_user = _user.id
                INNER JOIN {talentospilos_usuario} AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user 
                            WHERE cohort.idnumber = 'DISC2018B' 
    EXCEPT

    SELECT  talentospilos_usuario.num_doc
    FROM {cohort} AS cohort 
        INNER JOIN {cohort_members} AS cohort_members ON cohort_members.cohortid = cohort.id
            INNER JOIN {user} AS _user ON _user.id = cohort_members.userid
            INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 AND 	talentospilos_user_extended.id_moodle_user = _user.id
                INNER JOIN {talentospilos_usuario} AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user 
                    INNER JOIN {talentospilos_economics_data} AS talentospilos_economics_data ON talentospilos_economics_data.id_ases_user =	talentospilos_usuario.id
                
            WHERE cohort.idnumber = 'DISC2018B') AS total";

    $result = $DB->get_record_sql($sql);
    return $result->cant;
  
 }

 /**
 * Function get users without economics data who are members of DISC2018B cohort
 * @see get_students_ed()
 * @return array
 **/

function get_students_ed(){
    global $DB;

    $sql = "SELECT  talentospilos_usuario.num_doc, _user.firstname AS firstname_student, _user.lastname AS lastname_student
    FROM {cohort} AS cohort 
        INNER JOIN {cohort_members} AS cohort_members ON cohort_members.cohortid = cohort.id
            INNER JOIN {user} AS _user ON _user.id = cohort_members.userid
            INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 AND 	talentospilos_user_extended.id_moodle_user = _user.id
                INNER JOIN {talentospilos_usuario} AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user 
                            WHERE cohort.idnumber = 'DISC2018B' 
    EXCEPT

    SELECT  talentospilos_usuario.num_doc, _user.firstname AS firstname_student, _user.lastname AS lastname_student
    FROM {cohort} AS cohort 
        INNER JOIN {cohort_members} AS cohort_members ON cohort_members.cohortid = cohort.id
            INNER JOIN {user} AS _user ON _user.id = cohort_members.userid
            INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 AND talentospilos_user_extended.id_moodle_user = _user.id
                INNER JOIN {talentospilos_usuario} AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user 
                    INNER JOIN {talentospilos_economics_data} AS talentospilos_economics_data ON talentospilos_economics_data.id_ases_user = talentospilos_usuario.id
                
            WHERE cohort.idnumber = 'DISC2018B' ";

    $result = $DB->get_records_sql($sql);
    return $result;
  
 }

/**
 * Function get count users without health data  who are members of DISC2018B cohort
 * @see get_cant_sin_health_data()
 * @return int
 **/

function get_cant_sin_health_data(){
    global $DB;

    $sql = "SELECT COUNT(*) AS cant FROM (SELECT  talentospilos_usuario.num_doc
    FROM {cohort} AS cohort 
        INNER JOIN {cohort_members} AS cohort_members ON cohort_members.cohortid = cohort.id
            INNER JOIN {user} AS _user ON _user.id = cohort_members.userid
            INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 AND	talentospilos_user_extended.id_moodle_user = _user.id
                INNER JOIN {talentospilos_usuario} AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user 
                            WHERE cohort.idnumber = 'DISC2018B' 
    EXCEPT

    SELECT  talentospilos_usuario.num_doc
    FROM {cohort} AS cohort 
        INNER JOIN {cohort_members} AS cohort_members ON cohort_members.cohortid = cohort.id
            INNER JOIN {user} AS _user ON _user.id = cohort_members.userid
            INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 AND 	talentospilos_user_extended.id_moodle_user = _user.id
                INNER JOIN {talentospilos_usuario} AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user 
                    INNER JOIN {talentospilos_health_data} AS talentospilos_health_data ON talentospilos_health_data.id_ases_user = 	talentospilos_usuario.id
                
            WHERE cohort.idnumber = 'DISC2018B') AS total";

    $result = $DB->get_record_sql($sql);
    return $result->cant;
  
 }

 /**
 * Function get users without health data who are members of DISC2018B cohort
 * @see get_students_hd()
 * @return array
 **/

function get_students_hd(){
    global $DB;

    $sql = "SELECT  talentospilos_usuario.num_doc, _user.firstname AS firstname_student, _user.lastname AS lastname_student
    FROM {cohort} AS cohort 
        INNER JOIN {cohort_members} AS cohort_members ON cohort_members.cohortid = cohort.id
            INNER JOIN {user} AS _user ON _user.id = cohort_members.userid
            INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 AND 		talentospilos_user_extended.id_moodle_user = _user.id
                INNER JOIN {talentospilos_usuario} AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user 
                            WHERE cohort.idnumber = 'DISC2018B' 
    EXCEPT

    SELECT  talentospilos_usuario.num_doc, _user.firstname AS firstname_student, _user.lastname AS lastname_student
    FROM {cohort} AS cohort 
        INNER JOIN {cohort_members} AS cohort_members ON cohort_members.cohortid = cohort.id
            INNER JOIN {user} AS _user ON _user.id = cohort_members.userid
            INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 AND 	talentospilos_user_extended.id_moodle_user = _user.id
                INNER JOIN {talentospilos_usuario} AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user 
                    INNER JOIN {talentospilos_health_data} AS talentospilos_health_data ON talentospilos_health_data.id_ases_user = 	talentospilos_usuario.id
                
            WHERE cohort.idnumber = 'DISC2018B' ";

    $result = $DB->get_records_sql($sql);
    return $result;
  
 }

 /**
 * Function that load discapacity data switch num_doc 
 * @see get_detalle_discapacity($id_form)
 * @param $id_form---> num_doc user
 * @return string
 **/

function get_detalle_discapacity($id_form){
    global $DB;
				
    $sql = "SELECT talentospilos_usuario.json_detalle FROM {talentospilos_usuario} AS talentospilos_usuario WHERE talentospilos_usuario.num_doc = '$id_form'  ";

    $results = $DB->get_record_sql($sql);
    return $results->json_detalle;
}


 /**
 * Function that load discapacity data switch num_doc 
 * @see get_detalle_discapacity($id_form)
 * @param $id_form---> num_doc user
 * @return string
 **/

function get_data_to_grapich(){

    global $DB;
    $array_detalles = array();
    $array_records  = array();
 
    $sql = "SELECT  talentospilos_usuario.num_doc AS num_doc_act,  talentospilos_usuario.json_detalle AS detalle_disc FROM {cohort} AS cohort 
            INNER JOIN {cohort_members} AS cohort_members ON cohort_members.cohortid = cohort.id
                INNER JOIN {user} AS _user ON _user.id = cohort_members.userid
                    INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.tracking_status = 1 
                                                                        AND talentospilos_user_extended.id_moodle_user = _user.id
                         INNER JOIN {talentospilos_usuario} AS talentospilos_usuario ON talentospilos_usuario.id =  talentospilos_user_extended.id_ases_user 
                                                                        AND talentospilos_usuario.json_detalle IS NOT NULL
                WHERE cohort.idnumber = 'DISC2018B'";
                                
    $results = $DB->get_records_sql($sql);


    $cognitiva = 0;
    $psicosocial = 0;    
    $fisica = 0;
    $sensorial = 0;   
    $multiple = 0;
    $otra = 0;   
    

    foreach ($results as $record) {
        $tipo_discapacidad_register = json_decode($record->detalle_disc);
        $tipo                       = $tipo_discapacidad_register->tipo_discapacidad->tipo_discapacidad;
        switch($tipo){
        case "Cognitiva": 
        $cognitiva ++;
        break;    
        case "Psicosocial": 
        $psicosocial ++;
        break;   
        case "Física": 
        $fisica ++;
        break;   
        case "Sensorial": 
        $sensorial ++;
        break;   
        case "Múltiple": 
        $multiple ++;
        break;   
        case "Otra": 
        $otra ++;
        break;   
        }
    }
    $cant = count($results);
    array_push($array_detalles, $cognitiva, $psicosocial, $fisica, $sensorial, $multiple, $otra);
    

   return $array_detalles;


}
 

?>