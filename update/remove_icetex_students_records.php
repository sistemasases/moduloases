<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Modifica registros en la tabla {talentospilos_res_estudiante}
 */

global $DB;

$object_to_delete1 = array();
$object_to_delete1['id'] = 112;
$DB->delete_records('talentospilos_res_estudiante', $object_to_delete1);

$object_to_delete2 = array();
$object_to_delete2['id'] = 503;
$DB->delete_records('talentospilos_res_estudiante', $object_to_delete2);