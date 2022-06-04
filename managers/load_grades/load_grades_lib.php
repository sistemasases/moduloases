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

module_loader("periods");


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
function get_students_ases() {
    global $DB;

    $sql = "SELECT distinct username, id_ases_user FROM {user}
            INNER JOIN {talentospilos_user_extended} ext on {user}.id = ext.id_moodle_user
            WHERE tracking_status = 1";

    try {
        $ases_students = $DB    get_students_ases();->get_records_sql($sql);
    }
    catch (Exception $ex) {
        Throw New Exception($ex->getMessage());
    }

    return $ases_students;
}

function filter_students($var): bool
{
    $ases_students = get_students_ases();

    foreach ($ases_students as $ases_student) {
        if ($var->username == $ases_student->username) {
            return true;
        }
    }

    return false;
}

function send_alerts(array $grades, $instance_id) {
    $arr = array_filter($grades, "filter_students");
    $current_period = core_periods_get_current_period($instance_id);

    foreach ($arr as $item) {

        $id_professional = get_id_assigned_professional($item->id_ases_user);
        $id_practicante = get_id_assigned_pract($item->id_ases_user);

        $pract = get_full_user($id_practicante);
        $prof = get_full_user($id_professional);

        $errors = craft_and_send_email([$pract, $prof], $item);
    }
}

function craft_and_send_email(array $recipients, $student) {
    $error_email = [];
    $subject = "Registro de nota pérdida";

    $messageHtml = "Se registra una nota pérdida para el estudiante: <br><br>";
    $messageHtml .= "<b>Nombre completo</b>: $student->firstname $student->lastname <br>";
    $messageHtml .= "<b>Código:</b> $student->username <br>";
    $messageHtml .= "<b>Correo electrónico:</b> $student->email <br><br>";
    $messageHtml .= "<b>Curso:</b> $student->fullname <br>";
    $messageHtml .= "<b>Nota:</b> $student->finalgrade de $student->passingrade<br>";
    $messageHtml .= "<b>Fecha:</b> $student->fecha<br>";

    $from = get_full_user(107089); // sistemas1008

    foreach ($recipients as $recipient) {
        $result = email_to_user($recipient, $from, $subject, "", $messageHtml);

        if (!$result) {
            $error_email[] = $recipient;
        }
    }

    return $error_email;
}







