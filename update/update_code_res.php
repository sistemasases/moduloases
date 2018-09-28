<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$record_to_update = new StdClass;
$record_to_update->id = 43;
$record_to_update->id_semestre = 5;

$DB->update_record('talentospilos_res_icetex', $record_to_update);