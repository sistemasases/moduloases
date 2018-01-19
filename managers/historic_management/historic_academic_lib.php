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
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require_once dirname(__FILE__) . '/../../../../config.php';

 /**
 * Gets ASES id_user given student code associated to moodle username
 * 
 * @see get_ases_id_by_code($code)
 * @param $code --> code that represent username of moodle user
 * @return $id or false 
 */

function get_ases_id_by_code($code)
{
    global $DB;

    $sql_query = "SELECT MAX(id) as id_moodle FROM {user} WHERE username LIKE '" . $code . "%';";

    $id_moodle = $DB->get_record_sql($sql_query)->id_moodle;

    $sql_query = "SELECT id_ases_user as id FROM {talentospilos_user_extended} WHERE id_moodle_user =" . $id_moodle;
    
    $ases_user = $DB->get_record_sql($sql_query);
    
    if(!$ases_user){
        return false;
    }else{
        return $ases_user->id;
    }
}

 /**
 * Gets id of program given program code 
 * 
 * @see get_id_program($code)
 * @param $username --> student id associated to moodle user
 * @return $id or false 
 */

function get_id_program($code)
{
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_programa} WHERE cod_univalle = " . $code . " LIMIT 1;";

    $program = $DB->get_record_sql($sql_query);
    
    if(!$program){
        return false;
    }else{
        return $program->id;
    }
}

 /**
 * Gets id of semestre given semestre name 
 * 
 * @see get_id_semestre($name)
 * @param $name --> name of semestre
 * @return $id or false 
 */

function get_id_semestre($name)
{
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_semestre} WHERE nombre = '" . $name . "';";

    $semestre = $DB->get_record_sql($sql_query);
    
    if(!$semestre){
        return false;
    }else{
        return $semestre->id;
    }
}

/**
 * Creates an associative array given a header from a CSV file
 * 
 * @see getAssociativeTitles ($titlesPos)
 * @param $titlesPos --> header from CSV
 * @return array 
 */
function getAssociativeArray($array){
    
    $associativeArray = array();

    foreach ($array as $key => $value) {
        $associativeArray[$value] = $key; 
    }

    return $associativeArray;
}


