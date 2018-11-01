<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

//Modificación de registro fantasma en talentospilos_usuario
$usuario_record_1 = new StdClass;
$usuario_record_1->id = 6699;
$usuario_record_1->num_doc = '9999999999';
$usuario_record_1->num_doc_ini = '9999999999';
$DB->update_record('talentospilos_usuario', $usuario_record_1);


//Modificación de registro fantasma en talentospilos_usuario
$usuario_record_2 = new StdClass;
$usuario_record_2->id = 6753;
$usuario_record_2->num_doc = '9999999999';
$usuario_record_2->num_doc_ini = '9999999999';
$DB->update_record('talentospilos_usuario', $usuario_record_2);