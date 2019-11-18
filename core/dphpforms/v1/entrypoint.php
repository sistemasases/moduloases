<?php 
/**
 * @package	block_ases
 * @subpackage	core.dphpforms
 * @author 	Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__). '/../../../core/module_loader.php'); 
require_once( __DIR__ . "/DOMTools.php");
module_loader("security");

// -- Dev test block - This block cannot be considerated as documentation.
/*header('Content-Type: application/json');
 /*$xQuery = new stdClass();
 * $xQuery->form = "seguimiento_pares"; // Can be alias(String) or identifier(Number)
 * $xQuery->filterFields = [
 *     ["id_estudiante",[ ["%%","LIKE"]], false],
 *     ["fecha",[ ["%%","LIKE"] ], false],
 *     ["revisado_practicante",[ ["%%","LIKE"] ], false],
 *     ["revisado_profesional",[ ["%%","LIKE"] ], false]
 * ];
 * $xQuery->orderFields = [ ["fecha","DESC"] ];
 * $xQuery->orderByDatabaseRecordDate = false; // If true, 'orderField' is ignored. DESC
 * $xQuery->recordStatus = [ "!deleted" ]; // options "deleted" or "!deleted", can be both. Empty = both.
 * $xQuery->asFields = [ [ [ function( $_this ){ return (int) $_this['id_registro'] ; } ], "id_estudiante" ], ["revisado_profesional", "id_estudiante"] ]; 
 * $xQuery->selectedFields = [ "id_creado_por", "id_estudiante" ]; // Without support.
echo json_encode( _dphpforms_find_records( $xQuery ) );*/
// -- End Dev test block


/**
 * Function that given a valid xQuery returns the execution result.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @see _dphpforms_validate_xquery( ... ) in this file
 * @param Object $query stdClass with the specified properties. 
 * @return stdClass | Array
 */
 function _dphpforms_find_records( $query ){

    global $DB;

    $form = _dphpforms_get_form_info( $query->form );
    
    $validation_status = _dphpforms_validate_xquery($query);
    if( $validation_status['status_code'] === -1 ){
        return $validation_status;
    }
    
    $fields = _dphpforms_get_fields_form( $form->id );
    $list_fields_alias = [];
    $list_fields_alias_id = [];
    $list_fields_id_alias = [];
    $list_fields_data_type = [];
    $list_filter_fields_alias = [];
        
    foreach( $fields as $field ){
        array_push( $list_fields_alias, $field->local_alias );
        $list_fields_alias_id[$field->local_alias] = $field->id_pregunta;
        $list_fields_id_alias[$field->id_pregunta] = $field->local_alias;
        $list_fields_data_type[$field->id_pregunta] = $field->tipo_campo;
     }

     $sql_query = "";
     //Find with where clause
     if( count( $query->filterFields ) > 0 ){

        $flag_deleted = false;
        $flag_non_deleted = false;
        foreach( $query->recordStatus as $status ){
            if( $status === "deleted" ){
                $flag_deleted = true;
            }elseif( $status === "!deleted" ){
                $flag_non_deleted = true;
            }
        }

        $status = "";
        if( !$flag_deleted && $flag_non_deleted ){
            $status = "1";
        }elseif( $flag_deleted && !$flag_non_deleted ){
            $status = "0";
        }
        
        $sql_first_parameter = "SELECT DISTINCT id AS id_formulario_respuestas
                                FROM {talentospilos_df_form_resp}";
        
        if( $status !== "" ){
            $sql_first_parameter .= " WHERE estado = $status";
        }

        $inner_join_more_responses = "SELECT id_respuesta, FS1.id_formulario_respuestas
                                      FROM {talentospilos_df_form_solu} AS FS1 
                                      INNER JOIN ( $sql_first_parameter ) AS PQ ON FS1.id_formulario_respuestas = PQ.id_formulario_respuestas 
                                      ORDER BY FS1.id_formulario_respuestas ASC";
        
        $inner_join_values = "SELECT R3.id, IJMR.id_formulario_respuestas, R3.respuesta, R3.id_pregunta, R3.fecha_hora_registro
                              FROM {talentospilos_df_respuestas} AS R3 
                              INNER JOIN ( $inner_join_more_responses ) AS IJMR ON id_respuesta = R3.id";

        $where_clause = "";
        if( count( $query->filterFields ) > 0 ){
            $where_clause = "WHERE ";
            $first_filter_field = true;

            $filter_fields = $query->filterFields;
            
            foreach( $filter_fields as $filterField ){

                $fieldAlias = $filterField[0];
                $filterValues = $filterField[1];
                $optional =  $filterField[2];

                $filter_where = "";
                $belongs_block_AND = false;
                
                if( !$first_filter_field ){
                    if( $tmpNextFilterField = next($filter_fields) ){
                        $filter_where .= " OR ";
                    }
                }else{
                    $first_filter_field = false;
                }

                if( $optional ){
                    $belongs_block_AND = false;
                }

                foreach( $filterValues as $filterValue ){
                    $filter_where .= "(id_pregunta = " .$list_fields_alias_id[$fieldAlias]. " AND respuesta ".$filterValue[1]." '". $filterValue[0] . "')";
                    if( next($filterValues) ){
                        $filter_where .= " AND ";
                    }
                }

                $where_clause .= $filter_where;

            }
    
        };

        $sql_query = $inner_join_values . " " . $where_clause;

     };

     //Grouping
     $records =  $DB->get_records_sql( $sql_query );
     $records_ids =  [];
     $grouped_records = [];
     foreach( $records as $record ){
        array_push( $records_ids, $record->id_formulario_respuestas );
        $grouped_records[ $record->id_formulario_respuestas ][ "fecha_hora_registro" ] = strtotime($record->fecha_hora_registro);
        $grouped_records[ $record->id_formulario_respuestas ][ "id_registro" ] = $record->id_formulario_respuestas;
        $grouped_records[ $record->id_formulario_respuestas ][ $list_fields_id_alias[ $record->id_pregunta ] ] = $record->respuesta;
     };

     $records_ids = array_values(array_unique( $records_ids ));

     //echo( $sql_query . "\n" );

     $valid_records = [];

     //Si el registro agrupado tiene los campos para filtrar
    foreach($records_ids as $record_id){
         
         $record_completed = true;
         foreach( $query->filterFields as $filterField ){
            $field_alias = $filterField[0];
            $id_field = $list_fields_alias_id[ $field_alias ];
            $value_to_comparate = $filterField[1];
            $optional = $filterField[2];
            $operator = @$filterField[3]; //Implementación pendiente AND, OR
            $exist_in_grouped_record = array_key_exists( $field_alias, $grouped_records[$record_id] );
            if( !$exist_in_grouped_record && !$optional ){
                $record_completed = false;
            }
         };
         if($record_completed){
             //array_push($valid_records,$record_id);
             array_push($valid_records,$grouped_records[$record_id]);
         }
    }

    //asFields support
    if( count( $query->asFields ) > 0 ){
        $asFields = $query->asFields;
        foreach( $valid_records as &$valid_record ){
            foreach( $asFields as $key => $asField ){
                $type = gettype( $asField[0] );
                if( $type === "string" ){
                    $valid_record[$asField[1]] = $valid_record[$asField[0]];
                }else if( $type === "array" ){
                    $_this = $valid_record;
                    $callable_lambda = $asField[0][0];
                    $params = [ $_this ];
                    $first_callable = true;
                    foreach( $asField[0] as $key => $param ){
                        if( $first_callable ){
                            $first_callable = false;
                        }else{
                            array_push( $params, $param );
                        }
                    }
                    try {
                        $valid_record[$asField[1]] = call_user_func_array( $callable_lambda, $params );
                    }catch(Exception $e) {
                        $valid_record[$asField[1]] = $e->getMessage();
                    }
                }
            }
        }
     }

     if( !$query->orderByDatabaseRecordDate ){
        foreach ($query->orderFields as $orderField) {

            $alias = $orderField[0];
            $order = $orderField[1];
            $key_to_sort = array(); 

            foreach ($valid_records as $key => $record){
                $key_to_sort[$key] = $record[ $alias ];
            }
            if( strtoupper( $order ) === "ASC" ){
                array_multisort($key_to_sort, SORT_ASC, $valid_records);
            }elseif( strtoupper( $order ) === "DESC"  ){
                array_multisort($key_to_sort, SORT_DESC, $valid_records);
            }
        }   
     }else{
        $key_to_sort = array(); 
        foreach ($valid_records as $key => $record){
            $key_to_sort[$key] = $record[ "registered_timestamp" ];
        }
        array_multisort($key_to_sort, SORT_DESC, $valid_records);
     }

     //print_r( $valid_records );

     /*$sql = "";
     $filter = "";
     $ids = "";

     foreach( $query->selectedFields as $selectedField ){
        $filter .= "R.id_pregunta = " . $list_fields_alias_id[ $selectedField ];
        if( next( $query->selectedFields ) ){
            $filter .= " OR ";
        }
     }

     foreach( $valid_records as $record_id ){
        $ids .= "FS.id_formulario_respuestas = $record_id";
        if( next($valid_records) ){
            $ids .= " OR ";
        }
     }

     $sql .= "SELECT *
        FROM {talentospilos_df_respuestas} AS R
        INNER JOIN {talentospilos_df_form_solu} AS FS ON FS.id_respuesta = R.id
        WHERE ( $ids ) AND ( $filter )";

    $DB->get_records_sql( $sql );*/

    return $valid_records;

 }

 function _dphpforms_find_n_count_records( $query ){

    $response = _dphpforms_find_records( $query );
    if( $response["status_code"] !== -1 ){
        return count( $response );
    }else{
        return -1;
    }

 }

 /**
 * Function that return the basic dynamic form information.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int/$string Alias or identifier 
 * @return stdClass with id, nombre, alias, descripcion, method, action, enctype, fecha_hora_registro, estado
 */
 function _dphpforms_get_form_info( $alias_identifier ){
    
    global $DB;

    $criteria = "id = $alias_identifier";
    if( !is_numeric( $alias_identifier ) ){
        $criteria = "alias = '$alias_identifier'";
    }

    $sql = "SELECT id, nombre, alias, descripcion, method, action, enctype, fecha_hora_registro, estado 
    FROM {talentospilos_df_formularios} 
    WHERE $criteria
    AND estado = 1";

    return $DB->get_record_sql( $sql );

 }

 /**
 * Function that return a list of forms by criteria.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int/$string Alias or identifier 
 * @return stdClass
 */
