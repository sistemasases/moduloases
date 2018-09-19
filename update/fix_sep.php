<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Modifica registros en la tabla {talentospilos_user_extended}
   cuando :
   El id_ases_user este repetido y en ambos tracking_status este en 1
*/

global $DB;
$table = 'talentospilos_usuario';
print_r($DB->delete_records($table, array('id'=>'7820')));
print_r($DB->delete_records($table, array('id'=>'7742')));