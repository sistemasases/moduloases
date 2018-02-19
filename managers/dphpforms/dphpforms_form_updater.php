<?php 
    
    require_once(dirname(__FILE__). '/../../../../config.php');
    require_once('dphpforms_functions.php');

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

    $post = json_decode(file_get_contents('php://input'));
    if($post){
        $json_post = $post;
        if(property_exists($json_post, 'function')){

            //Actualizador de permisos
            if($json_post->function == 'update_permiso'){
                header('Content-Type: application/json');
                
                if(update_permiso($post->permiso_id, $post->permisos) == 0){
                    echo json_encode(
                        array(
                            'status' => '0',
                            'message' => 'Updated'
                        )
                    );
                }else{
                    echo json_encode(
                        array(
                            'status' => '-1',
                            'message' => 'Error'
                        )
                    );
                }
                die();
            }
            //Actualizador de enunciados de preguntas
            if($json_post->function == 'update_pregunta_enunciado'){
                header('Content-Type: application/json');
                
                if(update_pregunta_enunciado($post->pregunta_id, $post->enunciado) == 0){
                    echo json_encode(
                        array(
                            'status' => '0',
                            'message' => 'Updated'
                        )
                    );
                }else{
                    echo json_encode(
                        array(
                            'status' => '-1',
                            'message' => 'Error'
                        )
                    );
                }
                die();
            }
            //Actualizador de atributos de preguntas
            if($json_post->function == 'update_pregunta_atributos'){
                header('Content-Type: application/json');
                
                if(update_pregunta_atributos($post->pregunta_id, $post->atributos) == 0){
                    echo json_encode(
                        array(
                            'status' => '0',
                            'message' => 'Updated'
                        )
                    );
                }else{
                    echo json_encode(
                        array(
                            'status' => '-1',
                            'message' => 'Error'
                        )
                    );
                }
                die();
            }
            //Actualizador de opciones de preguntas
            if($json_post->function == 'update_pregunta_opciones'){
                header('Content-Type: application/json');
                
                if(update_pregunta_opciones($post->pregunta_id, $post->opciones) == 0){
                    echo json_encode(
                        array(
                            'status' => '0',
                            'message' => 'Updated'
                        )
                    );
                }else{
                    echo json_encode(
                        array(
                            'status' => '-1',
                            'message' => 'Error'
                        )
                    );
                }
                die();
            }
            //Crear pregunta
            if($json_post->function == 'create_pregunta'){
                //header('Content-Type: application/json');
                if(!(create_pregunta($post->form_id, $post->json_pregunta) == -1)){
                    echo json_encode(
                        array(
                            'status' => '0',
                            'message' => 'Created'
                        )
                    );
                }else{
                    echo json_encode(
                        array(
                            'status' => '-1',
                            'message' => 'Error'
                        )
                    );
                }
                die();
            }

        }
    };

    function update_pregunta_enunciado($pregunta_id, $new_enunciado){
        global $DB;
        $sql = "SELECT * FROM {talentospilos_df_preguntas} WHERE id = '$pregunta_id'";
        $pregunta = $DB->get_record_sql($sql);
        if($pregunta){
            $pregunta->enunciado = $new_enunciado;
            $DB->update_record('talentospilos_df_preguntas', $pregunta, $bulk=false);
            return 0;
        }else{
            return -1;
        }
    }

    function update_pregunta_atributos($pregunta_id, $new_atributos){
        global $DB;
        $sql = "SELECT * FROM {talentospilos_df_preguntas} WHERE id = '$pregunta_id'";
        $pregunta = $DB->get_record_sql($sql);
        if($pregunta){
            $pregunta->atributos_campo = $new_atributos;
            $DB->update_record('talentospilos_df_preguntas', $pregunta, $bulk=false);
            return 0;
        }else{
            return -1;
        }
    }

    function update_pregunta_opciones($pregunta_id, $new_opciones){
        global $DB;
        $sql = "SELECT * FROM {talentospilos_df_preguntas} WHERE id = '$pregunta_id'";
        $pregunta = $DB->get_record_sql($sql);
        if($pregunta){
            $pregunta->opciones_campo = $new_opciones;
            $DB->update_record('talentospilos_df_preguntas', $pregunta, $bulk=false);
            return 0;
        }else{
            return -1;
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
            $sql_permiso = "SELECT * FROM {talentospilos_df_per_form_pr} WHERE id_formulario_pregunta = '$pregunta->id_pregunta'";
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

    function update_permiso($id, $permisos){

        if( ($id) && ($permisos) ){
        
            global $DB;

            $sql_permiso = "SELECT * FROM {talentospilos_df_per_form_pr} WHERE id = '$id'";
            $db_permiso = $DB->get_record_sql($sql_permiso);
            $db_permiso->id = $db_permiso->id;
            $db_permiso->permisos = $permisos;

            $DB->update_record('talentospilos_df_per_form_pr', $db_permiso, $bulk=false);

            return 0;

        }else{
            return -1;
        }

    }

    function get_pregunta($id){
        
        global $DB;

        $sql_pregunta = "SELECT * FROM {talentospilos_df_preguntas} WHERE id = '$id'";
        return $DB->get_record_sql($sql_pregunta);

    }

    function get_tipo_campo($id){
        
        global $DB;

        $sql = "SELECT * FROM {talentospilos_df_tipo_campo} WHERE id = '$id'";
        return $DB->get_record_sql($sql);

    }

    function create_pregunta($form_id, $pregunta){

        global $DB;

        /*
            {
                "id_temporal": "cmp_XX",
                "enunciado": "Campo ABC",
                "tipo_campo": "CHECKBOX|TEXTAREA|ETC",
                "opciones_campo": [
                    { "enunciado": "Opc_1", "valor": "0", "posicion": "1" }
                ],
                "atributos_campo": {
                    "class": "css_selector_campo_abc col-xs-## col-sm-## col-md-## col-lg-##",
                    "name": "campo_abc",
                    "required": "false",
                    "maxlength": "####",
                    "local_alias": "local_alias_campo_abc"
                },
                "permisos_campo": [
                    { "rol": "rol_1", "permisos": ["lectura"] },
                    { "rol": "rol_2", "permisos": ["lectura", "escritura"] }
                ]
            }
        */
        $obj_pregunta = json_decode( $pregunta );
        $pregunta_details = array(
            'tipo_campo' => $obj_pregunta->tipo_campo,
            'opciones_campo' => $obj_pregunta->opciones_campo,
            'atributos_campo' => $obj_pregunta->atributos_campo,
            'enunciado' => $obj_pregunta->enunciado,
            'permisos_campo' => $obj_pregunta->permisos_campo 
        );
        $pregunta_id = dphpforms_store_pregunta( $pregunta_details );
        $form_preg_id = -1;

        if($pregunta_id){

            $sql_last_pregunta = "SELECT * FROM {talentospilos_df_form_preg}  WHERE id_formulario = '$form_id' ORDER BY posicion DESC LIMIT 1";
            $last_pregunta = $DB->get_record_sql($sql_last_pregunta);

            $form_preg = new stdClass();
            $form_preg->id_formulario = $form_id;
            $form_preg->id_pregunta = $pregunta_id;
            if($last_pregunta){
                $form_preg->posicion = $last_pregunta->posicion + 1;
            }else{
                $form_preg->posicion = 0;
            }
            $form_preg->estado = 1;
            $form_preg_id = $DB->insert_record('talentospilos_df_form_preg', $form_preg, $returnid=true, $bulk=false);
            
        }

        return $form_preg_id;

        //return -1;
    }

    function get_json_ordenamiento($form_id){
        $preguntas = get_preguntas_form($form_id);
    }
    
?>