<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Modifica registros en la tabla {talentospilos_user_extended}
 */

global $DB;

$registro_modificar1 = new StdClass;
$registro_modificar1->id = 43;
$registro_modificar1->id_semestre = 6;

echo $DB->update_record('talentospilos_res_icetex', $registro_modificar1);

echo '<br>';

$registro_modificar2 = new StdClass;
$registro_modificar2->id = 5034;
$registro_modificar2->program_status = 1;
$registro_modificar2->tracking_status = 1;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar2);

echo '<br>';

$registro_modificar3 = new StdClass;
$registro_modificar3->id = 5033;
$registro_modificar3->tracking_status = 1;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar3);

echo '<br>';

$registro_modificar4 = new StdClass;
$registro_modificar4->id = 436;
$registro_modificar4->id_ases_user = 95;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar4);

echo '<br>';
$sql_query = "UPDATE {talentospilos_history_academ} SET id_estudiante = 95 WHERE id_estudiante = 897";
$success = $DB->execute($sql_query);

echo $success;
echo '<br>';
$sql_query2 = "UPDATE {talentospilos_demografia} SET id_usuario = 95 WHERE id_usuario = 897";
$success = $DB->execute($sql_query2);

echo $success;
echo '<br>';
$sql_query3 = "UPDATE {talentospilos_seg_estudiante} SET id_estudiante = 95 WHERE id_estudiante = 897";
$success = $DB->execute($sql_query3);

echo $success;
echo '<br>';
$sql_query4 = "UPDATE {talentospilos_riesg_usuario} SET id_usuario = 95 WHERE id_usuario = 897";
$success = $DB->execute($sql_query4);

echo $success;
echo '<br>';
$sql_query5 = "UPDATE {talentospilos_res_estudiante} SET id_estudiante = 95 WHERE id_estudiante = 897";
$success = $DB->execute($sql_query5);

echo $success;
echo '<br>';

$registro_modificar5 = new StdClass;
$registro_modificar5->id = 95;
$registro_modificar5->num_doc = '1112792607';

echo $DB->update_record('talentospilos_usuario', $registro_modificar5);
echo '<br>';
$registro_modificar6 = new StdClass;
$registro_modificar6->id = 447;
$registro_modificar6->id_ases_user = 103;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar6);

echo '<br>';

$sql_query = "UPDATE {talentospilos_history_academ} SET id_estudiante = 103 WHERE id_estudiante = 898";
$success = $DB->execute($sql_query);

echo $success;
echo '<br>';
$sql_query2 = "UPDATE {talentospilos_demografia} SET id_usuario = 103 WHERE id_usuario = 898";
$success = $DB->execute($sql_query2);

echo $success;
echo '<br>';
$sql_query3 = "UPDATE {talentospilos_seg_estudiante} SET id_estudiante = 103 WHERE id_estudiante = 898";
$success = $DB->execute($sql_query3);

echo $success;
echo '<br>';
$sql_query4 = "UPDATE {talentospilos_riesg_usuario} SET id_usuario = 103 WHERE id_usuario = 898";
$success = $DB->execute($sql_query4);

echo $success;
echo '<br>';
$sql_query5 = "UPDATE {talentospilos_res_estudiante} SET id_estudiante = 103 WHERE id_estudiante = 898";
$success = $DB->execute($sql_query5);

echo $success;
echo '<br>';
$registro_modificar7 = new StdClass;
$registro_modificar7->id = 103;
$registro_modificar7->num_doc = '1124865827';

echo $DB->update_record('talentospilos_usuario', $registro_modificar7);
echo '<br>';

$registro_modificar8 = new StdClass;
$registro_modificar8->id = 952;
$registro_modificar8->id_ases_user = 119;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar8);

echo '<br>';

$sql_query = "UPDATE {talentospilos_history_academ} SET id_estudiante = 119 WHERE id_estudiante = 899";
$success = $DB->execute($sql_query);

echo $success;
echo '<br>';
$sql_query2 = "UPDATE {talentospilos_demografia} SET id_usuario = 119 WHERE id_usuario = 899";
$success = $DB->execute($sql_query2);

echo $success;
echo '<br>';
$sql_query3 = "UPDATE {talentospilos_seg_estudiante} SET id_estudiante = 119 WHERE id_estudiante = 899";
$success = $DB->execute($sql_query3);

echo $success;
echo '<br>';
$sql_query4 = "UPDATE {talentospilos_riesg_usuario} SET id_usuario = 119 WHERE id_usuario = 899";
$success = $DB->execute($sql_query4);

echo $success;
echo '<br>';
$sql_query5 = "UPDATE {talentospilos_res_estudiante} SET id_estudiante = 119 WHERE id_estudiante = 899";
$success = $DB->execute($sql_query5);

