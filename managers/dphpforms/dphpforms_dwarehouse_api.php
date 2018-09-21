
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

    if( isset($_POST['loadF'])){
        //In JavaScript document (dphpforms_backup_forms.js) a load request is sent (loadF)
        //loadF can be: loadForms, get_form, get_id_user
        
        if(  $_POST['loadF'] ==  "loadForms" ){
            //Example of loadF: loadForms valid: 
            //data: loadForms does not require params

              $columns = array();
            array_push($columns, array("title"=>"Formulario", "name"=>"id_form", "data"=>"id"));
            array_push($columns, array("title"=>"Usuario", "name"=>"id_user", "data"=>"id_user"));
            array_push($columns, array("title"=>"Acción", "name"=>"name_accion", "data"=>"name_accion"));
            array_push($columns, array("title"=>"Id respuesta", "name"=>"id_respuesta", "data"=>"id_respuesta"));
            array_push($columns, array("title"=>"Fecha", "name"=>"fecha_act", "data"=>"fecha_act" ));
           
    
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


        } else if ($_POST['loadF']== 'get_id_user'){
            //Example of loadF: get_id_user valid: 
            //data: get_id_user   params: cod_user
            if( count($_POST['params']) == 1 ){
                //Consulta para codigo
                $data = get_id_switch_user($_POST['params']);
                echo json_encode($data);

            }else{    return_with_code( -2 ); }
        }else if( $_POST['loadF'] == "get_form" ){
            //Example of loadF: get_form valid: 
            //data: get_form   params: id_form
          
            if( count($_POST['params']) == 1 ){

             //Get form data switch id form
               
                    $data = get_form_switch_id($_POST['params']);
                    echo json_encode($data);
                    
            }else{     
                return_with_code( -2 );
            }
        } else if ($_POST['loadF']== "get_like"){
             //Example of loadF: get_like valid: 
            //data: get_like   params: [cad, column]
            $data = get_like($_POST['params'][0],$_POST['params'][1]);
            echo json_encode($data);

        } else{
            // Function not defined
            return_with_code( -4 );
        }
    }else{
        return_with_code( -1 );
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