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
            
            $("#div_necesidades input[type=checkbox]").on("change", function(){
                if($(this).is(":checked")){
                   
                    $(this).parent().find(":input[type=text]").prop("disabled", false);
                    $(this).parent().find(":input[type=text]").prop("required", true);
                }else{
                    $(this).parent().find(":input[type=text]").prop("value", "");
                    $(this).parent().find(":input[type=text]").prop("disabled", true);
                    $(this).parent().find(":input[type=text]").prop("required", false);
                }     
                      
            });

            $("#div_posibilidades_condiciones input[type=range]").on("change", function(){
             if($(this).val()==1){
                $(this).parent().find(":input[type=text]").prop("value","No la puede realizar");
                
             }
             if($(this).val()==2){
                $(this).parent().find(":input[type=text]").prop("value","Lo hace sin apoyo");
            }
            if($(this).val()==3){
                $(this).parent().find(":input[type=text]").prop("value","Lo hace con apoyo");
            }
            });

            $("#check_otra_posibilidad").on("click", function(){
                if($(this).is(":checked")){
                    $("#div_otra_actividad").show();
                    $("#input_otro1").prop("required",true);
                    $("#input_tipo_otro").prop("required",true);
                    $("#input_tipo_otro").prop("disabled",false);
                    $("#clasificacion_otro").prop("disabled", false);
                    $("#clasificacion_otro").prop("required", true);
                }else{
                    $("#div_otra_actividad").hide();
                    $("#input_otro1").prop("value","");
                    $("#input_posib_otro").prop("value","");
                    $("#input_tipo_otro").prop("value","");
                    $("#input_otro1").prop("required",false);
                    $("#input_tipo_otro").prop("required",false);
                    $("#input_tipo_otro").prop("disabled",true);
                    $("#clasificacion_otro").prop("disabled", true);
                    $("#clasificacion_otro").prop("required", false);
                    $("#clasificacion_otro").prop("value", null);
                }
            });

            
            $("#div_cond_salud input[type=checkbox]").on("change", function(){
                if($(this).is(":checked")){
                    
                    $(this).parent().find(":input[type=text]").prop("disabled", false);
                    $(this).parent().find(":input[type=text]").prop("required", true);
                }else{
                    $(this).parent().find(":input[type=text]").prop("value", "");
                    $(this).parent().find(":input[type=text]").prop("disabled", true);
                    $(this).parent().find(":input[type=text]").prop("required", false);
                }     
                      
            });
            $("#div_dificultad_permanente input[type=checkbox]").on("change", function(){
                if($(this).is(":checked")){
                    $(this).parent().find(":input[type=text]").prop("disabled", false);
                    $(this).parent().find(":input[type=text]").prop("required", true);
                }else{
                    $(this).parent().find(":input[type=text]").prop("value", "");
                    $(this).parent().find(":input[type=text]").prop("disabled", true);
                    $(this).parent().find(":input[type=text]").prop("required", false);
                }     
                      
            });

            $("#div_posibilidades_condiciones input[type=checkbox]").on("change", function(){
                if($(this).is(":checked")){
                    $(this).parent().find(":input[type=range]").prop("disabled", false);
                    $(this).parent().find(":input[type=range]").prop("required", true);
                    $(this).parent().find(":input[name=input_apoyo]").prop("disabled", false);
                    $(this).parent().find(":input[name=input_apoyo]").prop("required", true);

                    
                }else{
                    $(this).parent().find(":input[type=range]").prop("value", null);
                    $(this).parent().find(":input[type=range]").prop("disabled", true);
                    $(this).parent().find(":input[type=range]").prop("required", false);
                    $(this).parent().find(":input[name=input_apoyo]").prop("disabled", true);
                    $(this).parent().find(":input[name=input_apoyo]").prop("required", false);
                    $(this).parent().find(":input[type=text]").prop("value", "");
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
                    $("#otro_cond_adquisicion").prop("value", "");
                }
            });

            $("#opciones_apoyo input[name=apoyo_principal]").on("change", function(){
                if($("#input_radio_otro_oa").is(":checked")){
                    $("#div_otro_apoyo_principal").show();
                    $("#input_otro_apoyo").prop("required", true);
                }else{
                    $("#div_otro_apoyo_principal").hide();
                    $("#input_otro_apoyo").prop("required", false);
                    $("#input_otro_apoyo").prop("value", "");
                }
            });

            $("#opciones_transporte input[name=desplazamiento]").on("change", function(){
                if($("#input_radio_otro_ot").is(":checked")){
                    $("#div_otro_transporte").show();
                    $("#input_otro_transporte").prop("required", true);
                }else{
                    $("#div_otro_transporte").hide();
                    $("#input_otro_transporte").prop("required", false);
                    $("#input_otro_transporte").prop("value", "");
                }
            });

            

            $("#tipo_discapacidad").on("click", function(){
                if($(this).val()== '0'){
                    $("#div_otra_discapacidad").show();
                    $("#otra_discapacidad").prop("required", true);
                    
                }else {
                    $("#div_otra_discapacidad").hide();
                    $("#otra_discapacidad").prop("required", false);
                    $("#otra_discapacidad").prop("value", "");
                }
            });
        
            $("#check_diagnostico").on("change",function(){
                if( $("#check_diagnostico").is(":checked") ) {
                    $("#div_descripcion_diagnostico").show();
                }else{
                    $("#div_descripcion_diagnostico").hide();
                    $("#textarea_diagnostico").prop("value", "");
                }
            });
            $("#check_certificado_invalidez").on("change",function(){
                if( $("#check_certificado_invalidez").is(":checked") ) {
                    $("#div_porcentaje_inv").show();
                    $("#input_porcentaje_inv").prop("required", true);
                }else{
                    $("#div_porcentaje_inv").hide();
                    $("#input_porcentaje_inv").prop("required", false);
                    $("#input_porcentaje_inv").prop("value", "");
                }
            });

            $("#check_org").on("change",function(){
                if( $("#check_org").is(":checked") ) {
                    $("#div_organizacion_asociacion").show();
                    $("#input_org").prop("required", true);
                }else{
                    $("#div_organizacion_asociacion").hide();
                    $("#input_org").prop("required", false);
                    $("#input_org").prop("value", "");
                }
            });

            $("#check_actividades_otros").on("change",function(){
                if( $("#check_actividades_otros").is(":checked") ) {
                    $("#div_actividades_otros_desc").show();
                    $("#input_actividades_otros").prop("required", true);
                }else{
                    $("#div_actividades_otros_desc").hide();
                    $("#input_actividades_otros").prop("required", false);
                    $("#input_actividades_otros").prop("value", "");
                }
            });

            $("#check_apoyo_institu").on("change",function(){
                if( $("#check_apoyo_institu").is(":checked") ) {
                    $("#div_institucion_apoyo").show();
                    $("#input_institucion").prop("required", true);
                    $("#input_apoyo").prop("required", true);
                }else{
                    $("#div_institucion_apoyo").hide();
                    $("#input_institucion").prop("required", false);
                    $("#input_institucion").prop("value", "");
                    $("#input_apoyo").prop("required", false);
                    $("#input_apoyo").prop("value", "");
                }
            });
            
            
            $("#save_ficha_discapacity").on("click", function(){
                let id_ases = $("#id_ases").val();
                //Traer valores de campos para validar campos
                let val_cond_adquisicion, val_apoyo_cotidiano, otro_apoyo_cotidiano,  key_apoyo_cotidiano, key_otro_apoyo_cotidiano,text_cond_adquisicion, otra_cond_adquisicion, 
                    descripcion_diagnostico, val_tipo_disc, text_tipo_disc, otro_tipo_disc, val_transporte, otro_transporte,  key_transporte, key_otro_transporte ,
                    val_participa_asociacion, key_participa_asociacion, val_asoc, key_asoc, porcentaje_invalidez, val_act, key_act, key_realiza_act, val_realiza_act,
                    json_dif_permanente, json_cond_salud, json_necesidad, json_factores_impacto, json_posibilidad, json_apoyo_cotidiano, json_transporte, 
                    json_participa_asoc, json_actividades_otros;
               
                
                let array_dif_perm = [], array_cond_salud = [], array_necesidades = [], array_factor_impacto = [], array_posibilidades=[];

                var json_detalle = {};

                //Traer condicion de adquisicion
                val_cond_adquisicion = $("#cond_adquisicion").val();
                text_cond_adquisicion = $("#cond_adquisicion").find(":selected").text();
                
                //Validar la opción de otra condicion de adquisicion
                if(val_cond_adquisicion == 0){
                    otra_cond_adquisicion = $("#otro_cond_adquisicion").val();
                }
                //-------------------------------------------------------------------------------

                //Traer diagnostico 
                if($("#check_diagnostico").is(":checked")){
                    descripcion_diagnostico = $("#textarea_diagnostico").val(); 
                }
                
               //-------------------------------------------------------------------------------
              
               //Traer tipo de discapacidad 
                val_tipo_disc = $("#tipo_discapacidad").val();
                text_tipo_disc = $("#tipo_discapacidad").find(":selected").text();

                //Validar la opción de otro tipo de discapacidad
                if(val_tipo_disc == 0){
                    otro_tipo_disc  = $("#otra_discapacidad").val();
                }
               //-------------------------------------------------------------------------------

               //Traer certificado invalidez
               if($("#check_certificado_invalidez").is(":checked")){
                porcentaje_invalidez = $("#input_porcentaje_inv").val(); 
                 }
              

                //-------------------------------------------------------------------------------


                //Traer dificultades permanentes
                $("#div_dificultad_permanente").find(":input[type=checkbox]").each( function(){
                    
                    if($(this).is(":checked")){
                        key_func_dp    = $(this).attr("id");
                        funcion_name   = $(this).attr("title");
                        dificult_name  =$(this).parent().find(":input[type=text]").val();
                        key_dif_dp     = $(this).parent().find(":input[type=text]").attr("id");
                        json_dif_permanente = {key_funcion: key_func_dp, funcion: funcion_name, key_dificultad: key_dif_dp,dificultad: dificult_name };
                        array_dif_perm.push(json_dif_permanente);
                    }
                   
                });

                //-------------------------------------------------------------------------------

                //Traer condiciones de salud a tener en cuenta
                $("#div_cond_salud").find(":input[type=checkbox]").each( function(){
                    
                    if($(this).is(":checked")){
                        key_org_cs      = $(this).attr("id");
                        organo_name     = $(this).attr("title");
                        condicion_name  =$(this).parent().find(":input[type=text]").val();
                        key_cond_cs     = $(this).parent().find(":input[type=text]").attr("id");

                        json_cond_salud = {key_organo: key_org_cs, organo: organo_name, key_condicion: key_cond_cs , condicion: condicion_name };
                        array_cond_salud.push(json_cond_salud);
                    }
                   
                });
              
                //-------------------------------------------------------------------------------

                 //Traer necesidades situaciones
                 $("#div_necesidades").find(":input[type=checkbox]").each( function(){
                    
                    if($(this).is(":checked")){
                        key_sit_ns      = $(this).attr("id");
                        situacion_name  = $(this).attr("title");
                        necesidad_name  = $(this).parent().find(":input[type=text]").val();
                        key_nec_ns      = $(this).parent().find(":input[type=text]").attr("id");

                        json_necesidad = {key_situacion: key_sit_ns, situacion: situacion_name, key_necesidad: key_nec_ns, necesidad: necesidad_name };
                        array_necesidades.push(json_necesidad);
                    }
                   
                });
              
                //-------------------------------------------------------------------------------

                
                 

                 //Traer factores de impacto
                 $("#div_factor_impacto").find(":input[type=checkbox]").each( function(){
                    
                    if($(this).is(":checked")){
                        key_factor_fi = $(this).attr("id");
                        factor_name   = $(this).attr("title");

                        json_factores_impacto = {key_factor: key_factor_fi, escenario: factor_name};
                        if(factor_name == "Otra ¿Cuál?" || factor_name == "Otros, ¿cuáles?" ){
                            //Traer otro factor de impacto
                            otro_factor_name    = $(this).parent().find(":input[type=text]").val();
                            key_otro_factor_fi  = $(this).parent().find("input[type=text]").attr("id");
                            json_factores_impacto.key_otro_factor  = key_otro_factor_fi;
                            json_factores_impacto.otro_factor      = otro_factor_name;

                        }
                        // if(factor_name == "Características del contexto universitario"){
                        //     //Traer factores del contexto universitario
                        //     //json_factores_impacto.factor_contexto = get_caracteristicas();

                        // }
                       
                        array_factor_impacto.push(json_factores_impacto);
                    }
                   
                });
             
         
                //-------------------------------------------------------------------------------

                 //Variables de las opciones de varias respuestas
                 var key_func_dp, key_dif_dp, funcion_name, dificult_name, key_org_cs, key_cond_cs, organo_name, condicion_name,
                 key_nec_ns, key_sit_ns, key_factor_fi, factor_name, key_otro_factor_fi, otro_factor_name, key_contexto_fc, 
                 key_otro_factor_fc, factor_contexto_name, otro_factor_contexto_name, key_act_pa, actividad_name, posibilidad_name,
                 key_apo_pa, key_apo_pa, tipo_apoyo_name, otra_actividad_key, otra_actividad_name ; 

                //Traer posibilidades en actividades/condiciones
                $("#div_posibilidades_condiciones").find(":input[type=checkbox]").each( function(){
                    
                    if($(this).is(":checked")){

                        if($(this).attr("id") == "check_otra_posibilidad"){
                            key_act_pa             = $(this).attr("id");
                            actividad_name         = $(this).attr("title");
                            posibilidad_name       = $("#input_posib_otro").val();
                            key_pos_pa             = $("#input_posib_otro").attr("id");
                            key_apo_pa             = $("#input_tipo_otro").attr("id");
                            tipo_apoyo_name        = $("#input_tipo_otro").val();
                            otra_actividad_key     = $("#input_otro1").attr("id");
                            otra_actividad_name    = $("#input_otro1").val(); 
                        }else{
                            key_act_pa        = $(this).attr("id");
                            actividad_name    = $(this).attr("title");
                            posibilidad_name  = $(this).parent().find(":input[type=text]").not(".input_apoyo").val();
                            key_pos_pa        = $(this).parent().find(":input[type=text]").not(".input_apoyo").attr("id");
                            key_apo_pa        = $(this).parent().find(":input[name=input_apoyo]").attr("id");
                            tipo_apoyo_name   = $(this).parent().find(":input[name=input_apoyo]").val();
                        }
 

                        json_posibilidad = {key_actividad: key_act_pa, actividad: actividad_name, key_posibilidad: key_pos_pa, posibilidad: posibilidad_name,
                                             key_apoyo: key_apo_pa , tipo_apoyo: tipo_apoyo_name };

                        if($(this).attr("id")== "check_otra_posibilidad"){

                            json_posibilidad.key_otra_actividad = otra_actividad_key;
                            json_posibilidad.otra_actividad = otra_actividad_name;

                        }                     
                        array_posibilidades.push(json_posibilidad);
                    }
                   
                });
              
                //-------------------------------------------------------------------------------

                //Traer apoyo principal cotidiano

                val_apoyo_cotidiano         = $("#opciones_apoyo").find(":input[type=radio]:checked").val();
                key_apoyo_cotidiano         = $("#opciones_apoyo").find(":input[type=radio]:checked").attr("id");

                json_apoyo_cotidiano = {key_apoyo: key_apoyo_cotidiano, apoyo_cotidiano: val_apoyo_cotidiano};

                if(val_apoyo_cotidiano=="Otro"){
                    key_otro_apoyo_cotidiano    = $("#input_otro_apoyo").attr("id");
                    otro_apoyo_cotidiano        = $("#input_otro_apoyo").val();

                    json_apoyo_cotidiano.key_otro_apoyo = key_otro_apoyo_cotidiano;
                    json_apoyo_cotidiano.otro_apoyo     = otro_apoyo_cotidiano;
                }


                //-------------------------------------------------------------------------------

                 //Traer medio de transporte

                 val_transporte         = $("#opciones_transporte").find(":input[type=radio]:checked").val();
                 key_transporte         = $("#opciones_transporte").find(":input[type=radio]:checked").attr("id");
 
                 json_transporte = {key_transoporte: key_transporte, transporte: val_transporte};
 
                 if(val_transporte=="Otro"){
                    key_otro_transporte     = $("#input_otro_transporte").attr("id");
                     otro_transporte        = $("#input_otro_transporte").val();
 
                     json_transporte.key_otro_transporte = key_otro_transporte;
                     json_transporte.otro_transporte     = otro_transporte;
                 }
 
 
                 //-------------------------------------------------------------------------------


                //Traer participa en alguna asociacion (?)
                
                val_participa_asociacion = $("#check_org").is(":checked");
                key_participa_asociacion = $("#check_org").attr("id");
                json_participa_asoc      = {key_participa: key_participa_asociacion, participa: val_participa_asociacion};

                if(val_participa_asociacion){

                    val_asoc   = $("#input_org").val();
                    key_asoc   =  $("#input_org").attr("id");

                    json_participa_asoc.key_asociacion = key_asoc;
                    json_participa_asoc.asociacion     = val_asoc;
                }

                //-------------------------------------------------------------------------------

                  //Traer realiza actividades con otras personas con discapacidad (?)
                
                  val_realiza_act = $("#check_actividades_otros").is(":checked");
                  key_realiza_act = $("#check_actividades_otros").attr("id");
                  json_actividades_otros      = {key_realiza: key_realiza_act, realiza: val_realiza_act};
  
                  if(val_realiza_act){
  
                      val_act   = $("#input_actividades_otros").val();
                      key_act   =  $("#input_actividades_otros").attr("id");
  
                      json_actividades_otros.key_actividad = key_act;
                      json_actividades_otros.actividad     = val_act;
                  }
  
                  //-------------------------------------------------------------------------------

                console.log(json_participa_asoc);
                console.log(json_actividades_otros);
                


                //validate_form();
                  
              });



        function validate_form(){
            
        }      

        function get_caracteristicas(){
            var json_factor_contexto; 
            var array_caracteristicas = [];
            $("#div_factor_contexto").find(":input[type=checkbox]").each(function(){

                if($(this).is(":checked")){

                    key_contexto_fc      = $(this).attr("id");
                    factor_contexto_name = $(this).attr("title");
                    json_factor_contexto     = {key_contexto: key_contexto_fc, contexto: factor_contexto_name};
                    if(factor_contexto_name == "Otros, ¿cuáles?"){
                                //Traer otro factor de impacto de contexto
                                otro_factor_contexto_name  = $(this).parent().find(":input[type=text]").val();
                                key_otro_factor_contexto   = $(this).parent().find(":input[type=text]").attr("id");
                                json_factor_contexto.key_otro_factor_contexto = key_otro_factor_contexto;
                                json_factor_contexto.otro_factor_contexto     = otro_factor_contexto_name;
                                
                    }
                    array_caracteristicas.push(json_factor_contexto);
                }
                
            });
            return array_caracteristicas;
        }

        }

    };
});