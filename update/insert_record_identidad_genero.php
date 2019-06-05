<?php

require_once(dirname(__FILE__). '/../../../config.php');
global $DB;

$new_gen = new stdClass();
$new_gen->genero  = 'NO DEFINIDO';
$new_gen->opcion_general  = 1;

if($DB->insert_record('talentospilos_identidad_gen', $new_gen)){
    echo "Éxito";
}


?>