<?php
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "SELECT ROW_NUMBER() OVER (ORDER BY u.id, info_field.shortname), u.id, info_field.shortname, info_data.data
              FROM {user} AS u INNER JOIN {user_info_data} AS info_data ON u.id = info_data.userid
                                  INNER JOIN {user_info_field} AS info_field ON info_data.fieldid = info_field.id
              WHERE info_data.data <> '' AND (info_field.shortname = 'idtalentos' OR info_field.shortname = 'idprograma' OR info_field.shortname = 'estado')";

$result_query = $DB->get_records_sql($sql_query);

$record_to_save = new stdClass();

for($i = 1; $i < count($result_query) - 2; $i += 3){
    $id_moodle = $result_query[$i]->id;

    if(($id_moodle == $result_query[$i+1]->id) && ($id_moodle == $result_query[$i+2]->id)){
        $record_to_save->id_moodle_user = $id_moodle;
        $record_to_save->id_ases_user = $result_query[$i+2]->data;
        $record_to_save->id_academic_program = $result_query[$i+1]->data;
        $record_to_save->program_status = $result_query[$i]->data;
    }
    else{
        print_r("********Falló***************");
    }
    print_r($record_to_save);
}
print_r("****************************************");
print_r($result_query);

// [1] => stdClass Object ( [row_number] => 1 [id] => 3 [shortname] => estado [data] => ACTIVO ) 
// [2] => stdClass Object ( [row_number] => 2 [id] => 3 [shortname] => idprograma [data] => 130 ) 
// [3] => stdClass Object ( [row_number] => 3 [id] => 3 [shortname] => idtalentos [data] => 108 ) 