<?php

require_once dirname(__FILE__) . '/../../../../config.php';

global $DB;

function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}


$user = $DB->get_record_sql($sql_query);
console_log($user);

