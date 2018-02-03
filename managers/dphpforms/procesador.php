<?php 

    require_once(dirname(__FILE__). '/../../../../config.php');
    
    global $DB;

    //print_r($_POST);

    $RECORD_ID = null;

    if(isset($_POST['id_registro'])){
        $RECORD_ID = $_POST['id_registro'];
    }
    
    $form = array(
        'id' => $_POST['id']
        //'id_monitor' => $_POST['id_monitor'],
        //'id_estudiante' => $_POST['id_estudiante']
    );

    $respuestas = array();

    foreach ($_POST as $key => $value) {
        if(is_numeric($key)){
            $elemento = $value;
            $respuesta = array(
                'id' => (string) $key,
                'valor' => (string) $elemento
            );
            array_push($respuestas, $respuesta);
        }
        next($_POST);
    }

    //echo 'RESPUESTAS-->';
    //print_r($respuestas);
    

    $full_form = array(
        'formulario' => $form,
        'respuestas' => $respuestas
    );

    $form_JSON = json_encode($full_form);
    


/*$formularioDiligenciado = '

{
    "formulario":{
        "id":43,
        "id_monitor":5245,
        "id_estudiante":6548
    },
    "respuestas":[
        {
            "id":139,
            "valor":"Jeison"
        },
        {
            "id":140,
            "valor":"Cardona"
        },
        {
            "id":141,
            "valor":"123456789"
        },
        {
            "id":142,
            "valor":"Masculino"
        },
        {
            "id":143,
            "valor":[
                "Lectura",
                "Escritura"
            ]
        },
        {
            "id":144,
            "valor":[
                "Si"
            ]
        },
        {
            "id":145,
            "valor":"Amet nisi proident enim occaecat Lorem consequat Lorem labore ut anim adipisicing culpa. Ipsum occaecat ea sint dolor minim ad et est dolore culpa mollit irure. Sint ut nulla reprehenderit deserunt magna eu nisi culpa dolore cupidatat."
        },
        {
            "id":146,
            "valor":17
        },
        {
            "id":147,
            "valor":"jcardona@keepler.com"
        },
        {
            "id":148,
            "valor":"jcardona@keepler.com"
        },
        {
            "id":149,
            "valor":"OSO"
        },
        {
            "id":150,
            "valor":"123456789"
        },
        {
            "id":151,
            "valor":17
        }
    ]
}

';*/
if($RECORD_ID){
    //echo 'SE VA A ACTUALIZAR';
    //echo json_encode($form_JSON);
    dphpforms_update_respuesta($form_JSON, $RECORD_ID);
}else{
    //echo 'REGISTRO NUEVO';
    dphpforms_new_store_respuesta($form_JSON);
}

function dphpforms_update_respuesta($completed_form, $RECORD_ID){
    //echo 'ID REGISTRO: ' . $RECORD_ID;

    $processable = true;

    $obj_form_completed = json_decode($completed_form);
    $processable = dphpforms_form_exist($obj_form_completed->{'formulario'}->{'id'});
   
    //print_r($obj_form_completed->{'respuestas'});

    foreach($obj_form_completed->{'respuestas'} as &$respuesta){
        if(dphpforms_pregunta_exist_into_form($respuesta->{'id'})){
            if($processable == false){
                break;
            }
        }else{
            $processable = false;
        };
    }

    $reglas = dphpforms_get_form_reglas($obj_form_completed->{'formulario'}->{'id'});
    $registered_respuestas = dphpforms_get_respuestas_form_completed($RECORD_ID);

    $updated_respuestas = array();
    if($processable){
        //echo "\n¿Procesable?: Sí.\n";
        $different_flag = false;
        
        foreach ($registered_respuestas as &$respuesta) {
            foreach ($obj_form_completed->{'respuestas'} as &$respuestaActualizada) {
                
                if( $respuesta['id'] == $respuestaActualizada->id ){

                    if( $respuesta['valor'] != $respuestaActualizada->valor ){
                        //echo ' SE VA A ACTUALIZAR: ' . $respuesta['id'] ;
                        array_push($updated_respuestas, array('id' => $respuesta['id'], 'valor' => $respuestaActualizada->valor) );
                        $different_flag = true;
                    }
                }else{
                    $exist_flag = false;
                    foreach ($updated_respuestas as $ra) {
                        if($ra['id'] == $respuesta['id']){
                            $exist_flag = true;
                        }
                    }
                    if(!$exist_flag){
                        array_push($updated_respuestas, array('id' => $respuesta['id'], 'valor' => $respuesta['valor']) );
                    }
                    
                }
            }
        }
        if($different_flag){
            
            //La última afectación es si las reglas son válidas
            $processable = dphpforms_reglas_validator(json_decode(json_encode($updated_respuestas)), $reglas);
           
            if($processable){

                //echo 'REGLAS OK, PENDIENTE';

                foreach($updated_respuestas as &$r){
                    
                    //echo "RESPUESTAS ACTUALIZADAS ==>" . count($updated_respuestas);

                    $updated = dphpforms_update_completed_form($RECORD_ID, $r['id'], $r['valor']);
                    //echo "REGISTRO: " . $RECORD_ID . " ID " . $r['id'] . " VALOR " . $r['valor'];
                    if(!$updated){
                        //echo 'ERROR ACTUALIZANDO';
                        echo json_encode(
                            array(
                                'status' => '-1',
                                'message' => 'Error updating',
                                'data' => ''
                            )
                        );
                        die();
                    }
                }
                echo json_encode(
                    array(
                        'status' => '0',
                        'message' => 'Updated',
                        'data' => ''
                    )
                );
            }else{
                echo json_encode(
                    array(
                        'status' => '-2',
                        'message' => 'Unfulfilled rules',
                        'data' => ''
                    )
                );
            }

        }else{
            //echo ' NO HAY NADA QUE ACTUALIZAR ';
            echo json_encode(
                array(
                    'status' => '-2',
                    'message' => 'Without changes',
                    'data' => ''
                )
            );
        }
    }
    
}

