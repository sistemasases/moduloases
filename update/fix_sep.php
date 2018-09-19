<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;
$table = 'talentospilos_usuario';
print_r($DB->delete_records($table, array('id'=>'7820')));
print_r($DB->delete_records($table, array('id'=>'7742')));