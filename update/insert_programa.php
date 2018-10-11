<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$new_program = new stdClass();
$new_program->codigosnies = 104614;
$new_program->cod_univalle = 2722;
$new_program->nombre = "TECNOLOGIA EN MANTENIMIENTO DE SISTEMAS ELECTROMECANICOS";
$new_program->id_sede = 7;
$new_program->jornada = "NOCTURNA";
$new_program->id_facultad = 8;

if($DB->insert_record("talentospilos_programa", $new_program, true)){
    echo "Éxito!";
};
 echo "Nada pendiente."

?>