function dphpforms_new_store_respuesta($completed_form){

    $processable = true;

    $obj_form_completed = json_decode($completed_form);
    $processable = dphpforms_form_exist($obj_form_completed->{'formulario'}->{'id'});
    foreach($obj_form_completed->{'respuestas'} as &$respuesta){
        if(dphpforms_pregunta_exist_into_form($respuesta->{'id'})){
            if($processable == false){
                break;
            }
        }else{
            $processable = false;
        };
    }

    $reglas = dphpforms_get_form_reglas($obj_form_completed->{'formulario'}->{'id'});

    $processable = dphpforms_reglas_validator($obj_form_completed->{'respuestas'}, $reglas);

    if($processable){
        //echo "\n¿Procesable?: Sí.\n";
        //echo "Inicio de registro en la base de datos\n";
        
        $resultadoRegistro = array();

        $ID_FORMULARIO_RESPUESTA = null;
        $ID_FORMULARIO_RESPUESTA = dphpforms_store_form_respuesta($obj_form_completed->{'formulario'});

        array_push($resultadoRegistro, array('ID_Formulario_respuesta' => $ID_FORMULARIO_RESPUESTA));

        // Registro de respuestas
        $respuestas_identifiers = array();
        foreach ($obj_form_completed->{'respuestas'} as &$respuesta) {
            array_push(
                $respuestas_identifiers, 
                array( 
                    'idRespuestaDB' => dphpforms_store_respuesta($respuesta)
                )
            );
        }

        array_push($resultadoRegistro, array('ids_respuestas' => $respuestas_identifiers));

        $form_soluciones_identifiers = array();
        foreach ($respuestas_identifiers as &$idsRespuesta) {
            array_push($form_soluciones_identifiers,
                array(
                    'idFormularioSolucionDB' => dphpforms_store_form_soluciones($ID_FORMULARIO_RESPUESTA, $idsRespuesta['idRespuestaDB'])
                )
            );
        }

        array_push($resultadoRegistro, array('ids_respuestas' => $form_soluciones_identifiers));
        //echo "\nResultado del registro:\n";
        //print_r($resultadoRegistro);
        echo json_encode(
            array(
                'status' => '0',
                'message' => 'Stored',
                'data' => $ID_FORMULARIO_RESPUESTA
            )
        );
        

    }else{
        //echo "¿Procesable?: No.\n";
        echo json_encode(
            array(
                'status' => '-1',
                'message' => 'It is not processable',
                'data' => ''
            )
        );
    }
    

}


function dphpforms_update_completed_form($form_identifier_respuesta, $pregunta_identifier, $new_value){
    
    global $DB;
    
       $sql = "
       
       SELECT * 
       FROM {talentospilos_df_respuestas} AS R 
       INNER JOIN 
           (
               SELECT * 
               FROM {talentospilos_df_form_resp} AS FR 
               INNER JOIN {talentospilos_df_form_solu} AS FS 
               ON FR.id = FS.id_formulario_respuestas 
               WHERE FR.id = '".$form_identifier_respuesta."'
           ) AS FRS 
       ON FRS.id_respuesta = R.id WHERE R.id_pregunta = '".$pregunta_identifier."';
       
       ";

       $result = $DB->get_record_sql($sql);
       $respuesta_identifier = $result->id_respuesta;

       $obj_updated_respuesta = new stdClass();
       $obj_updated_respuesta->id = $respuesta_identifier;
       $obj_updated_respuesta->respuesta = $new_value;
       
       $obj_updated_respuesta->fecha_hora_registro = "now()";

       $DB->update_record('talentospilos_df_respuestas', $obj_updated_respuesta, $bulk=false);

       //echo 'IDPREGUNTA' .$pregunta_identifier;

       return true;
}

