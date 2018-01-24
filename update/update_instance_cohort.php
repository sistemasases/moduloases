<?php
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "SELECT id FROM {cohort} WHERE idnumber LIKE 'SP%'";
$cohorts_id = $DB->get_records_sql($sql_query);

$instance_id = 450299;

foreach($cohorts_id as $cohort_id){

    $record_object = new stdClass();
    $record_object->id_cohorte = $cohort_id->id;
    $record_object->id_instancia = $instance_id;

    $result = $DB->insert_record('talentospilos_inst_cohorte', $record_object);

    print_r($result);

}