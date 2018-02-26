<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Modifica registros en la tabla {talentospilos_user_extended}
   cuando :
   El id_ases_user este repetido y en ambos tracking_status este en 1
*/

global $DB;

$sql_query = "UPDATE {talentospilos_user_extended} SET tracking_status = 1 WHERE tracking_status=0";
$success = $DB->execute($sql_query);

echo $success;

