/**
 * Controls discapacity form
 * @module amd/src/dphpforms_form_others_sp
 * @author Juan Pablo Castro
 * @copyright 2018 Juan Pablo Castro<juan.castro.vasquez@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'block_ases/jszip',
    'block_ases/jquery.dataTables',
    'block_ases/dataTables.autoFill',
    'block_ases/dataTables.buttons',
    'block_ases/buttons.html5',
    'block_ases/buttons.flash',
    'block_ases/buttons.print',
    'block_ases/bootstrap',
    'block_ases/sweetalert',
    'block_ases/jqueryui',
    'block_ases/select2'
], function ($, jszip, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2) {
    return {   
            init: function(){

                $("#economics_data").click(function(){
                    economics_data();
                });
                

                function economics_data(){
                    if($("#input_economics_saved").val() == "0"){
                        $("#save_economics_data").parent().show();
                    }
                    if($("#input_economics_saved").val() == "1"){
                        $("#save_economics_data").parent().hide();
                        $("#edit_economics_data").parent().show();
                    }
    
    
                    $("#collapse_econo input[type=checkbox]").not(".check_solvencia").on("change", function(){
                        if($(this).is(":checked")){
                            $(this).parent().next().show();
                            $(this).parent().next().children().children("input").prop("required", true);
                        }else{
                            $(this).parent().next().hide();
                            $(this).parent().next().children().children("input").prop("required", false);
                            $(this).parent().next().children().children("input").val("");
                        }
                       
                    });
    
                    $(".select_ocupacion").on("change", function(){
                        if($(this).val()=="option_ninguna"){
    
                            $(this).parent().next().hide();
                            $(this).parent().next().children("input").prop("required", false);
                            $(this).parent().next().children("input").val("");
    
                        }else{
    
                            $(this).parent().next().show();
                            $(this).parent().next().children("input").prop("required", true);
                        }
                            
                     
                    });
    
                    $("#save_economics_data").click(function(){
                       
                        //Validar las respuestas obtenidas
                        let respuesta = validateEconomicsData();


                        if(respuesta.status == "error"){
                            swal(respuesta.title,
                                respuesta.msg,
                                respuesta.status);
                        }else{
                        //Si no hay campos obligatorios vacíos, capturar datos

                        let json_economics_data = getEconomicsData();
                        json_economics_data = JSON.stringify(json_economics_data);
                        let id_ases = $("#id_ases").val();

                        saveEconomicsData(json_economics_data, id_ases);

                        }
                      
                      
                    });
                }
                function saveEconomicsData(json_data, ases_id){
                    $.ajax({
                        type: "POST",
                        data: {
                            func: 'save_economics_data',
                            json: json_data, 
                            ases: ases_id
                        },
                        url: "../managers/student_profile/discapacity_tab_api.php",
                        success: function(msg) {
                
                            swal(
                               { title: msg.title,
                                text: msg.msg,
                                type: msg.status
                               },
                               function(){
                                $("html, body").animate({scrollTop:650}, 'slow'); 
                               }
                            
        
                            );
        
        
                            //$("#un_input").attr("value", json_data);
        
                           
                        },
                        dataType: "json",
                        cache: "false",
                        error: function(msg) {
                            swal(
                                msg.title,
                                msg.msg,
                                msg.status
                            );
        
                        },
                    });
        
        
                }

                function getEconomicsData(){
                    //Crea JSON con todos los datos económicos registrados, así como sus identificadores de tags...

                    let array_economics_data = [];
                    //Estrato socio economico
                    let estrato = {
                        key_input: "#estrato_socioeconomico",
                        val_input:$("#estrato_socioeconomico").val()
                    }
                    array_economics_data.push(estrato);

                    //Prestación o ayuda  económica/beca/transporte/materiales
                    let objeto_json = {};

                    $("#collapse_econo input[type=checkbox]").not(".check_solvencia").each( function(){

                        objeto_json.key_input = $(this).attr("id");

                        if($(this).is(":checked")){

                            objeto_json.val_input = 1;

                            $(this).parent().next().children().children("input").each( function(){
                                
                               
                                let tipo  = $(this).attr("type");

                                if(tipo == "text"){
                                    objeto_json.key_input_text = $(this).attr("id");
                                    objeto_json.val_input_text = $(this).val();

                                }

                                if(tipo == "number"){
                                    objeto_json.key_input_number = $(this).attr("id");
                                    objeto_json.val_input_number = $(this).val();
                                }
                             
                                
                            });
                          
                        }else{
                            objeto_json.val_input = 0;
                        }

                        array_economics_data.push(objeto_json);
                        objeto_json = {};
                    });

                    //Solvencia económica

                    let solvencia_econo = {
                        key_input: "#check_solvencia"
                    };
                    if($("#check_solvencia").is(":checked") ){
                        solvencia_econo.val_input = 1;
                    }else{
                        solvencia_econo.val_input = 0;
                    }
                    array_economics_data.push(solvencia_econo);

                    //Expectativas laborales
                    let expectativas_lab = {
                        key_input: "#textarea_expectativas",
                        val_input: $("#textarea_expectativas").val()
                    }

                    array_economics_data.push(expectativas_lab);

                    //Info padres

                    let objeto_select = {};
                    let array_padres  = [];

                    obj = document.getElementById('info_padres');
                    for (i=0; ele = obj.getElementsByTagName('select')[i]; i++){

                    objeto_select.key_select =   ele.value; 
                    objeto_select.val_select =   ele.id;
                    if(ele.id == "select_ocupacion_padre" ||  ele.id == "select_ocupacion_madre"){
                        if(ele.value != "option_ninguna"){
                            objeto_select.key_input_select = "#" + $("#"+ele.id).parent().next().children("input").attr("id");
                            objeto_select.val_input_select = $("#"+ele.id).parent().next().children("input").val();
                        }
                    }

                    //Modificar para salvar datos de padre  y madre separados
                    //Se puede cambiar modelo para caracterizar datos separados
                    array_economics_data.push(objeto_select);
                    objeto_select = {};
                    }
                    

                    return array_economics_data;

                }

                function validateEconomicsData(){

                    var msg = new Object();

                    msg.title = "Éxito";
                    msg.msg = "El formulario fue validado con éxito";
                    msg.status = "success";


                    $("#collapse_econo input[type=checkbox]").not(".check_solvencia").each( function(){
                        if($(this).is(":checked")){

                            $(this).parent().next().children().children("input").each( function(){
                                let value = $(this).val();
                                let op    = $(this).attr("id");
                                let tipo  = $(this).attr("type");
                                if(value == ""){
    
                                msg.title = "Datos económicos";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" es obligatorio";
                                return msg;  
    
                                }
                                if(tipo == "text"){
                                    if(has_numbers(value)){
                                        msg.title = "Datos económicos";
                                        msg.status = "error";
                                        msg.msg = "El campo "+op+" no debe contener números";
                                        return msg;  
                                        }
                                }
                                if(tipo == "number"){
                                    if(Number.isNaN(value)){
                                        msg.title = "Datos económicos";
                                        msg.status = "error";
                                        msg.msg = "El campo "+op+" debe ser numérico";
                                        return msg;  
                                        }
                                        if(value < 0){
                                            msg.title = "Datos económicos";
                                            msg.status = "error";
                                            msg.msg = "El campo "+op+" no debe ser negativo";
                                            return msg;  
                                            }    
                                }
                                
                            });
                          
                        }
                       
                    });


                    if($("#estrato_socioeconomico").val() == ""){
                        let op    = "estrato_socioeconomico";

                        msg.title = "Datos económicos";
                        msg.status = "error";
                        msg.msg = "El campo "+op+" es obligatorio y debe ser número entero";
                        return msg;  
                       
                    }else {

                        let value = $("#estrato_socioeconomico").val();
                        let op    = "estrato_socioeconomico";

                        if(value < 1 || value > 6 || (value % 1) != 0 ){
                           
                            msg.title = "Datos económicos";
                            msg.status = "error";
                            msg.msg = "El campo "+op+" corresponde a estratos sociales (Del 1 al 6)";
                            return msg;  
                    }
                    }


                    if($("#select_ocupacion_madre").val() != "option_ninguna"){

                        let value = $("#input_ocupacion_madre").val();
                        let op    = "input_ocupacion_madre";

                        if( value == ""){
                           

                                msg.title = "Datos económicos";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" es obligatorio";
                                return msg;  
                        }
                    }

                    if($("#select_ocupacion_padre").val() != "option_ninguna"){

                        let value = $("#input_ocupacion_padre").val();
                        let op    = "input_ocupacion_padre";

                        if( value == ""){
                           

                                msg.title = "Datos económicos";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" es obligatorio";
                                return msg;  
                        }
                    }

                    return msg;

                }

                function has_numbers(str) {
                    var numbers = "0123456789";
                    for (i = 0; i < str.length; i++) {
                        if (numbers.indexOf(str.charAt(i), 0) != -1) {
                            return 1;
                        }
                    }
                    return 0;
                }

            }
     };
});