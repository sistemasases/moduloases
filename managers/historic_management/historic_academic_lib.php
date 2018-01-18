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
 * @param $username --> student id associated to moodle user
 * @return array 
 */

function get_ases_id_by_code($code)
{
    global $DB;

    $sql_query = "SELECT MAX(id) as id_moodle FROM {user} WHERE username LIKE '" . $code . "%';";

    $id_moodle = $DB->get_record_sql($sql_query)->id_moodle;

    $sql_query = "SELECT id_ases_user as id FROM {talentospilos_user_extended} WHERE id_moodle_user =" . $id_ases;
    
    $ases_id = $DB->get_record_sql($sql_query);
    
    if(!$ases_id){
        return false;
    }else{
        return $ases_id->id;
    }
}


