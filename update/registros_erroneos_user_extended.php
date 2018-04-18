<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Modifica registros en la tabla {talentospilos_user_extended}
cuando tracking status este en 0 lo modifica a 1
 */

global $DB;

// $delete = $DB->execute("DELETE FROM {talentospilos_user_extended} WHERE id = 1066");
$object_to_delete = array();
$object_to_delete['id'] = 1066;
$DB->delete_records('talentospilos_user_extended',$object_to_delete);
// print_r($delete);
$register1 = new StdClass;
$register2 = new StdClass;
$register3 = new StdClass;

$register1->id = 389;
$register2->id = 243;
$register3->id = 256;
$register1->id_ases_user = 333;
$register2->id_ases_user = 120;
$register3->id_ases_user = 152;

$DB->update_record('talentospilos_user_extended', $register1);
$DB->update_record('talentospilos_user_extended', $register2);
$DB->update_record('talentospilos_user_extended', $register3);

$new_motivo1= new StdClass;
$new_motivo2= new StdClass;
$new_estado_icetex= new StdClass;
$new_estado_icetex2= new StdClass;
$new_motivo1->descripcion = "Finaliza ciclo básico";
$new_motivo2->descripcion = "No definido";
$new_estado_icetex->nombre = "NO APLICA";
$new_estado_icetex->descripcion = "No aplica, estudiante que no forma parte del icetex.";
$new_estado_icetex2->nombre = "NO REGISTRA";
$new_estado_icetex2->descripcion = "No registra estado icetex.";

$validation1 = "SELECT * FROM {talentospilos_motivos} WHERE descripcion = 'Finaliza ciclo básico' LIMIT 1";
$validation2 = "SELECT * FROM {talentospilos_motivos} WHERE descripcion = 'No definido' LIMIT 1";
$validation3 = "SELECT * FROM {talentospilos_estados_icetex} WHERE nombre = 'NO APLICA' LIMIT 1";
$validation4 = "SELECT * FROM {talentospilos_estados_icetex} WHERE nombre = 'NO REGISTRA' LIMIT 1";

$result1 = $DB->get_record_sql($validation1);
$result2 = $DB->get_record_sql($validation2);
$result3 = $DB->get_record_sql($validation3);
$result4 = $DB->get_record_sql($validation4);

if(!$result1){
    $insert1 = $DB->insert_record('talentospilos_motivos', $new_motivo1, true);
}

if(!$result2){
    $insert2 = $DB->insert_record('talentospilos_motivos', $new_motivo2, true);
}

if(!$result3){
    $insert3 = $DB->insert_record('talentospilos_estados_icetex', $new_estado_icetex, true);
}

if(!$result4){
    $insert4 = $DB->insert_record('talentospilos_estados_icetex', $new_estado_icetex2, true);
}

