
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
        
        if(  $_POST['loadF'] ==  "loadForms" || $_POST['loadF']=="get_like" || $_POST['loadF'] == "get_records_simple" ){
            //Example of loadF: loadForms valid: 
            //data: loadForms does not require params
            //Example of loadF: get_like valid: 
            //data: get_like require cadena and atributo params

            $columns = array();
            array_push($columns, array("title"=>"Formulario", "name"=>"id_form", "data"=>"id"));
            array_push($columns, array("title"=>"Usuario", "name"=>"id_user", "data"=>"id_user"));
            array_push($columns, array("title"=>"Acción", "name"=>"name_accion", "data"=>"name_accion"));
            array_push($columns, array("title"=>"Id respuesta", "name"=>"id_respuesta", "data"=>"id_respuesta"));
            array_push($columns, array("title"=>"Fecha", "name"=>"fecha_act", "data"=>"fecha_act" ));
            array_push($columns, array("title"=>"Navegador", "name"=>"nav", "data"=>"nav" ));

            if($_POST['loadF'] == "loadForms"){

                $retorno = get_list_form();

            }else if($_POST['loadF'] == "get_like"){

                $retorno = get_like( $_POST['cadena'], $_POST['atributo'] );

            }else if($_POST['loadF'] == "get_records_simple"){

                $retorno = dwarehouse_get_simple( $_POST['username'], $_POST['is_student'] );
            }
    
            $data = array(
                        "bsort" => false,
                        "columns" => $columns,
                         "data" =>$retorno,
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


        } else if(  $_POST['loadF'] ==  "loadGeneralLogs" || $_POST['loadF']=="get_like_general_logs" ){
            //Example of loadF: loadGeneralLogs valid: 
            //data: loadGeneralLogs does not require params
            //Example of loadF: get_like_general_logs valid: 
            //data: get_like_general_logs require cadena and atributo params

            $columns = array();
            array_push($columns, array("title"=>"Logid", "name"=>"id_form", "data"=>"id"));
            array_push($columns, array("title"=>"Hecho por", "name"=>"id_user_moodle", "data"=>"id_moodle_user"));
            array_push($columns, array("title"=>"Hecho a", "name"=>"ases_user", "data"=>"username_ases_student"));
            array_push($columns, array("title"=>"Nombre (s)", "name"=>"firstname", "data"=>"firstname" ));
            array_push($columns, array("title"=>"Apellido (s)", "name"=>"lastname", "data"=>"lastname" ));
            array_push($columns, array("title"=>"Evento", "name"=>"name_event", "data"=>"name_event"));
            array_push($columns, array("title"=>"Fecha", "name"=>"fecha_act", "data"=>"fecha_act" ));

            if($_POST['loadF']=="loadGeneralLogs"){$retorno = get_list_general_logs();}
                //else if($_POST['loadF']=="get_like_general_logs"){$retorno=get_like($_POST['cadena'],$_POST['atributo']);}
    
            $data = array(
                        "bsort" => false,
                        "columns" => $columns,
                         "data" =>$retorno,
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
            if( count($_POST['params']) == 2 ){
                //Consulta para codigo
                $post = $_POST['params'];

                if($post[1] == "talentospilos_user_extended") {
                    $data = get_id_switch_user_ases($post[0]);
                }
                if($post[1]=="user")    {
                    $data = get_id_switch_user($post[0]);
                }
               
                echo json_encode($data);

            }else{    return_with_code( -2 ); }
        } else if( $_POST['loadF'] == "get_form" ){
            //Example of loadF: get_form valid: 
            //data: get_form   params: id_form
          
            if( count($_POST['params']) == 1 ){

             //Get form data switch id form

             $data = get_form_switch_id($_POST['params'], '{talentospilos_df_dwarehouse}' );
                    echo json_encode($data);
                    
            }else{     
                return_with_code( -2 );
            }
        } else if( $_POST['loadF'] == "get_form_general_logs" ){
            //Example of loadF: get_form_general_logs valid: 
            //data: get_form_general_logs   params: id_form
          
            if( count($_POST['params']) == 1 ){

             //Get form data switch id form

             $data = get_form_switch_id($_POST['params'], '{talentospilos_general_logs}' );
                    echo json_encode($data);
                    
            }else{     
                return_with_code( -2 );
            }
        } else if( $_POST['loadF'] == "get_values" ){
            //Example of loadF: get_values valid: 
            //data: get_values   params: id_pregunta
          
            if( count($_POST['params']) == 1 ){

             //Get pregunta data switch id

            $data = get_question_data($_POST['params']);
            echo json_encode($data);
                    
            }else{     
                return_with_code( -2 );
            }
        } else if( $_POST['loadF'] == "restore_dwarehouse_record" ){
            //Example of loadF: restore_dwarehouse_record valid: 
            //data: restore_dwarehouse_record   params: dwarehouse_id_form_to_restore
          
            if( count($_POST['params']) == 1 ){

            //Get 'registro_respuesta_form' 	FROM table mdl_talentospilos_df_dwarehouse switch params
            $registro_respuesta_form = get_registro_respuesta_form($_POST['params']);
            
            //Update 'estado' on record id loaded from mdl_talentospilos_df_form_resp
            $data = update_record_talentospilos_df_form_resp($registro_respuesta_form->id_registro_respuesta_form);
         
            if ($data){
                //Create new register into mdl_talentospilos_df_dwarehouse to RESTORE
                echo json_encode(log_to_restore_into_dwarehouse($registro_respuesta_form));

            }else{
                echo json_encode($data);
            }


            
                    
            }else{     
                return_with_code( -2 );
            }
        } else if( $_POST['loadF'] == "get_tipo_form" ){
            //Example of loadF: get_tipo_form valid: 
            //data: get_tipo_form   params: id_registro_respuesta_form
          
            if( count($_POST['params']) == 1 ){

            //Get 'tipo_form' 	FROM table mdl_talentospilos_df_formularios switch params

            $data = get_tipo_form($_POST['params']);
         
            echo json_encode($data);
                    
            }else{     
                return_with_code( -2 );
            }
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