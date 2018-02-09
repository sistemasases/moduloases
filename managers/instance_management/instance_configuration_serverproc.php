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
 * Talentos Pilos
 *
 * @author     Iader E. García 
 * @package    block_ases
 * @copyright  2018 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once('instance_lib.php');
require_once("../user_management/user_lib.php");
require_once("../periods_management/periods_lib.php");

if(isset($_POST['function'])){
    
    switch($_POST['function']){
        case 'insert_cohort':
            if(isset($_POST['cohort']) && isset($_POST['instance'])){
                insert_cohort($_POST['cohort'], $_POST['instance']);
            }             
            break;
    }
}

function insert_cohort($id_cohort, $id_instance){

    global $DB;

    $msg_to_return = new stdClass();

    $sql_query = "SELECT count(*) AS count
                  FROM {talentospilos_inst_cohorte}
                  WHERE id_cohorte = $id_cohort AND id_instancia = $id_instance";
    
    $result_query = $DB->get_record_sql($sql_query);

    if($result_query->count >= 1){
        $msg_to_return->msg = 'Error. La cohorte ya está asignada a la instancia.';
        $msg_to_return->status = 0;
    }else{
        $object_to_record = new stdClass();
        $object_to_record->id_cohorte = $id_cohort;
        $object_to_record->id_instancia = $id_instance;
        $result_insertion = $DB->insert_record('talentospilos_inst_cohorte', $object_to_record);
        $msg_to_return->msg = 'La cohorte ha sido correctamente asignada.';
        $msg_to_return->status = 1;
    }
    
    echo json_encode($msg_to_return);
}

function load_cohorts_assigned($id_instance){

    global $DB;

    $msg_to_return = new stdClass();

}
?>