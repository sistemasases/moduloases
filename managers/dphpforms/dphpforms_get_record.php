<?php 
    
    require_once(dirname(__FILE__). '/../../../../config.php');
    

    if( isset( $_GET['record_id'] ) ){
        header('Content-Type: application/json');
        $a_key = null;
        if(isset($_GET['alias_key'])){
            $a_key = $_GET['alias_key'];
        }
        echo dphpforms_get_record( $_GET['record_id'], $a_key );
    }
    
    function dphpforms_get_record($record_id, $alias_key){

        global $DB;

        $sql = "SELECT * FROM {talentospilos_df_preguntas} P 
                INNER JOIN (
                SELECT * FROM (
                    SELECT id AS id_form_preg, id_pregunta AS id_tabla_preguntas FROM {talentospilos_df_form_preg}
                    ) FP INNER JOIN (SELECT * 
                                FROM {talentospilos_df_respuestas} AS R 
                                INNER JOIN 
                                    (
                                        SELECT * 
                                        FROM {talentospilos_df_form_resp} AS FR 
                                        INNER JOIN {talentospilos_df_form_solu} AS FS 
                                        ON FR.id = FS.id_formulario_respuestas 
                                        WHERE FR.id = '".$record_id."' AND FR.estado = 1
                                    ) AS FRS 
                                ON FRS.id_respuesta = R.id) RF
                            ON RF.id_pregunta = FP.id_form_preg) TT
                ON id_tabla_preguntas = P.id";

        $list_respuestas = $DB->get_records_sql($sql);
        $list_respuestas = array_values($list_respuestas);

        $sql_record = "SELECT * FROM {talentospilos_df_form_resp} WHERE id = '"  . $record_id . "'";
        $record_info = $DB->get_record_sql($sql_record);

        $respuestas = array();
        $key = null;
        if(count($list_respuestas) > 0){
            foreach($list_respuestas as &$respuesta){
                $sql_field_type = "SELECT * FROM {talentospilos_df_tipo_campo} WHERE id = '$respuesta->tipo_campo'";
                $field_type = $DB->get_record_sql($sql_field_type);
                $tmp_respuesta = array(
                    'enunciado' => $respuesta->enunciado,
                    'respuesta' => $respuesta->respuesta,
                    'opciones' => $respuesta->opciones_campo,
                    'tipo_campo' => $field_type->campo,
                    'id_pregunta' => $respuesta->id_tabla_preguntas,
                    'id_relacion_form_pregunta' => $respuesta->id_form_preg,
                    'local_alias' => json_decode($respuesta->atributos_campo)->{'local_alias'},
                );
                if(($alias_key)&&(json_decode($respuesta->atributos_campo)->{'local_alias'} == $alias_key)){
                    $key = $tmp_respuesta;
                }
                array_push($respuestas, $tmp_respuesta);
            }
        }

        return json_encode(
            array(
                'record' => array(
                    'id_formulario' => $list_respuestas[0]->id_formulario,
                    'id_registro' => $list_respuestas[0]->id_formulario_respuestas,
                    'fecha_hora_registro' => $record_info->fecha_hora_registro,
                    'campos' => $respuestas,
                    'alias_key' => $key
                )
            )
        );
    }

?>