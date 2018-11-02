<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

//Modificación de registro fantasma en talentospilos_usuario
$usuario_record_1 = new StdClass;
$usuario_record_1->id = 6699;
$usuario_record_1->num_doc = '9999999949';
$usuario_record_1->num_doc_ini = '9999999949';
$DB->update_record('talentospilos_usuario', $usuario_record_1);


//Modificación de registro fantasma en talentospilos_usuario
$usuario_record_2 = new StdClass;
$usuario_record_2->id = 6753;
$usuario_record_2->num_doc = '9999999939';
$usuario_record_2->num_doc_ini = '9999999939';
$DB->update_record('talentospilos_usuario', $usuario_record_2);


/**
 * Actualización de registros en tabla talentospilos_res_estudiante 
 */

$icetex_record_1 = new StdClass;
$icetex_record_1->id = 656;
$icetex_record_1->id_estudiante = 88;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_1);

$icetex_record_2 = new StdClass;
$icetex_record_2->id = 658;
$icetex_record_2->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_2);

$icetex_record_3 = new StdClass;
$icetex_record_3->id = 1006;
$icetex_record_3->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_3);

$icetex_record_4 = new StdClass;
$icetex_record_4->id = 859;
$icetex_record_4->id_estudiante = 117;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_4);

$icetex_record_5 = new StdClass;
$icetex_record_5->id = 867;
$icetex_record_5->id_estudiante = 138;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_5);

$icetex_record_6 = new StdClass;
$icetex_record_6->id = 1511;
$icetex_record_6->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_6);

$icetex_record_7 = new StdClass;
$icetex_record_7->id = 1620;
$icetex_record_7->id_estudiante = 88;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_7);

$icetex_record_8 = new StdClass;
$icetex_record_8->id = 1035;
$icetex_record_8->id_estudiante = 150;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_8);

$icetex_record_9 = new StdClass;
$icetex_record_9->id = 1060;
$icetex_record_9->id_estudiante = 138;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_9);

$icetex_record_10 = new StdClass;
$icetex_record_10->id = 1109;
$icetex_record_10->id_estudiante = 173;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_10);

$icetex_record_11 = new StdClass;
$icetex_record_11->id = 1112;
$icetex_record_11->id_estudiante = 117;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_11);

$icetex_record_12 = new StdClass;
$icetex_record_12->id = 1466;
$icetex_record_12->id_estudiante = 117;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_12);

$icetex_record_13 = new StdClass;
$icetex_record_13->id = 97;
$icetex_record_13->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_13);

$icetex_record_14 = new StdClass;
$icetex_record_14->id = 1172;
$icetex_record_14->id_estudiante = 88;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_14);

$icetex_record_15 = new StdClass;
$icetex_record_15->id = 1164;
$icetex_record_15->id_estudiante = 156;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_15);

$icetex_record_16 = new StdClass;
$icetex_record_16->id = 1397;
$icetex_record_16->id_estudiante = 47;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_16);

$icetex_record_17 = new StdClass;
$icetex_record_17->id = 1405;
$icetex_record_17->id_estudiante = 391;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_17);

$icetex_record_18 = new StdClass;
$icetex_record_18->id = 1431;
$icetex_record_18->id_estudiante = 173;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_18);

$icetex_record_19 = new StdClass;
$icetex_record_19->id = 1773;
$icetex_record_19->id_estudiante = 488;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_19);

$icetex_record_20 = new StdClass;
$icetex_record_20->id = 658;
$icetex_record_20->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_20);

$icetex_record_21 = new StdClass;
$icetex_record_21->id = 658;
$icetex_record_21->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_21);

$icetex_record_22 = new StdClass;
$icetex_record_22->id = 658;
$icetex_record_22->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_22);

$icetex_record_23 = new StdClass;
$icetex_record_23->id = 658;
$icetex_record_23->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_23);

$icetex_record_24 = new StdClass;
$icetex_record_24->id = 658;
$icetex_record_24->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_24);

$icetex_record_25 = new StdClass;
$icetex_record_25->id = 658;
$icetex_record_25->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_25);

$icetex_record_26 = new StdClass;
$icetex_record_26->id = 658;
$icetex_record_26->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_26);

$icetex_record_27 = new StdClass;
$icetex_record_27->id = 658;
$icetex_record_27->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_27);

$icetex_record_28 = new StdClass;
$icetex_record_28->id = 658;
$icetex_record_28->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_28);

$icetex_record_29 = new StdClass;
$icetex_record_29->id = 658;
$icetex_record_29->id_estudiante = 91;
$DB->update_record('talentospilos_res_estudiante', $icetex_record_29);

