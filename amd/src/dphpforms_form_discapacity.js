/**
 * Controls discapacity form
 * @module amd/src/dphpforms_form_discapacity
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
            $("#div_factor_impacto input").on("change",function(){
                if($("#check_factor2").is(":checked")){
                    //Display other options
                    $("#div_factor_contexto").show();
                    if($("#check_factor2_7").is(":checked")){
                        $("#input_factor2_7").prop("disabled", false);
                        $("#input_factor2_7").prop("required", true);
                    }
                    else{
                    $("#input_factor2_7").prop("value", "");
                    $("#input_factor2_7").prop("disabled", true);
                    $("#input_factor2_7").prop("required", false);
                    }
                }else{
                    $("#div_factor_contexto").hide();
                }


                if($("#check_factor4").is(":checked")){
                    //Display input other
                    $("#input_factor4").prop("disabled", false);
                    $("#input_factor4").prop("required", true);
                }else {
                    $("#input_factor4").prop("value", "");
                    $("#input_factor4").prop("disabled", true);
                    $("#input_factor4").prop("required", false);
                    
                }
            });
            
            $("#div_necesidades input").on("change", function(){
                if($(this).is(":checked")){
                   
                    $(this).parent().find(":input[type=text]").prop("disabled", false);
                    $(this).parent().find(":input[type=text]").prop("required", true);
                }else{
                    $(this).parent().find(":input[type=text]").prop("value", "");
                    $(this).parent().find(":input[type=text]").prop("disabled", true);
                    $(this).parent().find(":input[type=text]").prop("required", false);
                }     
                      
            });
            
            $("#div_cond_salud input").on("change", function(){
                if($(this).is(":checked")){
                    
                    $(this).parent().find(":input[type=text]").prop("disabled", false);
                    $(this).parent().find(":input[type=text]").prop("required", true);
                }else{
                    $(this).parent().find(":input[type=text]").prop("value", "");
                    $(this).parent().find(":input[type=text]").prop("disabled", true);
                    $(this).parent().find(":input[type=text]").prop("required", false);
                }     
                      
            });
            $("#div_dificultad_permanente input").on("change", function(){
                if($(this).is(":checked")){
                    $(this).parent().find(":input[type=text]").prop("disabled", false);
                    $(this).parent().find(":input[type=text]").prop("required", true);
                }else{
                    $(this).parent().find(":input[type=text]").prop("value", "");
                    $(this).parent().find(":input[type=text]").prop("disabled", true);
                    $(this).parent().find(":input[type=text]").prop("required", false);
                }     
                      
            });

            $("#btn_ficha_inicial_discapacity").on("click", function() {
              $("#form_ficha_inicial").show();
            });
            
            $("#cond_adquisicion").on("click", function(){
                if($(this).val()== '0'){
                    $("#div_otro_cond_adq").show();
                    $("#otro_cond_adquisicion").prop("required", true);
                }else {
                    $("#div_otro_cond_adq").hide();
                    $("#otro_cond_adquisicion").prop("required", false);
                }
            });
            $("#tipo_discapacidad").on("click", function(){
                if($(this).val()== '0'){
                    $("#div_otra_discapacidad").show();
                    $("#otra_discapacidad").prop("required", true);
                    
                }else {
                    $("#div_otra_discapacidad").hide();
                    $("#otra_discapacidad").prop("required", false);
                }
            });
        
            $("#check_diagnostico").on("change",function(){
                if( $("#check_diagnostico").is(":checked") ) {
                    $("#div_descripcion_diagnostico").show();
                }else{
                    $("#div_descripcion_diagnostico").hide();
                }
            });
            $("#check_certificado_invalidez").on("change",function(){
                if( $("#check_certificado_invalidez").is(":checked") ) {
                    $("#div_porcentaje_inv").show();
                    $("#input_porcentaje_inv").prop("required", true);
                }else{
                    $("#div_porcentaje_inv").hide();
                    $("#input_porcentaje_inv").prop("required", false);
                }
            });
           

        }

    };
});