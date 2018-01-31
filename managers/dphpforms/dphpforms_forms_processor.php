<?php
require_once(dirname(__FILE__). '/../../../../config.php');
/*
PENDIENTE PARA MOVERSE A LA DOCUMENTACIÓN
$formulario = '{
    "datos_formulario":{
        "nombre":"Formulario JSON",
        "descripcion":"Primer formulario escrito en JSON para pruebas de registro",
        "method":"POST",
        "action":"procesador_formularios.php",
        "enctype":null
    },
    "preguntas":[
        {
            "id_temporal":"cmp_0",
            "enunciado":"Nombre(s)",
            "tipo_campo":"TEXTFIELD",
            "opciones_campo":"",
            "atributos_campo":{"placeholder":"Nombre", "type":"text"},
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_1",
            "enunciado":"Apellidos",
            "tipo_campo":"TEXTFIELD",
            "opciones_campo":"",
            "atributos_campo":{"placeholder":"Apellido"},
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_2",
            "enunciado":"Contraseña",
            "tipo_campo":"TEXTFIELD",
            "opciones_campo":"",
            "atributos_campo":{"type":"password", "placeholder":"*****"},
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_3",
            "enunciado":"Género",
            "tipo_campo":"RADIOBUTTON",
            "opciones_campo":[
                "Masculino",
                "Femenino"
            ],
            "atributos_campo":"",
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_4",
            "enunciado":"Permisos",
            "tipo_campo":"CHECKBOX",
            "opciones_campo":[
                "Lectura",
                "Escritura"
            ],
            "atributos_campo":"",
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_5",
            "enunciado":"¿Registra BIO?",
            "tipo_campo":"CHECKBOX",
            "opciones_campo":[
                "Sí"
            ],
            "atributos_campo":"",
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_6",
            "enunciado":"BIO",
            "tipo_campo":"TEXTAREA",
            "opciones_campo":"",
            "atributos_campo":{"placeholder":"Biografía"},
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_7",
            "enunciado":"Edad",
            "tipo_campo":"TEXTFIELD",
            "opciones_campo":"",
            "atributos_campo":{"placeholder":"Edad", "type":"number"},
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_8",
            "enunciado":"Email",
            "tipo_campo":"TEXTFIELD",
            "opciones_campo":"",
            "atributos_campo":{"placeholder":"Correo electrónico", "type":"email"},
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_9",
            "enunciado":"Correo de recuperación",
            "tipo_campo":"TEXTFIELD",
            "opciones_campo":"",
            "atributos_campo":{"placeholder":"example@domain.com", "type":"email"},
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_10",
            "enunciado":"Escriba OSO",
            "tipo_campo":"TEXTFIELD",
            "opciones_campo":"",
            "atributos_campo":{"placeholder":"Verificación", "type":"text"},
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_11",
            "enunciado":"Repita la contraseña",
            "tipo_campo":"TEXTFIELD",
            "opciones_campo":"",
            "atributos_campo":{"type":"password", "placeholder":"*****"},
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        },
        {
            "id_temporal":"cmp_12",
            "enunciado":"Escriba un número mayor a su edad",
            "tipo_campo":"TEXTFIELD",
            "opciones_campo":"",
            "atributos_campo":{"placeholder":"Verificación", "type":"number"},
            "permisos_campo":[{ "rol":0, "permisos":["lectura", "escritura"] }]
        }
    ],
    "reglas":[
        {
            "id_temporal_campo_a":"cmp_0",
            "id_temporal_campo_b":"cmp_2",
            "regla":"DIFFERENT"
        },
        {
            "id_temporal_campo_a":"cmp_6",
            "id_temporal_campo_b":"cmp_5",
            "regla":"BOUND"
        },
        {
            "id_temporal_campo_a":"cmp_8",
            "id_temporal_campo_b":"cmp_9",
            "regla":"EQUAL"
        },
        {
            "id_temporal_campo_a":"cmp_11",
            "id_temporal_campo_b":"cmp_2",
            "regla":"EQUAL"
        },
        {
            "id_temporal_campo_a":"cmp_12",
            "id_temporal_campo_b":"cmp_7",
            "regla":">"
        },
        {
            "id_temporal_campo_a":"cmp_7",
            "id_temporal_campo_b":"cmp_12",
            "regla":"<"
        }
    ]}';*/

$form = json_decode(file_get_contents("php://input"));

/*
if(!$form){
    echo json_encode(
        array(
            'id_formulario' => '-1',
            'mensaje_error' => 'NULL'
        )
    );
    die();
}*/

dphpforms_store_form($form);

