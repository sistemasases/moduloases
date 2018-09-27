<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;
$table = 'talentospilos_usuario';
/*print_r($DB->delete_records($table, array('id'=>'7820')));
print_r($DB->delete_records($table, array('id'=>'7742')));*/

//echo "Script obsoleto.";
//UPDATE mdl_talentospilos_user_extended SET id_ases_user = 7446, tracking_status = 0 WHERE id = 531
echo "Datos previos...<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 531");
print_r( json_encode($record) );

$record->tracking_status = 0;
$record->id_ases_user = 7446;

try {
    echo "<br><br>Actualizando...<br>";
    print_r( $DB->update_record( 'talentospilos_user_extended', $record ) . "<br>" );
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "<br><br>";
}

try {
    echo "Eliminando...<br>";
    print_r( $DB->delete_records( 'talentospilos_usuario', array( 'id'=>'837' ) ) );
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "<br><br>";
}

echo "Resultado:<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 531");
print_r( json_encode($record) . "<br><br>" );

// --------------

echo "Datos previos...<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id_moodle_user = 115914");
print_r( json_encode($record) );

$record->tracking_status = 0;
$record->id_moodle_user = 93335;
$record->id_academic_program = 60;

try {
    echo "<br><br>Actualizando...<br>";
    print_r( $DB->update_record( 'talentospilos_user_extended', $record ) . "<br>" );
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "<br><br>";
}

echo "Resultado:<br>";
$records = $DB->get_records_sql("SELECT * FROM {talentospilos_user_extended} WHERE id_moodle_user = 93335");
print_r( json_encode($records) );


die();