function _dphpforms_get_find_forms( $column_name, $value, $using_like = false, $status = 1 ){
    
    global $DB;

    if( !$column_name || !$value ){
        return [];
    }

    $criteria = "$column_name = '$value'";
    if( $using_like == true ){
        $criteria = "LIKE $column_name '%$value%'";
    }

    $sql = "SELECT id, nombre, alias, descripcion, method, action, enctype, fecha_hora_registro, estado 
    FROM {talentospilos_df_formularios} 
    WHERE $criteria
    AND estado = $status";

    return $DB->get_records_sql( $sql );

 }

 /**
 * Function that return a list of form fields.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $form_id 
 * @param int $status: 0 = deleted.
 * @return stdClass
 */
function _dphpforms_get_fields_form( $form_id, $status = 1 ){
    
    global $DB;

    if( !is_numeric( $form_id ) && !is_numeric( $status )  ){
        return [];
    }

    $sql = 
    "SELECT FP.id AS id_formulario_pregunta, FP.id_pregunta, P.enunciado, TC.campo AS tipo_campo, FP.posicion, P.atributos_campo, P.opciones_campo, P.fecha_hora_registro 
    FROM {talentospilos_df_form_preg} AS FP
    INNER JOIN (SELECT * FROM {talentospilos_df_preguntas} )AS P
    ON FP.id_pregunta = P.id
    INNER JOIN (SELECT * FROM {talentospilos_df_tipo_campo} ) AS TC
    ON P.tipo_campo = TC.id
    WHERE FP.id_formulario = $form_id
    AND FP.estado = $status
    ";

    $fields = $DB->get_records_sql( $sql );
    $fields = array_values( $fields );
    for( $i = 0; $i < count( $fields ); $i++ ){
        $atributos_campo = json_decode( $fields[$i]->atributos_campo );
        $opciones_campo = json_decode( $fields[$i]->opciones_campo );
        $fields[$i]->opciones_campo = $opciones_campo;
        $fields[$i]->atributos_campo = $atributos_campo;
        $fields[$i]->local_alias = $atributos_campo->local_alias;
    }

    return $fields;

 }

 
function _dphpforms_reverse_new_field_update( $form_id_alias, $id_pregunta, $default_value ){

    global $DB;

    $form_info = _dphpforms_get_form_info( $form_id_alias );

    $records_to_update = "SELECT id AS id_formulario_respuestas
    FROM {talentospilos_df_form_resp} 
    WHERE id_formulario = ( SELECT id FROM {talentospilos_df_formularios} WHERE alias = '" . $form_info->alias . "' AND estado = 1 ) 
    
    EXCEPT    
    
    SELECT FS.id_formulario_respuestas 
    FROM {talentospilos_df_form_solu} AS FS 
    INNER JOIN {talentospilos_df_respuestas} AS R 
    ON FS.id_respuesta = R.id 
    WHERE R.id_pregunta = $id_pregunta";

    $records = $DB->get_records_sql( $records_to_update );

    $fails = [];
    $correct = [];

    foreach( $records as $key => $record ){

        $return = _dphpforms_store_reverse_rield( $record->id_formulario_respuestas, $id_pregunta, $default_value );
        if( !$return ){
            array_push( $fails, $record->id_formulario_respuestas  );
        }else{
            array_push( $correct, $record->id_formulario_respuestas  );
        }
    }

    $to_return = new stdClass();
    $to_return->fails = $fails;
    $to_return->correct = $correct;

    return $to_return;

}

function _dphpforms_get_records_reverse_new_field_update( $id_respuesta, $form_id_alias ){

    global $DB;

    $form_info = _dphpforms_get_form_info( $form_id_alias );

    $records_to_update = "SELECT id AS id_formulario_respuestas
    FROM {talentospilos_df_form_resp} 
    WHERE id_formulario = ( SELECT id FROM {talentospilos_df_formularios} WHERE alias = '" . $form_info->alias . "' AND estado = 1 ) 
    
    EXCEPT    
    
    SELECT FS.id_formulario_respuestas 
    FROM {talentospilos_df_form_solu} AS FS 
    INNER JOIN {talentospilos_df_respuestas} AS R 
    ON FS.id_respuesta = R.id 
    WHERE R.id_pregunta = $id_respuesta";

    return $DB->get_records_sql( $records_to_update );

}

function _dphpforms_store_reverse_rield( $form_response_id, $id_pregunta, $value ){

    global $DB;

    $sql_form_solu_exist = 
    "SELECT FU.id FROM {talentospilos_df_form_solu} AS FU
    INNER JOIN {talentospilos_df_respuestas} AS R ON FU.id_respuesta = R.id
    WHERE R.id_pregunta = $id_pregunta AND FU.id_formulario_respuestas = $form_response_id";

    //If it does not exist.
    if( !$DB->get_record_sql( $sql_form_solu_exist ) ){
 
        $respuesta = _dphpforms_store_respuesta( $id_pregunta, $value );
        if( $respuesta ){
            return _dphpforms_store_form_soluciones( $form_response_id, $respuesta );
        }else{
            return null;
        }

    }
}

function _dphpforms_store_respuesta( $id, $value ){
    
    global $DB;

    $obj_respuesta = new stdClass();
    $obj_respuesta->id_pregunta = $id;
    $obj_respuesta->respuesta = $value;

    $pregunta = _dphpforms_get_pregunta( $id );

    if( $pregunta ){

        if( _dphpforms_regex_validator( $id, $value )->status ){
            $respuesta_identifier = $DB->insert_record('talentospilos_df_respuestas', $obj_respuesta, $returnid=true, $bulk=false);
            return $respuesta_identifier;
        }
        
    }else{
        return null;
    }
}

function _dphpforms_regex_validator( $id, $value ){

    global $DB;

    $to_return = new stdClass();
    $to_return->status = true;
    $to_return->human_readable = "";
    $to_return->example =  "";

    $pregunta_obj = _dphpforms_get_pregunta( $id );
    $tipo_campo_obj = _dphpforms_tipo_campo( $pregunta_obj->tipo_campo );

    $regex = $tipo_campo_obj->expresion_regular;

    if( $regex ){

        if( preg_match( $regex, $value ) == 0 ){

            $to_return = new stdClass();
            $to_return->status = false;
            $to_return->human_readable = $tipo_campo_obj->regex_legible_humanos;
            $to_return->example =  $tipo_campo_obj->ejemplo;
             
        }
    }

    return $to_return;

}

