<?php
require_once('tracing_functions.php');
require_once('../student_profile/studentprofile_lib.php');
require_once('../periods_management/periods_lib.php');

global $USER;

if(isset($_POST['type'])&&$_POST['type']=="getInfo"&&isset($_POST['instance'])) 
 {
    $datos=[];
    $datos["id"]=$USER->id;
    $datos["username"]=$USER->username;
    $datos["email"]=$USER->email;
    $datos["rol"]=get_id_rol($USER->id,$_POST['instance']);
    $datos["name_rol"]=get_name_rol($datos["rol"]);

    echo json_encode($datos);
}
if(isset($_POST['type'])&&$_POST['type']=="actualizar_personas"&&isset($_POST['id'])&&isset($_POST['instance'])) 
 {
    $roles = get_rol_ps();
    $retorno = get_people_onsemester($_POST['id'],$roles,$_POST['instance']);
    foreach($retorno as $person){
        $table.='<option value="'.$person->id_usuario.'">'.$person->username." - ".$person->firstname." ".$person->lastname.'</option>';
     }
    echo $table;

}
if(isset($_POST['type'])&&$_POST['type']=="consulta_sistemas"&&isset($_POST['id_persona'])&&isset($_POST['id_semestre'])&&isset($_POST['instance'])) 
 {
    $globalArregloPares = [];
    $globalArregloGrupal =[];
    $fechas = [];

    $intervalos =get_semester_interval($_POST['id_semestre']);
    $fechas[0] = $intervalos->fecha_inicio;
    $fechas[1] = $intervalos->fecha_fin;
    $fechas[2] = $intervalos->id;

    $retorno = get_users_rols($_POST['id_persona'],$_POST['id_semestre'],$_POST['instance']);
    if(empty($retorno)){
      $html="No tiene registros en ese periodo";
    }else{
    $usernamerole= get_name_rol($retorno->id_rol);
    if($usernamerole == 'monitor_ps'){
       $html = monitorUser($globalArregloPares,$globalArregloGrupal,$_POST['id_persona'],0,$_POST['instance'],$retorno->id_rol,$fechas,true);

    }else if ($usernamerole == 'practicante_ps'){
       $html=practicanteUser($globalArregloPares,$globalArregloGrupal,$_POST['id_persona'],$_POST['instance'],$retorno->id_rol,$fechas,true);

    }else if ($usernamerole == 'profesional_ps'){
       $html=profesionalUser($globalArregloPares,$globalArregloGrupal,$_POST['id_persona'],$_POST['instance'],$retorno->id_rol,$fechas,true);

    }

    echo $html;
}}

if(isset($_POST['type'])&&$_POST['type']=="info_monitor"&&isset($_POST['id'])&&isset($_POST['instance'])) 
 {
  
    $retorno = get_seguimientos_monitor($_POST['id'],$_POST['instance']);
    echo (json_encode($retorno));
}

if(isset($_POST['type'])&&$_POST['type']=="eliminar_registro"&&isset($_POST['id'])) 
 {
  
   $retorno = delete_tracking_peer($_POST['id']);
       echo (json_encode($retorno));

   
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


?>
