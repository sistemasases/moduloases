<?php

require_once(dirname(__FILE__). '/../../../config.php');
echo "En desarrollo";
/*header('Content-Type: application/json');
global $DB;

if($_GET['funcion'] == "select_user_ases"){
    $user_id = $_GET['user_id'];
    print_r( json_encode($DB->get_records_sql("SELECT * FROM {talentospilos_usuario} WHERE id = '$user_id'")) );
}

if($_GET['funcion'] == "select_user_moodle"){
    $user_id = $_GET['user_id'];
    print_r( json_encode($DB->get_records_sql("SELECT * FROM {user} WHERE username LIKE '%$user_id%'")) );
}

if($_GET['funcion'] == "select_user_extended"){
    $id = $_GET['id'];
    $id_moodle_user = $_GET['id_moodle_user'];
    $id_ases_user = $_GET['id_ases_user'];
    if( isset( $id ) ){
        print_r( json_encode($DB->get_records_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = $id")) );
    }else if( isset( $id_moodle_user ) ){
        print_r( json_encode($DB->get_records_sql("SELECT * FROM {talentospilos_user_extended} WHERE id_moodle_user = $id_moodle_user")) );
    }else if( isset( $id_ases_user ) ){
        print_r( json_encode($DB->get_records_sql("SELECT * FROM {talentospilos_user_extended} WHERE id_ases_user = $id_ases_user")) );
    }
}

if($_GET['funcion'] == "update_user_extended"){
    $id = $_GET['id'];
    $id_moodle_user = $_GET['id_moodle_user'];
    $id_ases_user = $_GET['id_ases_user'];
    if( isset( $id ) ){
        $record = $DB->get_records_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = $id");

        $record_id_moodle_user = $_GET['record_id_moodle_user'];
        $record_id_ases_user = $_GET['record_id_ases_user'];
        $record_id_academic_program = $_GET['record_id_academic_program'];
        $record_tracking_status = $_GET['record_tracking_status'];
        $record_program_status = $_GET['record_program_status'];

        if(isset( $record_id_moodle_user )){
            $record->id_moodle_user = $record_id_moodle_user;
        }
        if(isset( $record_id_ases_user )){
            $record->id_ases_user = $record_id_ases_user;
        }
        if(isset( $record_id_academic_program )){
            $record->id_academic_program = $record_id_academic_program;
        }
        if(isset( $record_tracking_status )){
            $record->tracking_status = $record_tracking_status;
        }
        if(isset( $record_program_status )){
            $record->program_status = $record_program_status;
        }
        print_r( "Actualizando registro en user_extended: " . $DB->update_record( 'talentospilos_user_extended', $record ) );

    }else if( isset( $id_moodle_user ) ){
        $record = $DB->get_records_sql("SELECT * FROM {talentospilos_user_extended} WHERE id_moodle_user = $id_moodle_user");
        print_r( json_encode($record) );

    }else if( isset( $id_ases_user ) ){
        $record = $DB->get_records_sql("SELECT * FROM {talentospilos_user_extended} WHERE id_ases_user = $id_ases_user");
        print_r( json_encode($record) );
    }
}*/

die();