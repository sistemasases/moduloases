<?php 

    require_once(dirname(__FILE__). '/../../../../config.php');
        
    if( isset( $_GET['record_id'] ) ){
        header('Content-Type: application/json');
        echo dphpforms_delete_record( $_GET['record_id'] );
    }

    function dphpforms_delete_record( $record_id ){

        global $DB;

        if(!is_numeric( $record_id )){
            return json_encode(
                array(
                    'status' => '-1',
                    'message' => 'Invalid record id',
                    'data' => ''
                )
            );
        }

        $sql = "SELECT * FROM {talentospilos_df_form_resp} WHERE id = '$record_id' AND estado = '1'";
        $result = $DB->get_record_sql($sql);
        
        if($result){
            
            $deleted_record = new stdClass();
            $deleted_record->id = $result->id;
            $deleted_record->id_formulario = $result->id_formulario; 
            $deleted_record->id_monitor = $result->id_monitor;
            $deleted_record->id_estudiante = $result->id_estudiante;
            $deleted_record->fecha_hora_registro = $result->fecha_hora_registro;
            $deleted_record->estado = '0';
            $DB->update_record('talentospilos_df_form_resp', $deleted_record, $bulk=false);
            return json_encode(
                array(
                    'status' => '0',
                    'message' => 'Deleted',
                    'data' => ''
                )
            );
            
        }else{
            return json_encode(
                array(
                    'status' => '-1',
                    'message' => 'Record does not exist',
                    'data' => ''
                )
            );
        }

    }


?>