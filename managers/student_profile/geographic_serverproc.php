<?php
require_once(dirname(__FILE__). '/../../../../config.php');
require_once('../lib/student_lib.php');
require_once('geographic_lib.php');

date_default_timezone_set('America/Bogota');

if(isset($_POST['func'])){
    if($_POST['func'] == 'load_geographic_info'){

        $id_ases = $_POST['id_ases'];
        load_geographic_info($id_ases);

    }else if($_POST['func'] == 'save_geographic_info'){

        $id_ases = $_POST['id_ases'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $neighborhood = $_POST['neighborhood'];
        $geographic_risk = $_POST['geographic_risk'];

        $msg = new stdClass();

        $result_save_info = save_geographic_info($id_ases, $latitude, $longitude, $neighborhood, $geographic_risk);
        
        if($result_save_info){
            $msg->title = 'Éxito';
            $msg->text = "La información geográfica ha sido guardada con éxito";
            $msg->type = "success";
        }else{
            $msg->title = 'Error';
            $msg->text = "La información geográfica no ha sido guardada. Intentalo nuevamente.";
            $msg->type = "error";
        }
        echo json_encode($msg);
    }
};
