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
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once ('../validate_profile_action.php');
require_once('tracking_functions.php');
require_once('../student_profile/studentprofile_lib.php');
require_once('../periods_management/periods_lib.php');
require_once ('../dphpforms/dphpforms_forms_core.php');
require_once ('../dphpforms/dphpforms_records_finder.php');
require_once ('../dphpforms/dphpforms_get_record.php');
require_once '../user_management/user_lib.php';

global $USER;

if(isset($_POST['type'])&&$_POST['type']=="getInfo"&&isset($_POST['instance'])) 
 {
    $datos=[];
    $datos["id"]=$USER->id;
    $datos["username"]=$USER->username;
    $datos["email"]=$USER->email;
    $datos["rol"]=get_id_rol_($USER->id,$_POST['instance']);
    $datos["name_rol"]=get_name_rol($datos["rol"]);

    echo json_encode($datos);
}

if(isset($_POST['type'])&&isset($_POST['instance'])&&$_POST['type']=="get_student_trackings"&&isset($_POST['student_code'])) 
 {
    // Student trackings (Seguimientos)

      $html_tracking_peer = "";



    $student_code = explode("-", $_POST['student_code']);

    $ases_student = get_ases_user_by_code($student_code[0]);
    $student_id = $ases_student->id;
    $array_peer_trackings_dphpforms = dphpforms_find_records('seguimiento_pares', 'seguimiento_pares_id_estudiante', $student_code[0], 'DESC');
    $array_peer_trackings_dphpforms = json_decode($array_peer_trackings_dphpforms);

    $array_detail_peer_trackings_dphpforms = array();
    $array_tracking_date = array();

    format_dates_trackings($array_detail_peer_trackings_dphpforms,$array_tracking_date,$array_peer_trackings_dphpforms);

    rsort($array_tracking_date);
    $seguimientos_ordenados=trackings_sorting($array_detail_peer_trackings_dphpforms,$array_tracking_date,$array_peer_trackings_dphpforms);

    $seguimientos_array = json_decode(json_encode($seguimientos_ordenados), true);
    $array_periodos = array();
    for ($x = 0; $x < count($seguimientos_array['index']); $x++) {
        array_push($array_periodos, $seguimientos_array[$seguimientos_array['index'][$x]]);
    }
    $peer_tracking_v2 = array(
        'index' => $seguimientos_array['index'],
        'periodos' => $array_periodos,
    );


    $html_tracking_peer = "";
    print_r(render_student_trackings($peer_tracking_v2));
 
    


}



?>