echo $success;
echo '<br>';
$registro_modificar9 = new StdClass;
$registro_modificar9->id = 119;
$registro_modificar9->num_doc = '1107521592';

echo $DB->update_record('talentospilos_usuario', $registro_modificar9);
echo '<br>';

echo '<br>';
$registro_modificar10 = new StdClass;
$registro_modificar10->id = 1064;
$registro_modificar10->id_ases_user = 130;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar10);

echo '<br>';

$sql_query = "UPDATE {talentospilos_history_academ} SET id_estudiante = 130 WHERE id_estudiante = 900";
$success = $DB->execute($sql_query);

echo $success;
echo '<br>';
$sql_query2 = "UPDATE {talentospilos_demografia} SET id_usuario = 130 WHERE id_usuario = 900";
$success = $DB->execute($sql_query2);

echo $success;
echo '<br>';
$sql_query3 = "UPDATE {talentospilos_seg_estudiante} SET id_estudiante = 130 WHERE id_estudiante = 900";
$success = $DB->execute($sql_query3);

echo $success;
echo '<br>';
$sql_query4 = "UPDATE {talentospilos_riesg_usuario} SET id_usuario = 130 WHERE id_usuario = 900";
$success = $DB->execute($sql_query4);

echo $success;
echo '<br>';
$sql_query5 = "UPDATE {talentospilos_res_estudiante} SET id_estudiante = 130 WHERE id_estudiante = 900";
$success = $DB->execute($sql_query5);

echo $success;
echo '<br>';

$registro_modificar11 = new StdClass;
$registro_modificar11->id = 5030;
$registro_modificar11->tracking_status = 0;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar11);

echo '<br>';

$registro_modificar12 = new StdClass;
$registro_modificar12->id = 675;
$registro_modificar12->id_ases_user = 5085;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar12);

echo '<br>';

$sql_query = "UPDATE {talentospilos_history_academ} SET id_estudiante = 5085 WHERE id_estudiante = 701";
$success = $DB->execute($sql_query);

echo $success;
echo '<br>';
$sql_query2 = "UPDATE {talentospilos_demografia} SET id_usuario = 5085 WHERE id_usuario = 701";
$success = $DB->execute($sql_query2);

echo $success;
echo '<br>';
$sql_query3 = "UPDATE {talentospilos_seg_estudiante} SET id_estudiante = 5085 WHERE id_estudiante = 701";
$success = $DB->execute($sql_query3);

echo $success;
echo '<br>';
$sql_query4 = "UPDATE {talentospilos_riesg_usuario} SET id_usuario = 5085 WHERE id_usuario = 701";
$success = $DB->execute($sql_query4);

echo $success;
echo '<br>';
$sql_query5 = "UPDATE {talentospilos_res_estudiante} SET id_estudiante = 5085 WHERE id_estudiante = 701";
$success = $DB->execute($sql_query5);

echo $success;
echo '<br>';
// $delete = echo $DB->execute("DELETE FROM {talentospilos_user_extended} WHERE id = 1066");

$object_to_delete1 = array();
$object_to_delete1['id'] = 4931;
echo $DB->delete_records('talentospilos_user_extended',$object_to_delete1);
echo '<br>';
$object_to_delete2 = array();
$object_to_delete2['id'] = 1289;
echo $DB->delete_records('talentospilos_user_extended',$object_to_delete2);
echo '<br>';
$object_to_delete3 = array();
$object_to_delete3['id'] = 1060;
echo $DB->delete_records('talentospilos_user_extended',$object_to_delete3);
echo '<br>';
$object_to_delete4 = array();
$object_to_delete4['id'] = 1004;
echo $DB->delete_records('talentospilos_user_extended',$object_to_delete4);
echo '<br>';
$object_to_delete5 = array();
$object_to_delete5['id'] = 1291;
echo $DB->delete_records('talentospilos_user_extended',$object_to_delete5);
echo '<br>';
$object_to_delete6 = array();
$object_to_delete6['id'] = 1050;
echo $DB->delete_records('talentospilos_user_extended',$object_to_delete6);
echo '<br>';

$object_to_delete7 = array();
$object_to_delete7['id'] = 897;
echo $DB->delete_records('talentospilos_user_extended',$object_to_delete7);
echo '<br>';

$object_to_delete8 = array();
$object_to_delete8['id'] = 1040;
echo $DB->delete_records('talentospilos_user_extended',$object_to_delete8);
echo '<br>';

$object_to_delete9 = array();
$object_to_delete9['id'] = 1041;
echo $DB->delete_records('talentospilos_user_extended',$object_to_delete9);
echo '<br>';

$object_to_delete10 = array();
$object_to_delete10['id'] = 1056;
echo $DB->delete_records('talentospilos_user_extended',$object_to_delete10);
echo '<br>';