<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Modifica registros en la tabla {talentospilos_user_extended}
 */

global $DB;


$registro_modificar1 = new StdClass;
$registro_modificar1->id = 675;
$registro_modificar1->tracking_status = 0;

echo $DB->update_record('talentospilos_user_extended', $registro_modificar1);

echo '<br>';
