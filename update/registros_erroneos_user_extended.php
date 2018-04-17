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
$new_motivo1->descripcion = "Finaliza ciclo basico";
$new_motivo2->descripcion = "No definido";
$new_estado_icetex->nombre = "NO APLICA";
$new_estado_icetex->descripcion = "No aplica, estudiante que no forma parte del icetex.";

$insert1 = $DB->insert_record('talentospilos_motivos', $new_motivo1, true);
$insert2 = $DB->insert_record('talentospilos_motivos', $new_motivo2, true);
$insert3 = $DB->insert_record('talentospilos_estados_icetex', $new_estado_icetex, true);
