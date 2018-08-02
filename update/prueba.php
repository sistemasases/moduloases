<?php

require_once(dirname(__FILE__). '/../../../config.php');
global $DB;

$eventos = $DB->get_records_sql('SELECT json_materias FROM mdl_talentospilos_history_academ ORDER by id DESC LIMIT 10');
foreach ($eventos as $evento) {
    print_r($evento);
    echo "<br>";
    echo "<br>";
    echo "<br>";
}

