<?php
require_once(dirname(__FILE__). '/../../../../config.php');
require_once('../lib/student_lib.php');

if(isset($_POST["func"])){
    if($_POST['func'] == 'save_profile'){
        $form = $_POST['form'];
        save_profile($form);
    } elseif($_POST['func'] == 'save_icetex_status') {
        $id_ases = $_POST['id_ases'];
        $new_status = $_POST['new_status'];
        save_status_icetex($new_status, $id_ases);
    } elseif($_POST['func'] == 'save_ases_status'){
        $id_ases = $_POST['id_ases'];
        $new_status = $_POST['new_status'];
        save_status_ases($new_status, $id_ases);
    } elseif($_POST['func'] == 'save_tracking_peer'){
        save_tracking_peer();
    }
    } else {
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
 *             $id_ses --> ID asociado a un estudiante en la tabla {talentospilos_usuario}
 * @return stdClass 
 */
 
function save_status_icetex($new_status, $id_ases){

    global $DB;
    
    $msg = new stdClass;
    
    $obj_updatable->id = (int)$id_ases;
    $obj_updatable->estado = $new_status;
    
    $result = $DB->update_record('talentospilos_usuario', $obj_updatable);
    
    if($result){
        $msg->title = 'Éxito';
        $msg->status = 'success';
        $msg->msg = 'El estado Icetex ha sido actualizado correctamente.';
    }else{
        $msg->title = 'Error';
        $msg->status = 'error';
        $msg->msg = 'El estado Icetex no ha sido actualizado correctamente. Error al conectarse a la base de datos.';
    }
    
    echo json_encode($msg);
}

 /**
 * Función que actualiza el campo 'estado Ases' de la tabla {talentospilos_usuario} 
 *
 * @see save_status_ases($new_status, $id_ases)
 * @parameters $new_status --> Nuevo estado a almacenar en el campo estado Icetex
 *             $id_ses --> ID asociado a un estudiante en la tabla {talentospilos_usuario}
 * @return stdClass 
 */
 
function save_status_ases($new_status, $id_ases){

    global $DB;
    
    $msg = new stdClass;
    
    $obj_updatable->id = $id_ases;
    $obj_updatable->estado_ases = $new_status;
    
    $result = $DB->update_record('talentospilos_usuario', $obj_updatable);

    if($result){
        $msg->title = 'Éxito';
        $msg->status = 'success';
        $msg->msg = 'El estado Ases ha sido actualizado correctamente.';
    }else{
        $msg->title = 'Error';
        $msg->status = 'error';
        $msg->msg = 'El estado Ases no ha sido actualizado correctamente. Error al conectarse a la base de datos.';
    }

    echo json_encode($msg);
}

function save_reason_dropout_student(){
    
    if(isset($_POST['talentosid']) && isset($_POST['motivoid']) && isset($_POST['detalle']))
    {
        echo json_encode(saveMotivoRetiro($_POST['talentosid'], $_POST['motivoid'],$_POST['detalle']));
        
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al comunicarse con el servidor. No se encuentran las variebles necesarias para guardar el motivo retiro";
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
        $msg->msg = "Error al comunicarse con el servidor. No se encuentran las variebles necesarias para cargar el motivo retiro";
        echo json_encode($msg);
    }
}

function save_tracking_peer(){
    
}