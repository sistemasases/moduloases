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
 * @author     Isabella Serna RamÄ†Ā­rez
 * @package    block_ases
 * @copyright  2017 Isabella Serna RamÄ†Ā­rez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;
require_once ('../validate_profile_action.php');
require_once ('tracking_functions.php');
require_once ('../lib/student_lib.php');
require_once ('../lib/lib.php');
require_once ('../student_profile/studentprofile_lib.php');
require_once ('../periods_management/periods_lib.php');
require_once ('../seguimiento_grupal/seguimientogrupal_lib.php');
require_once ('../dphpforms/dphpforms_forms_core.php');
require_once ('../dphpforms/dphpforms_records_finder.php');
require_once ('../dphpforms/dphpforms_get_record.php');
require_once ('../user_management/user_lib.php');
require_once ('../role_management/role_management_lib.php');
require_once (__DIR__ . '/../monitor_profile/monitor_profile_lib.php');
require_once (__DIR__ . '/../../core/module_loader.php');
require_once $CFG->dirroot . '/blocks/ases/managers/user_management/user_management_lib.php';

module_loader("periods");

global $USER;

if (isset($_POST['type']) && $_POST['type'] == "getInfo" && isset($_POST['instance']))
    {

    //Function that organizes a user's information

    $datos = [];
    $datos["id"] = $USER->id;
    $datos["username"] = $USER->username;
    $datos["email"] = $USER->email;
    $datos["rol"] = get_id_rol_($USER->id, $_POST['instance']);
    $datos["name_rol"] = get_name_rol($datos["rol"]);
    echo json_encode($datos);
    }


if (isset($_POST['type']) && $_POST['type'] == "user_specific_counting" && isset($_POST['instance'])){

    $user = null;
    if( empty( $_POST['user'] ) ){
        $user = $USER->id;
    }else{
        $user = $_POST['user'];
    }
    
    
    
    $current_semester = core_periods_get_current_period((int)$_POST['instance']);
    $role = get_id_rol_v2($user, $_POST['instance'], $current_semester->id);
    $role_name = get_name_rol($role);
    $array_final = null;

    if($role_name == 'profesional_ps'){
       $array_final = auxiliary_specific_countingV2($role_name,$user,$current_semester, $_POST['instance']);
    }else if($role_name =='practicante_ps'){
       $array_final = auxiliary_specific_countingV2($role_name,$user,$current_semester, $_POST['instance']);
    }else if($role_name =='monitor_ps'){
       $array_final = auxiliary_specific_countingV2($role_name,$user,$current_semester, $_POST['instance']);
    }

    echo json_encode($array_final);  
}


if (isset($_POST['type']) && isset($_POST['instance']) && $_POST['type'] == "get_groupal_trackings" && isset($_POST['student_code']))
    {

    //Function that obtains the groupal trackings of a monitor.

    $html_tracking_groupal = "";
    $student_code = $_POST['student_code'];
    $current_semester = core_periods_get_current_period($_POST['instance']);
    $array_groupal_trackings_dphpforms = get_tracking_grupal_monitor_current_semester($student_code, $current_semester->id);
    $html_tracking_groupal.= render_monitor_groupal_trackings($array_groupal_trackings_dphpforms);
    echo json_encode($html_tracking_groupal);
    }

if (isset($_POST['type']) && $_POST['type'] == "consult_students_name" && isset($_POST['students']))
    {
    //Function that generates the list of students attending a group trackings
    
    echo (get_names_students($_POST['students']));
    }

if (isset($_POST['type']) && isset($_POST['instance']) && $_POST['type'] == "get_student_trackings" && isset($_POST['student_code'])){

        // Function that obtains the peer trackings of a student.

        $html_tracking_peer = "";
        $student_code = explode("-", $_POST['student_code']);
        $ases_student = get_ases_user_by_code($student_code[0]);
        $student_id = $ases_student->id;
        $current_semester = core_periods_get_current_period((int)$_POST['instance']);
        $array_peer_trackings_dphpforms = get_tracking_current_semesterV3('student',$student_code[0], $current_semester->id);
        $array = render_student_trackingsV2($array_peer_trackings_dphpforms);
        echo json_encode($array);

};

