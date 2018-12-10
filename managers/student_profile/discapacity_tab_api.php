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
 * Dynamic PHP Forms
 *
 * @author     Juan Pablo Castro
 * @package    block_ases
 * @copyright  2018 Juan Pablo Castro<juan.castro.vasquez@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
 


require_once(dirname(__FILE__). '/../../../../config.php');
require_once(dirname(__FILE__).'/discapacity_tab_lib.php');
require_once(dirname(__FILE__).'/../student_profile/studentprofile_lib.php');
require_once(dirname(__FILE__).'/../../vendor/autoload.php');

use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;

global $DB;
global $USER;


$msg_error = new stdClass();
$msg = new stdClass();

if(isset($_POST['func'])){

    if($_POST['func'] == 'validate_json'){

        $data = $_POST['json'];
        $id_schema = $_POST['id_schema'];
        $schema_db = get_schema($id_schema);
        $data = json_decode($_POST['json']);
        $schema= json_decode($schema_db->json_schema);

        //General  data to logs
        $moodle_user   =  $USER->id;
        $data_prev     =  $_POST['json_prev'];
        $data_env      =  $_POST['json'];
        $eventoid      =  getIdEventLogs('edit_discapacity_initial_form_sp');
        $navegador     =  $_SERVER['HTTP_USER_AGENT'];
        $url           =  $_SERVER['HTTP_REFERER'];
        $id_ases       =  $_POST['ases'];

        // Validate
        $validator = new  Validator;

        $validator->validate($data,  $schema,Constraint::CHECK_MODE_APPLY_DEFAULTS);
            

         if ($validator->isValid()) {
             $data = json_encode($data);
            $result =  save_detalle_discapacidad($data, $id_ases);
            if($result){
                
                $msg->title  = "Éxito";
                $msg->status = "success";
                $msg->msg    = "La información se ha almacenado correctamente.";

               //Particular data to logs
                $data_alm    = $data;

                //Object to log

                $new_log = new stdClass();
                $new_log->id_moodle_user     = $moodle_user;
                $new_log->datos_previos      = $data_prev;
                $new_log->datos_enviados     = $data_env;
                $new_log->datos_almacenados  = $data_alm;
                $new_log->id_evento          = $eventoid->id;
                $new_log->url                = $url;
                $new_log->id_ases_user       = $id_ases;
                $new_log->navegador          = $navegador;
                $new_log->title_retorno      = $msg->title;
                $new_log->msg_retorno        = $msg->status;
                $new_log->status_retorno     = $msg->msg;

                $DB->insert_record('talentospilos_general_logs', $new_log, $returnid=false, $bulk=false);

               echo json_encode($msg);
               
           }else{
               $msg->title = "Error";
               $msg->msg = "No se ha actualizado correctamente.";
               $msg->status = "error";


               //Particular data to logs
               $data_alm    = "";

               //Object to log

               $new_log = new stdClass();
               $new_log->id_moodle_user     = $moodle_user;
               $new_log->datos_previos      = $data_prev;
               $new_log->datos_enviados     = $data_env;
               $new_log->datos_almacenados  = $data_alm;
               $new_log->id_evento          = $eventoid->id;
               $new_log->url                = $url;
               $new_log->id_ases_user       = $id_ases;
               $new_log->navegador          = $navegador;
               $new_log->title_retorno      = $msg->title;
               $new_log->msg_retorno        = $msg->status;
               $new_log->status_retorno     = $msg->msg;

               $DB->insert_record('talentospilos_general_logs', $new_log, $returnid=false, $bulk=false);

               echo json_encode($msg);
           }
         }else {
            $msg->title = "Error fatal";
            $msg->msg = "Informe al área de sistemas.";
            $msg->status = "error";
            echo json_encode($msg);

        //      echo "Falló";
        //     echo "JSON does not validate. Violations:\n";
        //    foreach ($validator->getErrors() as $error) {
        //   echo sprintf("[%s] %s\n", $error['property'], $error['message']);}
         }
         

    }
    else if ($_POST['func'] == 'save_economics_data') {

            $id_ases = $_POST['ases'];

            $register_economics_data                   = new  stdClass();
       

            $data = json_decode($_POST['json']);

            $register_economics_data->estrato                   = $data[0]->val_input;
            $register_economics_data->prestacion_economica      = json_encode($data[1]);
            $register_economics_data->beca                      = json_encode($data[2]);
            $register_economics_data->ayuda_transporte          = json_encode($data[3]);
            $register_economics_data->ayuda_materiales          = json_encode($data[4]);
            $register_economics_data->solvencia_econo           = $data[5]->val_input;
            $register_economics_data->ocupacion_padres          = json_encode($data[6]);
            $register_economics_data->nivel_educ_padres         = json_encode($data[7]);
            $register_economics_data->situa_laboral_padres      = json_encode($data[8]);
            $register_economics_data->expectativas_laborales    = json_encode($data[9]);
            $register_economics_data->id_ases_user              = $id_ases;

             //General  data to logs
            $moodle_user   =  $USER->id;
            $data_prev     =  $_POST['json_prev'];
            $data_env      =  $_POST['json'];
            $eventoid      =  getIdEventLogs('save_economics_tab_sp');
            $navegador     =  $_SERVER['HTTP_USER_AGENT'];
            $url           =  $_SERVER['HTTP_REFERER'];

            $result =  save_economics_data($register_economics_data);
            if($result){

                $msg->title = "Éxito";
                $msg->status = "success";
               $msg->msg = "La información se ha almacenado correctamente.";

               //Particular data to logs
               $data_alm    = $data_env;

               //Object to log

               $new_log = new stdClass();
               $new_log->id_moodle_user     = $moodle_user;
               $new_log->datos_previos      = $data_prev;
               $new_log->datos_enviados     = $data_env;
               $new_log->datos_almacenados  = $data_alm;
               $new_log->id_evento          = $eventoid->id;
               $new_log->url                = $url;
               $new_log->id_ases_user       = $id_ases;
               $new_log->navegador          = $navegador;
               $new_log->title_retorno      = $msg->title;
               $new_log->msg_retorno        = $msg->status;
               $new_log->status_retorno     = $msg->msg;

               $DB->insert_record('talentospilos_general_logs', $new_log, $returnid=false, $bulk=false);

               echo json_encode($msg);
               
           }else{

               $msg->title = "Error";
               $msg->msg = "No se ha guardado correctamente.";
               $msg->status = "error";

               //Particular data to logs
               $data_alm    = "";

               //Object to log

               $new_log = new stdClass();
               $new_log->id_moodle_user     = $moodle_user;
               $new_log->datos_previos      = $data_prev;
               $new_log->datos_enviados     = $data_env;
               $new_log->datos_almacenados  = $data_alm;
               $new_log->id_evento          = $eventoid->id;
               $new_log->url                = $url;
               $new_log->id_ases_user       = $id_ases;
               $new_log->navegador          = $navegador;
               $new_log->title_retorno      = $msg->title;
               $new_log->msg_retorno        = $msg->status;
               $new_log->status_retorno     = $msg->msg;

               $DB->insert_record('talentospilos_general_logs', $new_log, $returnid=false, $bulk=false);
               echo json_encode($msg);
           }
 

    }  else if ($_POST['func'] == 'edit_economics_data') {

            //Actualizar registro en BD. 

            $id_ases = $_POST['ases'];
            $data = $_POST['json'];

            //General  data to logs
            $moodle_user   =  $USER->id;
            $data_prev     =  $_POST['json_prev'];
            $data_env      =  $data;
            $eventoid      =  getIdEventLogs('edit_economics_tab_sp');
            $navegador     =  $_SERVER['HTTP_USER_AGENT'];
            $url           =  $_SERVER['HTTP_REFERER'];
            

            $result =       update_economics_data($data, $id_ases);
            if($result){
                $msg->title = "Éxito";
                $msg->status = "success";
               $msg->msg = "La información se ha actualizado correctamente.";

                //Particular data to logs
                $data_alm    = $data_env;

                //Object to log
 
                $new_log = new stdClass();
                $new_log->id_moodle_user     = $moodle_user;
                $new_log->datos_previos      = $data_prev;
                $new_log->datos_enviados     = $data_env;
                $new_log->datos_almacenados  = $data;
                $new_log->id_evento          = $eventoid->id;
                $new_log->url                = $url;
                $new_log->id_ases_user       = $id_ases;
                $new_log->navegador          = $navegador;
                $new_log->title_retorno      = $msg->title;
                $new_log->msg_retorno        = $msg->status;
                $new_log->status_retorno     = $msg->msg;
 
                $DB->insert_record('talentospilos_general_logs', $new_log, true);

               echo json_encode($msg);
               
           }else{
               $msg->title = "Error";
               $msg->msg = "No se ha actualizado correctamente.";
               $msg->status = "error";

                    //Particular data to logs
                    $data_alm    = "";

                    //Object to log
     
                    $new_log = new stdClass();
                    $new_log->id_moodle_user     = $moodle_user;
                    $new_log->datos_previos      = $data_prev;
                    $new_log->datos_enviados     = $data_env;
                    $new_log->datos_almacenados  = $data_alm;
                    $new_log->id_evento          = $eventoid->id;
                    $new_log->url                = $url;
                    $new_log->id_ases_user       = $id_ases;
                    $new_log->navegador          = $navegador;
                    $new_log->title_retorno      = $msg->title;
                    $new_log->msg_retorno        = $msg->status;
                    $new_log->status_retorno     = $msg->msg;
     
                    $DB->insert_record('talentospilos_general_logs', $new_log, $returnid=false, $bulk=false);

               echo json_encode($msg);
           }
 

    }    else if ($_POST['func'] == 'save_health_data') {

            $id_ases = $_POST['ases'];

            $register_health_data                   = new  stdClass();
       

             $data = json_decode($_POST['json']);

            $register_health_data->regimen_salud             = json_encode($data[0]);
            $register_health_data->servicio_salud_vinculado  = json_encode($data[1]);
            $register_health_data->servicios_usados          = json_encode($data[2]);
            $register_health_data->id_ases_user              = $id_ases;

              //General  data to logs
              $moodle_user   =  $USER->id;
              $data_prev     =  $_POST['json_prev'];
              $data_env      =  $_POST['json'];
              $eventoid      =  getIdEventLogs('save_salud_tab_sp');
              $navegador     =  $_SERVER['HTTP_USER_AGENT'];
              $url           =  $_SERVER['HTTP_REFERER'];

            $result =  save_health_data($register_health_data);
            if($result){
                $msg->title = "Éxito";
                $msg->status = "success";
                $msg->msg = "La información se ha almacenado correctamente.";

                 //Particular data to logs
                 $data_alm    = $data_env;

                 //Object to log
  
                 $new_log = new stdClass();
                 $new_log->id_moodle_user     = $moodle_user;
                 $new_log->datos_previos      = $data_prev;
                 $new_log->datos_enviados     = $data_env;
                 $new_log->datos_almacenados  = $data_alm;
                 $new_log->id_evento          = $eventoid->id;
                 $new_log->url                = $url;
                 $new_log->id_ases_user       = $id_ases;
                 $new_log->navegador          = $navegador;
                 $new_log->title_retorno      = $msg->title;
                 $new_log->msg_retorno        = $msg->status;
                 $new_log->status_retorno     = $msg->msg;
  
                 $DB->insert_record('talentospilos_general_logs', $new_log, $returnid=false, $bulk=false);

               echo json_encode($msg);
               
           }else{
               $msg->title = "Error";
               $msg->msg = "No se ha guardado correctamente.";
               $msg->status = "error";

                 //Particular data to logs
                 $data_alm    = "";

                 //Object to log
  
                 $new_log = new stdClass();
                 $new_log->id_moodle_user     = $moodle_user;
                 $new_log->datos_previos      = $data_prev;
                 $new_log->datos_enviados     = $data_env;
                 $new_log->datos_almacenados  = $data_alm;
                 $new_log->id_evento          = $eventoid->id;
                 $new_log->url                = $url;
                 $new_log->id_ases_user       = $id_ases;
                 $new_log->navegador          = $navegador;
                 $new_log->title_retorno      = $msg->title;
                 $new_log->msg_retorno        = $msg->status;
                 $new_log->status_retorno     = $msg->msg;
  
                 $DB->insert_record('talentospilos_general_logs', $new_log, $returnid=false, $bulk=false);

               echo json_encode($msg);
           }
 

    }  else if ($_POST['func'] == 'edit_health_data') {

        //Actualizar registro en BD. 


            $id_ases = $_POST['ases'];
            $data = $_POST['json'];

            //General  data to logs
            $moodle_user   =  $USER->id;
            $data_prev     =  $_POST['json_prev'];
            $data_env      =  $data;
            $eventoid      =  getIdEventLogs('edit_salud_tab_sp');
            $navegador     =  $_SERVER['HTTP_USER_AGENT'];
            $url           =  $_SERVER['HTTP_REFERER'];

            $result =       update_health_data($data, $id_ases);
            if($result){
                $msg->title = "Éxito";
                $msg->status = "success";
               $msg->msg = "La información se ha actualizado correctamente.";

                //Particular data to logs
                $data_alm    = $data_env;

                //Object to log
 
                $new_log = new stdClass();
                $new_log->id_moodle_user     = $moodle_user;
                $new_log->datos_previos      = $data_prev;
                $new_log->datos_enviados     = $data_env;
                $new_log->datos_almacenados  = $data_alm;
                $new_log->id_evento          = $eventoid->id;
                $new_log->url                = $url;
                $new_log->id_ases_user       = $id_ases;
                $new_log->navegador          = $navegador;
                $new_log->title_retorno      = $msg->title;
                $new_log->msg_retorno        = $msg->status;
                $new_log->status_retorno     = $msg->msg;
 
                $DB->insert_record('talentospilos_general_logs', $new_log, $returnid=false, $bulk=false);
               echo json_encode($msg);
               
           }else{
               $msg->title = "Error";
               $msg->msg = "No se ha actualizado correctamente.";
               $msg->status = "error";

               //Particular data to logs
               $data_alm    = "";

               //Object to log

               $new_log = new stdClass();
               $new_log->id_moodle_user     = $moodle_user;
               $new_log->datos_previos      = $data_prev;
               $new_log->datos_enviados     = $data_env;
               $new_log->datos_almacenados  = $data_alm;
               $new_log->id_evento          = $eventoid->id;
               $new_log->url                = $url;
               $new_log->id_ases_user       = $id_ases;
               $new_log->navegador          = $navegador;
               $new_log->title_retorno      = $msg->title;
               $new_log->msg_retorno        = $msg->status;
               $new_log->status_retorno     = $msg->msg;

               $DB->insert_record('talentospilos_general_logs', $new_log, $returnid=false, $bulk=false);
               echo json_encode($msg);
           }
 

    } 

    else{
        $msg->title = "Error";
        $msg->msg = "No se ha enviado una función. Informe al área de sistemas.";
        $msg->status = "error";
        echo json_encode($msg);
    }
}else{
    $msg->title = "Error";
    $msg->msg = "Error en el servidor. Informe al área de sistemas.";
    $msg->status = "error";
    echo json_encode($msg);
}


?>