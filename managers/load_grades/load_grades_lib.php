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
 * Librería de load_grades, la funcionalidad que carga el registro de notas
 * criticas por medio de un csv y genera las alertas.
 *
 * @author     David Santiago Cortés
 * @package    block_ases
 * @copyright  2022 David Santiago Cortés <david.cortes@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @return void
 */

require_once (__DIR__ . '/../../../../config.php');
require_once (__DIR__ . '/../../core/module_loader.php');
require_once (__DIR__ . '/../seguimiento_grupal/seguimientogrupal_lib.php');
require_once (__DIR__ . '/../lib/student_lib.php');

module_loader("periods");

$instance_id = 0;
$ases_students = [];

function load_csv(array $file) {
    $grades = array();

    $filename = $file["name"];
    if (!move_uploaded_file($file['tmp_name'], "../../view/archivos_subidos/" . $filename)) {
        return -1;
    }

    ini_set('auto_detect_line_endings', true);

    $rows = array_map('str_getcsv', file("../../view/archivos_subidos/$filename"));
    $header = array_shift($rows);

    foreach ($rows as $row) {
        $grades[] = array_combine($header, $row);
    }

    return $grades;
}

/**
 * @return array|void
 * @throws Exception
 */
function get_students_ases($instance_id): array {
    global $DB;

    $current_period = core_periods_get_current_period($instance_id)->id;

    $sql = "SELECT monest.id, username, id_ases_user FROM {user} 
            INNER JOIN {talentospilos_user_extended} ext on {user}.id = ext.id_moodle_user 
            INNER JOIN {talentospilos_monitor_estud} monest on monest.id_estudiante=ext.id_ases_user 
            WHERE monest.id_instancia=$instance_id 
            AND monest.id_semestre=$current_period 
            AND tracking_status = 1
            ";

    try {
        $ases_students = $DB->get_records_sql($sql);
    }
    catch (Exception $ex) {
        Throw New Exception($ex->getMessage());
    }

    return $ases_students;
}

function filter_students($var): bool
{
    global $ases_students;

    foreach ($ases_students as $ases_student) {
        if ($var->username == $ases_student->username) {
            $var->id_ases_user = $ases_student->id_ases_user;
            return true;
        }
    }

    return false;
}

function send_alerts(array $grades, $instance_id) {
    global $ases_students;

    $ases_students = get_students_ases($instance_id);

    $arr = array_filter($grades, "filter_students");

    $emails = [];

    $summ_data = [];
    foreach ($arr as $item) {

        $professional = get_assigned_professional($item->id_ases_user, $instance_id);
        $practicante = get_assigned_pract($item->id_ases_user, $instance_id);
        $monitor = get_assigned_monitor($item->id_ases_user, $instance_id);

        $emails[$professional->id] .= prepare_email($item, $monitor, $practicante);
        $emails[$practicante->id] .= prepare_email($item, $monitor);
    }

    $sending_user = get_full_user(107089);
    $sending_user->maildisplay = false;
    $sending_user->mailformat = 1;
    $subject = "ASES: Registro de notas pérdidas";
	
    $to_return = [
        "total" => count($emails),
        "success" => 0,
    ];

    foreach ($emails as $key => $val) {
        $receiving_user = get_full_user($key);
        $receiving_user->maildisplay = true;
        $receiving_user->mailformat = 1;
        $result = email_to_user($receiving_user, $sending_user,$subject,"",$val);

        if ($result == true) $to_return["success"]++;
    }
    
    send_summarized_alerts($grades, $arr, $instance_id);

    return $to_return;
}

/**
 * Envia el consolidado de alertas en forma de tabla a Yurani y Académico
 */
function send_summarized_alerts(array $grades, array $filtered_arr, $instance_id) {

    global $ases_students;

    switch ($instance_id) {
        case 450299:
            $ases_students = get_students_ases(1420604);
            break;

        case 1420604:
            $ases_students = get_students_ases(450299);
            break;

        default:
            return 0;
    }

    array_push(
        $filtered_arr,
        array_filter($grades, "filter_students")
    );

    $email_body = prepare_summarized_email($filtered_arr);

    $receiving_user1 = get_user_by_username("1107059505"); // Yurany Velasco
    $receiving_user1->maildisplay = true;
    $receiving_user1->mailformat = 1;

    $result_1 = email_to_user($receiving_user1, get_full_user(107089),"Consolidado Notas Pérdidas ASES Cali y Regionales","",$email_body);
    //$result_2 = email_to_user($receiving_user, get_full_user(107089),"Consolidado Notas Pérdidas ASES","",$email_body);
}


function prepare_summarized_email(array $data) {
    $message_html = "Tenga un cordial saludo, <br><br> A continuación hallará un consolidado con las notas pérdidas 
	de estudiantes ASES durante la semana anterior.<br>";

    $table_html =
	"<table style='table-layout:fixed;border-collapse:collapse;border:1px solid black;width:100%;text-align:center;'>
	    <thead >
		<tr style='letter-spacing:1px;'>
		    <th>Sede</th><th>Estudiante</th><th>Curso</th><th>Actividad</th><th>Nota</th><th>Fecha</th>
		</tr>
	    </thead>
	    <tbody>";

    foreach ($data as $datum) {
        $table_html .=
            "<tr style='letter-spacing:1px;'>
            <td style='border: 1px solid #333'>" . $datum->firstname . " " . $datum->lastname . " " . $datum->username . "</td>" .
            "<td style='border: 1px solid #333;'>" . $datum->fullname ."</td>" .
            "<td style='border: 1px solid #333;'>" . $datum->itemname ."</td>" .
            "<td style='border: 1px solid #333;'>" . $datum->finalgrade . "/" . $datum->grademax ."</td>" .
            "<td style='border: 1px solid #333;'>" . $datum->fecha ."</td>" .
            "</tr>";
    }

    $table_html .= "</tbody></table><br>";

    return $message_html .= $table_html;
}

function prepare_email($student, $monitor, $practicante=null) {

    if ($student->gradepass == $student->grademax) {
	    $student->gradepass = ceil($student->grademax / 2);
    }

    $messageHtml = "Tenga un cordial saludo, <br><br>";
    $messageHtml .= "Se registra una nota pérdida para el estudiante: <br><br>";
    $messageHtml .= "<b>Nombre completo</b>: $student->firstname $student->lastname ($student->email)<br>";
    $messageHtml .= "<b>Código:</b> $student->username <br>";
    $messageHtml .= "<b>Monitor:</b> $monitor->firstname $monitor->lastname ($monitor->email)<br>";
    if (isset($practicante)) {
	$messageHtml .= "<b>Practicante:</b> $practicante->firstname $practicante->lastname ($practicante->email)<br>";
    }
    $messageHtml .= "<b>Curso:</b> $student->fullname <br>";
    $messageHtml .= "<b>Actividad:</b> $student->itemname <br>";
    $messageHtml .= "<b>Nota obtenida:</b> $student->finalgrade de $student->grademax<br>";
    $messageHtml .= "<b>Nota mínima para pasar:</b> $student->gradepass<br>";
    $messageHtml .= "<b>Fecha:</b> $student->fecha<br>";

    return $messageHtml . "<hr/><br>";
}

