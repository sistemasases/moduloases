<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Ases block
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../../config.php');
/*header('Content-Type: application/json');

$xQuery = new stdClass();
$xQuery->form = "seguimiento_pares"; // Can be alias(String) or idntifier(Number)
$xQuery->filterFields = [
                         ["id_estudiante",[
                             ["%%","LIKE"]
                            ], false],
                        ["fecha",[
                             ["%%","LIKE"]
                            ], false],
                        ["revisado_practicante",[
                            ["%%","LIKE"]
                           ], false],
                       ["revisado_profesional",[
                        ["%%","LIKE"]
                       ], false]
                   ];
$xQuery->orderFields = [
                        ["fecha","DESC"]
                       ];

$xQuery->orderByDatabaseRecordDate = false; // If true, orderField is ignored. DESC
$xQuery->recordStatus = [ "!deleted" ];// options "deleted" or "!deleted", can be both. Empty = both.
$xQuery->asFields = [  [ [ function( $_this ){ return (int) $_this['id_registro'] ; } ], "id_estudiante" ], ["revisado_profesional", "id_estudiante"] ]; 
//No soportado aun
$xQuery->selectedFields = [ "id_creado_por", "id_estudiante" ]; // RecordId and BatabaseRecordDate are selected by default.


echo json_encode( dphpformsV2_find_records( $xQuery ) );*/

