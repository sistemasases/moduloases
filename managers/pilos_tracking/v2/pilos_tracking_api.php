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

    require_once(dirname(__FILE__). '/../../../../../config.php');
    require_once(dirname(__FILE__).'/pilos_tracking_lib.php');

    header('Content-Type: application/json');

    global $USER;
    
    $raw_data = file_get_contents("php://input");
    
    // Validation if the user is logged. 
    /*if( $USER->id == 0 ){
        return_with_code( -1 );
    }*/

    $input = json_decode( $raw_data );

    /**
     * Api para el control de la view report_trackings.
     * @author Jeison Cardona Gomez
     * @see pilos_tracking_lib.php
     * @param json $input This input is a json with a function name and their respective parameters. The order of these parameters is very important. See every function to notice of their parameters order.
     * @return json The structure is {"status_code":int, "error_message":string, "data_response":string }
    */

    // Example of valid input. params = Parameters
    // { "function":"get_monitors_by_instance", "params":[ instance_id ] }

    if( isset($input->function) && isset($input->params) ){

        // Get **
        // params[0] => instance_id
        if( $input->function === "get_tracking_count" ){

            /**
             * username : String
             * semester_id : int
             * instance : int
             * */
            
            if( count( $input->params ) == 3 ){

                // Order of params
                /**
                 * username
                 * semester
                 * instance
                 */
                
                if( ( $input->params[0] !== "" ) && is_numeric( $input->params[1] ) && is_numeric( $input->params[2] ) ){
                    
                    echo json_encode( 
                        array(
                            "status_code" => 0,
                            "error_message" => "",
                            "data_response" => pilos_tracking_get_tracking_count( $input->params[0], $input->params[1], $input->params[2], false )
                        )
                    );
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else if( $input->function === "get_tracking_count_student" ){

            /**
             * username : String
             * semester_id : int
             * instance : int
             * */
            
            if( count( $input->params ) == 3 ){

                // Order of params
                /**
                 * username
                 * semester
                 * instance
                 */
                
                if( ( $input->params[0] !== "" ) && is_numeric( $input->params[1] ) && is_numeric( $input->params[2] ) ){
                    
                    echo json_encode( 
                        array(
                            "status_code" => 0,
                            "error_message" => "",
                            "data_response" => pilos_tracking_get_tracking_count( $input->params[0], $input->params[1], $input->params[2], true )
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