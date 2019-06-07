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

if($DB->update_record('talentospilos_usuario', $record_to_update)){
    echo 'Registro actualizado con éxito';
}else{
    echo 'Error';
}

$record_to_update1 = new StdClass;
$record_to_update1->id = 10053;
$record_to_update1->num_doc = '9999999919';
$record_to_update1->num_doc_ini = '9999999919';

if($DB->update_record('talentospilos_usuario', $record_to_update1)){    
    echo 'Registro actualizado con éxito';
}else{
    echo 'Error';
}

$record_to_update2 = new StdClass;
$record_to_update2->id = 10054;
$record_to_update2->num_doc = '9999999909';
$record_to_update2->num_doc_ini = '9999999909';

if($DB->update_record('talentospilos_usuario', $record_to_update2)){
    echo 'Registro actualizado con éxito';
}else{
    echo 'Error';
}

//Registros a eliminar

$object_to_delete = array();
$object_to_delete['id'] = 7910;

if($DB->delete_records('talentospilos_user_extended', $object_to_delete)){
    echo 'Registro actualizado con éxito';
}else{
    echo 'Error';
}

$object_to_delete1 = array();
$object_to_delete1['id'] = 7911;

if($DB->delete_records('talentospilos_user_extended', $object_to_delete1)){
    echo 'Registro actualizado con éxito';
}else{
    echo 'Error';
}

$object_to_delete2 = array();
$object_to_delete2['id'] = 7912;

if($DB->delete_records('talentospilos_user_extended', $object_to_delete2)){
    echo 'Registro actualizado con éxito';
}else{
    echo 'Error';
}