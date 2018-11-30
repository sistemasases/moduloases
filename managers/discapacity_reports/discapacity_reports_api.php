
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
    require_once(dirname(__FILE__).'/discapacity_reports_lib.php');

    header('Content-Type: application/json');

    if( isset($_POST['load'])){
        //In JavaScript document (dphpforms_backup_forms.js) a load request is sent (loadF)
        //loadF can be: loadForms, get_form, get_id_user
        
        if(  $_POST['load'] ==  "loadTableDiscapacityReports"  ){
            //Example of loadF: loadForms valid: 
            //data: loadForms does not require params
            //Example of loadF: get_like valid: 
            //data: get_like require cadena and atributo params


           $retorno = get_list_discapacity_reports();
           $nuevo_retorno = array();
            foreach($retorno as $registro){
                //Objeto nuevo que representa un estudiante
                $objeto_nuevo = new stdClass ();
                $json_detalle = json_decode($registro->detalle_disc);
                $objeto_nuevo->num_doc_act         = $registro->num_doc_act;
                $objeto_nuevo->name_estudiante     = $registro->name_estudiante;
                $objeto_nuevo->lastname_estudiante = $registro->lastname_estudiante;

                //Tipo de discapacidad
                if($json_detalle->tipo_discapacidad->tipo_discapacidad != "Otra"){
                    $objeto_nuevo->tipo_discapacidad   = $json_detalle->tipo_discapacidad->tipo_discapacidad;
                }else{
                    $objeto_nuevo->tipo_discapacidad   = $json_detalle->tipo_discapacidad->tipo_discapacidad .": ". $json_detalle->tipo_discapacidad->otro_tipo;
                }

                //Condición de adquisición
                if($json_detalle->condicion_adquisicion->condicion != "Otra"){
                    $objeto_nuevo->condicion_adq   = $json_detalle->condicion_adquisicion->condicion;
                }else{
                    $objeto_nuevo->condicion_adq   = $json_detalle->condicion_adquisicion->condicion .": ". $json_detalle->condicion_adquisicion->otra_condicion;
                }

                //Certificado de invalidez
                if($json_detalle->certificado_invalidez->tiene_certificado != 1){
                    $objeto_nuevo->certificado_invalidez   = "NO";
                }else{
                    $objeto_nuevo->certificado_invalidez   = "SÍ";
                }

             
            
                array_push($nuevo_retorno, $objeto_nuevo);

            }

              $columns = array();
            array_push($columns, array("title"=>"Documento", "name"=>"num_doc", "data"=>"num_doc_act"));
            array_push($columns, array("title"=>"Nombre (s)", "name"=>"name_user", "data"=>"name_estudiante"));
            array_push($columns, array("title"=>"Apellido (s)", "name"=>"lastname_user", "data"=>"lastname_estudiante"));
            array_push($columns, array("title"=>"Discapacidad", "name"=>"tipo_discapacidad", "data"=>"tipo_discapacidad"));
            array_push($columns, array("title"=>"Adquisición", "name"=>"cond_adq", "data"=>"condicion_adq"));
            array_push($columns, array("title"=>"Invalidez", "name"=>"certificado_invalidez", "data"=>"certificado_invalidez"));

            $data = array(
                        "bsort" => false,
                        "columns" => $columns,
                         "data" =>$nuevo_retorno,
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