<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Modifica registros en la tabla {talentospilos_user_extended}
cuando tracking status este en 0 lo modifica a 1
 */

global $DB;

$registro_modificar = new StdClass;
$registro_modificar->id = 5019;
$registro_modificar->program_status = 1;
$registro_modificar->tracking_status = 1;

$DB->update_record('talentospilos_user_extended', $registro_modificar);

$registro_modificar2 = new StdClass;
$registro_modificar2->id = 114;
$registro_modificar2->program_status = 7;

$DB->update_record('talentospilos_user_extended', $registro_modificar2);

$registro_modificar3 = new StdClass;
$registro_modificar3->id = 154;
$registro_modificar3->program_status = 6;

$DB->update_record('talentospilos_user_extended', $registro_modificar3);

$registro_modificar4 = new StdClass;
$registro_modificar4->id = 5021;
$registro_modificar4->program_status = 1;
$registro_modificar4->tracking_status = 1;

$DB->update_record('talentospilos_user_extended', $registro_modificar4);

$registro_modificar5 = new StdClass;
$registro_modificar5->id = 157;
$registro_modificar5->program_status = 6;

$DB->update_record('talentospilos_user_extended', $registro_modificar5);

$registro_modificar6 = new StdClass;
$registro_modificar6->id = 5022;
$registro_modificar6->program_status = 1;
$registro_modificar6->tracking_status = 1;

$DB->update_record('talentospilos_user_extended', $registro_modificar6);

// $delete = $DB->execute("DELETE FROM {talentospilos_user_extended} WHERE id = 1066");
$object_to_delete = array();
$object_to_delete['id'] = 553;
$DB->delete_records('talentospilos_user_extended',$object_to_delete);

$object_to_delete2 = array();
$object_to_delete2['id'] = 1052;
$DB->delete_records('talentospilos_user_extended',$object_to_delete2);

$object_to_delete3 = array();
$object_to_delete3['id'] = 1059;
$DB->delete_records('talentospilos_user_extended',$object_to_delete3);
