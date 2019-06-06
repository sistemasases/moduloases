<?php

require_once(dirname(__FILE__). '/../../../config.php');
global $DB;

$new_sex = new stdClass();
$new_sex->sexo  = 'Intersexual';
$new_sex->opcion_general  = 1;

if($DB->insert_record('talentospilos_sexo', $new_sex)){
    echo "Éxito insertando opción".$new_sex->sexo;
}


$new_sex = new stdClass();
$new_sex->sexo  = 'NO REGISTRA';
$new_sex->opcion_general  = 1;

if($DB->insert_record('talentospilos_sexo', $new_sex)){
    echo "Éxito insertando opción".$new_sex->sexo;
}


?>