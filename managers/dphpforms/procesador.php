<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    $RECORD_ID = $_POST['id_registro'];
   
    $form = array(
        'id' => $_POST['id'],
        'id_monitor' => $_POST['id_monitor'],
        'id_estudiante' => $_POST['id_estudiante']
    );

    $respuestas = array();

    foreach ($_POST as $key => $value) {
        $elemento = $value;
        if(is_numeric(key($_POST))){

            $respuesta = array(
                'id' => key($_POST),
                'valor' => (string) $elemento
            );

            array_push($respuestas, $respuesta);
        }
        next($_POST);
    }

    print_r($respuestas);
    

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
    echo 'SE VA A ACTUALIZAR';
    echo json_encode($form_JSON);
    dphpforms_update_respuesta($form_JSON, $RECORD_ID);
}else{
    echo 'REGISTRO NUEVO';
    dphpforms_new_store_respuesta($form_JSON);
}

function dphpforms_update_respuesta($completed_form, $RECORD_ID){
    echo 'ID REGISTRO: ' . $RECORD_ID;

    $processable = true;

    $obj_form_completed = json_decode($completed_form);
    $processable = dphpforms_form_exist($obj_form_completed->{'formulario'}->{'id'});
   
    print_r($obj_form_completed->{'respuestas'});

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
        echo "\n¿Procesable?: Sí.\n";
        $different_flag = false;
        
        foreach ($registered_respuestas as &$respuesta) {
            foreach ($obj_form_completed->{'respuestas'} as &$respuestaActualizada) {
                
                if( $respuesta['id'] == $respuestaActualizada->id ){

                    if( $respuesta['valor'] != $respuestaActualizada->valor ){
                        echo ' SE VA A ACTUALIZAR: ' . $respuesta['id'] ;
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
            
            $processable = dphpforms_reglas_validator(json_decode(json_encode($updated_respuestas)), $reglas);
           
            if($processable){

                echo 'REGLAS OK, PENDIENTE';

                foreach($updated_respuestas as &$r){
                    
                    echo "RESPUESTAS ACTUALIZADAS ==>" . count($updated_respuestas);

                    $updated = dphpforms_update_completed_form($RECORD_ID, $r['id'], $r['valor']);
                    echo "REGISTRO: " . $RECORD_ID . " ID " . $r['id'] . " VALOR " . $r['valor'];
                    if($updated){
                        echo 'ACTUALIZADO';
                    }else{
                        echo 'ERROR ACTUALIZANDO';
                    }
                }
            }

        }else{
            echo ' NO HAY NADA QUE ACTUALIZAR ';
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
        echo "\n¿Procesable?: Sí.\n";
        echo "Inicio de registro en la base de datos\n";
        
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
        echo "\nResultado del registro:\n";
        print_r($resultadoRegistro);
        

    }else{
        echo "¿Procesable?: No.\n";
    }
    

}


function dphpforms_update_completed_form($form_identifier_respuesta, $pregunta_identifier, $new_value){
    $db_connection = pg_connect("host=localhost dbname=formularios user=administrator password=administrator");
    
       $sql = "
       
       SELECT * 
       FROM respuestas AS R 
       INNER JOIN 
           (
               SELECT * 
               FROM formulario_respuestas AS FR 
               INNER JOIN formulario_soluciones AS FS 
               ON FR.id = FS.id_formulario_respuestas 
               WHERE FR.id = '".$form_identifier_respuesta."'
           ) AS FRS 
       ON FRS.id_respuesta = R.id WHERE R.id_pregunta = '".$pregunta_identifier."';
       
       ";
       
   
       $result = pg_query($db_connection, $sql);
       $row = pg_fetch_row($result);
       $respuesta_identifier = $row[0];

       $sql = "
       
        UPDATE respuestas 
        SET respuesta = '" . $new_value . "' 
        WHERE id = '".$respuesta_identifier."'
       
       ";

       echo 'IDPREGUNTA' .$pregunta_identifier;

       $result = pg_query($db_connection, $sql);

       return true;
}

function dphpforms_store_form_soluciones($form_id, $respuesta_identifier){
    $db_connection = pg_connect("host=localhost dbname=formularios user=administrator password=administrator");
    
       $sql = "
       
           INSERT INTO formulario_soluciones(id_formulario_respuestas, id_respuesta)
           VALUES('".$form_id."', '".$respuesta_identifier."')
           RETURNING id
       
       ";
   
       $result = pg_query($db_connection, $sql);
       $row = pg_fetch_row($result);
       $form_identifier_soluciones = $row[0];
       return $form_identifier_soluciones;
}

function dphpforms_store_form_respuesta($form_detail){
    $db_connection = pg_connect("host=localhost dbname=formularios user=administrator password=administrator");
    $sql = "
    
        INSERT INTO formulario_respuestas(id_formulario, id_monitor, id_estudiante)
        VALUES('".$form_detail->{'id'}."', '".$form_detail->{'id_monitor'}."', '".$form_detail->{'id_estudiante'}."')
        RETURNING id
    
    ";

    $result = pg_query($db_connection, $sql);
    $row = pg_fetch_row($result);
    $form_identifier_respuesta = $row[0];
    return $form_identifier_respuesta;
}

function dphpforms_store_respuesta($respuesta){
    $db_connection = pg_connect("host=localhost dbname=formularios user=administrator password=administrator");

    if(is_array($respuesta->{'valor'})){
        $respuesta->{'valor'} = json_encode($respuesta->{'valor'});
    }

    $sql = "
    
        INSERT INTO respuestas(id_pregunta, respuesta)
        VALUES('".$respuesta->{'id'}."', '".$respuesta->{'valor'}."')
        RETURNING id
    
    ";

    $result = pg_query($db_connection, $sql);
    $row = pg_fetch_row($result);
    $respuesta_identifier = $row[0];
    return $respuesta_identifier;
}

function dphpforms_form_exist($id){
    $db_connection = pg_connect("host=localhost dbname=formularios user=administrator password=administrator");
    $sql = "
    
        SELECT * FROM formularios WHERE id = '" . $id . "'
    
    ";
    $result = pg_query($db_connection, $sql);
    $row = pg_fetch_row($result);
    $form_id = $row[0];
    if($form_id != null){
        return true;
    }else{
        return false;
    }
}

function dphpforms_pregunta_exist_into_form($pregunta_identifier){
    $db_connection = pg_connect("host=localhost dbname=formularios user=administrator password=administrator");
    $sql = "
    
        SELECT * FROM formulario_preguntas WHERE id = '" . $pregunta_identifier . "'
    
    ";
    $result = pg_query($db_connection, $sql);
    $row = pg_fetch_row($result);
    $pregunta_identifier = $row[0];
    if($pregunta_identifier != null){
        return true;
        
    }else{
        return false;
        
    }
}

function dphpforms_get_form_reglas($form_id){
    $db_connection = pg_connect("host=localhost dbname=formularios user=administrator password=administrator");
    $sql = "
    
        SELECT * FROM reglas_formulario_preguntas RFP INNER JOIN reglas R ON RFP.id_regla = R.id WHERE RFP.id_formulario = '" . $form_id . "'
    
    ";
    $result = pg_query($db_connection, $sql);
    
    $reglas = array();
    while($row = pg_fetch_row($result)){

        $regla = array(
            'respuesta_a' => $row[3],
            'regla' => $row[6],
            'respuesta_b' => $row[4]
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
            echo "Oops, algo pasa con las respuestas ingresadas\n";
            break;
        }

        if($regla == 'DIFFERENT'){

            if($respuesta_a->{'valor'} == $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);
                break;
            }else{
                $satisfied_reglas = true;
                echo "REGLA " . $regla . " CUMPLIDA\n";
            }
        }elseif($regla == 'EQUAL'){

            if($respuesta_a->{'valor'} != $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);
                break;
            }else{
                $satisfied_reglas = true;
                echo "REGLA " . $regla . " CUMPLIDA\n";
            }
        }elseif($regla == '>'){
            
            if($respuesta_a->{'valor'} < $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);
                break;
            }else{
                $satisfied_reglas = true;
                echo "REGLA " . $regla . " CUMPLIDA\n";
            }
        }elseif($regla == '<'){
            
            if($respuesta_a->{'valor'} > $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);
                break;
            }else{
                $satisfied_reglas = true;
                echo "REGLA " . $regla . " CUMPLIDA\n";
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
                echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);
                echo 'VALOR A' . $respuesta_a->{'valor'} . ' VALOR B' . $respuesta_a->{'valor'};
                break;
            }elseif((($respuesta_a->{'valor'} == null)||($respuesta_a->{'valor'} == "-#$%-")) && (($respuesta_b->{'valor'} != null) && ($respuesta_b->{'valor'} != "-#$%-") )){
                $satisfied_reglas = false;
                echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);
                echo 'VALOR A' . $respuesta_a->{'valor'} . ' VALOR B' . $respuesta_a->{'valor'};
                break;
            }else{
                $satisfied_reglas = true;
                echo "REGLA " . $regla . " CUMPLIDA\n";
            }
        }
    }

    return $satisfied_reglas;
}

function dphpforms_get_respuestas_form_completed($idFormularioDiligenciado){
    $db_connection = pg_connect("host=localhost dbname=formularios user=administrator password=administrator");
    $sql_respuestas = '
    
        SELECT * 
        FROM respuestas AS R 
        INNER JOIN 
            (
                SELECT * 
                FROM formulario_respuestas AS FR 
                INNER JOIN formulario_soluciones AS FS 
                ON FR.id = FS.id_formulario_respuestas 
                WHERE FR.id = '.$idFormularioDiligenciado.'
            ) AS FRS 
        ON FRS.id_respuesta = R.id;
    
    ';

    $result = pg_query($db_connection, $sql_respuestas);
    
    $respuestas = array();
    $row = pg_fetch_row($result);
    while($row){
        $tmp = array(
            'id' => $row[1],
            'valor' => $row[2]
        );
        array_push($respuestas, $tmp);
        $row = pg_fetch_row($result);
    }

    return $respuestas;

}

?>