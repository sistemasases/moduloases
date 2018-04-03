<?php
require_once dirname(__FILE__) . '/../../../config.php';

global $DB;


//update users 

$sql_query = "UPDATE {talentospilos_user_extended} SET tracking_status = 0 WHERE id_moodle_user=103287";
$success = $DB->execute($sql_query);

$sql_query = "UPDATE {talentospilos_user_extended} SET tracking_status = 0 WHERE id_moodle_user=106622";
$success = $DB->execute($sql_query);


$sql_query = "UPDATE {talentospilos_user_extended} SET tracking_status = 1 WHERE id_moodle_user=121571";
$success = $DB->execute($sql_query);

$sql_query = "UPDATE {talentospilos_user_extended} SET tracking_status = 0 WHERE id_moodle_user=114987";
$success = $DB->execute($sql_query);
