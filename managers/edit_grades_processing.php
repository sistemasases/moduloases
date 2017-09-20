<<<<<<< HEAD
<?php
require_once('query.php');


if(isset($_POST['user']) && isset($_POST['ar_items']) && isset($_POST['notas_v']) && isset($_POST['notas_n']) && isset($_POST['porcent']) && isset($_POST['categ'])) 
 {
    $user_id = $_POST['user'];
    $items = $_POST['ar_items'];
    $old_n = $_POST['notas_v'];
    $categorias = $_POST['categ'];
    $notas = $_POST['notas_n'];
    $porcentajes = $_POST['porcent'];
    
    $new_n = recalculate_totals($notas, $categorias, $porcentajes);
   
    $bool = update_notas($user_id, $items, $old_n, $new_n);

    if ($bool){
        $msg =  new stdClass();
        $msg->exito = "Exito!!";
        $msg->msg = "Se ha almacenado la información con exito!!";
        echo json_encode($msg);
    }else{
        $msg =  new stdClass();
        $msg->error = "Error !!";
        $msg->msg = "Error al guardar la información." ;
        echo json_encode($msg);
    }
    
 }else{
    $msg =  new stdClass();
        $msg->error = "Error !!";
        $msg->msg = "Error al comunicarse con el servidor.";
        echo json_encode($msg);
 }




=======
<?php
require_once('query.php');


if(isset($_POST['user']) && isset($_POST['ar_items']) && isset($_POST['notas_v']) && isset($_POST['notas_n']) && isset($_POST['porcent']) && isset($_POST['categ'])) 
 {
    $user_id = $_POST['user'];
    $items = $_POST['ar_items'];
    $old_n = $_POST['notas_v'];
    $categorias = $_POST['categ'];
    $notas = $_POST['notas_n'];
    $porcentajes = $_POST['porcent'];
    
    $new_n = recalculate_totals($notas, $categorias, $porcentajes);
   
    $bool = update_notas($user_id, $items, $old_n, $new_n);

    if ($bool){
        $msg =  new stdClass();
        $msg->exito = "Exito!!";
        $msg->msg = "Se ha almacenado la información con exito!!";
        echo json_encode($msg);
    }else{
        $msg =  new stdClass();
        $msg->error = "Error !!";
        $msg->msg = "Error al guardar la información." ;
        echo json_encode($msg);
    }
    
 }else{
    $msg =  new stdClass();
        $msg->error = "Error !!";
        $msg->msg = "Error al comunicarse con el servidor.";
        echo json_encode($msg);
 }




>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
?>