<?php 
    
    require_once(dirname(__FILE__). '/../../../../config.php');

    if(isset($_GET['function'])){
        if($_GET['function'] == 'get_forms'){
            header('Content-Type: application/json');
            echo json_encode(get_forms());
            die();
        }
    }

    if(isset($_GET['function'])){
        if($_GET['function'] == 'delete_form'){
            header('Content-Type: application/json');
            $response = delete_form( $_GET['id_form'] );
            if($response == 0){
                echo json_encode(
                    array(
                        'status' => '0',
                        'message' => 'Deleted'
                    )
                );
            }else{
                echo json_encode(
                    array(
                        'status' => '-1',
                        'message' => "Does not exist"
                    )
                );
            }
            die();
        }
    }

    if(isset($_GET['function'])){
        if($_GET['function'] == 'get_alias'){
            header('Content-Type: application/json');
            echo json_encode(get_alias());
            die();
        }
    }

    if(isset($_GET['function'])){
        if($_GET['function'] == 'delete_alias'){
            header('Content-Type: application/json');
            $response = delete_alias( $_GET['id_alias'] );
            if($response == 0){
                echo json_encode(
                    array(
                        'status' => '0',
                        'message' => 'Deleted'
                    )
                );
            }else{
                echo json_encode(
                    array(
                        'status' => '-1',
                        'message' => "Does not exist"
                    )
                );
            }
            die();
        }
    }

    function update_pregunta_position($id_form_pregunta, $new_position){
        global $DB;
        $sql = "SELECT * FROM {talentospilos_df_form_preg} WHERE id = '$id_form_pregunta' AND estado = 1";
        $form_preg = $DB->get_record_sql($sql);
        if($form_preg){
            $form_preg_updated = new stdClass();
            $form_preg_updated->id = $form_preg->id;
            $form_preg_updated->id_formulario = $form_preg->id_formulario;
            $form_preg_updated->id_pregunta = $form_preg->id_pregunta;
            $form_preg_updated->posicion = $new_position;
            $form_preg_updated->fecha_hora_registro = $form_preg->fecha_hora_registro;
            $form_preg_updated->estado = $form_preg->estado;
            $DB->update_record('talentospilos_df_form_preg', $form_preg_updated, $bulk=false);
            return 0;
        }else{
            return -1;
        }
    }

    function get_forms(){
        global $DB;
        $sql = "SELECT * FROM {talentospilos_df_formularios} WHERE estado = '1'";
        $formularios = $DB->get_records_sql($sql);
        return $formularios;
    }

    function delete_form($form_id){

        if(!$form_id){
            return -1;
        }

        global $DB;

        $sql = "SELECT * FROM {talentospilos_df_formularios} WHERE id = '$form_id' AND estado = '1'";
        $formulario = $DB->get_record_sql($sql);
        if($formulario){
            $formulario->estado = '0';
            $formulario->alias = null;
            $DB->update_record('talentospilos_df_formularios', $formulario, $bulk = false);
            return 0;
        }else{
            return -1;
        }
        
    }

    function get_alias(){
        global $DB;
        $sql = "SELECT * FROM {talentospilos_df_alias}";
        $alias_preguntas = $DB->get_records_sql($sql);
        return $alias_preguntas;
    }

    function delete_alias($id_alias){

        if(!$id_alias){
            return -1;
        }

        global $DB;
        $sql = "DELETE FROM {talentospilos_df_alias} WHERE id = '$id_alias'";
        $DB->execute($sql);

        return 0;
    }
    
?>