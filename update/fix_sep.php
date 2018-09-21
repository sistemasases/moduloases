<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;
$table = 'talentospilos_usuario';
/*print_r($DB->delete_records($table, array('id'=>'7820')));
print_r($DB->delete_records($table, array('id'=>'7742')));*/

//echo "Script obsoleto.";
//UPDATE mdl_talentospilos_user_extended SET id_ases_user = 7446, tracking_status = 0 WHERE id = 531

$record = $DB->get_record_sql("SELECT * FROM {talentospilos_user_extended} WHERE id = 531");
$record->tracking_status = 0;
$record->id_ases_user = 7446;
print_r( $DB->update_record( 'talentospilos_usuario', $record ) );
print_r( $DB->delete_records( 'talentospilos_usuario', array( 'id'=>'837' ) ) );

die();