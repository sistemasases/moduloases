<?php

require_once dirname(__FILE__) . '/../../../../config.php';

global $DB;

function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';

    // $test = $DB->get_record_sql($sql_query);
    // console_log($test);
}

$data=json_decode(file_get_contents('php://input'),1);
console_log($data);

$input = file_get_contents('php://input');

