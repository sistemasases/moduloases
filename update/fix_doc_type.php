<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Inserta y actualiza los campos realcioandos con el tipo de documento
      
*/

global $DB;

$record = new stdClass();
$result_cc = 0;
$result_ti = 0;
$result_cr = 0;

$record->nombre = "TI";
$record->descripcion = "Tarjeta de identidad";
$result_ti = $DB->insert_record("talentospilos_tipo_documento", $record, true);

if($result_ti){
    print_r("TI ---> ok");
}else{
    print_r("TI ---> failed");
}

$record->nombre = "CC";
$record->descripcion = "Cedula de ciudadanía";
$result_cc = $DB->insert_record("talentospilos_tipo_documento", $record, true);

if($result_cc){
    print_r("CC ---> ok");
}else{
    print_r("CC ---> failed");
}

$record->nombre = "CR";
$record->descripcion = "Contraseña";
$result_cr = $DB->insert_record("talentospilos_tipo_documento",  $record, true);

if($result_cr){
    print_r("CR ---> ok");
}else{
    print_r("CR ---> failed");
}

if($result_cr && $result_cc && $result_ti){
    
}else{
    print_r("Transacción abortada");
}

    





