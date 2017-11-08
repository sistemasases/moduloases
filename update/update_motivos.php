<?php

require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

for($i = 10; $i < 18; $i++){
    $sql_query = "DELETE FROM {talentospilos_motivos} WHERE id = $i";
    $DB->execute($sql_query);
}

for($i = 19; $i < 27; $i++){
    $sql_query = "DELETE FROM {talentospilos_motivos} WHERE id = $i";
    $DB->execute($sql_query);
}

for($i = 28; $i < 36; $i++){
    $sql_query = "DELETE FROM {talentospilos_motivos} WHERE id = $i";
    $DB->execute($sql_query);
}