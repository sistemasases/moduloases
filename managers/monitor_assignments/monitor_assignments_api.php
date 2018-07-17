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
    require_once(dirname(__FILE__).'/monitor_assignments_lib.php');

    header('Content-Type: application/json');

    global $USER;
    
    $raw_data = file_get_contents("php://input");
    
    // Validation if the user is logged. 
    if( $USER->id == 0 ){
        return_with_code( -1 );
    }

    $input = json_decode( $raw_data );

    // Example of valid input. params = Parameters
    // { "function":"get_monitors_by_instance", "params":[ instance_id ] }

    if( isset($input->function) && isset($input->params) ){

        // Get practicant monitor relationship by instance
        // params[0] => instance_id
        if( ( $input->function == "get_practicant_monitor_relationship_by_instance" ) ){

            /* In this request is only valid pass like param(Parameters) the instance identificatior, 
             * for this reason, the input param only can be equal in quantity to one.
             * */
            if( count( $input->params ) == 1 ){
                /**
                 * The instance value only can be a number.
                 */
                
                if( is_numeric( $input->params[0] ) ){

                    echo json_encode( array_values( monitor_assignments_get_practicant_monitor_relationship_by_instance( $input->params[0] ) ) );
                    
                }else{
                    return_with_code( -2 );
                }
            }else{
                return_with_code( -2 );
            }

        }else{
            return_with_code( -2 );
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
                        "error_code" => "You are not allowed to access this resource."
                    )
                );
                break;
            case -2:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_code" => "Error in the scheme."
                    )
                );
                break;
            case -3:
                echo json_encode(
                    array(
                        "status_code" => $code,
                        "error_code" => "Invalid values in the parameters."
                    )
                );
                break;

        }

        die();
    }

?>