function _dphpforms_get_pregunta( $id ){

    global $DB;

    $sql = "SELECT * FROM {talentospilos_df_preguntas} WHERE id = " . $id;
    return $DB->get_record_sql( $sql );

}

function _dphpforms_tipo_campo( $id ){

    global $DB;

    $sql = "SELECT * FROM {talentospilos_df_tipo_campo} WHERE id = " . $id;
    return $DB->get_record_sql( $sql );

}

function _dphpforms_store_form_soluciones($form_response_id, $respuesta_identifier){

    global $DB;

    $obj_form_soluciones = new stdClass();
    $obj_form_soluciones->id_formulario_respuestas = $form_response_id;
    $obj_form_soluciones->id_respuesta = $respuesta_identifier;
   
    $form_solucines_identifier = $DB->insert_record('talentospilos_df_form_solu', $obj_form_soluciones, $returnid=true, $bulk=false);
    return $form_solucines_identifier;

}

function _dphpforms_get_permisos_pregunta( $id_formulario_pregunta ){
    
    global $DB;
    $sql =  "SELECT * FROM {talentospilos_df_per_form_pr} WHERE id_formulario_pregunta = '$id_formulario_pregunta'";
    $permisos_obj = $DB->get_record_sql( $sql );
    if( $permisos_obj ){
        return $permisos_obj->permisos;
    }else{
        return null;
    }

}