function dphpforms_store_form_soluciones($form_id, $respuesta_identifier){

    global $DB;

       $obj_form_soluciones = new stdClass();
       $obj_form_soluciones->id_formulario_respuestas = $form_id;
       $obj_form_soluciones->id_respuesta = $respuesta_identifier;
   
       $form_solucines_identifier = $DB->insert_record('talentospilos_df_form_solu', $obj_form_soluciones, $returnid=false, $bulk=false);

       return $form_solucines_identifier;
}

function dphpforms_store_form_respuesta($form_detail){
    
    global $DB;

    $obj_form_respuesta = new stdClass();
    $obj_form_respuesta->id_formulario = $form_detail->{'id'};
    $obj_form_respuesta->id_monitor = '-1';
    $obj_form_respuesta->id_estudiante = '-1';

    $form_respuesta_identifier = $DB->insert_record('talentospilos_df_form_resp', $obj_form_respuesta, $returnid=true, $bulk=false);

    return $form_respuesta_identifier;
}

function dphpforms_store_respuesta($respuesta){
    
    global $DB;

    if(is_array($respuesta->{'valor'})){
        $respuesta->{'valor'} = json_encode($respuesta->{'valor'});
    }

    $obj_respuesta = new stdClass();
    $obj_respuesta->id_pregunta = $respuesta->{'id'};
    $obj_respuesta->respuesta = $respuesta->{'valor'};

    $respuesta_identifier = $DB->insert_record('talentospilos_df_respuestas', $obj_respuesta, $returnid=true, $bulk=false);
    return $respuesta_identifier;
}

function dphpforms_form_exist($id){

    global $DB;

    $sql = "
    
        SELECT * FROM {talentospilos_df_formularios} WHERE id = '" . $id . "'
    
    ";

    $result = $DB->get_record_sql($sql);

    $form_id = $result->id;
    if($form_id != null){
        return true;
    }else{
        return false;
    }
}

function dphpforms_pregunta_exist_into_form($pregunta_identifier){

    global $DB;
    
    $sql = "
    
        SELECT * FROM {talentospilos_df_form_preg} WHERE id = '" . $pregunta_identifier . "'
    
    ";
    $result = $DB->get_record_sql($sql);
    $pregunta_identifier = $result->id;
    if($pregunta_identifier != null){
        return true;
        
    }else{
        return false;
        
    }
}

function dphpforms_get_form_reglas($form_id){

    global $DB;

    $sql = "
    
        SELECT RFP.id, RFP.id_form_pregunta_a, RFP.id_form_pregunta_b, R.regla FROM {talentospilos_df_reg_form_pr} RFP INNER JOIN {talentospilos_df_reglas} R ON RFP.id_regla = R.id WHERE RFP.id_formulario = '" . $form_id . "'
    
    ";
    $result = $DB->get_records_sql($sql);
    $result = array_values($result);
    
    $reglas = array();
    for($i = 0; $i < count($result); $i++){
        $row = $result[$i];
        $regla = array(
            'respuesta_a' => $row->id_form_pregunta_a,
            'regla' => $row->regla,
            'respuesta_b' => $row->id_form_pregunta_b
        );
        
        array_push($reglas, $regla);
    }

    return $reglas;
}

