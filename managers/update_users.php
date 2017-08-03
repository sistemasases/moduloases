<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    global $DB;
    
    echo "users";
    
    $DB->insert_record('talentospilos_seg_estudiante',array('id_seguimiento'=>41, 'id_estudiante'=>2));
    
    