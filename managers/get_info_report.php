<?php
require('query.php');

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

if(isset($_POST['type'])&&$_POST['type']=="getRol"&&isset($_POST['instance'])&&isset($_POST['id'])) 
 { 
  $retorno = get_name_rol($_POST['id'],$_POST['instance']);
   echo($retorno);
}

if(isset($_POST['type'])&&$_POST['type']=="info_monitor"&&isset($_POST['id'])&&isset($_POST['instance'])) 
 {
  
    $retorno = get_seguimientos_monitor($_POST['id'],$_POST['instance']);
    echo (json_encode($retorno));
}

if(isset($_POST['type'])&&$_POST['type']=="eliminar_registro"&&isset($_POST['id'])) 
 {
  
   $retorno = eliminar_registro($_POST['id']);
   echo $retorno;
   
}

if(isset($_POST['type'])&&$_POST['type']=="actualizar_registro") 
 {
  $objeto =(object)$_POST['seguimiento'];
  $retorno = updateSeguimiento_pares($objeto);
  echo $retorno;
  
 }

if(isset($_POST['type'])&&$_POST['type']=="number_seg_monitor"&&isset($_POST['id'])&&isset($_POST['instance'])) 
 {
    $retorno = get_cantidad_seguimientos_monitor($_POST['id'],$_POST['instance']);
    echo (json_encode($retorno));
}

if(isset($_POST['type'])&&$_POST['type']=="info_practicante"&&isset($_POST['id'])) 
 {
    $retorno = get_monitores_practicante($_POST['id']);
    echo (json_encode($retorno));
}

if(isset($_POST['type'])&&$_POST['type']=="info_profesional"&&isset($_POST['id'])&&isset($_POST['instance'])) 
 {
    $retorno = get_practicantes_profesional($_POST['id'],$_POST['instance']);
    echo (json_encode($retorno));
}

if(isset($_POST['type'])&&$_POST['type']=="getProfesional"&&isset($_POST['instance'])&&isset($_POST['id'])) 
 { 
   $retorno = get_profesional_practicante($_POST['id'],$_POST['instance']);
   echo($retorno);
}


if(isset($_POST['type'])&&$_POST['type']=="send_email_to_user"&&isset($_POST['message'])&&isset($_POST['tipoSeg'])&&isset($_POST['codigoEnviarN1'])&&isset($_POST['codigoEnviarN2'])&&isset($_POST['fecha'])&&isset($_POST['nombre'])) 
{
 echo send_email_to_user($_POST['tipoSeg'],$_POST['codigoEnviarN1'],$_POST['codigoEnviarN2'],$_POST['fecha'],$_POST['nombre'],$_POST['message']);
}


//funcion para enviar un mensaje al monitor que se desea hacer la obsrvacion
function send_email_to_user($tipoSeg,$codigoEnviarN1,$codigoEnviarN2,$fecha,$nombre,$messageText){

    global $USER;
    $emailToUser = new stdClass;
    $emailFromUser = new stdClass;

    $sending_user = get_full_user($USER->id);
    $receiving_user = get_full_user($codigoEnviarN1);
    
    $monitor = get_full_user($codigoEnviarN1);
    $name_monitor=$monitor->firstname;
    $name_monitor.=" ";
    $name_monitor.=$monitor->lastname;
    $name_prof = $sending_user->firstname." ".$sending_user->lastname;
    
    $emailToUser->email = $receiving_user->email;
    $emailToUser->firstname = $receiving_user->firstname;
    $emailToUser->lastname = $receiving_user->lastname;
    $emailToUser->maildisplay = true;
    $emailToUser->mailformat = 1;
    $emailToUser->id = $receiving_user->id; 
    $emailToUser->alternatename = '';
    $emailToUser->middlename = '';
    $emailToUser->firstnamephonetic = '';
    $emailToUser->lastnamephonetic = '';

    $emailFromUser->email = $sending_user->email;
    $emailFromUser->firstname = $sending_user->firstname;
    $emailFromUser->lastname = $sending_user->lastname;
    $emailFromUser->maildisplay = false;
    $emailFromUser->mailformat = 1;
    $emailFromUser->id = $sending_user->id; 
    $emailFromUser->alternatename = '';
    $emailFromUser->middlename = '';
    $emailFromUser->firstnamephonetic = '';
    $emailFromUser->lastnamephonetic = '';
    
    if($tipoSeg=="individual")
    {
      $subject = "Observaciones seguimiento del dia $fecha del estudiante $nombre"; 
    }else
    {
      $subject = "Observaciones seguimiento del dia $fecha de los estudiantes $nombre";
    }
    
    
    $messageHtml.="<b>OBSERVACION:<b><br><br>";
    $messageHtml.="Estimado monitor $name_monitor<br><br>";
    
    
    if($tipoSeg=="individual")
    {
      $messageHtml.="Revisando el seguimiento realizado al estudiante $nombre  el dia $fecha, mis comentarios son los siguientes:<br><br>";
    }else
    {
      $messageHtml.="Revisando el seguimiento realizado a los estudiantes $nombre  el dia $fecha, mis comentarios son los siguientes:<br><br>";
    }
    
    $messageHtml.=$messageText."<br><br>";
    $messageHtml.="Cordialmente<br>";
    $messageHtml.="$name_prof";
    echo $messageHtml;
    
    $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
    if($email_result!=1)
    {
     return $email_result;
    }else{
     
      $email_result=0;
      //************************************************************************************************************
      //************************************************************************************************************
      //AHORA SE REENVIA EL CORREO AL PROFESIONAL
      //************************************************************************************************************
      //************************************************************************************************************
    
      $receiving_user = get_full_user($USER->id);
      $emailToUser->email = $receiving_user->email;
      $emailToUser->firstname = $receiving_user->firstname;
      $emailToUser->lastname = $receiving_user->lastname;
      $emailToUser->maildisplay = true;
      $emailToUser->mailformat = 1;
      $emailToUser->id = $receiving_user->id; 
      $emailToUser->alternatename = '';
      $emailToUser->middlename = '';
      $emailToUser->firstnamephonetic = '';
      $emailToUser->lastnamephonetic = '';
      
      $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
      if($email_result!=1)
      {
      return $email_result;
      }else{
       
       $email_result=0;
      //************************************************************************************************************
      //************************************************************************************************************
      //SE ENVIA EL CORREO AL SEGUNDO USUARIO CORRESPONDIENTE
      //************************************************************************************************************
      //************************************************************************************************************
    
      $receiving_user = get_full_user($codigoEnviarN2);
    
      $emailToUser->email = $receiving_user->email;
      $emailToUser->firstname = $receiving_user->firstname;
      $emailToUser->lastname = $receiving_user->lastname;
      $emailToUser->maildisplay = true;
      $emailToUser->mailformat = 1;
      $emailToUser->id = $receiving_user->id; 
      $emailToUser->alternatename = '';
      $emailToUser->middlename = '';
      $emailToUser->firstnamephonetic = '';
      $emailToUser->lastnamephonetic = '';
      
      $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
      return $email_result;
      }
    }
}

// send_email_to_user("individual",1057,1122,"22 Mar 2017","LUIS ESTEBAN PEREA ANGULO","asf");