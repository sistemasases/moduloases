<?php
require_once(dirname(__FILE__). '/../../../../config.php');
require_once('../lib/student_lib.php');
require_once('../student_profile/studentprofile_lib.php');

date_default_timezone_set('America/Bogota');

$msg_error = new stdClass();

if(isset($_POST["func"])){
    if($_POST['func'] == 'save_profile'){
        $form = $_POST['form'];
        save_profile($form);

    } elseif($_POST['func'] == 'save_icetex_status') {
        
        if(isset($_POST['id_ases'])){
            $id_ases = $_POST['id_ases'];
        }else{
            $msg_error->title = "Error";
            $msg_error->msg = "Problema en la conexión al servidor. El ID del estudiante no llegó al servidor.";
            $msg_error->type = "error";

            return $msg_error;
        }

        if(isset($_POST['new_status'])){
            $new_status = $_POST['new_status'];
        }else{
            $msg_error->title = "Error";
            $msg_error->msg = "Problema en la conexión al servidor. El Nuevo Estado del estudiante no llegó al servidor.";
            $msg_error->type = "error";

            return $msg_error;
        }

        if(isset($_POST['id_reason'])){
            $id_reason = $_POST['id_reason'];
        }else{
           $id_reason = null;
        }

        if(isset($_POST['observations'])){
            $observations = $_POST['observations'];
        }else{
            $observations = null;
        }

        save_status_icetex_proc($new_status, $id_ases, $id_reason, $observations);

    } elseif($_POST['func'] == 'save_ases_status'){

        if(isset($_POST['id_ases'])){
            $id_ases = $_POST['id_ases'];
        }else{
            $msg_error->title = "Error";
            $msg_error->msg = "Problema en la conexión al servidor. El ID del estudiante no llegó al servidor.";
            $msg_error->type = "error";

            return $msg_error;
        }

        if(isset($_POST['new_status'])){
            $new_status = $_POST['new_status'];
        }else{
            $msg_error->title = "Error";
            $msg_error->msg = "Problema en la conexión al servidor. El Nuevo Estado del estudiante no llegó al servidor.";
            $msg_error->type = "error";

            return $msg_error;
        }

        if(isset($_POST['id_reason'])){
            $id_reason = $_POST['id_reason'];
        }else{
           $id_reason = null;
        }

        if(isset($_POST['observations'])){
            $observations = $_POST['observations'];
        }else{
            $observations = null;
        }

        save_status_ases_proc($new_status, $id_ases, $id_reason, $observations);

    } elseif($_POST['func'] == 'save_tracking_peer'){
        save_tracking_peer_proc();
    } elseif($_POST['func'] == 'delete_tracking_peer' && isset($_POST['id_tracking'])){
        $id_tracking_peer = $_POST['id_tracking'];
        delete_tracking_peer_proc($id_tracking_peer);
    } elseif($_POST['func'] == 'is_student'){
        $code_student = $_POST['code_student'];
        validate_student_proc($code_student);
    } else if($_POST['func'] == 'send_email'){
        send_email($_POST["risk_array"], $_POST["observations_array"],'' ,$_POST["id_student_moodle"], $_POST["id_student_pilos"], $_POST["date"],'', '', $_POST["url"]);
    }
    else {
        $msg->msg = "No se reconoce la función a ejecutar. Contacte al área de sistemas.";
        echo json_encode($msg);
    }
}else{
    $msg->msg = "No se es posible contactar con el servidor. Revise su conexión a Internet.";
    echo json_encode($msg);
}

 /**
 * Función que actualiza los campos de la tabla {talentospilos_usuario} 
 *
 * @see save_profile($form)
 * @parameters $form Array que contiene los campos a actualizar
 * @return sdtClass
 */