if (isset($_POST['type']) && isset($_POST['instance']) && $_POST['type'] == "get_monitors_of_practicant" && isset($_POST['monitor_code'])){

    // Get Monitors of practicant

    $monitor_id = search_user($_POST['monitor_code']);
    $current_semester = core_periods_get_current_period((int)$_POST['instance']);
    $students_by_monitor = get_students_of_monitor($monitor_id->id, $_POST['instance']);
    $array = render_monitor_new_form($students_by_monitor, null, $_POST['instance']);
    $array_groupal_trackings_dphpforms = get_tracking_grupal_monitor_current_semester($monitor_id->id, $current_semester->id);
    $array.= render_groupal_tracks_monitor_new_form($array_groupal_trackings_dphpforms, $monitor_id->id, null, $_POST['instance']);

    echo json_encode($array);
}

if (isset($_POST['type']) && isset($_POST['instance']) && $_POST['type'] == "get_practicants_of_professional" && isset($_POST['practicant_code'])){

    // Get practicant of professional

    $current_semester= core_periods_get_current_period((int)$_POST['instance']);

    $practicant_id = search_user($_POST['practicant_code']);
    $monitors_of_pract = get_monitors_of_pract($practicant_id->id, $_POST['instance']);
    $array = render_practicant_new_form($monitors_of_pract, $_POST['instance']);
    $msg = new stdClass();
    $msg->render =$array;

    echo json_encode($msg);
}

if (isset($_POST['type']) && $_POST['type'] == "update_people" && isset($_POST['id']) && isset($_POST['instance']))
    {
    $roles = get_rol_ps();
    $retorno = get_people_onsemester($_POST['id'], $roles, $_POST['instance']);
    foreach($retorno as $person)
        {
        $table.= '<option data-username="'.$person->username.'" value="' . $person->id_usuario . '">' . $person->username . " - " . $person->firstname . " " . $person->lastname . '</option>';
        }

    echo $table;
    }

if (isset($_POST['type']) && $_POST['type'] == "consulta_sistemas" && isset($_POST['id_persona']) && isset($_POST['id_semestre']) && isset($_POST['instance']))
    {
    $globalArregloPares = [];
    $globalArregloGrupal = [];
    $fechas = [];
    $intervalos = core_periods_get_period_by_id( (int)$_POST['id_semestre']);
    $fechas[0] = $intervalos->fecha_inicio;
    $fechas[1] = $intervalos->fecha_fin;
    $fechas[2] = $intervalos->id;
    /*Compara con fecha desde que se utilizan los nuevos formularios*/
    $choosen_date = strtotime($fechas[0]);
    $new_forms_date = strtotime('2018-01-01 00:00:00');
    $retorno = get_users_rols($_POST['id_persona'], $_POST['id_semestre'], $_POST['instance']);
    if ($choosen_date >= $new_forms_date)
        {
        if (empty($retorno))
            {
            $html = "No tiene registros en ese periodo";
            }
          else
            {
            $usernamerole = get_name_rol($retorno->id_rol);
            $id_person = $_POST['id_persona'];
            $id_instance = $_POST['instance'];
            if ($usernamerole == 'monitor_ps')
                {
                $students_by_monitor = get_students_of_monitor($id_person, $id_instance);
                $html = render_monitor_new_form($students_by_monitor, $intervalos->id, $id_instance);
                $array_groupal_trackings_dphpforms = get_tracking_grupal_monitor_current_semester($id_person, $intervalos->id);
                $html.= render_groupal_tracks_monitor_new_form($array_groupal_trackings_dphpforms, $id_person, null, $_POST['instance']);
                }
              else
            if ($usernamerole == 'practicante_ps')
                {
                $monitors_of_pract = get_monitors_of_pract($id_person, $id_instance);
                $html = render_practicant_new_form($monitors_of_pract, $id_instance, $intervalos->id);
                }
              else
            if ($usernamerole == 'profesional_ps')
                {
                $practicant_of_prof = get_pract_of_prof($id_person, $id_instance);
                $html = render_professional_new_form($practicant_of_prof, $id_instance, $intervalos->id);
                }

            $actions = authenticate_user_view($USER->id, $_POST['instance'], 'report_trackings');
            $html = show_according_permissions($html, $actions);
            echo $html;
            }
        }
    }

if(isset($_POST['monitor'])&&isset($_POST['type'])&&$_POST['type']=='redirect_tracking_time_control'){
    $username_monitor = $_POST['monitor'];
   echo  get_user_by_username($username_monitor)->id;
}    


