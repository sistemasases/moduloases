<?php
require_once dirname(__FILE__) . '/../../../config.php';

global $DB;

$query_nulls = "SELECT *
                FROM {talentospilos_user_extended}
                WHERE tracking_status IS NULL";

$result = $DB->get_records_sql($query_nulls);

foreach ($result as $register) {
    
    //validate if have more users with tracking_status = 1
    $query = "SELECT *
             FROM {talentospilos_user_extended};
             WHERE id_ases_user = $register->id_ases_user";

    $result_query = $DB->get_records_sql($query);

    $has_tracking = false;

    foreach ($result_query as $resultado) {
        if($resultado->tracking_status === 1){
            $has_tracking = true;
        }
    }

    $record = new stdClass;
    $record->id = $register->id;

    if($has_tracking){
        $record->tracking_status = 1;
    }else{
        $record->tracking_status = 0;
    }

    echo $DB->update_record('talentospilos_user_extended', $record);

}