function _dphpforms_generate_html_recorder( $id_form, $rol_, $initial_config = null, bool $minify = false  ){

    global $DB;

    $FORM_ID = null;
    $ROL = $rol_;
    $html = null;
    
    $dom = new DOMDocument();
    
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;

    $form_info = _dphpforms_get_form_info( $id_form );
    if( $form_info ){
        if( $form_info->estado == 1 ){
            $FORM_ID = $form_info->id;
        }else{
            return '';
        }
    }else{
        return '';
    }

    $form_name_formatted = $form_info->alias . "_" . $form_info->id;
    
    $register_button = '<button data-form-id="'.$form_name_formatted.'" type="submit" class="btn-dphpforms btn-dphpforms-sendform">Registrar</button>';
    $register_button = _core_dphpforms_build_tag( $dom, "button", new DOMAttributeList([
        "data-form-id" => $form_name_formatted,
        'type' => "submit",
        'class' => [
            "btn-dphpforms",
            "btn-dphpforms-sendform"
        ]
    ]) );
    $register_button->nodeValue = "Registrar";
    
    $form_action = $form_info->action;
    
    if( $initial_config ){
        if( property_exists($initial_config, 'allow_register') ){
            if( !$initial_config->allow_register ){
                $register_button = NULL;
                $form_action = uniqid();
            }
        }
    }

    /*$aditional_form_classes = "";
    if( $initial_config ){
        if( property_exists($initial_config, 'aditional_form_classes') ){
            $aditional_form_classes = array_map(
                function($class){
                    $default_classes = [
                        "dphpforms",
                        "dphpforms-response"
                    ];
                    if( in_array( $class, $default_classes ) ){
                        return null;
                    }else{
                        return $class;
                    }
                },
                $initial_config->aditional_form_classes
            );
            $aditional_form_classes = join( $aditional_form_classes, " " );
        }
    }*/
    
    $form_uniqid = uniqid("dphpforms_",true);
    
    /*$html ='<form id="'. $form_name_formatted .'" data-uid="'. $form_uniqid .'" method="'. $form_info->method .'" action="'. $form_action .'" class="dphpforms dphpforms-response '.$aditional_form_classes.'">
        <h1>'.$form_info->nombre.'</h1><hr class="header-hr-dphpforms">
        <input name="id" value="'. $form_info->id .'" style="display:none;">
    ';*/
    
    $form = _core_dphpforms_build_tag($dom, "form", new DOMAttributeList([
        'id' => $form_name_formatted,
        'data-uid' => $form_uniqid,
        'data-dphpforms' => "dphpforms",
        'method' => $form_info->method,
        'action' => $form_action,
        'class' => array_merge( [ 'dphpforms', 'dphpforms-response' ], $aditional_form_classes )
    ]));
    
    $title = _core_dphpforms_build_tag($dom, "h1", new DOMAttributeList([]));
    $title->nodeValue = $form_info->nombre;
    
    $title_separator = _core_dphpforms_build_tag($dom, "hr", new DOMAttributeList([
        'class' => [ 'dphpforms-header-hr' ]
    ]));
    
    $hidden_input_form_id = _core_dphpforms_build_tag($dom, "input", new DOMAttributeList([
        'name'=>'id',
        'value' =>  $form_info->id,
        'type' => 'hidden'
    ]));
    
    $form->appendChild( $title );
    $form->appendChild( $title_separator );
    $form->appendChild( $hidden_input_form_id );

    $dom->appendChild($form);
     

    // Form statements
    $sql = '
        SELECT * FROM {talentospilos_df_tipo_campo} AS TC 
        INNER JOIN (
            SELECT * FROM {talentospilos_df_preguntas} AS P 
            INNER JOIN (
                SELECT *, F.id AS mod_id_formulario, FP.id AS mod_id_formulario_pregunta FROM {talentospilos_df_formularios} AS F
                INNER JOIN {talentospilos_df_form_preg} AS FP
                ON F.id = FP.id_formulario WHERE F.id = '.$FORM_ID.'
                ) AS AA ON P.id = AA.id_pregunta
            ) AS AAA
        ON TC.id = AAA.tipo_campo
        ORDER BY posicion
    ';

    $result =  array_values( (array) $DB->get_records_sql($sql) );
    
    foreach($result as $key => $statement){
       
        $campo = $statement->campo;
        $enunciado = $statement->enunciado;
        $atributos = json_decode( $statement->atributos_campo );

        //Consulta de permisos
        $permisos_JSON = json_decode( _dphpforms_get_permisos_pregunta( $statement->id_pregunta ) );
        
        foreach ($permisos_JSON as $key => $v_rol) {

            if($v_rol->rol == $ROL){

                $lectura = false;
                $escritura = false;

                foreach ($v_rol->permisos as $key2 => $value) {
                    if($value == "lectura"){
                        $lectura = true;
                    }
                    if($value == "escritura"){
                        $escritura = true;
                    }

                }

                if($lectura){

                    $disabled = false;
                    if(!$escritura){
                        $disabled = true;
                    }
                    
                    $options = json_decode($statement->opciones_campo);
                    
                    $context = [
                        'attr_class' => '',
                        'attr_local_alias' => '',
                        'attr_inputclass' => '',
                        'attr_max' => '',
                        'attr_min' => '',
                        'attr_type' => '',
                        'attr_placeholder' => '',
                        'default_value' => '',
                        'attr_maxlength' => '',
                        'enabled' => $disabled,
                        'attr_required' => '',
                        'options' => $options,
                        'attr_radioclass' => '',
                        'attr_group_radio_class' => '',
                        'attr_checkclass' => ''
                    ];

                    if(property_exists($atributos, 'class')){
                        $context[ 'attr_class' ] = $atributos->class;
                    }

                    if(property_exists($atributos, 'type')){
                        $context[ 'attr_type' ] = $atributos->type;
                    }

                    if(property_exists($atributos, 'placeholder')){
                        $context[ 'attr_placeholder' ] = $atributos->placeholder;
                    }

                    if(property_exists($atributos, 'maxlength')){
                        $context[ 'attr_maxlength' ] = $atributos->maxlength;
                    }

                    if(property_exists($atributos, 'inputclass')){
                        $context[ 'attr_inputclass' ] = $atributos->inputclass;
                    }

                    if(property_exists($atributos, 'required')){
                        $field_attr_required = $atributos->required;
                        if($field_attr_required == 'true'){
                            $field_attr_required = true;
                        }elseif($field_attr_required == 'false'){
                            $field_attr_required = false;
                        }
                        
                        $context[ 'attr_required' ] = $field_attr_required;
                    }

                    if(property_exists($atributos, 'local_alias')){
                        $context[ 'attr_local_alias' ] = $atributos->local_alias;
                    }

                    if(property_exists($atributos, 'max')){
                        $field_attr_max = $atributos->max;
                        if( $field_attr_max == "today()" ){
                            $today = new DateTime('now');
                            $field_attr_max = $today->format('Y-m-d');
                        }
                        $context[ 'attr_max' ] = $field_attr_max;
                    }

                    if(property_exists($atributos, 'min')){
                        $field_attr_min = $atributos->min;
                        if( $field_attr_min == "today()" ){
                            $today = new DateTime('now');
                            $field_attr_min = $today->format('Y-m-d');
                        }
                        $context[ 'attr_min' ] = $field_attr_min;
                    }

                    if(property_exists($atributos, 'radioclass')){
                        $context[ 'attr_radioclass' ] = $atributos->radioclass;
                    }

                    if(property_exists($atributos, 'groupradioclass')){
                        $context[ 'attr_group_radio_class' ] = $atributos->groupradioclass;
                    }

                    if(property_exists($atributos, 'checkclass')){
                        $context[ 'attr_checkclass' ] = $atributos->checkclass;
                    }

                    $field_default_value = "";

                    //Initial values config
                    if( $initial_config ){
                        if( property_exists($initial_config, 'initial_values') ){
                            $initial_values = $initial_config->initial_values;
                            foreach( $initial_values as &$initial_value ){
                                if( $initial_value->alias === $context[ 'attr_local_alias' ] ){
                                    $field_default_value = $initial_value->default_value;
                                }
                            }
                        }
                    }
                    
                    $context[ 'default_value' ] = $field_default_value;
                    
                    $field = NULL;

                    switch ($campo) {
                        case "TEXTFIELD":
                            $field = _dphpforms_generate_TEXTFIELD( $dom, $statement->mod_id_formulario_pregunta, $context, $enunciado, $form_uniqid );
                            break;
                        case "TEXTFIELD_LIST":
                            $field = _dphpforms_generate_TEXTFIELD_LIST( $dom, $statement->mod_id_formulario_pregunta, $context, $enunciado, $form_uniqid );
                            break;
                        case "TEXTAREA":
                            $field = _dphpforms_generate_TEXTAREA( $dom, $statement->mod_id_formulario_pregunta, $context, $enunciado, $form_uniqid );
                            break;
                        case "DATE":
                            $field = _dphpforms_generate_DATE( $dom, $statement->mod_id_formulario_pregunta, $context, $enunciado, $form_uniqid );
                            break;
                        case "DATETIME":
                            $field = _dphpforms_generate_DATETIME( $dom, $statement->mod_id_formulario_pregunta, $context, $enunciado, $form_uniqid );
                            break;
                        case "TIME":
                            $field = _dphpforms_generate_TIME( $dom, $statement->mod_id_formulario_pregunta, $context, $enunciado, $form_uniqid );
                            break;
                        case "FILE":
                            $field = _dphpforms_generate_FILE( $dom, $statement->mod_id_formulario_pregunta, $context, $enunciado, $form_uniqid );
                            break;
                        case "RADIOBUTTON":
                            $field = _dphpforms_generate_RADIOBUTTON( $dom, $statement->mod_id_formulario_pregunta, $context, $enunciado, $form_uniqid );
                            break;
                        case "CHECKBOX":
                            $field = _dphpforms_generate_CHECKBOX( $dom, $statement->mod_id_formulario_pregunta, $context, $enunciado, $form_uniqid );
                            break;
                        case "SELECT":
                            $field = _dphpforms_generate_SELECT( $dom, $statement->mod_id_formulario_pregunta, $context, $enunciado, $form_uniqid );
                            break;
                    }
                    
                    if( !is_null( $field ) ){
                        $form->appendChild( $field );
                    }
                    
                }

                //Prevent that a double role definition in the permission generate two blocks of the same question.
                break;

            }
        }

    }
    
    $final_hr = _core_dphpforms_build_tag( $dom, "hr", new DOMAttributeList([
        "class" => ["footer-hr-dphpforms"]
    ]) );
    $form->appendChild( $final_hr );
    
    if( !is_null( $register_button ) ){
        $form->appendChild( $register_button );
    }

    if( $initial_config ){

        //Aditional buttons section. 

        /*Example of a additional button
        aditional_buttons: [
            {
                "alias" : "send_email",
                "text" : "Send Email",
                "main_classes" : "send-email big"
            },
            {...}
        ]*/
        if( property_exists($initial_config, 'aditional_buttons') ){

            $buttons = $initial_config->aditional_buttons;
            
            $new_buttons_aliases = [];
            $reserved_aliases = [
                "update",
                "delete",
                "reset"
            ];

            foreach( $buttons as $key => $button ){

                //Verification of button alias.
                if( is_null( $button->alias ) || ( $button->alias == "" ) ){
                    return _dphpforms_build_exception_message( "<strong>button->alias</strong> cannot be empty" );
                }

                //Validation of alias string structure.
                if( !preg_match( '/^[a-z0-9_]+$/', $button->alias )  ){
                    return _dphpforms_build_exception_message( "<strong>".$button->alias."</strong> is not a valid alias, valid regex [a-z0-9_]+, for instance, alias_1 " );
                }

                //Prevent that many buttons with the same identifier can be defined.
                if( !in_array( $button->alias, $new_buttons_aliases ) ){

                    array_push( $new_buttons_aliases, $button->alias );

                    //Validation of reserved aliases.
                    $allow_reserved_alias = false;
                    if( in_array( $button->alias, $reserved_aliases ) ){
                        if( property_exists ($initial_config, 'allow_' . $button->alias ) ){

                            //Buttons with 'update' or 'delete' as alias, only can be defined with an allow flag.
                            if( ((array) $initial_config)[ 'allow_' . $button->alias ] ){
                                $allow_reserved_alias = true;
                            }
                        }
                    }
                   
                    $html_button = _dphpforms_generate_html_button( $dom, $button->alias, $button->text, $button->classes, false );
                    
                    //If return is null means that was defined an invalid alias or was tried to define and reserved alias without flag.
                    if( !$html_button ){
                        return _dphpforms_build_exception_message( "<strong>" . $button->alias . "</strong> is an reserved alias and its not allowed for recorder" );
                    }else{
                        $form->appendChild( $html_button );
                    }

                }else{
                    return _dphpforms_build_exception_message( "<strong>" . $button->alias . "</strong> cannot be defined more that one time" );
                }
            }
        }
    }
    
    /*$html = $html .  ' 
        <hr class="footer-hr-dphpforms">
        <div class="dphpforms_response_recorder_buttons">
            '.$register_button.'
            '.$html_aditional_buttons.'
        </div>
    </form>';*/
    
    
    $html = $dom->saveHTML();
    
    $dom = $dom->loadHTML($dom->saveHTML());
   
    return  ( $minify ?  _dphpforms_html_minifier( $html ) :  $html );

}

function _dphpforms_generate_TEXTFIELD_LIST( &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){
    
    $div_attr = new DOMAttributeList( [
        'class' =>  [ 
            "div-$id_formulario_pregunta", 
            "dphpf-list", 
            $context[ 'attr_class' ] ,
            $context[ 'attr_local_alias' ] 
        ],
        'data-uid' => uniqid( $prefix_uniqid, true )
    ] );
    
    $inner_element_attr = [
        'class' =>  [ $context[ 'attr_inputclass' ] ],
        'disabled' => $context[ 'enabled' ],
        'required' => $context[ 'attr_required' ],
        'type' => "text"
    ];
    
    $elements = $context['options']->initial_list;
    
    $elements_pos = array();
    foreach ($elements as $key => $_row){
        $elements_pos[$key] = $_row->pos;
    }
    array_multisort($elements_pos, SORT_ASC, $elements);

    $div = _core_dphpforms_build_tag( $dom, "div", $div_attr );
    
    $statem = _core_dphpforms_build_tag( $dom, "label" );
    $statem->nodeValue = $statement . ":";
    
    $div->appendChild( $statem );

    foreach( $elements as $key => $element ){
        
        $inner_element_attr['name'] = $id_formulario_pregunta . "_" . $element->id . "[]";
        
        $dom_element_attr = new DOMAttributeList( $inner_element_attr );
        
        $elem_container = _core_dphpforms_build_tag( $dom, "div", new DOMAttributeList([
            "class" => [ "dphpf-list-element" ]
        ]) );
        
        $label = _core_dphpforms_build_tag( $dom, "label", new DOMAttributeList() );
        $label->nodeValue = $element->statement . ":";
        
        $br = _core_dphpforms_build_tag( $dom, "br", new DOMAttributeList() );
        
        $textfield = _core_dphpforms_build_tag( $dom, "input", $dom_element_attr) ;
        
        $elem_container->appendChild( $label );
        $elem_container->appendChild( $br );
        $elem_container->appendChild( $textfield );
        
        $div->appendChild( $elem_container );
    }
    
    return $div;

}

