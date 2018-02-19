<?php 
    
    require_once(dirname(__FILE__). '/../../../../config.php');
    
    if( isset($_GET['form_id']) && isset($_GET['pregunta_id']) && isset($_GET['criterio']) && isset($_GET['order'])){
        header('Content-Type: application/json');
        echo dphpforms_find_records($_GET['form_id'], $_GET['pregunta_id'], $_GET['criterio'], $_GET['order']);
    }
    
    //Se busca por if_from_preg (info en dphpforms_get_record)
    function dphpforms_find_records($id_form, $id_pregunta, $criterio, $order = 'DESC'){

        global $DB;

        $FORM_ID = $id_form;
        $PREGUNTA_ID = $id_pregunta;

        if(!is_numeric($id_form)){
            $sql_alias = "SELECT id FROM {talentospilos_df_formularios} WHERE alias = '$id_form' AND estado = 1";
            $form_record = $DB->get_record_sql($sql_alias);
            if($form_record != null){
                $FORM_ID = (int) $form_record->id;
            }
        }

        if(!is_numeric($FORM_ID)){
            return json_encode(
                array(
                    'results' => array()
                )
            );
        };

        if(!is_numeric($id_pregunta)){
            $sql_alias = "SELECT id_pregunta FROM {talentospilos_df_alias} WHERE alias = '$id_pregunta'";
            $preg_record = $DB->get_record_sql($sql_alias);
            if($preg_record != null){
                $PREGUNTA_ID = (int) $preg_record->id_pregunta;
            }
        }

        if(!is_numeric($PREGUNTA_ID)){
            return json_encode(
                array(
                    'results' => array()
                )
            );
        };

        /* !!!!!!!!!! Modificación temporal de la consulta del criterio en la respuesta con un LIKE */
        $sql = "SELECT FRS.id_formulario_respuestas AS id_registro, FRS.fecha_hora_registro_respuesta AS fecha_hora_registro
        FROM {talentospilos_df_respuestas} AS R 
        INNER JOIN 
            (
                SELECT FR.id_formulario, FR.fecha_hora_registro AS fecha_hora_registro_respuesta, FS.id_formulario_respuestas, FS.id_respuesta
                FROM {talentospilos_df_form_resp} AS FR 
                INNER JOIN {talentospilos_df_form_solu} AS FS 
                ON FR.id = FS.id_formulario_respuestas 
        WHERE FR.id_formulario = '" . $FORM_ID . "' AND FR.estado = 1
            ) AS FRS 
        ON FRS.id_respuesta = R.id
        WHERE R.respuesta LIKE '" . $criterio . "%' AND R.id_pregunta = '" . $PREGUNTA_ID . "'
        ORDER BY FRS.fecha_hora_registro_respuesta " . $order;

        $resultados = $DB->get_records_sql($sql);
        return json_encode(
            array(
                'results' => array_values($resultados)
            )
        );
    }

?>