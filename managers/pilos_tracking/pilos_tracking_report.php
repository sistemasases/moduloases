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
 * @author     Isabella Serna RamĆ­rez
 * @package    block_ases
 * @copyright  2017 Isabella Serna RamĆ­rez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
require_once '../user_management/user_lib.php';
require_once '../role_management/role_management_lib.php';
global $USER;

if (isset($_POST['type']) && $_POST['type'] == "getInfo" && isset($_POST['instance']))
    {
    $datos = [];
    $datos["id"] = $USER->id;
    $datos["username"] = $USER->username;
    $datos["email"] = $USER->email;
    $datos["rol"] = get_id_rol_($USER->id, $_POST['instance']);
    $datos["name_rol"] = get_name_rol($datos["rol"]);
    echo json_encode($datos);
    }
if (isset($_POST['type']) && isset($_POST['instance']) && $_POST['type'] == "get_groupal_trackings" && isset($_POST['student_code']))
    {

    // Groupal trackings (Seguimientos)

    $html_tracking_groupal = "";
    $student_code = $_POST['student_code'];

    $current_semester = get_current_semester();
    $array_groupal_trackings_dphpforms =get_tracking_grupal_monitor_current_semester($student_code,$current_semester->max);

    $html_tracking_groupal.=render_monitor_groupal_trackings($array_groupal_trackings_dphpforms);

    echo json_encode($html_tracking_groupal);
    }

if (isset($_POST['type']) && $_POST['type'] == "consult_students_name" && isset($_POST['students']))
    {

      echo (get_names_students($_POST['students']));

    }

if (isset($_POST['type']) && isset($_POST['instance']) && $_POST['type'] == "get_student_trackings" && isset($_POST['student_code']))
    {

    // Student trackings (Seguimientos)

    $html_tracking_peer = "";
    $student_code = explode("-", $_POST['student_code']);
    $ases_student = get_ases_user_by_code($student_code[0]);
    $student_id = $ases_student->id;
    $current_semester = get_current_semester();
    $array_peer_trackings_dphpforms = get_tracking_peer_student_current_semester($student_code[0], $current_semester->max);
    $array = render_student_trackings($array_peer_trackings_dphpforms);
    echo json_encode($array);
    }

if (isset($_POST['type']) && isset($_POST['instance']) && $_POST['type'] == "get_monitors_of_practicant" && isset($_POST['monitor_code']))
    {

    // Get Monitors of practicant
    $monitor_id =search_user($_POST['monitor_code']);
    $current_semester = get_current_semester();
    $students_by_monitor=get_students_of_monitor($monitor_id->id,$_POST['instance']);
    $array=render_monitor_new_form($students_by_monitor);

    echo json_encode($array);
    }

if (isset($_POST['type']) && isset($_POST['instance']) && $_POST['type'] == "get_practicants_of_professional" && isset($_POST['practicant_code']))
    {

    // Get practicant of professional
    $practicant_id =search_user($_POST['practicant_code']);
    $monitors_of_pract = get_monitors_of_pract($practicant_id->id,$_POST['instance']);
    $array=render_practicant_new_form($monitors_of_pract,$_POST['instance']);
    echo json_encode($array);
    }


if (isset($_POST['type']) && $_POST['type'] == "actualizar_personas" && isset($_POST['id']) && isset($_POST['instance']))
    {
    $roles = get_rol_ps();
    $retorno = get_people_onsemester($_POST['id'], $roles, $_POST['instance']);
    foreach($retorno as $person)
        {
        $table.= '<option value="' . $person->id_usuario . '">' . $person->username . " - " . $person->firstname . " " . $person->lastname . '</option>';
        }

    echo $table;
    }

