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
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2018 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../config.php');
require_once('../lib/student_lib.php');
require_once('../student_profile/studentprofile_lib.php');

date_default_timezone_set('America/Bogota');

$msg_error = new stdClass();
$msg = new stdClass();

if(isset($_POST['func'])){

    if($_POST['func'] == 'update_status_ases') {
        $result_save_dropout = 1;

        if (isset($_POST['current_status']) && isset($_POST['new_status']) && isset($_POST['instance_id']) && isset($_POST['code_student'])) {
            if (isset($_POST['id_reason_dropout']) && isset($_POST['observation'])) {
                if ($_POST['id_reason_dropout'] . trim() != "" && $_POST['observation'] . trim() != "") {
                    $result_save_dropout = save_reason_dropout_ases($_POST['code_student'], $_POST['id_reason_dropout'], $_POST['observation']);
                    $result = update_status_ases($_POST['current_status'], $_POST['new_status'], $_POST['instance_id'], $_POST['code_student'], $_POST['id_reason_dropout']);
                } else {
                    $result = 0;
                    $result_save_dropout = 0;
                }
            } else {
                $result = update_status_ases($_POST['current_status'], $_POST['new_status'], $_POST['instance_id'], $_POST['code_student']);
            }

            $msg = new stdClass();

            if ($result && $result_save_dropout) {
                $msg->title = 'Éxito';
                $msg->msg = 'Estado actualizado con éxito.';
                $msg->status = 'success';
                $msg->previous_status = $_POST['current_status'];

            } else {
                $msg->title = "Error";
                $msg->msg = "Error al realizar registro.";
                $msg->status = 'error';
                if (!$result_save_retiro) {
                    $msg->msg .= "\nNo se guarda motivo de retiro en la base de datos.";
                }
                if (!$result) {
                    $msg->msg .= "\nNo se guarda estado en la base de datos.";

                }
                if (!$result && !$result_save_dropout) {
                    $msg->msg .= "\nCampos obligatorios no enviados.";
                }
            }

            echo json_encode($msg);
        } else {
            $msg_error = new stdClass();
            $msg_error->title = 'Error';
            $msg_error->msg = 'Error al conectarse con el servidor.';
            $msg_error->status = 'error';

            echo json_encode($msg_error);
        }
    }elseif($_POST['func'] == 'save_tracking_peer'){
        save_tracking_peer_proc();
    }elseif($_POST['func'] == 'delete_tracking_peer' && isset($_POST['id_tracking'])){
        $id_tracking_peer = $_POST['id_tracking'];
        delete_tracking_peer_proc($id_tracking_peer);
    }else if($_POST['func'] == 'send_email'){
        send_email($_POST["risk_array"], $_POST["observations_array"],'' ,$_POST["id_student_moodle"], $_POST["id_student_pilos"], $_POST["date"],'', '', $_POST["url"]);
    }else if($_POST['func'] == 'update_tracking_status'){
        $id_ases_student = $_POST['id_ases_student'];
        $id_academic_program = $_POST['id_academic_program'];
        $result = update_tracking_status($id_ases_student, $id_academic_program);

        if($result){
            $msg =  new stdClass();
            $msg->title = "Éxito";
            $msg->msg = "El campo se ha actualizado con éxito";
            $msg->status = "success";
            echo json_encode($msg);
        } else {
            $msg =  new stdClass();
            $msg->title = "Error";
            $msg->msg = "Error al actualizar el campo";
            $msg->status = "error";
            echo json_encode($msg);
        }
    } else if($_POST['func'] == 'update_user_image'){
        $new_user_image = $_FILES['new_image_file'];
        $user_id = $_POST['mdl_user_id'];
        $user = new stdClass();
        $user->id = $user_id;
        update_user_image_profile($user->id, 0);
        print_r($user_id);
    }
}

/**
 * Updates 'estado Ases' field on {talentospilos_usuario} table
 *
 * @see save_status_ases_proc($new_status, $id_ases, $id_reason = null, $observations=null)
 * @param $new_status --> New status to save on 'estado Ases' field
 * @param $id_ses --> ASES student id
 * @param $id_reason = null --> Retirement reason id
 * @param $observations = null --> observations to save
 * @return object in a json format
 */

function save_status_ases_proc($new_status, $id_ases, $id_reason = null, $observations=null){

    $result = save_status_ases($new_status, $id_ases, $id_reason, $observations);

    echo json_encode($result);
}

function loadMotivos(){
    $result = getMotivosRetiros();
    $msg = new stdClass();
    $msg->size = count($result);
    $msg->data = $result;
    echo json_encode($msg);
}

function loadMotivoRetiroStudent(){
    if(isset($_POST['talentosid']))
    {
        echo json_encode(getMotivoRetiroEstudiante($_POST['talentosid']));

    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al comunicarse con el servidor. No se encuentran las variables necesarias para cargar el motivo retiro";
        echo json_encode($msg);
    }
}
