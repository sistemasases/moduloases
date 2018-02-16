<?php
require_once(dirname(__FILE__). '/../../../../config.php');
require_once('dphpforms_functions.php');


$form_proc = json_decode(file_get_contents("php://input"));


/*if(!$form){
    echo json_encode(
        array(
            'id_formulario' => '-1',
            'mensaje_error' => 'NULL'
        )
    );
    die();
}*/

dphpforms_store_form($form_proc);


?>