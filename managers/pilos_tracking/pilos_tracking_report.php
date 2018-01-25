<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Estrategia ASES
 *
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once ('../validate_profile_action.php');
require_once('tracking_functions.php');
require_once('../student_profile/studentprofile_lib.php');
require_once('../periods_management/periods_lib.php');

global $USER;

if(isset($_POST['type'])&&$_POST['type']=="getInfo"&&isset($_POST['instance'])) 
 {
    $datos=[];
    $datos["id"]=$USER->id;
    $datos["username"]=$USER->username;
    $datos["email"]=$USER->email;
    $datos["rol"]=get_id_rol_($USER->id,$_POST['instance']);
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
    $actions = authenticate_user_view($USER->id,$_POST['instance'],'report_trackings');

    $html=show_according_permissions($html,$actions);

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
