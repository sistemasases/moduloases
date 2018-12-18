<?php

error_reporting(E_ALL | E_STRICT);   // NOT FOR PRODUCTION SERVERS!
ini_set('display_errors', '1');         // NOT FOR PRODUCTION SERVERS!
require_once(__DIR__. '/../classes/ExternInfo/EstadoAsesEIManager.php');

require_once(__DIR__.'/../managers/jquery_datatable/jquery_datatable_lib.php');
$estado_ases_csv_manager = new EstadoAsesEIManager();


$estado_ases_csv_manager->execute();

?>