function dphpforms_store_form($form_JSON){

    $json_obj_form = $form_JSON;
    
    $form_db_id = null; 
    $form_details = array(
        'nombre' => $json_obj_form->{'datos_formulario'}->{'nombre'},
        'descripcion' => $json_obj_form->{'datos_formulario'}->{'descripcion'},
        'method' => $json_obj_form->{'datos_formulario'}->{'method'},
        'action' => $json_obj_form->{'datos_formulario'}->{'action'},
        'enctype' => $json_obj_form->{'datos_formulario'}->{'enctype'}
    );

    $form_db_id = dphpforms_store_form_details($form_details);

    

    $identifiers_preguntas = array();
    foreach ($json_obj_form->{'preguntas'} as &$pregunta) {
        $pregunta_details = array(
            'tipo_campo' => $pregunta->{'tipo_campo'},
            'opciones_campo' => $pregunta->{'opciones_campo'},
            'atributos_campo' => $pregunta->{'atributos_campo'},
            'enunciado' => $pregunta->{'enunciado'},
            'permisos_campo' => $pregunta->{'permisos_campo'} 
        );
        array_push(
            $identifiers_preguntas, 
            array( 
                'idPreguntaDB' => dphpforms_store_pregunta($pregunta_details),
                'idPreguntaTemporal' => $pregunta->{'id_temporal'},
                'permisosCampo' => json_encode($pregunta->{'permisos_campo'})
            )
        );
    }

    $identifiers_form_preguntas = array();
    foreach ($identifiers_preguntas as $key => $identifiers_pregunta) {
        array_push($identifiers_form_preguntas,
            array(
                'idRelacionFormPreg' => dphpforms_store_form_pregunta($form_db_id, $identifiers_pregunta['idPreguntaDB'], $key),
                'idPreguntaDB' => $identifiers_pregunta['idPreguntaDB'],
                'idPreguntaTemporal' => $identifiers_pregunta['idPreguntaTemporal']
            )
        );
    }


    $identifiers_reglas = array();
    if(property_exists($json_obj_form, 'reglas')){
        foreach ($json_obj_form->{'reglas'} as &$regla) {
            $identifier_pregunta_A = null;
            $identifier_pregunta_B = null;
           
            for($i = 0; $i < count($identifiers_form_preguntas); $i++){
                
                if($identifiers_form_preguntas[$i]['idPreguntaTemporal'] == $regla->{'id_temporal_campo_a'}){
                    $identifier_pregunta_A = $identifiers_form_preguntas[$i]['idRelacionFormPreg'];
                }
    
                if($identifiers_form_preguntas[$i]['idPreguntaTemporal'] == $regla->{'id_temporal_campo_b'} ){
                    $identifier_pregunta_B = $identifiers_form_preguntas[$i]['idRelacionFormPreg'];
                }
    
                if(($identifier_pregunta_A != null)&&($identifier_pregunta_B != null)){
                    break;
                }
            }
            
            array_push($identifiers_reglas, dphpforms_store_form_regla($form_db_id, $regla->{'regla'}, $identifier_pregunta_A, $identifier_pregunta_B));
        }
    }

    $identifiers_disparadores = dphpforms_store_form_disparadores($form_db_id, $json_obj_form->{'disparadores'}, $identifiers_form_preguntas);
    if(!$identifiers_disparadores){
        echo json_encode(
            array(
                'id_formulario' => '-1',
                'mensaje_error' => 'ERROR REGISTRANDO DISPARADORES'
            )
        );
        die();
    }

    echo json_encode(
        array(
            'id_formulario' => $form_db_id,
            'mensaje_error' => ''
        )
    );
    
}

function dphpforms_store_form_details($form_details){

    global $DB;
     
    $obj_form_details = new stdClass();
    $obj_form_details->nombre = $form_details['nombre'];
    $obj_form_details->descripcion = $form_details['descripcion'];
    $obj_form_details->method = $form_details['method'];
    $obj_form_details->action = $form_details['action'];
    $obj_form_details->enctype = $form_details['enctype'];

    $form_id = $DB->insert_record('talentospilos_df_formularios', $obj_form_details, $returnid=true, $bulk=false) ;
    return $form_id;
}

function dphpforms_store_pregunta($pregunta_details){

    global $DB;

    $result = null;
    $sql = "SELECT * FROM {talentospilos_df_tipo_campo}";
    $result = $DB->get_records_sql($sql);
    $result = (array) $result;
    
    $fields = array();
    $result = array_values($result);
    if(count($result) > 0){
        for($i = 0; $i < count($result); $i++){
            $row = $result[$i];
            array_push($fields, array('id' => $row->id, 'campo' => $row->campo));
        }
    }

    foreach($fields as &$field){
        if(in_array($pregunta_details['tipo_campo'], $field)){
            $pregunta_details['tipo_campo'] = (int) $field['id'];
        };
    };    

    $obj_pregunta = new stdClass();
    $obj_pregunta->tipo_campo = $pregunta_details['tipo_campo'];
    $obj_pregunta->opciones_campo = json_encode($pregunta_details['opciones_campo']);
    $obj_pregunta->atributos_campo = json_encode($pregunta_details['atributos_campo']);
    $obj_pregunta->enunciado = $pregunta_details['enunciado'];

    $pregunta_identifier = $DB->insert_record('talentospilos_df_preguntas', $obj_pregunta, $returnid=true, $bulk=false);

    $obj_permisos_formulario_pregunta = new stdClass();
    $obj_permisos_formulario_pregunta->id_formulario_pregunta = $pregunta_identifier;
    $obj_permisos_formulario_pregunta->permisos = json_encode($pregunta_details['permisos_campo']);

    $permission_identifier = $DB->insert_record('talentospilos_df_per_form_pr', $obj_permisos_formulario_pregunta, $returnid=true, $bulk=false);

    /*if($permission_identifier){
        echo " PERMISO REGISTRADO. ";
    }*/
    
    return $pregunta_identifier;
}

