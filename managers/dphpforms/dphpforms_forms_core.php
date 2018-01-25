<?php 

    require_once(dirname(__FILE__). '/../../../../config.php');
    require_once('dphpforms_record_updater.php');
    require_once('dphpforms_response_recorder.php');

    //ALPHA
    function dphpforms_render_recorder($id_form, $rol, $id_estudiante, $id_monitor){
        return dphpforms_generate_html_recorder($id_form, $rol, $id_estudiante, $id_monitor);
    };
    function dphpforms_render_updater($id_completed_form, $rol, $record_id){
        return dphpforms_generate_html_updater($id_completed_form, $rol, $record_id);
    };


?>