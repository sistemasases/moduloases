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
header('Content-Type: application/json');

$xQuery = new stdClass();
$xQuery->form = "seguimiento_pares"; // Can be alias(String) or idntifier(Number)
/*$xQuery->filterFields = [
                         ["id_estudiante", [["428","="]], false],
                         ["lugar", [["Plazoleta de ingenieria","!="],["Biblioteca","!="],["Edificio 320","!="]], false],
                         ["fecha", [["2018-07-01",">"]], true], 
                         ["id_instancia", [["value","="]], true], 
                         ["id_monitor",[["value","="]], true]
                        ];*/
$xQuery->filterFields = [
                            ["fecha", [["2018-02-01",">="],["2018-07-31","<="]], false],
                            ["id_instancia", [["450299","="]], false],
                            ["revisado_profesional",[["0","="]],false]
                           ];
$xQuery->orderFields = [
                        ["id_instancia","ASC"], 
                        ["id_creado_por", "DESC"]  
                       ];
$xQuery->orderByDatabaseRecordDate = false; // If true, orderField is ignored

$xQuery->recordStatus = [ "!deleted", "!deleted" ];// options "deleted" or "!deleted", can be both.
$xQuery->selectedFields = [ "id_creado_por", "id_estudiante" ]; // RecordId and BatabaseRecordDate are selected by default.

echo json_encode( dphpformsV2_find_records( $xQuery ) );

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

        // Criteria base: R.id_pregunta = XX AND R.respuesta = 'ABC'
        $criteria = "";
        foreach( $query->filterFields[0][1] as $filterValue ){
            $criteria .= "(R.id_pregunta = " .$list_fields_alias_id[$query->filterFields[0][0]]. " AND R.respuesta ".$filterValue[1]." '". $filterValue[0] . "')";
            /**
             * next: Warning This function may return Boolean FALSE, but may also return a non-Boolean value which evaluates to FALSE
             */
            if( next( $query->filterFields[0][1] ) ) {
                // http://127.0.0.1/moodle34/blocks/ases/managers/dphpforms/v2/dphpforms_lib.php
                $criteria .= " AND "; // Manejar AND o OR
            }
        }

        //Find by the firt param the records id.
        $sql_first_parameter = "SELECT DISTINCT FS.id_formulario_respuestas
                                FROM {talentospilos_df_respuestas} AS R
                                INNER JOIN {talentospilos_df_form_solu} AS FS ON FS.id_respuesta = R.id
                                WHERE " . $criteria;
        
        $inner_join_more_responses = "SELECT id_respuesta, FS1.id_formulario_respuestas
                                      FROM {talentospilos_df_form_solu} AS FS1 
                                      INNER JOIN ($sql_first_parameter) AS PQ ON FS1.id_formulario_respuestas = PQ.id_formulario_respuestas 
                                      ORDER BY FS1.id_formulario_respuestas";
        
        $inner_join_values = "SELECT R3.id, IJMR.id_formulario_respuestas, R3.respuesta, R3.id_pregunta, R3.fecha_hora_registro
                              FROM {talentospilos_df_respuestas} AS R3 
                              INNER JOIN ( $inner_join_more_responses ) AS IJMR ON id_respuesta = R3.id";

        $where_clause = "WHERE ";
        if( count( $query->filterFields ) > 1 ){
            
            $first_filter_field = true;
            
            foreach( $query->filterFields as $filterField ){

                $fieldAlias = $filterField[0];
                $filterValues = $filterField[1];
                $optional =  $filterField[2];

                $filter_where = "";
                $belongs_block_AND = false;
                
                if( !$first_filter_field ){
                    if( $tmpNextFilterField = next($query->filterFields) ){
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
        $grouped_records[ $record->id_formulario_respuestas ][ $record->id_pregunta ] = $record->respuesta;
     };

     $records_ids = array_values(array_unique( $records_ids ));

     echo( $sql_query . "\n" );
     //print_r( $grouped_records );
     //print_r( $list_fields_id_alias );

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
            $exist_in_grouped_record = array_key_exists( $id_field, $grouped_records[$record_id] );
            if( !$exist_in_grouped_record && !$optional ){
                $record_completed = false;
            }
         };
         if($record_completed){
             array_push($valid_records,$grouped_records[$record_id]);
         }
     }

     //print_r("\nVALIDOS\n");
     print_r( $valid_records );

 }

function dphpformsV2_apply_operators( $expected_value, $operator, $input_value ){
    
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
    FROM mdl_talentospilos_df_form_preg AS FP
    INNER JOIN (SELECT * FROM mdl_talentospilos_df_preguntas )AS P
    ON FP.id_pregunta = P.id
    INNER JOIN (SELECT * FROM mdl_talentospilos_df_tipo_campo) AS TC
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


?>
