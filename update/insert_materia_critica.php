<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$new_critica = new StdClass;
$new_critica->codigo_materia = "111065M";

if($DB->insert_record("talentospilos_materias_criti", $new_critica, true)){
    echo "Éxito!";
};