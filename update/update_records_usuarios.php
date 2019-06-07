<?php
require_once(dirname(__FILE__). '/../../../config.php');

/* 
    Modifica registros en la tabla {talentospilos_usuario}
    Elimina registros en la tabla {talentospilos_user_extended}
 */

global $DB;

//Registros a modificar

$record_to_update = new StdClass;
$record_to_update->id = 10052;
$record_to_update->num_doc = '9999999929';
$record_to_update->num_doc_ini = '9999999929';

$DB->update_record('talentospilos_usuario', $record_to_update);

$record_to_update1 = new StdClass;
$record_to_update1->id = 10053;
$record_to_update1->num_doc = '9999999919';
$record_to_update1->num_doc_ini = '9999999919';

$DB->update_record('talentospilos_usuario', $record_to_update1);

$record_to_update2 = new StdClass;
$record_to_update2->id = 10054;
$record_to_update2->num_doc = '9999999909';
$record_to_update2->num_doc_ini = '9999999909';

$DB->update_record('talentospilos_usuario', $record_to_update2);

$updated_record = $DB->get_record(10052);
$updated_record1 = $DB->get_record(10053);
$updated_record2 = $DB->get_record(10054);

if($updated_record->num_doc == '9999999929' && $updated_record1 == '9999999919' && $updated_record2 == '9999999909'){
    echo 'Registros actualizados con éxito';
}else{
    echo 'Error';
}

//Registros a eliminar

$object_to_delete = array();
$object_to_delete['id'] = 7910;

$DB->delete_records('talentospilos_user_extended', $object_to_delete);

$object_to_delete1 = array();
$object_to_delete1['id'] = 7911;

$DB->delete_records('talentospilos_user_extended', $object_to_delete1);

$object_to_delete2 = array();
$object_to_delete2['id'] = 7912;

$DB->delete_records('talentospilos_user_extended', $object_to_delete2);

$deleted_records_array = $DB->get_records_sql('SELECT id FROM {talentospilos_user_extended} WHERE
                                                id=7910 OR id=7911 OR id=7912');
if(is_null($deleted_records_array)){
    echo 'Registros actualizados con éxito';
}else{
    echo 'Error';
}