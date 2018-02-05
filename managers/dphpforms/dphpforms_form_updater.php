<?php 
    
    require_once(dirname(__FILE__). '/../../../../config.php');

    if(isset($_GET['function'])){
        if($_GET['function'] == 'get_forms'){
            echo json_encode(get_forms());
            die();
        }
    }

    if(isset($_GET['function'])){
        if($_GET['function'] == 'delete_form'){
            delete_form( $_GET['id_form'] );
            die();
        }
    }

    if(isset($_GET['function'])){
        if($_GET['function'] == 'get_alias'){
            echo json_encode(get_alias());
            die();
        }
    }

    if(isset($_GET['function'])){
        if($_GET['function'] == 'delete_alias'){
            delete_alias( $_GET['id_alias'] );
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

        global $DB;

        $sql = "SELECT * FROM {talentospilos_df_formularios} WHERE id = '$form_id' AND estado = '1'";
        $formulario = $DB->get_record_sql($sql);
        $formulario->estado = '0';
        $formulario->alias = null;

        $DB->update_record('talentospilos_df_formularios', $formulario, $bulk = false);

    }

    function get_alias(){
        global $DB;
        $sql = "SELECT * FROM {talentospilos_df_alias}";
        $alias_preguntas = $DB->get_records_sql($sql);
        return $alias_preguntas;
    }

    function delete_alias($id_alias){
        global $DB;
        $sql = "DELETE FROM {talentospilos_df_alias} WHERE id = '$id_alias'";
        $DB->execute($sql);
    }
    
?>