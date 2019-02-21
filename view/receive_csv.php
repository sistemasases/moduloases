<?php

error_reporting(E_ALL | E_STRICT);   // NOT FOR PRODUCTION SERVERS!
ini_set('display_errors', '1');         // NOT FOR PRODUCTION SERVERS!
require_once(__DIR__. '/../classes/ExternInfo/EstadoAsesEIManager.php');
require_once(__DIR__. '/../classes/ExternInfo/condicion_exepcion/CondicionExcepcionEIManager.php');
require_once(__DIR__. '/../classes/ExternInfo/historial_academico/HistorialAcademicoEIManager.php');

require_once(__DIR__. '/../classes/API/BaseAPI.php');
require_once(__DIR__.'/../managers/mass_management/endpoints.php');
$api = new BaseAPI();
$ases_user_endpoint = \mass_management\endpoints\UPLOAD_ASES_USERS;
$cond_exepcion_endpoint = \mass_management\endpoints\UPDATE_COND_EXEPCION;
$history_academic = \mass_management\endpoints\UPDATE_ACADEMIC_HISTORY;
$api->post("$ases_user_endpoint/:cohort_id/:instance_id", function($args, $data) {
    $estado_ases_csv_manager = new EstadoAsesEIManager($data['cohort_id'], $data['instance_id']);
    $estado_ases_csv_manager->execute();
});
$api->post("$cond_exepcion_endpoint/:cohort_id/:instance_id", function($args, $data) {
    $cond_excepcion_manager = new CondicionExcepcionEIManager();
    $cond_excepcion_manager->execute();
});
$api->post("$history_academic/:cohort_id/:instance_id", function($args, $data) {
    $history_academ_manager = new HistorialAcademicoEIManager();
    $history_academ_manager->execute();
});
$api->run();

?>