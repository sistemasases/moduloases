<?php
define('CLI_SCRIPT', true);
require_once(dirname(__FILE__).'/../../../config.php');
global $DB;
global $CFG;
/***********************************************************
 * Script para corregir la versión del plugin en producción.
 * Una vez se confirme que está corregida debe eliminarse
 * David S. Cortés
 */
$object = new stdClass();
$object->id = 1659;
$object->value = 2021032313380;
try {
    $DB->update_record('config_plugins', $object);
} catch(Exception $ex) {
    throw Exception($ex->getMessage());
}
