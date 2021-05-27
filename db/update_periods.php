<?php
/**
 * Este script debería correrse después o antes de la actualización.
 * Le añade la instancia 450299 (Ases cali) a los semestres desde 2020A hacia abajo.
 * Esto se hace para poder acceder al modulo por primera vez con periodos instanciados
 * ya que el plugin una de las primeras cosas que hace es traer el semestre actual.
 *
 * Los semestres para regionales se añaden desde la interfaz del modulo.
 * @author David S. Cortés
 */
define('CLI_SCRIPT', true);
require_once(dirname(__FILE__).'/../../../config.php');
global $DB;
$sql = 
    "UPDATE mdl_talentospilos_semestre
    SET id_instancia=$1
    WHERE fecha_inicio >=$2";

$params = [450299,'2020-08-14'];

$DB->execute($sql, $params);
