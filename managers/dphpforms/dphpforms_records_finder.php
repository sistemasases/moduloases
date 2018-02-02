<?php 
    
    require_once(dirname(__FILE__). '/../../../../config.php');
    
    if( isset($_GET['form_id']) && isset($_GET['pregunta_id']) && isset($_GET['criterio']) && isset($_GET['order'])){
        header('Content-Type: application/json');
        echo dphpforms_find_records($_GET['form_id'], $_GET['pregunta_id'], $_GET['criterio'], $_GET['order']);
    }
    
    //Se busca por if_from_preg (info en dphpforms_get_record)
    function dphpforms_find_records($form_id, $pregunta_id, $criterio, $order = 'DESC'){

        global $DB;

        $sql = "SELECT FRS.id_formulario_respuestas AS id_registro, FRS.fecha_hora_registro_respuesta AS fecha_hora_registro
        FROM {talentospilos_df_respuestas} AS R 
        INNER JOIN 
            (
                SELECT FR.id_formulario, FR.fecha_hora_registro AS fecha_hora_registro_respuesta, FS.id_formulario_respuestas, FS.id_respuesta
                FROM {talentospilos_df_form_resp} AS FR 
                INNER JOIN {talentospilos_df_form_solu} AS FS 
                ON FR.id = FS.id_formulario_respuestas 
        WHERE FR.id_formulario = '" . $form_id . "'
            ) AS FRS 
        ON FRS.id_respuesta = R.id
        WHERE R.respuesta = '" . $criterio . "' AND R.id_pregunta = '" . $pregunta_id . "'
        ORDER BY FRS.fecha_hora_registro_respuesta " . $order;

        $resultados = $DB->get_records_sql($sql);
        return json_encode(
            array(
                'results' => array_values($resultados)
            )
        );
    }

?>