/**
 * 
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int 
 * @param int 
 * @return stdClass
 */
 function dphpformsV2_find_records( $query ){

    global $DB;

    $form = dphpformsV2_get_form_info( $query->form );
    
    if( $form ){
        $fields = dphpformsV2_get_fields_form( $form->id );
        $list_fields_alias = [];
        $list_fields_alias_id = [];
        $list_fields_id_alias = [];
        $list_fields_data_type = [];
        $list_valid_operators = ["=",">","<",">=","<=","!=", "LIKE"];
        $list_asFields_alias = [];
        $list_filter_fields_alias = [];
        foreach( $fields as $field ){
            array_push( $list_fields_alias, $field->local_alias );
            $list_fields_alias_id[$field->local_alias] = $field->id_pregunta;
            $list_fields_id_alias[$field->id_pregunta] = $field->local_alias;
            $list_fields_data_type[$field->id_pregunta] = $field->tipo_campo;
        };

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
                };
                
                array_push( $list_filter_fields_alias, $filterField[0] );

           }else{
            return [
                "status_code" => -1,
                "error_message" => "QUERY->filterFields: ".json_encode($filterField)." DOES NOT MATCH WITH THE STRUCTURE [\"alias_field\", \"value\", optional = true or false, operator = \">,<,=,!=,<=,>=\"]",
                "data_response" => ""
            ];
           };
        };
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
                 };
            }else{
             return [
                 "status_code" => -1,
                 "error_message" => "QUERY->orderFields: ".json_encode($orderField)." DOES NOT MATCH WITH THE STRUCTURE [\"alias_field\", \"ASC OR DESC\"]",
                 "data_response" => ""
             ];
            };
        };
         
        //Validation if the selected fields exist.
        foreach( $query->selectedFields as $selectedField ){
            if( !in_array( $selectedField, $list_fields_alias ) ){
                 return [
                     "status_code" => -1,
                     "error_message" => "QUERY->selectedFields: ".json_encode($selectedField)." DOES NOT EXIST AS A FIELD",
                     "data_response" => ""
                 ];
            };
        };

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
                };

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
                    };

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
            
        };
        

    }else{
        return [
            "status_code" => -1,
            "error_message" => "QUERY->form: $query->form DOES NOT EXIST",
            "data_response" => ""
        ];
    };

    if( gettype( $query->orderByDatabaseRecordDate ) !== "boolean" ){
        return [
            "status_code" => -1,
            "error_message" => "QUERY->orderByDatabaseRecordDate: $query->orderByDatabaseRecordDate DOES NOT HAVE A VALID VALUE, USE bool true OR false NOT ". gettype( $query->orderByDatabaseRecordDate ),
            "data_response" => ""
        ];
    };

    //Validation of record status
    foreach( $query->recordStatus as $rStatus ){
        $valid_values = [ "deleted", "!deleted" ];
        if( !in_array( $rStatus, $valid_values ) ){
             return [
                 "status_code" => -1,
                 "error_message" => "QUERY->recordStatus: ".json_encode($rStatus)." IS NOT A VALID VALUE",
                 "data_response" => ""
             ];
        };
     };

     //Validations completed


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
            $operator = $filterField[3];
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

 function dphpformsV2_find_n_count_records( $query ){

    $response = dphpformsV2_find_records( $query );
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
 * @return stdClass
 */
 function dphpformsV2_get_form_info( $alias_identifier ){
    
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
function dphpformsV2_get_find_forms( $column_name, $value, $using_like = false, $status = 1 ){
    
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
function dphpformsV2_get_fields_form( $form_id, $status = 1 ){
    
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

 
  function dphpformsV2_reverse_new_field_update( $form_id_alias, $id_pregunta, $default_value ){

    global $DB;

    $form_info = dphpformsV2_get_form_info( $form_id_alias );

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

        $return = dphpformsv2_store_reverse_rield( $record->id_formulario_respuestas, $id_pregunta, $default_value );
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

  function dphpformsV2_get_records_reverse_new_field_update( $id_respuesta, $form_id_alias ){

    global $DB;

    $form_info = dphpformsV2_get_form_info( $form_id_alias );

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

function dphpformsv2_store_reverse_rield( $form_response_id, $id_pregunta, $value ){

    global $DB;

    $sql_form_solu_exist = 
    "SELECT FU.id FROM {talentospilos_df_form_solu} AS FU
    INNER JOIN {talentospilos_df_respuestas} AS R ON FU.id_respuesta = R.id
    WHERE R.id_pregunta = $id_pregunta AND FU.id_formulario_respuestas = $form_response_id";

    //If it does not exist.
    if( !$DB->get_record_sql( $sql_form_solu_exist ) ){
 
        $respuesta = dphpformsv2_store_respuesta( $id_pregunta, $value );
        if( $respuesta ){
            return dphpformsV2_store_form_soluciones( $form_response_id, $respuesta );
        }else{
            return null;
        }

    }
}

function dphpformsv2_store_respuesta( $id, $value ){
    
    global $DB;

    $obj_respuesta = new stdClass();
    $obj_respuesta->id_pregunta = $id;
    $obj_respuesta->respuesta = $value;

    $pregunta = dphpformsV2_get_pregunta( $id );

    if( $pregunta ){

        if( dphpformsV2_regex_validator( $id, $value )->status ){
            $respuesta_identifier = $DB->insert_record('talentospilos_df_respuestas', $obj_respuesta, $returnid=true, $bulk=false);
            return $respuesta_identifier;
        }
        
    }else{
        return null;
    }
}

function dphpformsV2_regex_validator( $id, $value ){

    global $DB;

    $to_return = new stdClass();
    $to_return->status = true;
    $to_return->human_readable = "";
    $to_return->example =  "";

    $pregunta_obj = dphpformsV2_get_pregunta( $id );
    $tipo_campo_obj = dphpformsV2_tipo_campo( $pregunta_obj->tipo_campo );

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

function dphpformsV2_get_pregunta( $id ){

    global $DB;

    $sql = "SELECT * FROM {talentospilos_df_preguntas} WHERE id = " . $id;
    return $DB->get_record_sql( $sql );

}

function dphpformsV2_tipo_campo( $id ){

    global $DB;

    $sql = "SELECT * FROM {talentospilos_df_tipo_campo} WHERE id = " . $id;
    return $DB->get_record_sql( $sql );

}

function dphpformsV2_store_form_soluciones($form_response_id, $respuesta_identifier){

    global $DB;

    $obj_form_soluciones = new stdClass();
    $obj_form_soluciones->id_formulario_respuestas = $form_response_id;
    $obj_form_soluciones->id_respuesta = $respuesta_identifier;
   
    $form_solucines_identifier = $DB->insert_record('talentospilos_df_form_solu', $obj_form_soluciones, $returnid=true, $bulk=false);
    return $form_solucines_identifier;

}

function dphpformsV2_get_permisos_pregunta( $id_formulario_pregunta ){
    
    global $DB;
    $sql =  "SELECT * FROM {talentospilos_df_per_form_pr} WHERE id_formulario_pregunta = '$id_formulario_pregunta'";
    $permisos_obj = $DB->get_record_sql( $sql );
    if( $permisos_obj ){
        return $permisos_obj->permisos;
    }else{
        return null;
    }

}


// Example
$initial_config = '{
    "allow_update":true,
    "allow_delete":true,
    "main_form_classes" : "col-xs-12 col-sm-12",
    "initial_values" : [
        {
            "alias" : "lugar",
            "default_value" : "Lugar de prueba"
        },
        {
            "alias" : "objetivos",
            "default_value" : "Objetivos de prueba"
        },{
            "alias" : "id_instancia",
            "default_value" : "450299"
        }
    ],
    "aditional_buttons" : [
        {
            "alias" : "extra_button",
            "text" : "Extra Button",
            "main_classes" : "e-class e-class-2"
        },
        {
            "alias" : "update",
            "text" : "Actualizar",
            "main_classes" : ""
        },
        {
            "alias" : "delete",
            "text" : "Eliminar",
            "main_classes" : ""
        }
    ]
}';
$initial_config = json_decode( $initial_config );
echo dphpformsV2_generate_html_recorder( 'seguimiento_pares', "sistemas", $initial_config  );

function dphpformsV2_generate_html_recorder( $id_form, $rol_, $initial_config = null  ){

    global $DB;

    $FORM_ID = null;
    $ROL = $rol_;
    $html = null;

    $form_info = dphpformsV2_get_form_info( $id_form );
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

    $html ='
        <form id="'. $form_name_formatted .'" method="'. $form_info->method .'" action="'. $form_info->action .'" class="dphpforms dphpforms-response ">
            <h1>'.$form_info->nombre.'</h1><hr class="header-hr-dphpforms">
            <input name="id" value="'. $form_info->id .'" style="display:none;">
    ';

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

    $result = $DB->get_records_sql($sql);
    $result = (array) $result;
    $result = array_values($result);
    
    foreach($result as $key => $statement){
       
        $campo = $statement->campo;
        $enunciado = $statement->enunciado;
        $atributos = json_decode( $statement->atributos_campo );

        //Consulta de permisos
        $permisos_JSON = json_decode( dphpformsV2_get_permisos_pregunta( $statement->id_pregunta ) );
        
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

                    $enabled = null;
                    if(!$escritura){
                        $enabled = "disabled";
                    }

                    $field_attr_class = '';
                    $field_attr_type = '';
                    $field_attr_placeholder = '';
                    $field_attr_maxlength = '';
                    $field_attr_inputclass = '';
                    $field_attr_required = '';
                    $field_attr_local_alias = '';
                    $field_attr_max = '';
                    $field_attr_min = '';

                    if(property_exists($atributos, 'class')){
                        $field_attr_class = $atributos->class;
                    }

                    if(property_exists($atributos, 'type')){
                        $field_attr_type = $atributos->type;
                    }

                    if(property_exists($atributos, 'placeholder')){
                        $field_attr_placeholder = $atributos->placeholder;
                    }

                    if(property_exists($atributos, 'maxlength')){
                        $field_attr_maxlength = $atributos->maxlength;
                    }

                    if(property_exists($atributos, 'inputclass')){
                        $field_attr_inputclass = $atributos->inputclass;
                    }

                    if(property_exists($atributos, 'required')){
                        $field_attr_required = $atributos->required;
                        if($field_attr_required == 'true'){
                            $field_attr_required = 'required';
                        }elseif($field_attr_required == 'false'){
                            $field_attr_required = '';
                        }
                    }

                    if(property_exists($atributos, 'local_alias')){
                        $field_attr_local_alias = $atributos->local_alias;
                    }

                    if(property_exists($atributos, 'max')){
                        $field_attr_max = $atributos->max;
                        if( $field_attr_max == "today()" ){
                            $today = new DateTime('now');
                            $field_attr_max = $today->format('Y-m-d');
                        }
                    }

                    if(property_exists($atributos, 'min')){
                        $field_attr_min = $atributos->min;
                        if( $field_attr_min == "today()" ){
                            $today = new DateTime('now');
                            $field_attr_max = $today->format('Y-m-d');
                        }
                    }

                    $field_default_value = "";

                    //Initial values config

                    if( $initial_config ){
                        if( property_exists($initial_config, 'initial_values') ){
                        
                            $initial_values = $initial_config->initial_values;
                            foreach( $initial_values as &$initial_value ){
                                if( $initial_value->alias === $field_attr_local_alias ){
                                    $field_default_value = $initial_value->default_value;
                                }
                            }
                        }
                    }


                    $field_options[ 'attr_class' ] = $field_attr_class;
                    $field_options[ 'attr_local_alias' ] =  $field_attr_local_alias;
                    $field_options[ 'attr_inputclass' ] = $field_attr_inputclass;
                    $field_options[ 'attr_max' ] = $field_attr_max;
                    $field_options[ 'attr_min' ] = $field_attr_min;
                    $field_options[ 'attr_type' ] = $field_attr_type;
                    $field_options[ 'attr_placeholder' ] = $field_attr_placeholder;
                    $field_options[ 'default_value' ] = $field_default_value;
                    $field_options[ 'attr_maxlength' ] = $field_attr_maxlength;
                    $field_options[ 'enabled' ] = $enabled;
                    $field_options[ 'attr_required' ] = $field_attr_required;

                    if($campo == "TEXTFIELD"){
                        $html .= dphpformsV2_generate_TEXTFIELD( $statement->mod_id_formulario_pregunta, $field_options, $enunciado );
                    }

                    if($campo == 'TEXTAREA'){
                        $html .= dphpformsV2_generate_TEXTAREA( $statement->mod_id_formulario_pregunta, $field_options, $enunciado );
                    }

                    if($campo == 'DATE'){
                        $html .= dphpformsV2_generate_DATE( $statement->mod_id_formulario_pregunta, $field_options, $enunciado );
                    }
                    
                    if($campo == 'DATETIME'){
                        $html = $html .  '<div class="div-'.$statement->mod_id_formulario_pregunta.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                        $html = $html .  ' <input id="'.$statement->mod_id_formulario_pregunta.'" class="form-control ' . $field_attr_inputclass . '" value="'.$field_default_value.'" max="' . $field_attr_max . '"  min="' . $field_attr_min . '" type="datetime-local" name="'.$statement->mod_id_formulario_pregunta.'" '.$enabled.' '.$field_attr_required.'><br>' . "\n";
                        $html = $html .  '</div>';
                    }

                    if($campo == 'TIME'){
                        $html = $html .  '<div class="div-'.$statement->mod_id_formulario_pregunta.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                        $html = $html .  ' <input id="'.$statement->mod_id_formulario_pregunta.'" class="form-control ' . $field_attr_inputclass . '" value="'.$field_default_value.'" max="' . $field_attr_max . '"  min="' . $field_attr_min . '" type="time" name="'.$statement->mod_id_formulario_pregunta.'" '.$enabled.' '.$field_attr_required.'><br>' . "\n";
                        $html = $html .  '</div>';
                    }

                    if($campo == 'RADIOBUTTON'){
                        $opciones = json_decode($statement->opciones_campo);
                        $array_opciones = (array)$opciones;
                        $number_opciones = count($array_opciones);

                        $html = $html .  '<div class="div-'.$statement->mod_id_formulario_pregunta.' '.$field_attr_class.' '.$field_attr_local_alias.'" >';
                        $html = $html .  '<input type="hidden" name="'.$statement->mod_id_formulario_pregunta.'" value="-#$%-">';
                        if($enunciado){
                            $html = $html . '<label>'.$enunciado.'</label>';
                        }

                        $field_attr_radioclass = '';
                        if(property_exists($atributos, 'radioclass')){
                            $field_attr_radioclass = $atributos->radioclass;
                        }

                        /*
                            Se utiliza para controlar el registro de una sola
                            condición de required para el primer radio.
                        */
                        $required_temporal = $field_attr_required;

                        $field_attr_group_radio_class = '';
                        if(property_exists($atributos, 'groupradioclass')){
                            $field_attr_group_radio_class = $atributos->groupradioclass;
                        }
                                          
                        $html = $html .  '<div class="opcionesRadio ' .  $field_attr_group_radio_class . '" style="margin-bottom:0.4em">';
                        for($x = 0; $x < $number_opciones; $x++){
                            $opcion = (array) $array_opciones[$x];

                            $html = $html .  '
                                <div id="'.$statement->mod_id_formulario_pregunta.'" name="'.$statement->mod_id_formulario_pregunta.'" class="radio ' . $field_attr_radioclass . '">
                                    <label><input type="radio" class=" ' . $field_attr_inputclass . '" name="'.$statement->mod_id_formulario_pregunta.'" value="'.$opcion['valor'].'" name="optradio" '.$enabled.'   ' . $required_temporal . '>'.$opcion['enunciado'].'</label>
                                </div>
                            ' . "\n";
                            /*
                                Si el grupo de radios es requerido y ya se ha puesto esa condición en el 
                                primer radio, a pesar de que se concatene la variable al input, se limpia después
                                de pintar el primer radio.
                            */
                            if(  $required_temporal != ''  ){
                                $required_temporal = '';
                            }
                        }
                        $html = $html .  '</div><a href="javascript:void(0);" class="limpiar btn btn-xs btn-default" >Limpiar</a>
                         </div>
                        ' . "\n";
                    }

                    if($campo == 'CHECKBOX'){
                        $opciones = json_decode($statement->opciones_campo);
                        $array_opciones = (array)$opciones;
                        $number_opciones = count($array_opciones);

                        $html = $html .  '<div class="div-'.$statement->mod_id_formulario_pregunta.' '.$field_attr_class.' '.$field_attr_local_alias.'" >';
                        if($enunciado){
                            $html = $html . '<label>'.$enunciado.'</label>';
                        }

                        $field_attr_checkclass = '';
                        if(property_exists($atributos, 'checkclass')){
                            $field_attr_checkclass = $atributos->checkclass;
                        }

                        $name_checkbox = $statement->mod_id_formulario_pregunta;
                        if($number_opciones > 1){
                            $name_checkbox = $statement->mod_id_formulario_pregunta . '[]';
                        }

                        for($x = 0; $x < $number_opciones; $x++){
                            $opcion = (array) $array_opciones[$x];
                            $html = $html . '<div class="checkbox ' . $field_attr_checkclass . '">' . "\n";

                            $option_attr_checkclass = '';
                            if(array_key_exists('class', $opcion)){
                                $option_attr_checkclass = $opcion['class'];
                            }

                            if($number_opciones == 1){
                                $html = $html . '   <input type="hidden" name="'. $name_checkbox .'" value="-1">' . "\n";
                            }

                            $html = $html . '   <label class="' . $option_attr_checkclass . '" ><input type="checkbox" class="' . $field_attr_inputclass . '" name="'. $name_checkbox .'" value="'.$opcion['valor'].'" '.$enabled.'>'.$opcion['enunciado'].'</label>' . "\n";
                            $html = $html . '</div>';
                            $html = $html . '' . "\n";
                        }
                        $html = $html . '</div>';

                    }

                }

                //Prevent that a double role definition in the permission generate two blocks of the same question.
                break;

            }
        }

    }

    
    $html_aditional_buttons = "";

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
                "delete"
            ];

            foreach( $buttons as $key => $button ){

                //Verification of button alias.
                if( is_null( $button->alias ) || ( $button->alias == "" ) ){
                    return dphpformsV2_build_exception_message( "<strong>button->alias</strong> cannot be empty" );
                }

                //Validation of alias string structure.
                if( !preg_match( '/^[a-z0-9_]+$/', $button->alias )  ){
                    return dphpformsV2_build_exception_message( "<strong>".$button->alias."</strong> is not a valid alias, valid regex [a-z0-9_]+, for instance, alias_1 " );
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
                   
                    $html_button = dphpformsV2_generate_html_button( $button->alias, $button->text, $button->main_classes, $allow_reserved_alias );

                    //If return is null means that was defined an invalid alias or was tried to define and reserved alias without flag.
                    if( !$html_button ){
                        return dphpformsV2_build_exception_message( "<strong>" . $button->alias . "</strong> is an reserved alias and its allow flag is not defined" );
                    }else{
                        $html_aditional_buttons .= $html_button;
                    }

                }else{
                    return dphpformsV2_build_exception_message( "<strong>" . $button->alias . "</strong> cannot be defined more that one time" );
                }
            }
        }
    }

    $html = $html .  ' 
        <hr class="footer-hr-dphpforms">
        <div class="dphpforms_response_recorder_buttons">
            <button type="submit" class="btn-dphpforms btn-dphpforms-sendform">Registrar</button>
            '.$html_aditional_buttons.'
        </div>
    </form>';

    return $html;

}

