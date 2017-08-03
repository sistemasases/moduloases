<?php
require('seguimientopilos_lib.php');

global $USER;

if(isset($_POST['type'])&&$_POST['type']=="getid") 
 {
    echo($USER->id);
}

if(isset($_POST['type'])&&$_POST['type']=="getName") 
 {
    echo($USER->username);
}

if(isset($_POST['type'])&&$_POST['type']=="getEmail") 
 {
    echo($USER->email);
}

echo "es ".$POST['id'];
if(isset($_POST['type'])&&$_POST['type']=="getRol"&&isset($_POST['instance'])&&isset($_POST['id'])) 
 { 
   $retorno = get_name_rol($_POST['id'],$_POST['instance']);
    echo($retorno);
}


// send_email_to_user("individual",1057,1122,"22 Mar 2017","LUIS ESTEBAN PEREA ANGULO","asf");