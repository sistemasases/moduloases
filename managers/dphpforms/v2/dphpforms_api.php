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
 * Dynamic PHP Forms
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs

    require_once(dirname(__FILE__).'/dphpforms_lib.php');

    header('Content-Type: application/json');

    global $USER;
    
    $raw_data = file_get_contents("php://input");
    
    // Validation if the user is logged. 
    // if( $USER->id == 0 ){
    //    return_with_code( -1 );
    //}

    $input = json_decode( $raw_data );

    /**
     * Api la consulta de datos respecto a estudiantes, monitores, practicantes y profesionales.
     * @author Jeison Cardona Gomez
     * @see dphpforms_lib.php
     * @param json $input This input is a json with a function name and their respective parameters. The order of these parameters is very important. See every function to notice of their parameters order.
     * @return json The structure is {"status_code":int, "error_message":string, "data_response":string }
    */

    // Example of valid input. params = Parameters
    // { "function":"function_name", "params":[ "x" ] }

    if( isset($input->function) && isset($input->params) ){

        /* Params: student_ases_id, instance_id, semester_id
        * */
        
        if( $input->function === "find_records" ){

            /* In this request is only valid pass like param(Parameters) the instance identificatior, 
             * for this reason, the input param only can be equal in quantity to one.
             * */
            
            if( count( $input->params ) == 1 ){

                //Ejemplo: Obtiene todos los seguimientos
                /*{
                    "function":"find_records",
                    "params":[
                        {
                            "form":"seguimiento_pares",
                            "filterFields":[
                                    ["id_estudiante",[["%%","LIKE"]], false]
                                ],
                            "orderFields":[],
                            "orderByDatabaseRecordDate":false,
                            "recordStatus":["!deleted"],
                            "selectedFields":[]
                            
                        }
                    ]
                    
                }*/

                //Ejemplo 2: Obtiene todos los seguimientos con el campo 'comentarios_familiar' diferente de vacío.
                /*{
                    "function":"find_records",
                    "params":[
                        {
                            "form":"seguimiento_pares",
                            "filterFields":[
                                    ["comentarios_familiar",[["","!="]], false]
                                ],
                            "orderFields":[],
                            "orderByDatabaseRecordDate":false,
                            "recordStatus":["!deleted"],
                            "selectedFields":[]
                            
                        }
                    ]
                    
                }*/
 
                // Validation                
                if( true ){
                    
                    echo json_encode( 
                        array(
                            "status_code" => 0,
                            "error_message" => "",
                            "data_response" => dphpformsV2_find_records( $input->params[0] )
                        )
                    );
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else if( $input->function === "get_records_reverse_new_field_update" ){

            /* [0] => id_respuesta (int)
             * [1] => form_id_alias (int or string)
             * */
            
            if( count( $input->params ) == 2 ){
 
                // Validation                
                if( is_numeric( $input->params[0] ) ){
                    
                    echo json_encode( 
                        array(
                            "status_code" => 0,
                            "error_message" => "",
                            "data_response" => json_encode( dphpformsV2_get_records_reverse_new_field_update( $input->params[0], $input->params[1] ) )
                        )
                    );
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else if( $input->function === "reverse_new_field_update" ){

            /* [0] => form_id_alias (int)
             * [1] => id_pregunta (int or string)
             * [2] => default_value (string)
             * */
            
            if( count( $input->params ) == 3 ){
 
                // Validation                
                if( is_numeric( $input->params[0] ) ){
                    
                    echo json_encode( 
                        array(
                            "status_code" => 0,
                            "error_message" => "",
                            "data_response" => json_encode( dphpformsV2_reverse_new_field_update( $input->params[0], $input->params[1], $input->params[2] ) )
                        )
                    );
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else{
            // Function not defined
            return_with_code( -4 );
        }
        
    }else{
        return_with_code( -2 );
    }

    // Reserved status code list [-1, -2, -3 ,-4 ,-99]
    function return_with_code( $code ){
        
        switch( $code ){

            case -1:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "You are not allowed to access this resource.",
                        "data_response" => ""
                    )
                );
                break;
            case -2:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "Error in the scheme.",
                        "data_response" => ""
                    )
                );
                break;
            case -3:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "Invalid values in the parameters.",
                        "data_response" => ""
                    )
                );
                break;
            case -4:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "Function not defined.",
                        "data_response" => ""
                    )
                );
                break;
            
            case -99:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "critical error.",
                        "data_response" => ""
                    )
                );
                break;

        }

        die();
    }

?>