function dphpformsV2_generate_TEXTFIELD( $id_formulario_pregunta, $field_options, $statement ){
    
    $field_attr_class = $field_options[ 'attr_class' ];
    $field_attr_local_alias = $field_options[ 'attr_local_alias' ];
    $field_attr_inputclass = $field_options[ 'attr_inputclass' ];
    $field_attr_max = $field_options[ 'attr_max' ];
    $field_attr_min = $field_options[ 'attr_min' ];
    $field_attr_type = $field_options[ 'attr_type' ];
    $field_attr_placeholder = $field_options[ 'attr_placeholder' ];
    $field_default_value = $field_options[ 'default_value' ];
    $field_attr_maxlength = $field_options[ 'attr_maxlength' ];
    $field_enabled = $field_options[ 'enabled' ];
    $field_attr_required = $field_options[ 'attr_required' ];
    
    $html = '
    <div class="div-'.$id_formulario_pregunta.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' 
        . $statement . ':<br>
        <input id="'.$id_formulario_pregunta.'" class="form-control ' . $field_attr_inputclass . '" max="' . $field_attr_max . '"  min="' . $field_attr_min . '" type="'.$field_attr_type.'" placeholder="'.$field_attr_placeholder.'" name="'.$mod_id_formulario_pregunta.'" value="'.$field_default_value.'" maxlength="'.$field_attr_maxlength.'" '.$field_enabled.' '.$field_attr_required.'>
    </div>';

    return $html;
    
}