function dphpforms_reglas_validator($respuestas, $reglas){
    
    $satisfied_reglas = false;
    if(count($reglas) == 0){
        return true;
    }
    for($i = 0; $i < count($reglas); $i++){
        $regla = $reglas[$i]['regla'];
        $respuesta_a = null;
        $respuesta_b = null;
        foreach ($respuestas as &$respuesta) {
            if($reglas[$i]['respuesta_a'] == $respuesta->{'id'}){
                $respuesta_a = $respuesta;
            }
        }
        foreach ($respuestas as &$respuesta) {
            if($reglas[$i]['respuesta_b'] == $respuesta->{'id'}){
                $respuesta_b = $respuesta;
            }
        }

        if(($respuesta_a == null)&&($respuesta_b == null)){
            //echo "Oops, algo pasa con las respuestas ingresadas\n";
            break;
        }

        if($regla == 'DIFFERENT'){

            if($respuesta_a->{'valor'} == $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);*/
                return false;
                break;
            }else{
                $satisfied_reglas = true;
                //echo "REGLA " . $regla . " CUMPLIDA\n";
            }
        }elseif($regla == 'EQUAL'){

            if($respuesta_a->{'valor'} != $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);*/
                return false;
                break;
            }else{
                $satisfied_reglas = true;
                //echo "REGLA " . $regla . " CUMPLIDA\n";
            }
        }elseif($regla == '>'){

            /* Validation for time XX:XX */
            
            if((count($respuesta_a->{'valor'}) == 5)&&(count($respuesta_b->{'valor'}) == 5)){
                    if(($respuesta_a->{'valor'}[2] == ':')&&($respuesta_b->{'valor'}[2] == ':')){
                        if(
                            (is_numeric(substr($respuesta_a->{'valor'}, 0, 1)))&&(is_numeric(substr($respuesta_a->{'valor'}, 3, 4)))&&
                            (is_numeric(substr($respuesta_b->{'valor'}, 0, 1)))&&(is_numeric(substr($respuesta_b->{'valor'}, 3, 4)))
                            ){
                                $time_a = strtotime($respuesta_a->{'valor'});
                                $time_b = strtotime($respuesta_b->{'valor'});
                                if($time_a > $time_b){
                                    $satisfied_reglas = true;
                                }else{
                                    $satisfied_reglas = false;
                                    return false;
                                    break;
                                }
                        }
                    }
            }
            
            if($respuesta_a->{'valor'} < $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);*/
                return false;
                break;
            }else{
                $satisfied_reglas = true;
                //echo "REGLA " . $regla . " CUMPLIDA\n";
            }

        }elseif($regla == '<'){

            /* Validation for time XX:XX */
            
            if((count($respuesta_a->{'valor'}) == 5)&&(count($respuesta_b->{'valor'}) == 5)){
                    if(($respuesta_a->{'valor'}[2] == ':')&&($respuesta_b->{'valor'}[2] == ':')){
                        if(
                            (is_numeric(substr($respuesta_a->{'valor'}, 0, 1)))&&(is_numeric(substr($respuesta_a->{'valor'}, 3, 4)))&&
                            (is_numeric(substr($respuesta_b->{'valor'}, 0, 1)))&&(is_numeric(substr($respuesta_b->{'valor'}, 3, 4)))
                            ){
                                $time_a = strtotime($respuesta_a->{'valor'});
                                $time_b = strtotime($respuesta_b->{'valor'});
                                if($time_a < $time_b){
                                    $satisfied_reglas = true;
                                }else{
                                    $satisfied_reglas = false;
                                    return false;
                                    break;
                                }
                        }
                    }
            }
            
            if($respuesta_a->{'valor'} > $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);*/
                return false;
                break;
            }else{
                $satisfied_reglas = true;
                //echo "REGLA " . $regla . " CUMPLIDA\n";
            }
        }elseif($regla == 'DEPENDS'){
            /*
                BOUND replaces DEPENDS
            */
        }elseif($regla == 'BOUND'){
            /*
                    Se usa -#$%- para enviar cuando el RadioButton está vacío, esto con el fin
                    de asignarle un valor nulo diferente a 0, con el fin de no entrar en conflicto
                    con lo enviado por un CheckBox
                */
            if((($respuesta_a->{'valor'} != null) && ($respuesta_a->{'valor'} != "-#$%-") ) && (($respuesta_b->{'valor'} == null)||($respuesta_b->{'valor'} == "-#$%-"))){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);
                echo 'VALOR A' . $respuesta_a->{'valor'} . ' VALOR B' . $respuesta_a->{'valor'};*/
                return false;
                break;
            }elseif((($respuesta_a->{'valor'} == null)||($respuesta_a->{'valor'} == "-#$%-")) && (($respuesta_b->{'valor'} != null) && ($respuesta_b->{'valor'} != "-#$%-") )){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);
                echo 'VALOR A' . $respuesta_a->{'valor'} . ' VALOR B' . $respuesta_a->{'valor'};*/
                return false;
                break;
            }else{
                $satisfied_reglas = true;
                //echo "REGLA " . $regla . " CUMPLIDA\n";
            }
        }
    }

    return $satisfied_reglas;
}

function dphpforms_get_respuestas_form_completed($idFormularioDiligenciado){
    
    global $DB;

    $sql_respuestas = '
    
        SELECT * 
        FROM {talentospilos_df_respuestas} AS R 
        INNER JOIN 
            (
                SELECT * 
                FROM {talentospilos_df_form_resp} AS FR 
                INNER JOIN {talentospilos_df_form_solu} AS FS 
                ON FR.id = FS.id_formulario_respuestas 
                WHERE FR.id = '.$idFormularioDiligenciado.'
            ) AS FRS 
        ON FRS.id_respuesta = R.id;
    
    ';

    $result = $DB->get_records_sql($sql_respuestas);
    $result = array_values($result);
    
    $respuestas = array();

    for($i = 0; $i < count($result); $i++){
        $row = $result[$i];
        $tmp = array(
            'id' => $row->id_pregunta,
            'valor' => $row->respuesta
        );
        array_push($respuestas, $tmp);
    }

    return $respuestas;

}

?>