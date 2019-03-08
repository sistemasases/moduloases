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

    require_once(dirname(__FILE__). '/../../../../config.php');
    require_once(dirname(__FILE__).'/incident_lib.php');

    header('Content-Type: application/json');

    global $USER;
    
    $raw_data = file_get_contents("php://input");
    
    // Validation if the user is logged. 
    if( $USER->id == 0 ){
        return_with_code( -1 );
    }

    $input = json_decode( $raw_data );

    /**
     * Api para el control de las asignaciones de monitor - practicante y monitor - estudiante.
     * @author Jeison Cardona Gomez
     * @see incident_lib.php
     * @param json $input This input is a json with a function name and their respective parameters. The order of these parameters is very important. See every function to notice of their parameters order.
     * @return json The structure is {"status_code":int, "error_message":string, "data_response":string }
    */

    // Example of valid input. params = Parameters
    // { "function":"get_monitors_by_instance", "params":[ instance_id ] }

    if( isset($input->function) && isset($input->params) ){

        if( $input->function == "create_incident" ){

            /**
             * details
             * system_info
             */
            
            if( count( $input->params ) == 2 ){

                // Order of params
                /**
                 * user_id (moolde)
                 * details
                 * system_info
                 */
                
                if( true ){
                    
                    $ticket_id = incident_create_incident( $USER->id, $input->params[0], $input->params[1] );

                    if( $ticket_id ){
                        echo json_encode( 
                            array(
                                "status_code" => 0,
                                "error_message" => "",
                                "data_response" => $ticket_id
                            )
                        );
                    }else{
                        echo json_encode( 
                            array(
                                "status_code" => -6,
                                "error_message" => "",
                                "data_response" => "-1"
                            )
                        );
                    }                    
                    
                }else{
                    return_with_code( -2 );
                }

            }else{
                return_with_code( -2 );
            }

        }else if( $input->function == "get_logged_user_incidents" ){

            /**
             * details
             * system_info
             */
            
            if( count( $input->params ) == 0 ){
                
                if( true ){
                    
                    echo json_encode( 
                            array(
                                "status_code" => 0,
                                "error_message" => "",
                                "data_response" => array_values(incident_get_logged_user_incidents())
                            )
                        );
                        
                }else{
                    return_with_code( -2 );
                }

            }else{
                return_with_code( -2 );
            }

        }else if( $input->function == "close_logged_user_incident" ){

            /**
             * incident_id
             */
            
            if( count( $input->params ) == 1 ){
                
                if( is_numeric( $input->params[0] ) ){

                    $return = incident_close_logged_user_incident( $input->params[0] );

                    if( $return ){
                        echo json_encode( 
                            array(
                                "status_code" => 0,
                                "error_message" => "",
                                "data_response" => $return
                            )
                        );
                    }else{
                        echo json_encode( 
                            array(
                                "status_code" => -6,
                                "error_message" => "The incident with id " . $input->params[0] . " does not exist.",
                                "data_response" => ""
                            )
                        );
                    }
                        
                }else{
                    return_with_code( -2 );
                }

            }else{
                return_with_code( -2 );
            }

        }else if( $input->function == "get_incident" ){

            /**
             * incident_id
             */
            
            if( count( $input->params ) == 1 ){
                
                if( is_numeric( $input->params[0] ) ){

                    $return = incident_get_incident( $input->params[0] );

                    if( $return ){
                        echo json_encode( 
                            array(
                                "status_code" => 0,
                                "error_message" => "",
                                "data_response" => json_encode( $return )
                            )
                        );
                    }else{
                        echo json_encode( 
                            array(
                                "status_code" => -6,
                                "error_message" => "The incident with id " . $input->params[0] . " does not exist.",
                                "data_response" => ""
                            )
                        );
                    }
                        
                }else{
                    return_with_code( -2 );
                }

            }else{
                return_with_code( -2 );
            }

        }else if( $input->function == "get_ids_open_incidents" ){
            
            if( count( $input->params ) == 0 ){
                
                if( true ){

                    echo json_encode( 
                        array(
                            "status_code" => 0,
                            "error_message" => "",
                            "data_response" => array_values( incident_get_ids_open_incidents() )
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
            
            case -5:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_message" => "Duplicate.",
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