function dphpformsV2_generate_TEXTAREA( $id_formulario_pregunta, $field_options, $statement ){

    $field_attr_class = $field_options[ 'attr_class' ];
    $field_attr_local_alias = $field_options[ 'attr_local_alias' ];
    $field_attr_inputclass = $field_options[ 'attr_inputclass' ];
    $field_attr_placeholder = $field_options[ 'attr_placeholder' ];
    $field_default_value = $field_options[ 'default_value' ];
    $field_attr_maxlength = $field_options[ 'attr_maxlength' ];
    $field_enabled = $field_options[ 'enabled' ];
    $field_attr_required = $field_options[ 'attr_required' ];

    $html = '
    <div class="div-'.$id_formulario_pregunta.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' 
        . $statement . ':<br>
        <textarea id="'.$id_formulario_pregunta.'" class="form-control ' . $field_attr_inputclass . '" name="'. $id_formulario_pregunta .'" placeholder="'.$field_attr_placeholder.'" maxlength="'.$field_attr_maxlength.'" '.$field_enabled.' '.$field_attr_required.'>'.$field_default_value.'</textarea>
    </div>';

    return $html;
    
}

function dphpformsV2_generate_DATE( $id_formulario_pregunta, $field_options, $statement ){

    $field_attr_class = $field_options[ 'attr_class' ];
    $field_attr_local_alias = $field_options[ 'attr_local_alias' ];
    $field_attr_inputclass = $field_options[ 'attr_inputclass' ];
    $field_attr_max = $field_options[ 'attr_max' ];
    $field_attr_min = $field_options[ 'attr_min' ];
    $field_enabled = $field_options[ 'enabled' ];
    $field_attr_required = $field_options[ 'attr_required' ];

    $html = '
    <div class="div-'.$id_formulario_pregunta.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' 
        . $statement . ':<br>
        <input id="'.$id_formulario_pregunta.'" class="form-control ' . $field_attr_inputclass . '" max="' . $field_attr_max . '"  min="' . $field_attr_min . '" type="date" name="'.$id_formulario_pregunta.'" '.$field_enabled.' '.$field_attr_required.'>
    </div>';

    return $html;

}



