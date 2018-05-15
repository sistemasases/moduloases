<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$registro_modificar = new StdClass;
$registro_modificar->id = 610;
$registro_modificar->id_estudiante = 409;

$DB->update_record('talentospilos_res_estudiante', $registro_modificar);

$registro_modificar2 = new StdClass;
$registro_modificar2->id = 927;
$registro_modificar2->id_estudiante = 409;

$DB->update_record('talentospilos_res_estudiante', $registro_modificar2);

$registro_modificar3 = new StdClass;
$registro_modificar3->id = 1010;
$registro_modificar3->id_estudiante = 409;

$DB->update_record('talentospilos_res_estudiante', $registro_modificar3);