<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

//Modificación de registro fantasma en talentospilos_usuario
$usuario_record_2 = new StdClass;
$usuario_record_2->id = 6764;
$usuario_record_2->num_doc = '9999999929';
$usuario_record_2->num_doc_ini = '9999999929';
$DB->update_record('talentospilos_usuario', $usuario_record_2);