<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    
    global $DB;
    $update_estados = "UPDATE {talentospilos_est_estadoases} SET id_estado_ases = 1  WHERE id_estado_ases IS NULL";
  

    echo $DB->execute($update_estados);
