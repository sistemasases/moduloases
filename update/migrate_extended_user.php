<?php
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "SELECT ROW_NUMBER() OVER (ORDER BY u.id, info_field.shortname), u.id, info_field.shortname, info_data.data
              FROM {user} AS u INNER JOIN {user_info_data} AS info_data ON u.id = info_data.userid
                                  INNER JOIN {user_info_field} AS info_field ON info_data.fieldid = info_field.id
              WHERE info_data.data <> '' AND (info_field.shortname = 'idtalentos' OR info_field.shortname = 'idprograma' OR info_field.shortname = 'estado')";

$result_query = $DB->get_records_sql($sql_query);

$record_to_save = new stdClass(); //stdclass que se utiliza para guardar cada registro a almacenar
$errors = []; //Arreglo para guardar los registros en los cuales se presentaron errores de inserción

$i = 1;

while($i < count($result_query)+1){
    $id_moodle = $result_query[$i]->id;

    if($id_moodle == $result_query[$i+1]->id){
        if($id_moodle == $result_query[$i+2]->id){
            $record_to_save->id_moodle_user = $id_moodle;
            $record_to_save->id_ases_user = $result_query[$i+2]->data;
            $record_to_save->id_academic_program = $result_query[$i+1]->data;
            $record_to_save->program_status = $result_query[$i]->data;

            $result_insertion = $DB->insert_record('talentospilos_user_extended', $record_to_save, true);
            //print_r($record_to_save);
            $i = $i + 3;
        }else{
            array_push($errors, $result_query[$i+2]->id);
            $i = $i + 2;
        }
    }else{
        array_push($errors, $result_query[$i+1]->id);
        $i = $i + 1;
    }
}

// Errores

$sql_query = "SELECT username, firstname, lastname FROM {user} WHERE";
$where_cl = "";

for($i = 0; $i < count($errors) - 1; $i++){
    $where_cl = $where_cl." id = ".$errors[$i]." OR ";
}

$where_cl = $where_cl." id = ".end($errors);
$sql_query = $sql_query.$where_cl;

$error_reg = $DB->get_records_sql($sql_query);

echo("Se encontraron errores en los siguientes registros: \n");

foreach($error_reg as $error){
    echo($error->username." ".$error->firstname." ".$error->lastname."////");
}