// param $_POST['date'] is obsolete.
if (isset($_POST['type']) && $_POST['type'] == "send_email_to_user" && isset($_POST['message_to_send']) && isset($_POST['tracking_type']) && isset($_POST['monitor_code']) && isset($_POST['date']) && isset($_POST['instance']))
    {

    /*
    La linea siguiente no se adiciona a el if previo, con el
    fin de evitar problemas con otros script que hagan uso del mĆ©todo.
    */
    
    $place = $_POST['place'];
    $tracking_type = $_POST['tracking_type'];
    $instance = $_POST['instance'];
        error_log(
            "[".date('Y-M-d H:i e')." API CALL entering API to send emails]\n" ,
            3,
            "/var/log/mail-errors.log"
        );

    if (is_numeric($instance)) {
       $instance = intval($instance); 
    } else {
       throw new Exception('Instancia no numérica'); 
    }

    $courseid = $_POST['courseid'];
    if ($_POST['form'] == 'new_form')
        {
            error_log(
                "[".date('Y-M-d H:i e')." API CALL form is new_form]\n" ,
                3,
                "/var/log/mail-errors.log"
            );

            $register = null;
            if( $tracking_type == "individual" ){
                $register = dphpforms_get_record($_POST['id_tracking'], 'id_estudiante');
            }elseif ( $tracking_type == "individual_inasistencia" ) {
                $register = dphpforms_get_record($_POST['id_tracking'], 'in_id_estudiante');
            }
            $date = "";
            $json = json_decode($register, true);
            foreach( $json['record']['campos'] as $key => $field ){
                if( ( $field['local_alias'] == "fecha" ) || ( $field['local_alias'] == "in_fecha" ) ){
                    $date = $field['respuesta'];
                }
            }
            error_log(
                "[".date('Y-M-d H:i e')." API CALL entering try-catch]\n" ,
                3,
                "/var/log/mail-errors.log"
            );
            try {
                $period = core_periods_get_period_by_id($_POST['semester']);

                $id_moodle_student = user_management_get_full_ases_user($json['record']['alias_key']['respuesta']);
                $id_ases_student = $json['record']['alias_key']['respuesta'];

                $monitor = get_student_monitor($id_ases_student, $period->id, $instance);
                if (!$monitor) {
                    Throw New Exception(
                        "No se ha encontrado monitor del estudiante $id_moodle_student->username durante el periodo $period->nombre. 
                        Favor revisar el período seleccionado."
                    );
                }

                $monitor_code = $monitor->id_monitor;
                $practicant_code = get_boss_of_monitor_by_semester($monitor_code, $period->id, $instance);
                if (count((array)$practicant_code) == 0) {
                    Throw New Exception(
                        "No se ha encontrado practicante del monitor $monitor->username durante el periodo $period->nombre.
                        Favor revisar el período seleccionado."
                    );
                }

                $profesional_code = get_practicant_boss_under_period(
                    $practicant_code->id_usuario,
                    $period->id
                );
                error_log(
                    "[".date('Y-M-d H:i e')." API CALL got all socioed info]\n" ,
                    3,
                    "/var/log/mail-errors.log"
                );

                if (isset($monitor_code)|| count((array)$practicant_code) > 0 || count((array)$profesional_code) > 0) {
                    error_log(
                        "[".date('Y-M-d H:i e')." API CALL about to call lib to send email]\n" ,
                        3,
                        "/var/log/mail-errors.log"
                    );
                    echo json_encode( send_email_to_user(
                        $_POST['tracking_type'],
                        $monitor_code,
                        $practicant_code->id_usuario,
                        $profesional_code->id,
                        date("Y-m-d", strtotime($date)),
                        $id_moodle_student->firstname . " " . $id_moodle_student->lastname,
                        $_POST['message_to_send'],
                        $place,
                        $instance,
                        $courseid,
                        $id_ases_student
                    ));
                } else {
                    error_log(
                        "[".date('Y-M-d H:i e')." API CALL monitor,practicant or professional not found]\n" ,
                        3,
                        "/var/log/mail-errors.log"
                    );
                    throw new Exception("Monitor o practicante o profesional no encontrado.");
                }

            } catch (Exception $ex) {
                error_log(
                    "[".date('Y-M-d H:i e')." API CALL " .$ex->getMessage(). "]\n" ,
                    3,
                    "/var/log/mail-errors.log"
                );

                echo json_encode(array(
                    "status_code" => -1,
                    "error_message" => $ex->getMessage(),
                    "data_response" => null
                ));
            }

        }
    }

?>