/**
 * Function that generates the html of the buttons.
 * @author Jeison Cardona Gómez, <jeison.cardona@correounivalle.edu.co>
 * @param String $alias, this alias will be used as class-identifier, for instance, btn-dphpforms-alias
 * @param String $text, it is the buttom value.
 * @param String $main_classes, aditional css classes.
 * @return String HTML with the buttons tags.
 */

function dphpformsV2_generate_html_button( $alias, $text, $main_classes, $allow_reserved_alias = false ){
    
    $reserved_aliases = [
        "update",
        "delete"
    ];

    if( is_null( $alias ) ){
        return null;
    } 
    
    if( in_array( $alias, $reserved_aliases ) && !$allow_reserved_alias ){
        return null;
    }

    return '<input type="button" class="button btn-dphpforms btn-dphpforms-'. $alias .' '. $main_classes .'" value="'.$text.'" >';
}

/**
 * Function that allow build and standard error message when the process of rendering cannot be completed.
 * @author Jeison Cardona Gómez, <jeison.cardona@correounivalle.edu.co>
 * @param String $reason, cause of exception.
 * @return String standard exception message.
 */
function dphpformsV2_build_exception_message( $reason ){
    return "<h1>Error rendering</h1> The form cannot be rendered for the following reason: " . $reason . "."; 
}
  

?>