function _dphpforms_generate_FILE( &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){
            
    $div = _core_dphpforms_build_tag( $dom, "div", new DOMAttributeList() );
    
    return $div;

}

function _dphpforms_generate_SELECT( &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){
            
    $div = _core_dphpforms_build_tag( $dom, "div", new DOMAttributeList() );
    
    return $div;

}

function _dphpforms_generate_TEXTFIELD(  &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){
            
    $div_attr = new DOMAttributeList( [
        'class' =>  [ "div-$id_formulario_pregunta", $context[ 'attr_class' ] ,$context[ 'attr_local_alias' ] ],
        'data-uid' => uniqid( $prefix_uniqid, true )
    ] );
    
    $inner_element_attr = new DOMAttributeList( [
        'id' => $id_formulario_pregunta,
        'class' =>  [ "form-control", $context[ 'attr_inputclass' ] ],
        'name' => $id_formulario_pregunta,
        'max' => $context[ 'attr_max' ],
        'min' => $context[ 'attr_min' ],
        'type' => ( $context[ 'attr_type' ] == "" ? "text" : $context[ 'attr_type' ]),
        'placeholder' => $context[ 'attr_placeholder' ],
        'maxlength' => $context[ 'attr_maxlength' ],
        'disabled' => $context[ 'enabled' ],
        'required' => $context[ 'attr_required' ],
        'value' =>  $context[ 'default_value' ]   
    ]);
    
    $div = _core_dphpforms_build_tag( $dom, "div", $div_attr );
    $input = _core_dphpforms_build_tag( $dom, "input", $inner_element_attr );
    $label = _core_dphpforms_build_tag( $dom, "label" );
    $label->nodeValue = $statement . ":";
    
    $line_break = _core_dphpforms_build_tag( $dom, "br" );
    
    $div->appendChild($label);
    $div->appendChild($line_break);
    $div->appendChild($input);
    
    return $div;
    
}

function _dphpforms_generate_TEXTAREA( &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){
    
    $div_attr = new DOMAttributeList([
        'class' =>  [ "div-$id_formulario_pregunta", $context[ 'attr_class' ] ,$context[ 'attr_local_alias' ] ],
        'data-uid' => uniqid( $prefix_uniqid, true )
    ]);
    
    $inner_element_attr = new DOMAttributeList([
        'id' => $id_formulario_pregunta,
        'class' =>  [ "form-control", $context[ 'attr_inputclass' ] ],
        'name' => $id_formulario_pregunta,
        'placeholder' => $context[ 'attr_placeholder' ],
        'maxlength' => $context[ 'attr_maxlength' ],
        'disabled' => $context[ 'enabled' ],
        'required' => $context[ 'attr_required' ]
    ]);
    
    $default_value = $context[ 'default_value' ];
    
    $div = _core_dphpforms_build_tag( $dom, "div", $div_attr );
    $textarea = _core_dphpforms_build_tag( $dom, "textarea", $inner_element_attr );
    $textarea->nodeValue = $default_value;
    
    $label = _core_dphpforms_build_tag( $dom, "label" );
    $label->nodeValue = $statement . ":";
    
    $line_break = _core_dphpforms_build_tag( $dom, "br" );
    
    $div->appendChild($label);
    $div->appendChild($line_break);
    $div->appendChild($textarea);
        
    return $div;
    
}

function _dphpforms_generate_DATE( &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){
    
    $div_attr = new DOMAttributeList( [
        'class' =>  [ "div-$id_formulario_pregunta", $context[ 'attr_class' ] ,$context[ 'attr_local_alias' ] ],
        'data-uid' => uniqid( $prefix_uniqid, true )
    ] );
    
    $inner_element_attr = new DOMAttributeList( [
        'id' => $id_formulario_pregunta,
        'class' =>  [ "form-control", $context[ 'attr_inputclass' ] ],
        'name' => $id_formulario_pregunta,
        'max' => $context[ 'attr_max' ],
        'min' => $context[ 'attr_min' ],
        'type' => "date",
        'maxlength' => $context[ 'attr_maxlength' ],
        'disabled' => $context[ 'enabled' ],
        'required' => $context[ 'attr_required' ],
        'value' =>  $context[ 'default_value' ]   
    ]);
    
    $div = _core_dphpforms_build_tag( $dom, "div", $div_attr );
    $input = _core_dphpforms_build_tag( $dom, "input", $inner_element_attr );
    $label = _core_dphpforms_build_tag( $dom, "label" );
    $label->nodeValue = $statement . ":";
    
    $line_break = _core_dphpforms_build_tag( $dom, "br" );
    
    $div->appendChild($label);
    $div->appendChild($line_break);
    $div->appendChild($input);
    
    return $div;

}

function _dphpforms_generate_DATETIME( &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){
    
    $div_attr = new DOMAttributeList( [
        'class' =>  [ "div-$id_formulario_pregunta", $context[ 'attr_class' ] ,$context[ 'attr_local_alias' ] ],
        'data-uid' => uniqid( $prefix_uniqid, true )
    ] );
    
    $inner_element_attr = new DOMAttributeList( [
        'id' => $id_formulario_pregunta,
        'class' =>  [ "form-control", $context[ 'attr_inputclass' ] ],
        'name' => $id_formulario_pregunta,
        'max' => $context[ 'attr_max' ],
        'min' => $context[ 'attr_min' ],
        'type' => "datetime-local",
        'disabled' => $context[ 'enabled' ],
        'required' => $context[ 'attr_required' ],
        'value' =>  $context[ 'default_value' ]   
    ]);
        
    $div = _core_dphpforms_build_tag( $dom, "div", $div_attr );
    $input = _core_dphpforms_build_tag( $dom, "input", $inner_element_attr );
    $label = _core_dphpforms_build_tag( $dom, "label" );
    $label->nodeValue = $statement . ":";
    
    $line_break = _core_dphpforms_build_tag( $dom, "br" );
    
    $div->appendChild($label);
    $div->appendChild($line_break);
    $div->appendChild($input);
    
    return $div;

}

function _dphpforms_generate_TIME( &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){
    
    $div_attr = new DOMAttributeList( [
        'class' =>  [ "div-$id_formulario_pregunta", $context[ 'attr_class' ] ,$context[ 'attr_local_alias' ] ],
        'data-uid' => uniqid( $prefix_uniqid, true )
    ] );
    
    $inner_element_attr = new DOMAttributeList( [
        'id' => $id_formulario_pregunta,
        'class' =>  [ "form-control", $context[ 'attr_inputclass' ] ],
        'name' => $id_formulario_pregunta,
        'max' => $context[ 'attr_max' ],
        'min' => $context[ 'attr_min' ],
        'type' => "time",
        'disabled' => $context[ 'enabled' ],
        'required' => $context[ 'attr_required' ],
        'value' =>  $context[ 'default_value' ]   
    ]);
        
    $div = _core_dphpforms_build_tag( $dom, "div", $div_attr );
    $input = _core_dphpforms_build_tag( $dom, "input", $inner_element_attr );
    $label = _core_dphpforms_build_tag( $dom, "label" );
    $label->nodeValue = $statement . ":";
    
    $line_break = _core_dphpforms_build_tag( $dom, "br" );
    
    $div->appendChild($label);
    $div->appendChild($line_break);
    $div->appendChild($input);
    
    return $div;

}

