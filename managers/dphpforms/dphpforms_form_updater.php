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

    function get_preguntas_form($form_id){

        if(!$form_id){
            return null;
        }

        global $DB;

        $sql = "SELECT * FROM {talentospilos_df_tipo_campo} AS TC 
        INNER JOIN (
            SELECT * FROM {talentospilos_df_preguntas} AS P 
            INNER JOIN (
                SELECT *, F.id AS mod_id_formulario, FP.id AS mod_id_formulario_pregunta FROM {talentospilos_df_formularios} AS F
                INNER JOIN {talentospilos_df_form_preg} AS FP
                ON F.id = FP.id_formulario WHERE F.id = '$form_id' AND F.estado = '1'
                ) AS AA ON P.id = AA.id_pregunta
            ) AS AAA
        ON TC.id = AAA.tipo_campo
        ORDER BY posicion";

        $preguntas_form = $DB->get_records_sql($sql);
        return $preguntas_form;
    }

    function get_permisos_form($form_id){

        if(!$form_id){
            return null;
        }

        global $DB;

        $sql = "SELECT * FROM {talentospilos_df_tipo_campo} AS TC 
        INNER JOIN (
            SELECT * FROM {talentospilos_df_preguntas} AS P 
            INNER JOIN (
                SELECT *, F.id AS mod_id_formulario, FP.id AS mod_id_formulario_pregunta FROM {talentospilos_df_formularios} AS F
                INNER JOIN {talentospilos_df_form_preg} AS FP
                ON F.id = FP.id_formulario WHERE F.id = '$form_id' AND F.estado = '1'
                ) AS AA ON P.id = AA.id_pregunta
            ) AS AAA
        ON TC.id = AAA.tipo_campo
        ORDER BY posicion";

        $preguntas_form = $DB->get_records_sql($sql);

        $preguntas_with_permissions = array();

        foreach ($preguntas_form as $key => $pregunta) {
            $sql_permiso = "SELECT * FROM {talentospilos_df_per_form_pr} WHERE id_formulario_pregunta = '$pregunta->mod_id_formulario_pregunta'";
            $permiso = $DB->get_record_sql($sql_permiso);

            $permiso_pregunta = new stdClass();
            $permiso_pregunta->id_pregunta = $pregunta->id_pregunta;
            $permiso_pregunta->id_formulario_pregunta = $pregunta->mod_id_formulario_pregunta;
            $permiso_pregunta->campo = $pregunta->campo;
            $permiso_pregunta->enunciado = $pregunta->enunciado;
            $permiso_pregunta->permisos = $permiso->permisos;
            $permiso_pregunta->id_permiso = $permiso->id;

            array_push($preguntas_with_permissions, $permiso_pregunta);

        }

        return $preguntas_with_permissions;
    }

    function get_permiso($id){
        
        global $DB;

        $sql_permiso = "SELECT * FROM {talentospilos_df_per_form_pr} WHERE id = '$id'";
        return $DB->get_record_sql($sql_permiso);

    }
    
?>