function save_profile($form){
    
    global $DB;
    
    try{
        $id_ases = $form[0]['value'];
        $msg = new stdClass();

        //Se define un arreglo que va a contener la info a actualizar
        $obj_updatable = array();
        
        // Se inserta los campos necesarios
        for($i = 0; $i < count($form); $i++){
            $obj_updatable[$form[$i]['name']] = $form[$i]['value'];
        }
        $obj_updatable = (object) $obj_updatable;
        //se le asigna el id del usario a actualizar
        $obj_updatable->id = $id_ases;

        $sql_query = "SELECT observacion FROM {talentospilos_usuario} WHERE id = $id_ases";
        $observations = $DB->get_record_sql($sql_query)->observacion;

        $conc_observations = $obj_updatable->observacion."\n".$observations;

        $obj_updatable->observacion = $conc_observations;
        
        $result = $DB->update_record('talentospilos_usuario', $obj_updatable);
        
        if($result){
            $msg->title = "Éxito";
            $msg->status = "success";
            $msg->msg = "La información se ha almacenado correctamente";
        }else{
            $msg->title = "Error";
            $msg->status = "error";
            $msg->msg = "Error al guardar la información. 
                         Posibles Causas: Si usted cambió el número de cedula, es posible que el nuevo número ya exista en la base de datos. 
                                          Revise los cambios realizados e intentelo de nuevo.";
        }
        
        echo json_encode($msg);
        
    }catch(Exception $e){
        
        $msg->title = "Error";
        $msg->status = "error";
        $msg->msg = "No ha sido posible comunicarse con el servidor.";
        
        echo json_encode($msg);
       
    }
}

 /**
 * Función que actualiza el campo 'estado Icetex' de la tabla {talentospilos_usuario} 
 *
 * @see save_status_icetex($new_status, $id_ases)
 * @parameters $new_status --> Nuevo estado a almacenar en el campo estado Icetex
 *             $id_ases --> ID asociado a un estudiante en la tabla {talentospilos_usuario}
 *             $id_reason -> ID de la razón del mótivo de retiro
 * @return stdClass 
 */
 
function save_status_icetex_proc($new_status, $id_ases, $id_reason = null,  $observations=null){

    $result = save_status_icetex($new_status, $id_ases, $id_reason, $observations);

    echo json_encode($result);

}

 /**
 * Función que actualiza el campo 'estado Ases' de la tabla {talentospilos_usuario} 
 *
 * @see save_status_ases($new_status, $id_ases)
 * @parameters $new_status --> Nuevo estado a almacenar en el campo estado Icetex
 *             $id_ses --> ID asociado a un estudiante en la tabla {talentospilos_usuario}
 * @return stdClass 
 */
 
function save_status_ases_proc($new_status, $id_ases, $id_reason = null, $observations=null){

    $result = save_status_ases($new_status, $id_ases, $id_reason, $observations);

    echo json_encode($result);
}

function save_reason_dropout_student(){
    
    if(isset($_POST['talentosid']) && isset($_POST['motivoid']) && isset($_POST['detalle']))
    {
        echo json_encode(saveMotivoRetiro($_POST['talentosid'], $_POST['motivoid'],$_POST['detalle']));
        
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al comunicarse con el servidor. No se encuentran las variables necesarias para guardar el motivo retiro";
        echo json_encode($msg);
    }
}

function loadMotivos(){
    $result = getMotivosRetiros();
    $msg = new stdClass();
    $msg->size = count($result);
    $msg->data = $result;
    echo json_encode($msg);
}

function loadMotivoRetiroStudent(){
    if(isset($_POST['talentosid']))
    {
        echo json_encode(getMotivoRetiroEstudiante($_POST['talentosid']));
        
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al comunicarse con el servidor. No se encuentran las variables necesarias para cargar el motivo retiro";
        echo json_encode($msg);
    }
}

/**
 * Función que valida si el formulario que llega al servidor está completo
 *
 * Por hacer: Validar la correctitud del formulario
 *
 * @param void
 * @return String --> Resultado de la validación
 */
