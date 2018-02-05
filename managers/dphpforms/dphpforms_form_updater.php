<?php 
    
    require_once(dirname(__FILE__). '/../../../../config.php');

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

?>