function _dphpforms_generate_RADIOBUTTON( &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){

    $field_attr_required = $context[ 'attr_required' ];
    $options = $context[ 'options' ];
        
    $div = _core_dphpforms_build_tag(
            $dom, "div", new DOMAttributeList([
                'class' => [
                        "div-$id_formulario_pregunta", 
                        $context['attr_class'], 
                        $context['attr_local_alias']
                    ],
                'data-uid' => uniqid($prefix_uniqid, true)
            ])
    );

    $inner_hidden_input = _core_dphpforms_build_tag(
            $dom, "input", new DOMAttributeList([
                'name' => $id_formulario_pregunta,
                'value' => "-#$%-",
                'type' => "hidden"
            ])
    );
    
    /**
     * Se utiliza para controlar el registro de una sola
     * condición de required para el primer radio.
    **/
    $required_temporal = $field_attr_required;
    
    $opt_radio_container = _core_dphpforms_build_tag(
            $dom, "div", new DOMAttributeList([
                'class' =>  [ 'opcionesRadio', $context[ 'attr_group_radio_class' ] ],
                'style' => "margin-bottom:0.4em"
            ])
    );
    
    // Pendiente de pruebas
    $option_pos = array();
    foreach ($options as $key => $_row){
        $option_pos[$key] = $_row->posicion;
    }
    array_multisort($option_pos, SORT_ASC, $options);
    // Fin del pendiente
    
    foreach($options as $key => $option){
        $option = (array) $option;
        
        $option_title = '';
        if(array_key_exists('title', $opcion)){
            $option_title = $opcion['title'];
        }
        
        $opt = _core_dphpforms_build_tag(
                $dom, "div", new DOMAttributeList([
                    'id' => $id_formulario_pregunta,
                    'name' => $id_formulario_pregunta,
                    'class' =>  [ 'radio', $context[ 'attr_radioclass' ] ],
                    'title' => $option_title,
                ])
        );
        
        $opt_label = _core_dphpforms_build_tag($dom, "label");
        
        $opt_label_span = _core_dphpforms_build_tag($dom, "span");
        $opt_label_span->nodeValue = $option['enunciado'];
        
        $opt_label_input = _core_dphpforms_build_tag(
                $dom, "input", new DOMAttributeList([
                    'type' => 'radio',
                    'class' => [ $context[ 'attr_inputclass' ] ],
                    'name' => $id_formulario_pregunta,
                    'value' => $option['valor'],
                    'disabled' => $context[ 'enabled' ],
                    'required' => $required_temporal
                ])
        );
        
        $opt_label->appendChild($opt_label_input);
        $opt_label->appendChild($opt_label_span);
        
        $opt->appendChild( $opt_label );
        
        $opt_radio_container->appendChild($opt);
        
        $required_temporal = false;
    }
    
    $clean_btn = _core_dphpforms_build_tag(
            $dom, "a", new DOMAttributeList([
                'href' => 'javascript:void(0);',
                'class' =>  [ "limpiar", "btn", "btn-xs", "btn-default" ]
            ])
    );
    $clean_btn->nodeValue = "Limpiar";
        
    $div->appendChild( $inner_hidden_input );
    
    if($statement){
      //$html = $html . '<label>'.$statement.'</label>';
       $label = $dom->createElement('label');
       $label->nodeValue = $statement . ":";
       
       $line_break = _core_dphpforms_build_tag( $dom, "br" );
       
       $div->appendChild( $label );
       $div->appendChild( $line_break );
       $div->appendChild( $inner_hidden_input );
     }
     
    $div->appendChild( $opt_radio_container );
    $div->appendChild( $clean_btn  );
    
    return $div;

}

function _dphpforms_generate_CHECKBOX( &$dom, $id_formulario_pregunta, $context, $statement, $prefix_uniqid ){

    $options = $context[ 'options' ];

    $opt_num = count($options);
    
    $div = _core_dphpforms_build_tag(
            $dom, "div", new DOMAttributeList([
                'class' => [
                        "div-$id_formulario_pregunta", 
                        $context['attr_class'], 
                        $context['attr_local_alias']
                    ],
                'data-uid' => uniqid($prefix_uniqid, true)
            ])
    );

    
     if( $statement ){
        $st_label = _core_dphpforms_build_tag(
                $dom, "label", new DOMAttributeList()
            );
        $st_label->nodeValue = $statement;
        $div->appendChild( $st_label );
    }

    $name_checkbox = ( 
        $opt_num > 1 ? 
        $id_formulario_pregunta . '[]' :  
        $id_formulario_pregunta
    );
    
    // Pendiente de pruebas
    $option_pos = array();
    foreach ($options as $key => $_row){
        $option_pos[$key] = $_row->posicion;
    }
    array_multisort($option_pos, SORT_ASC, $options);
    // Fin del pendiente

    foreach( $options as $key => $option ){
        $option = (array) $option;
        
        $option_title = ( array_key_exists('title', $option) ? $opcion['title'] : '' );
        
        $opt_container = _core_dphpforms_build_tag(
                $dom, "div", new DOMAttributeList([
                    'class' => [
                            "checkbox", 
                            $context[ 'attr_checkclass' ] 
                        ],
                    'title' => $option_title
                ])
        );

        $option_attr_checkclass = ( array_key_exists('class', $option) ? $option['class'] : '' );

        if($opt_num == 1){
            $hidden_input = _core_dphpforms_build_tag(
                    $dom, "input", new DOMAttributeList([
                        'type' => "hidden",
                        'name' => $name_checkbox,
                        'value'=> "-1"
                    ])
            );
            $opt_container->appendChild( $hidden_input );
        }
        
        $label = _core_dphpforms_build_tag(
                    $dom, "label", new DOMAttributeList([
                        'class' => [ $option_attr_checkclass ]
                    ])
            );
        
        $label_span = _core_dphpforms_build_tag( $dom, "span", new DOMAttributeList() );
        $label_span->nodeValue = $option['enunciado'];
        
        $opt = _core_dphpforms_build_tag(
                    $dom, "input", new DOMAttributeList([
                        'type' => "checkbox",
                        'class' => [ $context[ 'attr_inputclass' ] ],
                        'name' => $name_checkbox,
                        'value' => $option['valor'],
                        'disabled' => $context[ 'enabled' ],
                    ])
            );
        
        $label->appendChild( $opt );
        $label->appendChild( $label_span );
        $opt_container->appendChild( $label );
        
        
        $div->appendChild( $opt_container );
    }   
    
    return $div;
    
}

/**
 * Function that generates the html of the buttons.
 * @author Jeison Cardona Gómez, <jeison.cardona@correounivalle.edu.co>
 * @param String $alias, this alias will be used as class-identifier, for instance, btn-dphpforms-alias
 * @param String $text, it is the buttom value.
 * @param String $classes, aditional css classes.
 * @return String HTML with the buttons tags.
 */

function _dphpforms_generate_html_button( &$dom, $alias, $text, $classes, $allow_reserved_alias = false ){
    
    $reserved_aliases = [
        "update",
        "delete",
        "reset"
    ];

    if( is_null( $alias ) ){
        return null;
    } 
    
    if( in_array( $alias, $reserved_aliases ) && !$allow_reserved_alias ){
        return null;
    }

    $aditional_classes = "";

    if( $classes ){
        $classes = array_map(
            function($class) use ($alias){
                $default_classes = [
                    "button",
                    "btn-dphpforms",
                    "btn-dphpforms-" . $alias,
                ];
                if( in_array( $class, $default_classes ) ){
                    return null;
                }else{
                    return $class;
                }
            },
            $classes
        );
        $aditional_classes = join( $classes, " " );
    }
    
    $btn_input = _core_dphpforms_build_tag(
            $dom, "input", new DOMAttributeList([
                'class' => array_merge(
                        ["button", "btn-dphpforms", "btn-dphpforms-" . $alias,],
                        $classes
                ),
                'type' => "button",
                'value' => $text
             ])
    );

    return $btn_input;
    
    //return '<input type="button" class="button btn-dphpforms btn-dphpforms-'. $alias .' '. $aditional_classes .'" value="'.$text.'" >';
}

/**
 * Function that allow build and standard error message when the process of rendering cannot be completed.
 * @author Jeison Cardona Gómez, <jeison.cardona@correounivalle.edu.co>
 * @param String $reason, cause of exception.
 * @return String standard exception message.
 */
function _dphpforms_build_exception_message( $reason ){
    return "<h1>Error rendering</h1> The form cannot be rendered for the following reason: " . $reason . "."; 
}
  