function validate_form_tracking_peer(){
    if(!isset($_POST['date'])){
        return "El campo FECHA no llegó al servidor.";
    }else if(!isset($_POST['place'])){
        return "El campo LUGAR no llegó al servidor.";    
    }else if(!isset($_POST['h_ini'])){
        return "El campo HORA INICIAL no llegó al servidor.";
    }else if(!isset($_POST['m_ini'])){
        return "El campo MINUTO INICIAL no llegó al servidor.";
    }else if(!isset($_POST['h_fin'])){
        return "El campo HORA FINALIZACIÓN no llegó al servidor.";
    }else if(!isset($_POST['m_fin'])){
        return "El campo MINUTO FINALIZACIÓN no llegó al servidor.";
    }else if(!isset($_POST['tema'])){
        return "El campo TEMA no llegó al servidor.";
    }else if(!isset($_POST['objetivos'])){
        return "El campo OBJETIVOS no llegó al servidor.";
    }else if(!isset($_POST['individual'])){
        return "El campo ACT. INDIVIDUAL no llegó al servidor.";
    }else if(!isset($_POST['riesgo_ind'])){
        return "El campo RIESGO INDIVIDUAL no llegó al servidor.";
    }else if(!isset($_POST['familiar'])){
        return "El campo ACT. FAMILIAR no llegó al servidor.";
    }else if(!isset($_POST['riesgo_familiar'])){
        return "El campo RIESGO FAMILIAR no llegó al servidor.";
    }else if(!isset($_POST['academico'])){
        return "El campo ACT. ACADÉMICO no llegó al servidor.";
    }else if(!isset($_POST['riesgo_aca'])){
        return "El campo RIESGO ACADÉMICO no llegó al servidor.";
    }else if(!isset($_POST['economico'])){
        return "El campo ACT. ECONÓMICO no llegó al servidor.";
    }else if(!isset($_POST['riesgo_econom'])){
        return "El campo RIESGO ECONÓMICO no llegó al servidor.";
    }else if(!isset($_POST['vida_uni'])){
        return "El campo ACT. VIDA UNIVERSITARIA Y CIUDAD no llegó al servidor.";
    }else if(!isset($_POST['riesgo_uni'])){
        return "El campo RIESGO VIDA UNIVERSITARIA Y CIUDAD no llegó al servidor.";
    }else if(!isset($_POST['id_ases'])){
        return "El campo ID ESTUDIANTE ASES no llegó al servidor.";
    }else if(!isset($_POST['id_instance'])){
        return "El campo ID INSTANCIA BLOQUE no llegó al servidor.";
    }else if(!isset($_POST['observaciones'])){
        return "El campo OBSERVACIONES no llegó al servidor.";
    }else if(!isset($_POST['id_tracking_peer'])){
        return "El campo ID SEGUIMIENTO no llegó al servidor.";
    }else{
        return "success";
    }
}

/**
 * Función que guarda valida y envía el formulario de seguimiento de pares a la libreria 
 * que almacena el formulario en la base de datos
 *
 * Por hacer: 
 *
 * @param void
 * @return String --> Resultado de la validación
 */
function save_tracking_peer_proc(){

    global $USER;

    $result_msg = new stdClass();
    $is_valid = validate_form_tracking_peer();

    if($is_valid == "success"){

        $date = new DateTime();
        $date->getTimestamp();

        $tracking_object = new stdClass();
        $tracking_object->id = (int)$_POST['id_tracking_peer'];
        $tracking_object->id_monitor = $USER->id;
        $tracking_object->created = time();
        $tracking_object->fecha = strtotime($_POST['date']);
        $tracking_object->lugar = $_POST['place'];
        $tracking_object->hora_ini = $_POST['h_ini'].":".$_POST['m_ini'];
        $tracking_object->hora_fin = $_POST['h_fin'].":".$_POST['m_fin'];
        $tracking_object->tema = $_POST['tema'];
        $tracking_object->objetivos = $_POST['objetivos'];
        $tracking_object->tipo = "PARES";
        $tracking_object->status = 1;
        $tracking_object->individual = $_POST['individual'];
        $tracking_object->individual_riesgo = $_POST['riesgo_ind'];
        $tracking_object->familiar_desc = $_POST['familiar'];
        $tracking_object->familiar_riesgo = $_POST['riesgo_familiar'];
        $tracking_object->academico = $_POST['academico'];
        $tracking_object->academico_riesgo = $_POST['riesgo_aca'];
        $tracking_object->economico = $_POST['economico'];
        $tracking_object->economico_riesgo = $_POST['riesgo_econom'];
        $tracking_object->vida_uni = $_POST['vida_uni'];
        $tracking_object->vida_uni_riesgo = $_POST['riesgo_uni'];
        $tracking_object->id_estudiante_ases = $_POST['id_ases'];
        $tracking_object->id_instancia = $_POST['id_instance'];
        $tracking_object->revisado_profesional = 0;
        $tracking_object->revisado_practicante = 0;
        $tracking_object->observaciones = $_POST['observaciones'];

        $result_saving = save_tracking_peer($tracking_object);

        echo json_encode($result_saving);

    }else{
        $result_msg->title = "Error";
        $result_msg->msg = $is_valid;
        $result_msg->type = "error";

        echo json_encode($result_msg);
    }   
}

/**
 * Función que realiza un borrado lógico de un seguimiento
 * cambiando su estado en la base de datos
 *
 * @param Integer $id_tracking_peer --> ID del seguimiento a borrar
 * @return String --> Resultado de la acción
 */
function delete_tracking_peer_proc($id_tracking_peer){

    $result_delete = delete_tracking_peer((int)$id_tracking_peer);

    echo json_encode($result_delete);
}

/**
 * Función que valida si un estudiante existe en la base de datos
 *
 * @param $code_student --> código del estudiante
 * @return Array --> Resultado de la acción
 */

function validate_student_proc($code_student){

    $confirm_msg = validate_student($code_student);

    echo $confirm_msg;

}