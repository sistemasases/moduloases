<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

echo "Datos previos_1...<br>";
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

echo "Resultado:<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 531");
print_r( json_encode($record) . "<br><br><hr>" );

// --------------

echo "Datos previos_2...<br>";
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
print_r( json_encode($records) . "<br><br><hr>" );

// --------------
echo "Datos previos_3...<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 5537");
print_r( json_encode($record) );
$record->id_ases_user = 102;
try {
    echo "<br><br>Actualizando...<br>";
    print_r( $DB->update_record( 'talentospilos_user_extended', $record ) . "<br>" );
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "<br><br>";
}

echo "Resultado:<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 5537");
print_r( json_encode($record) . "<br><br><hr>" );
// --------------

echo "Datos previos_4...<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 531");
print_r( json_encode($record) );
$record->id_ases_user = 7446;
$record->tracking_status = 0;
try {
    echo "<br><br>Actualizando...<br>";
    print_r( $DB->update_record( 'talentospilos_user_extended', $record ) . "<br>" );
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "<br><br>";
}

echo "Resultado:<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 531");
print_r( json_encode($record) . "<br><br><hr>" );

// --------------

echo "Datos previos_5...<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 1092");
print_r( json_encode($record) );
$record->id_moodle_user = 121673;
try {
    echo "<br><br>Actualizando...<br>";
    print_r( $DB->update_record( 'talentospilos_user_extended', $record ) . "<br>" );
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "<br><br><hr>";
}

echo "Resultado:<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 1092");
print_r( json_encode($record) . "<br><br><hr>" );
// --------------

echo "Datos previos_6...<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 1356");
print_r( json_encode($record) );
$record->id_moodle_user = 129898;
try {
    echo "<br><br>Actualizando...<br>";
    print_r( $DB->update_record( 'talentospilos_user_extended', $record ) . "<br>" );
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "<br><br><hr>";
}

echo "Resultado:<br>";
$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 1356");
print_r( json_encode($record) . "<br><br><hr>" );
// --------------


die();