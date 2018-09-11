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
 * @author     Juan Pablo Castro
 * @package    block_ases
 * @copyright  2018 Juan Pablo Castro<juan.castro.vasquez@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs

    require_once(dirname(__FILE__). '/../../../../config.php');
    require_once(dirname(__FILE__).'/dphpforms_dwarehouse_lib.php');

    header('Content-Type: application/json');
    /**
     * Api la consulta de datos respecto a usuarios, y formularios.
     * @author Juan Pablo Castro
     * @see user_management_lib.php
     * @param json $input This input is a json with a function name and their respective parameters. The order of these parameters is very important. See every function to notice of their parameters order.
     * @return json The structure is {"status_code":int, "error_message":string, "data_response":string }
    */

    // Example of valid input. params = Parameters
    // { "function":"LA_FUNCION", "params":[ id_user , id_form ] }

    if( isset($_POST['loadF'])){

        /* Params: student_ases_id, instance_id, semester_id
        * */
        
        if(  $_POST['loadF'] ==  "loadForms" ){

            /* In this request is only valid pass like param(Parameters) the instance identificatior, 
             * for this reason, the input param only can be equal in quantity to one.
             * */
              $columns = array();
            array_push($columns, array("title"=>"Id usuario", "name"=>"id_user", "data"=>"id_user"));
            array_push($columns, array("title"=>"Acción", "name"=>"name_accion", "data"=>"name_accion"));
            array_push($columns, array("title"=>"Id respuesta", "name"=>"id_respuesta", "data"=>"id_respuesta"));
            array_push($columns, array("title"=>"Fecha", "name"=>"fecha_act", "data"=>"fecha_act"));
            array_push($columns, array("title"=>"Identificador", "name"=>"id_form", "data"=>"id"));
    
            $data = array(
                        "bsort" => false,
                        "columns" => $columns,
                        "data" =>get_list_form(),
                        "language" => 
                         array(
                            "search"=> "Buscar:",
                            "oPaginate" => array(
                                "sFirst"=>    "Primero",
                                "sLast"=>     "Último",
                                "sNext"=>     "Siguiente",
                                "sPrevious"=> "Anterior"
                                ),
                            "sProcessing"=>     "Procesando...",
                            "sLengthMenu"=>     "Mostrar _MENU_ registros",
                            "sZeroRecords"=>    "No se encontraron resultados",
                            "sEmptyTable"=>     "Ningún dato disponible en esta tabla",
                            "sInfo"=>           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                            "sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
                            "sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
                            "sInfoPostFix"=>    "",
                            "sSearch"=>         "Buscar:",
                            "sUrl"=>            "",
                            "sInfoThousands"=>  ",",
                            "sLoadingRecords"=> "Cargando...",
                         ),
                        "order"=> array(0, "desc"),
                        "dom"=>'lifrtpB',
    
                        "buttons"=>array(
                            array(
                                "extend"=>'print',
                                "text"=>'Imprimir'
                            ),
                            array(
                                "extend"=>'csvHtml5',
                                "text"=>'CSV'
                            ),
                            array(
                                "extend" => "excel",
                                                "text" => 'Excel',
                                                "className" => 'buttons-excel',
                                                "filename" => 'Export excel',
                                                "extension" => '.xls'
                            )
                        )
    
                    );
               
            echo json_encode($data);


        }else if( $_POST['loadF'] == "get_form" ){

            /* Params: student_ases_id, created_by_id, instance_id, semester_id
             * */
           
            if( count($_POST['params']) == 1 ){

                // Order of params
                /**
                 * The student_ases_id value only can be a number.
                 * The created_by_id value only can be a number.
                 * The instance_id value only can be a number.
                 * The semester_id value only can be a number.
                 */
                    $columns = array();
                    array_push($columns, array("title"=>"Id usuario", "name"=>"id_user", "data"=>"id_user"));
                    array_push($columns, array("title"=>"Acción", "name"=>"name_accion", "data"=>"name_action"));
                    array_push($columns, array("title"=>"Datos previos", "name"=>"dp_form", "data"=>"dp_form"));
                    array_push($columns, array("title"=>"Datos enviados", "name"=>"de_form", "data"=>"de_form"));
                    array_push($columns, array("title"=>"Datos almacenados", "name"=>"da_form", "data"=>"da_form"));
                    array_push($columns, array("title"=>"Fecha", "name"=>"fecha_act", "data"=>"fecha_form"));
                  
            
                    $data = array(
                                "bsort" => false,
                                "columns" => $columns,
                                "data" =>get_form_switch_id($_POST['params']),
                                "language" => 
                                 array(
                                    "search"=> "Buscar:",
                                    "oPaginate" => array(
                                        "sFirst"=>    "Primero",
                                        "sLast"=>     "Último",
                                        "sNext"=>     "Siguiente",
                                        "sPrevious"=> "Anterior"
                                        ),
                                    "sProcessing"=>     "Procesando...",
                                    "sLengthMenu"=>     "Mostrar _MENU_ registros",
                                    "sZeroRecords"=>    "No se encontraron resultados",
                                    "sEmptyTable"=>     "Ningún dato disponible en esta tabla",
                                    "sInfo"=>           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                    "sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
                                    "sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
                                    "sInfoPostFix"=>    "",
                                    "sSearch"=>         "Buscar:",
                                    "sUrl"=>            "",
                                    "sInfoThousands"=>  ",",
                                    "sLoadingRecords"=> "Cargando...",
                                 ),
                                "order"=> array(0, "desc"),
                                "dom"=>'lifrtpB',
            
                                "buttons"=>array(
                                    array(
                                        "extend"=>'print',
                                        "text"=>'Imprimir'
                                    ),
                                    array(
                                        "extend"=>'csvHtml5',
                                        "text"=>'CSV'
                                    ),
                                    array(
                                        "extend" => "excel",
                                                        "text" => 'Excel',
                                                        "className" => 'buttons-excel',
                                                        "filename" => 'Export excel',
                                                        "extension" => '.xls'
                                    )
                                )
            
                            );
                       
                    echo json_encode($data);
                    
                
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






if(isset($_POST['loadF']) && $_POST['loadF'] == 'loadForms'){		
          

        } 

        
?>