if (isset($_POST['type']) && $_POST['type'] == "consulta_sistemas" && isset($_POST['id_persona']) && isset($_POST['id_semestre']) && isset($_POST['instance']))
    {
    $globalArregloPares = [];
    $globalArregloGrupal = [];
    $fechas = [];
    $intervalos = get_semester_interval($_POST['id_semestre']);
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
                $html = render_monitor_new_form($students_by_monitor, $intervalos->id);
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
      else
        {
        if (empty($retorno))
            {
            $html = "No tiene registros en ese periodo";
            }
          else
            {
            $usernamerole = get_name_rol($retorno->id_rol);
            if ($usernamerole == 'monitor_ps')
                {
                $html = monitorUser($globalArregloPares, $globalArregloGrupal, $_POST['id_persona'], 0, $_POST['instance'], $retorno->id_rol, $fechas, true);
                }
              else
            if ($usernamerole == 'practicante_ps')
                {
                $html = practicanteUser($globalArregloPares, $globalArregloGrupal, $_POST['id_persona'], $_POST['instance'], $retorno->id_rol, $fechas, true);
                }
              else
            if ($usernamerole == 'profesional_ps')
                {
                $html = profesionalUser($globalArregloPares, $globalArregloGrupal, $_POST['id_persona'], $_POST['instance'], $retorno->id_rol, $fechas, true);
                }

            $actions = authenticate_user_view($USER->id, $_POST['instance'], 'report_trackings');
            $html = show_according_permissions($html, $actions);
            echo $html;
            }
        }
    }

if (isset($_POST['type']) && $_POST['type'] == "info_monitor" && isset($_POST['id']) && isset($_POST['instance']))
    {
    $retorno = get_seguimientos_monitor($_POST['id'], $_POST['instance']);
    echo (json_encode($retorno));
    }

if (isset($_POST['type']) && $_POST['type'] == "eliminar_registro" && isset($_POST['id']))
    {
    $retorno = delete_tracking_peer($_POST['id']);
    echo (json_encode($retorno));
    }

if (isset($_POST['type']) && $_POST['type'] == "actualizar_registro")
    {
    $objeto = (object)$_POST['seguimiento'];
    $retorno = updateSeguimiento_pares($objeto);
    echo $retorno;
    }

if (isset($_POST['type']) && $_POST['type'] == "number_seg_monitor" && isset($_POST['id']) && isset($_POST['instance']))
    {
    $retorno = get_cantidad_seguimientos_monitor($_POST['id'], $_POST['instance']);
    echo (json_encode($retorno));
    }

if (isset($_POST['type']) && $_POST['type'] == "info_practicante" && isset($_POST['id']))
    {
    $retorno = get_monitores_practicante($_POST['id']);
    echo (json_encode($retorno));
    }

if (isset($_POST['type']) && $_POST['type'] == "info_profesional" && isset($_POST['id']) && isset($_POST['instance']))
    {
    $retorno = get_practicantes_profesional($_POST['id'], $_POST['instance']);
    echo (json_encode($retorno));
    }

if (isset($_POST['type']) && $_POST['type'] == "getProfesional" && isset($_POST['instance']) && isset($_POST['id']))
    {
    $retorno = get_profesional_practicante($_POST['id'], $_POST['instance']);
    echo ($retorno);
    }

if (isset($_POST['type']) && $_POST['type'] == "send_email_to_user" && isset($_POST['message_to_send']) && isset($_POST['tracking_type']) && isset($_POST['monitor_code']) && isset($_POST['date']))
    {
 
        /*
            La linea siguiente no se adiciona a el if previo, con el 
            fin de evitar problemas con otros script que hagan uso del método.
        */
        $place = $_POST['lugar'];

    if ($_POST['form'] == 'new_form')
        {
        $register = dphpforms_get_record($_POST['id_tracking'], 'id_estudiante');
        $json = json_decode($register, true);
        $id_moodle_student = get_name_by_username($json['record']['alias_key']['respuesta']);
        $id_ases_student = get_id_ases_user($id_moodle_student->id);
        $monitor_code = get_student_monitor($id_ases_student->id_ases_user, $_POST['semester'], $_POST['instance']);
        $practicant_code = get_boss_of_monitor_by_semester($monitor_code, $_POST['semester'], $_POST['instance']);
        
        echo send_email_to_user($_POST['tracking_type'], $monitor_code, $practicant_code->id_jefe, $practicant_code->id_usuario, $_POST['date'], $id_moodle_student->firstname . " " . $id_moodle_student->lastname, $_POST['message_to_send'], $place);
        }
      else
        {
        echo send_email_to_user($_POST['tipoSeg'], $_POST['codigoEnviarN1'], $_POST['codigoEnviarN2'], $practicant_code->id_usuario, $_POST['fecha'], $_POST['nombre'], $_POST['message'], $place);
        }
    }

?>