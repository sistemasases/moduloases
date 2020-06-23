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
 * @author     Jorge Eduardo Mayor Fernández
 * @package    block_ases
 * @copyright  2020 Jorge Eduardo Mayor <mayor.jorge@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Gets the cohort id by its name
 *
 * @param $cohort_name string
 * @return int cohort id
 */
function get_cohort_id_by_name($cohort_name){

    global $DB;

    $sql_query = "SELECT cohort.id FROM {cohort} AS cohort WHERE cohort.idnumber = '$cohort_name'";
    $result = $DB->get_record_sql($sql_query);

    if(isset($result) && $result >= 0)
        return $result->id;
    else
        return -1;
}

/**
 * Gets the emails of the members of all the cohorts specified
 *
 * @see get_cohort_id_by_name()
 * @param $cohorts array array with the names of the cohorts
 * @return array containing the emails of cohort members specified
 */
function get_user_ids_by_cohort($cohorts){

    global $DB;

    $condition = "true";
    foreach ($cohorts AS $cohort)
    {
        $cohort_id = get_cohort_id_by_name($cohort);
        $condition .= " OR cohort.id = ".$cohort_id;
    }

    $sql_query = "SELECT cm.userid
                      FROM {talentospilos_usuario} AS user_ 
                        INNER JOIN {talentospilos_user_extended} AS ue ON ue.id_ases_user = user_.id
                        INNER JOIN {cohort_members} AS cm ON cm.userid = ue.id_moodle_user
                        INNER JOIN {cohort} AS cohort ON cm.cohortid = cohort.id
                      WHERE $condition";

    return $DB->get_records_sql($sql_query);
}

/**
 * Sends emails
 *
 * @param $additional_emails
 * @param $cohorts array names of cohorts whose members are
 *                       gonna receive the email
 * @param $subject
 * @param $message_body
 * @param $course_id
 * @return boolean specifying if all messages have been sent
 * @see get_emails_by_cohorts()
 */
function communications_send_email($additional_emails, $cohorts, $subject, $message_body, $course_id){

    global $DB, $USER;

    $user_ids = get_user_ids_by_cohort($cohorts);

    file_put_contents('../../test.txt', "Start\n\n\n");

    foreach($user_ids  as $user_id) {

        //file_put_contents('../../test.txt', json_encode($user_id)."\n", FILE_APPEND);
        $userto = $DB->get_record('user', array('id' => $user_id));

        $message = new \core\message\message();
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = $USER;
        $message->userto = $userto;
        $message->subject = $subject;
        $message->fullmessage = $message_body;
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = $message_body;
        $message->smallmessage = 'small message';
        $message->notification = '0';
        $message->contexturl = 'http://www.campusvirtual.univalle.edu.co/moodle/blocks/ases/view/communications.php';
        $message->contexturlname = 'ASES - Universidad del Valle';
        $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
        $message->set_additional_content('email', $content);
        $message->courseid = $course_id;

        message_send($message);
    }


    return true;
}