function dphpforms_store_form_pregunta($form_id, $identifier_pregunta, $position){
    
    global $DB;

    $obj_form_preguntas = new stdClass();
    $obj_form_preguntas->id_formulario = $form_id;
    $obj_form_preguntas->id_pregunta = $identifier_pregunta;
    $obj_form_preguntas->posicion = $position;

    $idRelacion = $DB->insert_record('talentospilos_df_form_preg', $obj_form_preguntas, $returnid=true, $bulk=false);

    //$identifier_permission = dphpforms_store_form_pregunta_permits($idRelacion, $permits);
    if(!$idRelacion){
        echo json_encode(
            array(
                'id_formulario' => '-1',
                'mensaje_error' => 'ERROR REGISTRANDO PREGUNTA'
            )
        );
        die();
    }

    return $idRelacion;

}

function dphpforms_store_form_regla($form_id, $text_rule, $identifier_pregunta_A, $identifier_pregunta_B){
    
    global $DB;

    $identifier_regla = null;

    $sql = "SELECT * from {talentospilos_df_reglas}";
    $result = $DB->get_records_sql($sql);
    $result = (array) $result;

    if(count($result) > 0){
        for($i = 1; $i < count($result); $i++){
            $row = $result[$i];
            if($row->regla == $text_rule){
                $identifier_regla = $row->id;
                break;
            }
        }
    }

    $obj_regla_form_pregunta = new stdClass();
    $obj_regla_form_pregunta->id_formulario         = $form_id;
    $obj_regla_form_pregunta->id_regla              = $identifier_regla;
    $obj_regla_form_pregunta->id_form_pregunta_a    = $identifier_pregunta_A;
    $obj_regla_form_pregunta->id_form_pregunta_b    = $identifier_pregunta_B;

    $regla_identifier = $DB->insert_record('talentospilos_df_reg_form_pr', $obj_regla_form_pregunta, $returnid=true, $bulk=false);

    return $regla_identifier;

}

function dphpforms_store_form_pregunta_permits($form_id_pregunta, $permits){
    
    global $DB;

    $obj_permisos_formulario_pregunta = new stdClass();
    $obj_permisos_formulario_pregunta->id_formulario_pregunta = $form_id_pregunta;
    $obj_permisos_formulario_pregunta->permisos = $permits;

    //echo ' INFO: FORM ID PREGUNTA ' . $form_id_pregunta . ' PERMISOS ' . $permits;
    //print_r($obj_permisos_formulario_pregunta);
    $identifier_permission = $DB->insert_record('talentospilos_df_per_form_pr', $obj_permisos_formulario_pregunta, $returnid=true, $bulk=false);
    //echo ' ID_PERMISO:::::: ' . $identifier_permission . ' :::::::';
    return $identifier_permission;
}

function dphpforms_store_form_disparadores($form_id, $disparadores, $identifiers_form_preguntas){

    global $DB;
    $disparadores_string = json_encode($disparadores);
    foreach ($identifiers_form_preguntas as &$value) {
        //echo 'REEMPLAZO DE'  . $value['idPreguntaTemporal'] . ' CON ' . $value['idRelacionFormPreg'] . ' <::::';
        $disparadores_string = str_replace('"'.$value['idPreguntaTemporal'].'"', '"'.$value['idRelacionFormPreg'].'"', $disparadores_string);
        if($value['idPreguntaTemporal'] == 'cmp_17'){
            //echo '>' . $value['idPreguntaTemporal'] . 'NUEVO CAMPO ::::::::' . $value['idRelacionFormPreg'];
            //echo $disparadores_string;
        }
    }
    //echo $disparadores_string;


    $obj_disparadores_permisos_formulario_diligenciado = new stdClass();
    $obj_disparadores_permisos_formulario_diligenciado->id_formulario = $form_id;
    $obj_disparadores_permisos_formulario_diligenciado->disparadores = $disparadores_string;

    $identifier_disparador = $DB->insert_record('talentospilos_df_disp_fordil', $obj_disparadores_permisos_formulario_diligenciado, $returnid=true, $bulk=false);
    return $identifier_disparador;
    
}

?>