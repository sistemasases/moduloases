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
                        $(this).parent().next().show();
                        $(this).parent().next().children("input").prop("required", true);
                        $(this).parent().next().children("input").val("");
                 
                });

                $("#save_economics_data").click(function(){
                    let respuesta = validateEconomicsData();
                  
                        swal(respuesta.title,
                            respuesta.msg,
                            respuesta.status);
                  
                  
                });

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
    
                                msg.title = "Datos econocómicos";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" es obligatorio";
                                return msg;  
    
                                }
                                if(tipo == "text"){
                                    if(has_numbers(value)){
                                        msg.title = "Datos econocómicos";
                                        msg.status = "error";
                                        msg.msg = "El campo "+op+" no debe contener números";
                                        return msg;  
                                        }
                                }
                                if(tipo == "number"){
                                    if(Number.isNaN(value)){
                                        msg.title = "Datos econocómicos";
                                        msg.status = "error";
                                        msg.msg = "El campo "+op+" debe ser numérico";
                                        return msg;  
                                        }
                                        if(value < 0){
                                            msg.title = "Datos econocómicos";
                                            msg.status = "error";
                                            msg.msg = "El campo "+op+" no debe ser negativo";
                                            return msg;  
                                            }    
                                }
                                
                            });
                          
                        }
                       
                    });

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