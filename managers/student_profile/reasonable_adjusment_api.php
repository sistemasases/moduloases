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
 * Dynamic PHP Forms
 *
 * @author     Juan Pablo Castro
 * @package    block_ases
 * @copyright  2018 Juan Pablo Castro<juan.castro.vasquez@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
 


require_once(dirname(__FILE__). '/../../../../config.php');
require_once(dirname(__FILE__).'/reasonable_adjusment_lib.php');
require_once(dirname(__FILE__).'/../student_profile/studentprofile_lib.php');

global $USER;
global $COURSE;


$msg_error = new stdClass();
$msg = new stdClass();

if(isset($_POST['func'])){

    if($_POST['func'] == 'load_discapacity_reasonable_adjusment_theme'){
        
        /**
         * [0] => id_ases: string
         * [1] => tab_name: string
         */
        $params = $input->params;
        if(count($params) == 2) {

            $id_ases = $params[0];
            $tab_name = $params[1];

            if(is_string($id_ases) && is_string($tab_name)) {

                $result = load_discapacity_reasonable_adjusment_theme($id_ases);

                if($result != null){
                    echo json_encode(
                        array(
                            "status_code" => 0,
                            "message" => $tab_name." information",
                            "data_response" => $result
                        )
                    );
                } else {
                    return_with_code(-5);
                }
            } else {
                return_with_code(-2);
            }
        } else {
            return_with_code(-6);
        }
    } 
    else{
        $msg->title = "Error";
        $msg->msg = "No se ha enviado una función. Informe al área de sistemas.";
        $msg->status = "error";
        echo json_encode($msg);
    }
}else{
    $msg->title = "Error";
    $msg->msg = "Error en el servidor. Informe al área de sistemas.";
    $msg->status = "error";
    echo json_encode($msg);
}


?>