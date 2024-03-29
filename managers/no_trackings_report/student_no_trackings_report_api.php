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
 * Trackings count
 *
 * @author     Jeison Cardona Gómez
 * @author     Joan Manuel Tovar Guzmán
 * @package    block_ases
 * @copyright  2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @copyright  2018 Joan Manuel Tovar GUzmán <joan.tovar@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
    require_once(dirname(__FILE__). '/../../../../config.php');
    require_once(dirname(__FILE__).'/students_no_trackings_report_lib.php');

    header('Content-Type: application/json');

    global $USER;
    
    $raw_data = file_get_contents("php://input");

    // Validation if the user is logged. 
    if( $USER->id == 0 ){
        return_with_code( -1 );
    }

    $input = json_decode( $raw_data );

    /**
     * Students no trackings API functions.
     * @author Jeison Cardona Gomez
     * @see students_no_trackings_report_lib.php
     * @param json $input This input is a json with a function name and their respective parameters. The order of these parameters is very important. See every function to notice of their parameters order.
     * @return json The structure is {"status_code":int, "error_message":string, "data_response":string }
    */

    // Example of valid input. params = Parameters
    // { "function":"function_name", "params":[ instance_id ] }

    if( isset($input->function) && isset($input->params) ){

        // Get practicant monitor relationship by instance
        // params[0] => instance_id
        if( $input->function == "load_report" ){

            /* In this request is only valid pass like param(Parameters) the instance identificatior, 
             * for this reason, the input param only can be equal in quantity to one.
             * */
            
            if( count( $input->params ) == 1 ){

                // Order of params
                /**
                 * The instance value only can be a number.
                 */
                
                if( is_numeric( $input->params[0] ) ){
                    
                    echo json_encode( 
                        array(
                            "status_code" => 0,
                            "error_message" => "",
                            "data_response" => @students_no_trackings_generate_datatable( $input->params[0] )
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