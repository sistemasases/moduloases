<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 15/02/19
 * Time: 04:28 PM
 */
error_reporting(E_ALL | E_STRICT);   // NOT FOR PRODUCTION SERVERS!
ini_set('display_errors', '1');         // NOT FOR PRODUCTION SERVERS!

require_once (__DIR__ . '/../classes/module.php');

$historico_academico_materias_json_schema = new JsonSchema();
$historico_academico_materias_json_schema->alias = HistorialAcademico::JSON_MATERIAS_SCHEMA_ALIAS;
$historico_academico_materias_json_schema->json_schema = '{"type":"object","title":"Registro de materia en el historico academico","required":["nombre_materia","codigo_materia","creditos","nota","fecha_cancelacion_materia"],"properties":{"nombre_materia":{"type":"string","title":"Nombre materia","default":"","examples":["CÁLCULO I"],"pattern":"^(.*)$"},"codigo_materia":{"type":"string","title":"Codigo materia","default":"","examples":["111050M"],"pattern":"^(.*)$"},"creditos":{"type":"integer","title":"Creditos","default":"","examples":["4"],"minimum":1,"maximum":70},"nota":{"type":"number","title":"The Nota Schema","default":"","examples":["1.0"],"minimum":0,"maximum":5},"fecha_cancelacion_materia":{"type":"string","title":"The Fecha_cancelacion_materia Schema","default":"","examples":[""],"pattern":"","format":"date"}}}';
if(JsonSchema::exists(array(JsonSchema::ALIAS => $historico_academico_materias_json_schema->alias))) {
    echo "Un json schema ya existe con el alias $historico_academico_materias_json_schema->alias";
} else {
    $historico_academico_materias_json_schema->save();
    echo "Se ha guardado el json schema";
}