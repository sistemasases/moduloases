<?php

require_once(dirname(__FILE__). '/../../../config.php');

/**
 * Script para registrar nuevos estados de programa
 */

global $DB;

$new_estado1= new StdClass;
$new_estado2= new StdClass;

$new_estado1->nombre = "REGULARIZADO";
$new_estado2->nombre = "ADMISIÓN";

$new_estado1->descripcion = "El estudiante finalizó el programa Talentos y se regularizó en un nuevo programa";
$new_estado2->descripcion = "El estudiante no se regularizó a traves del programa Talentos pero entró por Admision a un nuevo programa";

$validation1 = "SELECT * FROM {talentospilos_estad_programa} WHERE nombre = 'REGULARIZADO' LIMIT 1";
$validation2 = "SELECT * FROM {talentospilos_estad_programa} WHERE nombre = 'ADMISIÓN' LIMIT 1";

$result1 = $DB->get_record_sql($validation1);
$result2 = $DB->get_record_sql($validation2);

if(!$result1){
    $insert1 = $DB->insert_record('talentospilos_estad_programa', $new_estado1, true);
}

if(!$result2){
    $insert2 = $DB->insert_record('talentospilos_estad_programa', $new_estado2, true);
}
