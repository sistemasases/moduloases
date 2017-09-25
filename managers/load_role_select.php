<<<<<<< HEAD
<?php

/**
 * Carga los datos de los roles para ubicarlos en un HTML select
 * @return array con los datos de los roles codificado en JSON
 * @author Edgar Mauricio Ceron Florez
 */ 
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "SELECT id, nombre_rol FROM {talentospilos_rol}";
$result = $DB->get_records_sql($sql_query);
=======
<?php

/**
 * Carga los datos de los roles para ubicarlos en un HTML select
 * @return array con los datos de los roles codificado en JSON
 * @author Edgar Mauricio Ceron Florez
 */ 
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "SELECT id, nombre_rol FROM {talentospilos_rol}";
$result = $DB->get_records_sql($sql_query);
>>>>>>> db_management
echo json_encode($result);