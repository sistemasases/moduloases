<?php
require_once dirname(__FILE__) . '/../../../config.php';
global $DB;

// Buscar los estudiantes que pertenecen a más de una instancia
$sql_query = "SELECT * 
              FROM {user} AS user_moodle
              INNER JOIN {talentospilos_user_extended}";