function _dphpforms_html_minifier($buffer) {

    $search = array(
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        '/(\s)+/s',         // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    );

    $replace = array(
        '>',
        '<',
        '\\1',
        ''
    );

    $buffer = preg_replace($search, $replace, $buffer);

    return trim($buffer);
}

/**
 * Function that given a xQuery return a validation status.
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @param Object $query stdClass with the specified properties.
 * @return Array
 * 
 * Input example:
 * 
 * $xQuery = new stdClass();
 * $xQuery->form = "seguimiento_pares"; // Can be alias(String) or identifier(Number)
 * $xQuery->filterFields = [
 *     ["id_estudiante",[ ["%%","LIKE"]], false],
 *     ["fecha",[ ["%%","LIKE"] ], false],
 *     ["revisado_practicante",[ ["%%","LIKE"] ], false],
 *     ["revisado_profesional",[ ["%%","LIKE"] ], false]
 * ];
 * $xQuery->orderFields = [ ["fecha","DESC"] ];
 * $xQuery->orderByDatabaseRecordDate = false; // If true, 'orderField' is ignored. DESC
 * $xQuery->recordStatus = [ "!deleted" ]; // options "deleted" or "!deleted", can be both. Empty = both.
 * $xQuery->asFields = [ [ [ function( $_this ){ return (int) $_this['id_registro'] ; } ], "id_estudiante" ], ["revisado_profesional", "id_estudiante"] ]; 
 * $xQuery->selectedFields = [ "id_creado_por", "id_estudiante" ]; // Without support.
*/
function _dphpforms_validate_xquery( $query ){
    
    $form = _dphpforms_get_form_info( $query->form );
    
    if( $form ){
        $fields = _dphpforms_get_fields_form( $form->id );
        $list_fields_alias = [];
        $list_fields_alias_id = [];
        $list_fields_id_alias = [];
        $list_fields_data_type = [];
        $list_valid_operators = ["=",">","<",">=","<=","!=", "LIKE"];
        //$list_asFields_alias = [];
        $list_filter_fields_alias = [];
        
        foreach( $fields as $field ){
            array_push( $list_fields_alias, $field->local_alias );
            $list_fields_alias_id[$field->local_alias] = $field->id_pregunta;
            $list_fields_id_alias[$field->id_pregunta] = $field->local_alias;
            $list_fields_data_type[$field->id_pregunta] = $field->tipo_campo;
        }

        //Validation if the filter fields exist.
        foreach( $query->filterFields as $filterField ){
           if( count( $filterField ) == 3 ){
                if( !in_array( $filterField[0], $list_fields_alias ) ){
                    return [
                        "status_code" => -1,
                        "error_message" => "QUERY->filterFields: ".json_encode($filterField)." DOES NOT EXIST AS A FIELD",
                        "data_response" => ""
                    ];
                };
                if( gettype( $filterField[2] ) !== "boolean" ){
                    return [
                        "status_code" => -1,
                        "error_message" => "QUERY->filterFields: ".json_encode($filterField)." DOES NOT HAVE A VALID VALUE, USE bool true OR false NOT ". gettype( $filterField[2] ),
                        "data_response" => ""
                    ];
                };
                if( gettype( $filterField[1] ) !== "array" ){
                    return [
                        "status_code" => -1,
                        "error_message" => "QUERY->filterFields: ".json_encode($filterField)." DOES NOT MATCH WITH THE STRUCTURE. [...,[\"value\",\"operator\"],...]  ",
                        "data_response" => ""
                    ];
                }else{
                    foreach( $filterField[1] as $filterValues  ){
                        if( count( $filterValues ) != 2 ){
                            return [
                                "status_code" => -1,
                                "error_message" => "QUERY->filterFields: ".json_encode($filterValues)." DOES NOT MATCH WITH THE STRUCTURE. [\"value\",\"operator\"]  ",
                                "data_response" => ""
                            ];      
                        }else{
                            if( !in_array( $filterValues[1], $list_valid_operators ) ){
                                return [
                                    "status_code" => -1,
                                    "error_message" => "QUERY->filterFields: ".json_encode($filterValues)." DOES NOT HAVE A VALID OPERATOR, USE ".json_encode($list_valid_operators)." NOT ". $filterValues[1],
                                    "data_response" => ""
                                ];
                            }
                        }
                    }
                }
                
                array_push( $list_filter_fields_alias, $filterField[0] );

           }else{
            return [
                "status_code" => -1,
                "error_message" => "QUERY->filterFields: ".json_encode($filterField)." DOES NOT MATCH WITH THE STRUCTURE [\"alias_field\", \"value\", optional = true or false, operator = \">,<,=,!=,<=,>=\"]",
                "data_response" => ""
            ];
           }
        }
        
        //Validation if the order fields exist.
        foreach( $query->orderFields as $orderField ){
            if( count( $orderField ) == 2 ){
                 if( !in_array( $orderField[0], $list_fields_alias ) ){
                     return [
                         "status_code" => -1,
                         "error_message" => "QUERY->orderFields: ".json_encode($orderField)." DOES NOT EXIST AS A FIELD",
                         "data_response" => ""
                     ];
                 }else{
                     if( !((strtoupper($orderField[1]) == "ASC") || (strtoupper($orderField[1]) == "DESC") )){
                        return [
                            "status_code" => -1,
                            "error_message" => "QUERY->orderFields: ".json_encode($orderField)." DOES NOT HAVE A VALID VALUE, USE 'ASC' OR 'DESC'",
                            "data_response" => ""
                        ];
                     }
                 }
            }else{
             return [
                 "status_code" => -1,
                 "error_message" => "QUERY->orderFields: ".json_encode($orderField)." DOES NOT MATCH WITH THE STRUCTURE [\"alias_field\", \"ASC OR DESC\"]",
                 "data_response" => ""
             ];
            }
        }
         
        //Validation if the selected fields exist.
        foreach( $query->selectedFields as $selectedField ){
            if( !in_array( $selectedField, $list_fields_alias ) ){
                 return [
                     "status_code" => -1,
                     "error_message" => "QUERY->selectedFields: ".json_encode($selectedField)." DOES NOT EXIST AS A FIELD",
                     "data_response" => ""
                 ];
            }
        }

        //Validation if the asFields fields exist.
        foreach( $query->asFields as $asField ){

            if( count( $asField ) == 2 ){
        
                $asType = gettype( $asField[0] );
                    
                if( ( $asType !== "string") && ($asType !== "array" ) ){
                    return [
                        "status_code" => -1,
                        "error_message" => "QUERY->asFields: ".json_encode($asField)." DOES NOT MATCH WITH THE STRUCTURE [\"alias_field\", \"new_alias_field_name\" ] or [ [ instanceof Closure( \$_this, \$arg1, \$arg2, ... ), \"param\"||\$param, ... ], \"new_alias_field_name\" ]",
                        "data_response" => ""
                    ];
                }

                if( $asType === "array" ){
                    if( count( $asField[1] ) < 1 ){
                        
                        return [
                            "status_code" => -1,
                            "error_message" => "QUERY->asFields: ".json_encode($asField)." DOES NOT MATCH WITH THE STRUCTURE [\"alias_field\", \"new_alias_field_name\" ] or [ [ instanceof Closure( \$_this, \$arg1, \$arg2, ... ), \"param\"||\$param, ... ], \"new_alias_field_name\" ]",
                            "data_response" => ""
                        ];
                    }else{
                        if( !is_callable( $asField[0][0] ) ){
                            return [
                                "status_code" => -1,
                                "error_message" => "QUERY->asFields: THE FIRST ELEMENT OF THE ARRAY IS NOT CALLABLE.",
                                "data_response" => ""
                            ];
                        }
                    }

                }else if( $asType === "string" ){

                    if( !in_array( $asField[0], $list_filter_fields_alias ) ){
                        return [
                            "status_code" => -1,
                            "error_message" => "QUERY->asFields: ".json_encode($asField)." DOES NOT EXIST AS A FILTER FIELD",
                            "data_response" => ""
                        ];
                    }

                }

                if( $asField[1] === "" ){
                    return [
                        "status_code" => -1,
                        "error_message" => "QUERY->asFields: ".json_encode($asField)." ALIAS '". $asField[1]."' MUST BE DIFFERENT FROM EMPTY.",
                        "data_response" => ""
                    ];
                }

            }else{
                return [
                    "status_code" => -1,
                    "error_message" => "QUERY->asFields: ".json_encode($asField)." DOES NOT MATCH WITH THE STRUCTURE [\"alias_field\", \"new_alias_field_name\" ] or [ [ instanceof Closure( \$_this, \$arg1, \$arg2, ... ), \"param\"||\$param, ... ], \"new_alias_field_name\" ]",
                    "data_response" => ""
                ];
            }
            
        }
        

    }else{
        return [
            "status_code" => -1,
            "error_message" => "QUERY->form: $query->form DOES NOT EXIST",
            "data_response" => ""
        ];
    }

    if( gettype( $query->orderByDatabaseRecordDate ) !== "boolean" ){
        return [
            "status_code" => -1,
            "error_message" => "QUERY->orderByDatabaseRecordDate: $query->orderByDatabaseRecordDate DOES NOT HAVE A VALID VALUE, USE bool true OR false NOT ". gettype( $query->orderByDatabaseRecordDate ),
            "data_response" => ""
        ];
    }

    //Validation of record status
    foreach( $query->recordStatus as $rStatus ){
        $valid_values = [ "deleted", "!deleted" ];
        if( !in_array( $rStatus, $valid_values ) ){
             return [
                 "status_code" => -1,
                 "error_message" => "QUERY->recordStatus: ".json_encode($rStatus)." IS NOT A VALID VALUE",
                 "data_response" => ""
             ];
        }
     }

    return [
        "status_code" => 0,
        "error_message" => NULL,
        "data_response" => NULL
    ];
    
}

  /*$xQuery = new stdClass();
  $xQuery->form = "seguimiento_pares"; // Can be alias(String) or identifier(Number)
  $xQuery->filterFields = [
      ["id_estudiante",[ ["%%","LIKE"]], false],
      ["fecha",[ ["%%","LIKE"] ], false],
      ["revisado_practicante",[ ["%%","LIKE"] ], false],
      ["revisado_profesional",[ ["%%","LIKE"] ], false]
  ];
  $xQuery->orderFields = [ ["fecha","DESC"] ];
  $xQuery->orderByDatabaseRecordDate = false; // If true, 'orderField' is ignored. DESC
  $xQuery->recordStatus = [ "!deleted" ]; // options "deleted" or "!deleted", can be both. Empty = both.
  $xQuery->asFields = [ [ "fecha", "id_estudiante_x" ], ["revisado_profesional", "id_estudiante"] ]; 
  $xQuery->selectedFields = [ "id_creado_por", "id_estudiante" ]; // Without support.
  
  echo _dphpforms_get_json_xquery($xQuery); die();*/
 

function _dphpforms_get_json_xquery( $query ){
    
    $validation_data = _dphpforms_validate_xquery($query);
    if( $validation_data['status_code'] === -1 ){
        return null;
    }
    
    $form = _dphpforms_get_form_info( $query->form );
    
    $query_params = [
        'filterFields',
        'orderFields',
        'orderByDatabaseRecordDate',
        'recordStatus',
        'asFields',
        'selectedFields',
        'selectedFields'
    ];
    
    $_xquery = [
        'form' => $form->alias,
    ];
    
    foreach ($query_params as $key => $param){
        $_xquery[$param] = ($query->$param ? $query->$param : []);
    }
    
    //orderByDatabaseRecordDate cannot be an empty array [].
    if( empty($_xquery['orderByDatabaseRecordDate']) ){
        $_xquery['orderByDatabaseRecordDate'] = false;
    }
    
    return json_encode( $_xquery );
    
}

/**
 * Function that given a record id, return its history. 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @param integer $record_id
 * @return array History.
 */
function _dphpforms_get_record_history( $record_id ){
    
    if( !is_numeric( $record_id ) ){
        throw new Exception( "Invalid record id", -1 );
    }
    
    global $DB;
    
    $query = "
        SELECT 
            * 
        FROM 
            {talentospilos_df_dwarehouse}
        WHERE 
            id_registro_respuesta_form = '$record_id'
        ORDER BY 
            fecha_hora_registro ASC";
    
    $history = $DB->get_records_sql( $query );
    
    return $history;
    
}

/**
 * Function that given a record id, return its history. 
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @see _dphpforms_get_record_history(...) in this file.
 * @param integer $record_id
 * @return array History.
 */
function _dphpforms_get_pretty_record_history( $record_id ){
       
    $history = _dphpforms_get_record_history( $record_id );
    return array_values(array_filter(array_map(function($in){
        
        global $DB;
        
        if( strpos($in->accion, "PRE-") !== false ){
            return NULL;
        }
        
        $user_id = $in->id_usuario_moodle;
        
        unset( $in->datos_previos );
        unset( $in->datos_enviados );
        unset( $in->datos_almacenados );
        unset( $in->dts_retorno );
        unset( $in->navegador );
        unset( $in->id_usuario_moodle );
        unset( $in->url_request );
        unset( $in->id );
        unset( $in->id_registro_respuesta_form );
        
        $query = "
            SELECT 
                CONCAT( firstname, ' ', lastname ) AS fullname 
            FROM 
                {user}
            WHERE 
                id = $user_id";
        
        $user = $DB->get_record_sql( $query );
        
        $in->usuario_moodle = $user->fullname;
        
        $y = date("Y",  strtotime($in->fecha_hora_registro));
        $m = date("m",  strtotime($in->fecha_hora_registro));
        $d = date("d",  strtotime($in->fecha_hora_registro));
        $time = date("h:i:s a",  strtotime($in->fecha_hora_registro));
        
        switch ($m){
            case 1:
                $m = "enero";
                break;
            case 2:
                $m = "febrero";
                break;
            case 3:
                $m = "marzo";
                break;
            case 4:
                $m = "abril";
                break;
            case 5:
                $m = "mayo";
                break;
            case 6:
                $m = "junio";
                break;
            case 7:
                $m = "julio";
                break;
            case 8:
                $m = "agosto";
                break;
            case 9:
                $m = "septiembre";
                break;
            case 10:
                $m = "octubre";
                break;
            case 11:
                $m = "noviembre";
                break;
            case 12:
                $m = "diciembre";
                break;
        }
            
        
        $in->fecha_hora_registro = "$d de $m de $y - Hora $time";
        
        return $in;
        
    }, $history)));
    
}

/**
 * Function that checks if the given record id exist in the database.
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param integer $record_id Record ID
 * 
 * @return bool True if exist a record with the given ID.
 */
function _dphpforms_record_exist( int $record_id ):bool
{
    global $DB;                                                                 // Moodle DB manager.
    
    $query = "SELECT *                                                          
    FROM {talentospilos_df_form_resp} 
    WHERE id = '$record_id' AND estado = 1";
    
    $record = $DB->get_record_sql( $query );                                    
    
    return ( property_exists($record, "id") ? true : false );
    
}

/**
 * **
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 * 
 * @param integer $record_id Record ID.
 * @throws Exception If the given record ID doesn't exist.
 * 
 * @return string K
 */
function _dphpforms_get_k( int $record_id ) :string
{
    if( !_dphpforms_record_exist( $record_id ) ){
        throw new Exception( "Invalid ID.", -1 );
    }
    
    $hash_rule = NULL;
    if( $record_id > 99999 ){                                                   // Validation of size.
        $hash_rule = substr( (string) $record_id , 0, 5);                       // Cut to a valid size.
    }else{
        $hash_rule = (string) $record_id;                                       
    }
    
    return core_secure_find_key($hash_rule);
    
}

function _dphpforms_get_form_rules( $form_id ){

    global $DB;

    $sql = "
        SELECT 
            RFP.id, RFP.id_formulario, RFP.id_regla, R.regla, 
            RFP.id_form_pregunta_a , RFP.id_form_pregunta_b
        FROM 
            {talentospilos_df_reg_form_pr} AS RFP
        INNER JOIN
            {talentospilos_df_reglas} AS R 
        ON 
            RFP.id_regla = R.id
        WHERE 
            id_formulario = '$form_id'
    ";

    return $DB->get_records_sql( $sql );

}

function _dphpforms_add_new_form_rule( $form_id, $preg_a_id, $rule_id, $preg_b_id ){

    global $DB;

    $new_form_rule = new stdClass();
    $new_form_rule->id_formulario = $form_id;
    $new_form_rule->id_regla = $rule_id;
    $new_form_rule->id_form_pregunta_a = $preg_a_id;
    $new_form_rule->id_form_pregunta_b = $preg_b_id;

    return $DB->insert_record( 'talentospilos_df_reg_form_pr', $new_form_rule );

}

?>