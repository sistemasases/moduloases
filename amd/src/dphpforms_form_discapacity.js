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

            if($("#input_json_saved").val() != ""){
               // $("#li_view_discapacity_initial").show();
                $("#cancel_ficha_discapacity").parent().show();
            }

            $("#btn_ficha_inicial_discapacity").on("click", function(){
               
            });

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
                $(this).parent().find(":input[type=text]").prop("value","No realiza");
                
             }
             if($(this).val()==2){
                $(this).parent().find(":input[type=text]").prop("value","Sin apoyo");
            }
            if($(this).val()==3){
                $(this).parent().find(":input[type=text]").prop("value","Con apoyo");
            }
            });

            $("#check_otra_posibilidad").on("click", function(){
                if($(this).is(":checked")){
                    $("#div_otra_actividad").show();
                    $("#input_otro1").prop("required",true);
                    $("#input_otro1").prop("disabled",false);
                    $("#input_tipo_otro").prop("required",true);
                    $("#input_tipo_otro").prop("disabled",false);
                    $("#clasificacion_otro").prop("disabled", false);
                    $("#clasificacion_otro").prop("required", true);
                }else{
                    $("#div_otra_actividad").hide();
                    $("#input_otro1").prop("value","");
                    $("#input_otro1").prop("disabled",true);
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
                    $(this).parent().find(":input[type=range]").next().val("Sin apoyo");
                    $(this).parent().find(":input[name=input_apoyo]").prop("disabled", false);
                    $(this).parent().find(":input[name=input_apoyo]").prop("required", true);
                    $(this).parent().find(":input[name=input_apoyo]").val("NO REGISTRA.");

                    
                }else{
                    $(this).parent().find(":input[type=range]").prop("value", null);
                    $(this).parent().find(":input[type=range]").prop("disabled", true);
                    $(this).parent().find(":input[type=range]").prop("required", false);
                    $(this).parent().find(":input[type=range]").next().val("");
                    $(this).parent().find(":input[name=input_apoyo]").prop("disabled", true);
                    $(this).parent().find(":input[name=input_apoyo]").prop("required", false);
                    $(this).parent().find(":input[type=text]").prop("value", "");
                }     
                      
            });

            

            $("#edit_discapacity_initial").on("click", function() {


              $("#btn_ficha_inicial_discapacity").empty();
              $("#btn_ficha_inicial_discapacity").append('Ficha inicial <span class="caret"></span><strong> (Edición) </strong>');
              $("#form_ficha_inicial").show();
              enableForm();  
              let json_saved_register = $("#input_json_saved").val();
              if(json_saved_register != ""){
                json_saved_register = JSON.parse(json_saved_register);
              }
              showFormSaved(json_saved_register);
              $("html, body").animate({scrollTop:650}, 'slow'); 
              $("#form_ficha_inicial").animate({scrollTop:0}, 'slow');

              $("#save_ficha_discapacity").parent().show();
              if($("#input_json_saved").val() !== ""){
                $("#cancel_ficha_discapacity").parent().show();
              }
              

              let contenido = $("#textarea_diagnostico").val(); 
              document.getElementById("descrip_diagnostico").innerHTML = contenido;
             
            });

            $("#view_discapacity_initial").on("click", function() {
                $("#btn_ficha_inicial_discapacity").empty();
                $("#btn_ficha_inicial_discapacity").append('Ficha inicial <span class="caret"></span> <strong> (Visualización) </strong>');
                viewFormDisabled();
                $("html, body").animate({scrollTop:650}, 'slow'); 
                $("#form_ficha_inicial").animate({scrollTop:0}, 'slow');

                $("#save_ficha_discapacity").parent().hide();
                $("#cancel_ficha_discapacity").parent().hide();

                let contenido = $("#textarea_diagnostico").val(); 
                document.getElementById("descrip_diagnostico").innerHTML =  contenido;

              });

            
            $("#cond_adquisicion").on("click", function(){
                let resp_adquis = $(this).val();
                if(resp_adquis.includes("0") ){
                    $("#div_otro_cond_adq").show();
                    $("#otro_cond_adquisicion").prop("required", true);
                    enabledInput("div_otro_cond_adq");
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
                    enabledInput("div_otro_apoyo_principal");
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
                    enabledInput("div_otro_transporte");
                }else{
                    $("#div_otro_transporte").hide();
                    $("#input_otro_transporte").prop("required", false);
                    $("#input_otro_transporte").prop("value", "");
                }
            });

            $("#tipo_discapacidad").on("click", function(){
                let resp_tipo = $(this).val();
                if(resp_tipo.includes("0")){
                    $("#div_otra_discapacidad").show();
                    $("#otra_discapacidad").prop("required", true);
                    enabledInput("div_otra_discapacidad");
                }else {
                    $("#div_otra_discapacidad").hide();
                    $("#otra_discapacidad").prop("required", false);
                    $("#otra_discapacidad").prop("value", "");
                }
            });
        
            $("#check_diagnostico").on("change",function(){
                if( $("#check_diagnostico").is(":checked") ) {
                    $("#div_descripcion_diagnostico").show();
                    $("#textarea_diagnostico").prop("required", true);
                }else{
                    $("#div_descripcion_diagnostico").hide();
                    $("#textarea_diagnostico").prop("value", "");
                    $("#textarea_diagnostico").prop("required", false);
                }
            });

            $("#textarea_diagnostico").on("change", function(){
                let contenido = $("#textarea_diagnostico").val(); 
                document.getElementById("descrip_diagnostico").innerHTML =  contenido;
             
            });

            $("#check_certificado_invalidez").on("change",function(){
                if( $("#check_certificado_invalidez").is(":checked") ) {
                    $("#div_porcentaje_inv").show();
                    $("#input_porcentaje_inv").prop("required", true);
                    enabledInput("div_porcentaje_inv");
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
                    enabledInput("div_organizacion_asociacion");
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
                    enabledInput("div_actividades_otros_desc");
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
                    enabledInput("div_institucion_apoyo");
                }else{
                    $("#div_institucion_apoyo").hide();
                    $("#input_institucion").prop("required", false);
                    $("#input_institucion").prop("value", "");
                    $("#input_apoyo").prop("required", false);
                    $("#input_apoyo").prop("value", "");
                }
            });
            
            $("#cancel_ficha_discapacity").on("click", function(){

                swal({
                    title: "Advertencia",
                    text: "¿Está seguro(a) que desea revertir los cambios?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                },
                function(isConfirm){
       
                    if (isConfirm) {
                        viewFormDisabled();
                        $("html, body").animate({scrollTop:650}, 'slow'); 
                        $("#form_ficha_inicial").animate({scrollTop:0}, 'slow');
                        $("#btn_ficha_inicial_discapacity").empty();
                        $("#btn_ficha_inicial_discapacity").append('Ficha inicial <span class="caret"></span> <strong> (Visualización) </strong>');
                        $("#save_ficha_discapacity").parent().hide();
                        $("#cancel_ficha_discapacity").parent().hide();
                        
                    } 
                });

                

            });
            
            $("#save_ficha_discapacity").on("click", function(){

                //Traer valores de campos para validar campos
                let val_cond_adquisicion, val_apoyo_cotidiano, otro_apoyo_cotidiano,  key_apoyo_cotidiano, key_otro_apoyo_cotidiano, otra_cond_adquisicion, 
                    descripcion_diagnostico, val_tipo_disc, otro_tipo_disc, val_transporte, otro_transporte,  key_transporte, key_otro_transporte ,key_otra_condicion_adq,
                    val_participa_asociacion, key_participa_asociacion, val_asoc, key_asoc, porcentaje_invalidez, val_act, key_act, key_realiza_act, val_realiza_act, key_descripcion, check_diagnostico,
                    key_check_diagnostico, json_diagnostico,  key_tipo_disc, key_otra_disc,json_tipo_disc,  check_certificado, key_check_certificado, json_certificado, key_porcentaje_inv,
                    val_apoyo_institu, key_apoyo_institu, val_apoyo_tipo, key_apoyo_tipo, val_institu, key_institu, key_cond_adquisicion, json_condicion_adq, json_apoyo_institu,
                    json_dif_permanente, json_cond_salud, json_necesidad, json_factores_impacto, json_posibilidad, json_apoyo_cotidiano, json_transporte, 
                    json_participa_asoc, json_actividades_otrosm, json_percepcion, key_percepcion_discapacidad = "", descripcion_percepcion;
               
                    //Variables de las opciones de varias respuestas
                    var key_func_dp, key_dif_dp, funcion_name, dificult_name, key_org_cs, key_cond_cs, organo_name, condicion_name,
                    key_nec_ns, key_sit_ns, key_factor_fi, factor_name, key_otro_factor_fi, otro_factor_name, key_contexto_fc, 
                    key_otro_factor_fc, factor_contexto_name, otro_factor_contexto_name, key_act_pa, actividad_name, posibilidad_name,
                    key_apo_pa, key_apo_pa, tipo_apoyo_name, otra_actividad_key, otra_actividad_name; 

                    let array_dif_perm = [], array_cond_salud = [], array_necesidades = [], array_factor_impacto = [], array_posibilidades=[];

                    var json_detalle_discapacidad;

                //Traer percepci+on de discapacidad
                key_percepcion_discapacidad = $("#div_percepcion_estudiante").find(":input[type=radio]:checked").attr("id");
                descripcion_percepcion      = $("#textarea_percepcion").val(); 

                json_percepcion = {key_percepcion: key_percepcion_discapacidad, descripcion: descripcion_percepcion};

                //Traer condicion de adquisicion
                val_cond_adquisicion  = $("#cond_adquisicion").val();
                key_cond_adquisicion  = $("#cond_adquisicion").attr("id");
                let text_cond_adquisicion = [];
                $("#cond_adquisicion").find(":selected").each(function() {
                    $(this).css("background","lightslategrey" );
                    text_cond_adquisicion.push( $(this).text());
                });
                $("#cond_adquisicion option:not(:selected)").each(function() {
                    $(this).css("background","white" );
                });

                json_condicion_adq = {key_condicion: key_cond_adquisicion, condicion: text_cond_adquisicion};
                
                //Validar la opción de otra condicion de adquisicion
                if(val_cond_adquisicion.includes("0")){
                    key_otra_condicion_adq = $("#otro_cond_adquisicion").attr("id");
                    otra_cond_adquisicion  = $("#otro_cond_adquisicion").val();
                    json_condicion_adq.key_otra_condicion = key_otra_condicion_adq;
                    json_condicion_adq.otra_condicion     = otra_cond_adquisicion;
                }

                //-------------------------------------------------------------------------------

                       //-------------------------------------------------------------------------------

               //Traer tipo de discapacidad 
               val_tipo_disc   = $("#tipo_discapacidad").val();
               key_tipo_disc   = $("#tipo_discapacidad").attr("id");

               let text_tipo_disc = [];
               $("#tipo_discapacidad").find(":selected").each(function() {
                   $(this).css("background","lightslategrey" );
                   text_tipo_disc.push( $(this).text());
               });
               $("#tipo_discapacidad option:not(:selected)").each(function() {
                   $(this).css("background","white" );
               });

               json_tipo_disc  ={key_tipo: key_tipo_disc, tipo_discapacidad: text_tipo_disc};

               //Validar la opción de otro tipo de discapacidad
               if(val_tipo_disc.includes("0")){
                   otro_tipo_disc  = $("#otra_discapacidad").val();
                   key_otra_disc   = $("#otra_discapacidad").attr("id");

                   json_tipo_disc.key_otro_tipo   =  key_otra_disc;
                   json_tipo_disc.otro_tipo       =  otro_tipo_disc;
               }


                //Traer diagnostico 
                check_diagnostico     =  $("#check_diagnostico").is(":checked");
                key_check_diagnostico =  $("#check_diagnostico").attr("id");
                json_diagnostico      =  {key_diagnostico: key_check_diagnostico};
                if(check_diagnostico){
                    descripcion_diagnostico          = $("#textarea_diagnostico").val(); 
                    key_descripcion                  = $("#textarea_diagnostico").attr("id"); 
                    json_diagnostico.tiene_diagnostico = 1; 
                    json_diagnostico.key_descripcion = key_descripcion;
                    json_diagnostico.descripcion     = descripcion_diagnostico;
                }else {
                    json_diagnostico.tiene_diagnostico = 0; 
                }

                
               //-------------------------------------------------------------------------------
               //Traer certificado invalidez
               check_certificado     =  $("#check_certificado_invalidez").is(":checked");
               key_check_certificado =  $("#check_certificado_invalidez").attr("id");
               json_certificado      =  {key_certificado: key_check_certificado};
               if(check_certificado){
                key_porcentaje_inv                 = $("#input_porcentaje_inv").attr("id"); 
                porcentaje_invalidez               = parseInt($("#input_porcentaje_inv").val()); 
                json_certificado.tiene_certificado = 1;
                json_certificado.key_porcentaje    = key_porcentaje_inv;
                json_certificado.porcentaje        = porcentaje_invalidez;
                 }else {
                    json_certificado.tiene_certificado = 0; 
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
                json_participa_asoc      = {key_participa: key_participa_asociacion};

                if(val_participa_asociacion){

                    val_asoc   = $("#input_org").val();
                    key_asoc   =  $("#input_org").attr("id");
                    json_participa_asoc.participa = 1;
                    json_participa_asoc.key_asociacion = key_asoc;
                    json_participa_asoc.asociacion     = val_asoc;
                }else {
                    json_participa_asoc.participa = 0;
                }

                //-------------------------------------------------------------------------------

                  //Traer realiza actividades con otras personas con discapacidad (?)
                
                  val_realiza_act = $("#check_actividades_otros").is(":checked");
                  key_realiza_act = $("#check_actividades_otros").attr("id");
                  json_actividades_otros      = {key_realiza: key_realiza_act};
  
                  if(val_realiza_act){
  
                      val_act   = $("#input_actividades_otros").val();
                      key_act   =  $("#input_actividades_otros").attr("id");
  
                      json_actividades_otros.realiza = 1;
                      json_actividades_otros.key_actividad = key_act;
                      json_actividades_otros.actividad     = val_act;
                  }else{
                      json_actividades_otros.realiza = 0;
                  }
  
                  //-------------------------------------------------------------------------------
                  

                  //Traer institucion que apoya (?)
                
                  val_apoyo_institu = $("#check_apoyo_institu").is(":checked");
                  key_apoyo_institu = $("#check_apoyo_institu").attr("id");
                  json_apoyo_institu     = {key_apoya: key_apoyo_institu};
  
                  if(val_apoyo_institu){
  
                      val_apoyo_tipo   = $("#input_apoyo").val();
                      key_apoyo_tipo  =  $("#input_apoyo").attr("id");
                      
                      json_apoyo_institu.apoyo = 1;
                      json_apoyo_institu.key_apoyo_institu = key_apoyo_tipo;
                      json_apoyo_institu.apoyo_institu     = val_apoyo_tipo;

                      val_institu   = $("#input_institucion").val();
                      key_institu  =  $("#input_institucion").attr("id");
  
                      json_apoyo_institu.key_institucion = key_institu;
                      json_apoyo_institu.institucion     = val_institu;
                  }else {
                    json_apoyo_institu.apoyo = 0;
                  }
  
                  //-------------------------------------------------------------------------------


                                
                //Crear objeto JSON que representa el detalle de discapacidad
                        
                json_detalle_discapacidad = 
                {   
                    percepcion_discapacidad:            json_percepcion,
                    condicion_adquisicion:              json_condicion_adq ,
                    diagnostico_discapacidad:           json_diagnostico,
                    tipo_discapacidad:                  json_tipo_disc,
                    certificado_invalidez:              json_certificado,
                    dificultad_permanente_funciones:    array_dif_perm,
                    condicion_salud_organos:            array_cond_salud,
                    necesidades_situaciones:            array_necesidades,
                    posibilidad_actividades:            array_posibilidades,
                    factores_impacto:                   array_factor_impacto,
                    apoyo_principal_cotidiano:          json_apoyo_cotidiano,
                    medio_transporte:                   json_transporte,
                    participa_asociacion:               json_participa_asoc,
                    actividades_otros:                  json_actividades_otros, 
                    apoyo_institucion:                  json_apoyo_institu
                }
                ;

                //Validar las respuestas obtenidas
                var result_validation =  validate_form(json_detalle_discapacidad);

                //console.log(result_validation);

                if(result_validation.status == "error"){
                    swal(result_validation.title,
                        result_validation.msg,
                        result_validation.status);
                }else{
                var obj = json_detalle_discapacidad;
                obj = JSON.stringify(obj);
                        
                let id_ases = $("#id_ases").val();
                validate_json (obj, id_ases);
                
                }

                //console.log(json_detalle_discapacidad);
                
                  
              });


        function validate_json(json_data, ases_id){
            let json_prev = $("#input_json_saved").val();
            $.ajax({
                type: "POST",
                data: {
                    func: 'validate_json',
                    json: json_data, 
                    json_prev: json_prev,
                    ases: ases_id,
                    instanceid: getIdinstancia(),
                    courseid: getIdcourse(),
                    id_schema: 1
                },
                url: "../managers/student_profile/others_tab_api.php",
                success: function(msg) {
        
                    swal(
                       { title: msg.title,
                        text: msg.msg,
                        type: msg.status
                       },
                       function(){
                        $("html, body").animate({scrollTop:650}, 'slow'); 
                        $("#form_ficha_inicial").animate({scrollTop:0}, 'slow');
                       }
                    

                    );


                    $("#input_json_saved").attr("value", json_data);

                   
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

        function viewFormDisabled(){
                  
            $("#form_ficha_inicial").show();
            cleanAllForm();
            disabledTag('input');
            disabledTag('select');
            disabledTag('textarea');
            
            let json_saved_register = $("#input_json_saved").val();
            json_saved_register = JSON.parse(json_saved_register);
            
            showFormSaved(json_saved_register);
          }

          function disabledTag(tag){
            //Disabled inputs 
            obj = document.getElementById('form_ficha_inicial');
            for (i=0; ele = obj.getElementsByTagName(tag)[i]; i++){
                ele.disabled = true;
            }
          }

        function cleanAllForm(){

            $("#cond_adquisicion option").each(function() {
                $(this).css("background","white" );
                $(this).prop("selected", false);
            });

            obj = document.getElementById('form_ficha_inicial');
            for (i=0; ele = obj.getElementsByTagName('input')[i]; i++){
                if(ele.id != "input_json_saved"){
                    if(ele.type == "checkbox"){
                        ele.checked = false;
                    }
                    if(ele.type == "radio"){
                        ele.checked = false;
                    }
                    if(ele.type == "text"){
                        ele.value = "";
                    }
                    if(ele.type == "range"){
                        ele.value = 2;
                    }
                    if(ele.type == "number"){
                        ele.value = 0;
                    }
                
            }
        }

        $("#div_institucion_apoyo").hide();
        $("#div_actividades_otros_desc").hide();
        $("#div_organizacion_asociacion").hide();
        $("#div_otro_transporte").hide();
        $("#div_otro_apoyo_principal").hide();
        $("#div_porcentaje_inv").hide();
        $("#div_otra_discapacidad").hide();
        $("#div_descripcion_diagnostico").hide();
        $("#div_otro_cond_adq").hide();
        
        
    }

        function validate_form(json_detalle){

            var msg = new Object();

            msg.title = "Éxito";
            msg.msg = "El formulario fue validado con éxito";
            msg.status = "success";

            for(i in json_detalle){
                switch(i){
                    case "percepcion_discapacidad":
                    let obj_percepcion= json_detalle[i];
                    for(op in obj_percepcion){
                        if(op == "key_percepcion"){
                         if(obj_percepcion[op] === undefined){

                             msg.title = "Percepción de discapacidad";
                             msg.status = "error";
                             msg.msg = "El campo "+op+" de adquisición es obligatorio";
                             return msg;  
                         }

                        }
                     }

                    break;
                    case "condicion_adquisicion":
                    let obj_cond_adq = json_detalle[i];
                        for(op in obj_cond_adq){
                           if(op == "otra_condicion"){
                            if(obj_cond_adq[op]== ""){

                                msg.title = "Condición de adquisición";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" de adquisición es obligatorio";
                                return msg;  
                            }
                            if(has_numbers(obj_cond_adq[op])){

                                msg.title = "Condición de adquisición";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" de adquisición no debe contener números";
                                return msg;  
                            }

                           }
                        }
                    break;

                    case "diagnostico_discapacidad": 

                    let obj_diagnostico = json_detalle[i];
                    for(op in obj_diagnostico){
                       if(op == "descripcion"){
                        if(obj_diagnostico[op]== ""){

                            msg.title = "Diagnóstico de discapacidad";
                            msg.status = "error";
                            msg.msg = "El campo "+op+" diagnóstico es obligatorio";
                            return msg;  
                        }

                       }
                    }
                    break;

                    case "tipo_discapacidad" :

                    let obj_tipo_disc = json_detalle[i];
                        for(op in obj_tipo_disc){
                           if(op == "otro_tipo"){
                            if(obj_tipo_disc[op]== ""){

                                msg.title = "Tipo de discapacidad";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" de discapacidad es obligatorio";
                                return msg;  
                            }
                            if(has_numbers(obj_tipo_disc[op])){

                                msg.title = "Tipo de discapacidad";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" de discapacidad no debe contener números";
                                return msg;  
                            }

                           }
                        }

                    break;

                    case "certificado_invalidez" :

                    let obj_cert= json_detalle[i];
                        for(op in obj_cert){
                           if(op == "porcentaje"){
                            if(obj_cert[op]== ""){

                                msg.title = "Certificado de invalidez";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" es obligatorio";
                                return msg;  
                            }
                            if(obj_cert[op] < 0){

                                msg.title = "Certificado de invalidez";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" no debe ser negativo";
                                return msg;  
                            }
                            if(Number.isNaN(obj_cert[op])){

                                msg.title = "Certificado de invalidez";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" debe ser numérico";
                                return msg;  
                            }

                           }
                        }

                    break;

                    case "dificultad_permanente_funciones": 

                    let array_dif = json_detalle[i], obj_dif;

                    for(item in array_dif){
                        //Recorre arreglo de dificultades permanentes para validar cada una
                        obj_dif= array_dif[item];
                        for(op in obj_dif){
                        if(op == "dificultad"){
                            if(obj_dif[op]== ""){

                                msg.title = "Dificultades permanentes";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" de la opción '"+ obj_dif["funcion"] +"' es obligatorio";
                                return msg;  
                            }

                        }
                        }
                    }    
                     
                    break;

                    case "condicion_salud_organos": 

                    let array_cond = json_detalle[i], obj_cond;

                    for(item in array_cond){
                        //Recorre arreglo de condiciones de salud para validar cada una
                        obj_cond= array_cond[item];
                        for(op in obj_cond){
                        if(op == "condicion"){
                            if(obj_cond[op]== ""){

                                msg.title = "Condiciones de salud";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" de la opción '"+ obj_cond["organo"] +"' es obligatorio";
                                return msg;  
                            }
                 
                        }
                        }
                    }    
                     
                    break;

                    case "necesidades_situaciones": 

                    let array_nec = json_detalle[i], obj_nec;

                    for(item in array_nec){
                        //Recorre arreglo de condiciones de salud para validar cada una
                        obj_nec= array_nec[item];
                        for(op in obj_nec){
                        if(op == "necesidad"){
                            if(obj_nec[op]== ""){

                                msg.title = "Necesidades situaciones";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" de la opción '"+ obj_nec["situacion"] +"' es obligatorio";
                                return msg;  
                            }
                   
                        }
                        }
                    }    
                     
                    break;

                    case "factores_impacto":

                    let array_fact = json_detalle[i], obj_fact;

                    for(item in array_fact){
                      //Recorre arreglo de factores de impacto para validar cada una
                      obj_fact= array_fact[item];
                      for(op in obj_fact){
                      if(op == "otro_factor"){
                          if(obj_fact[op]== ""){

                              msg.title = "Factores de impacto";
                              msg.status = "error";
                              msg.msg = "El campo "+op+" de la opción '"+ obj_fact["escenario"] +"' es obligatorio";
                              return msg;  
                          }

                      }
                      }
                  }  
                    break;

                    case "posibilidad_actividades":

                    let array_pos = json_detalle[i], obj_pos;

                    for(item in array_pos){
                      //Recorre arreglo de posibilidades en actividades para validar cada una
                      obj_pos = array_pos[item];
                      for(op in obj_pos){
                      if( op == "otra_actividad" || op == "posibilidad" || op == "tipo_apoyo"   ){
                          if(obj_pos[op]== ""){

                              msg.title = "Posibilidades en actividades";
                              msg.status = "error";
                              msg.msg = "El campo "+op+" de la opción '"+ obj_pos["actividad"] +"' es obligatorio";
                              return msg;  
                          }
           
                      }
                      }
                    }
                    break;


                    case "apoyo_principal_cotidiano":

                    let obj_apoyo= json_detalle[i];
                    if(JSON.stringify(obj_apoyo)=='{}'){
                        msg.title = "Apoyo principal";
                        msg.status = "error";
                        msg.msg = "El campo apoyo principal es obligatorio";
                        return msg;  
                    }else{
                        for(op in obj_apoyo){
                            if(op == "otro_apoyo"){
                             if(obj_apoyo[op]== ""){
     
                                 msg.title = "Apoyo principal";
                                 msg.status = "error";
                                 msg.msg = "El campo "+op+" de la opción '"+ obj_apoyo["apoyo_cotidiano"]+"' es obligatorio";
                                 return msg;  
                             }
                      
                            }
                         }
     
                    }
                   
                    break;
                    
                    case "medio_transporte":

                    let obj_transporte= json_detalle[i];
                    if(JSON.stringify(obj_transporte)=='{}'){
                        msg.title = "Medio de transporte";
                        msg.status = "error";
                        msg.msg = "El campo medio de transporte es obligatorio";
                        return msg;  
                    }else{
                    for(op in obj_transporte){
                       if(op == "otro_transporte"){
                        if(obj_transporte[op]== ""){

                            msg.title = "Medio de transporte";
                            msg.status = "error";
                            msg.msg = "El campo "+op+" de la opción '"+ obj_transporte["transporte"]+"' es obligatorio";
                            return msg;  
                        }

                       }
                    }
                }
                    break;

                    case "participa_asociacion":

                    let obj_asoc= json_detalle[i];
                    for(op in obj_asoc){
                        if(op == "asociacion"){
                         if(obj_asoc[op]== ""){
 
                             msg.title = "Participación en organización/asociación";
                             msg.status = "error";
                             msg.msg = "El campo "+op+" es obligatorio";
                             return msg;  
                         }
                    
                        }
                     }
                    break;
                    
                    case "actividades_otros":

                    let obj_act= json_detalle[i];
                    for(op in obj_act){
                        if(op == "actividad"){
                         if(obj_act[op]== ""){
 
                             msg.title = "Actividades con otros";
                             msg.status = "error";
                             msg.msg = "El campo "+op+" es obligatorio";
                             return msg;  
                         }
                        }
                     }

                    break;

                    case "apoyo_institucion":

                    let obj_inst= json_detalle[i];
                    for(op in obj_inst){
                        if(op == "apoyo_institu" || op == "institucion" ){
                         if(obj_inst[op]== ""){
 
                             msg.title = "Apoyo institución";
                             msg.status = "error";
                             msg.msg = "El campo "+op+" es obligatorio";
                             return msg;  
                         }
                        }
                     }
                    break;
                }
            }

            return msg;
        } 
        
        function enabledInput(element){
           $("#"+element).find("input").each( function(){
                    $(this).prop("disabled", false);
                });  
        }

        function enableForm(){
            $("#form_ficha_inicial").find(":input").not(".input_fields_discapacity_tab").not(".range_input").each( function(){
            if($(this).attr("id") != "check_documentos_soporte"){
                $(this).prop("disabled", false);
            }
           
            });  
        }

        function showFormSaved(json_bd){
            let array, key, val, name,  key_otro, val_otro, key_input, val_input, range;

            for(i in json_bd){

                switch(i){

                    case "percepcion_discapacidad":
                    key        = json_bd[i]['key_percepcion'];        
                    val_input       = json_bd[i]['descripcion'];
                    key_input       = "textarea_percepcion";


                    if(key != ""){
                        $("#"+key).prop("checked", true);
                        $("#"+key_input).val(val_input);
                    }
                   

                    break;

                    case "condicion_adquisicion":
                    
                    key        = json_bd[i]['key_condicion'];        
                    name       = json_bd[i]['condicion'];
                    key_otro   = json_bd[i]['key_otra_condicion'];   
                    val_otro   = json_bd[i]['otra_condicion'];
                    if(Array.isArray(name)){
                        for (condicion in name){
                            setOptionSelect(key,name[condicion] , key_otro, val_otro);
                        }
                          
                    }else{
                            setOptionSelect(key, name, key_otro, val_otro);
                    }
                    $("#cond_adquisicion").find(":selected").each(function() {
                        $(this).css("background","lightslategrey" );
                    });
                    
                    
                    
                    break;

                    case "diagnostico_discapacidad":
                    
                    key         = json_bd[i]['key_diagnostico'];          
                    val         = json_bd[i]['tiene_diagnostico'];
                    key_input   = json_bd[i]['key_descripcion'];          
                    val_input   = json_bd[i]['descripcion'];
                    showOption(key, val, key_input, val_input);
                    
                    break;

                    case "tipo_discapacidad":
                    
                    key        = json_bd[i]['key_tipo'];          
                    name       = json_bd[i]['tipo_discapacidad'];
                    key_otro   = json_bd[i]['key_otro_tipo'];     
                    val_otro   = json_bd[i]['otro_tipo'];

                    if(Array.isArray(name)){
                        for (condicion in name){
                            setOptionSelect(key,name[condicion] , key_otro, val_otro);
                        }
                          
                    }else{
                            setOptionSelect(key, name, key_otro, val_otro);
                    }
                    $("#cond_adquisicion").find(":selected").each(function() {
                        $(this).css("background","lightslategrey" );
                    });
                    
                    
                    break;

                    case "certificado_invalidez":
                    
                    key         = json_bd[i]['key_certificado'];          
                    val         = json_bd[i]['tiene_certificado'];
                    key_input   = json_bd[i]['key_porcentaje'];           
                    val_input   = json_bd[i]['porcentaje'];

                    showOption(key, val, key_input, val_input);
                    
                    break;

                    case "dificultad_permanente_funciones":
                    
                    array = json_bd[i];
                    showOptionsMult(array, 'key_funcion', 'funcion', 'key_dificultad', 'dificultad');
                    
                    break;
                    case "condicion_salud_organos":
                    
                    array = json_bd[i];
                    showOptionsMult(array, 'key_organo', 'organo', 'key_condicion', 'condicion');
                    
                    break;
                    case "necesidades_situaciones":
                    
                    array = json_bd[i];
                    showOptionsMult(array, 'key_situacion', 'situacion', 'key_necesidad', 'necesidad');
                    
                    break;
                    case "posibilidad_actividades":

                    array = json_bd[i];

                    for(obj_json in array){

                        if(array[obj_json]['key_actividad'] == "check_otra_posibilidad"){
                            //Desplegar la opcion otro
                            $("#"+array[obj_json]['key_otra_actividad']).val(array[obj_json]['otra_actividad']);
                            $("#"+array[obj_json]['key_otra_actividad']).parent().show();
                        }

                        $("#"+array[obj_json]['key_actividad']).prop("checked", true);
                        $("#"+array[obj_json]['key_posibilidad']).val(array[obj_json]['posibilidad']);
                        $("#"+array[obj_json]['key_apoyo']).val(array[obj_json]['tipo_apoyo']);
                        
                        if(array[obj_json]['posibilidad'] == "No realiza") {
                            range = 1;
                        }else 
                        {
                            if(array[obj_json]['posibilidad'] == "Sin apoyo") {
                            range = 2;
                            } else
                                {
                                    if(array[obj_json]['posibilidad'] == "Con apoyo") {
                                        range = 3;
                                        } 
                                }
                        }
                        
                        $("#"+array[obj_json]['key_posibilidad']).siblings(":input[type=range]").val(range);
                        
                    }

                    break;
                    case "factores_impacto":
                    
                    array = json_bd[i];
                    for(obj_json in array){

                        if(array[obj_json]['key_factor'] == "check_factor4" || array[obj_json]['key_factor'] == "check_factor2_7" ){
                            //Desplegar la opcion otro
                            $("#"+array[obj_json]['key_otro_factor']).val(array[obj_json]['otro_factor']);
                            
                        }
                        if(array[obj_json]['key_factor'] == "check_factor2"){
                            //Desplegar div que contiene opcionwes de contexto
                            $("#"+array[obj_json]['key_factor']).parent().next().show();
                        }

                        $("#"+array[obj_json]['key_factor']).prop("checked", true);

                    }

                    break;
                    case "apoyo_principal_cotidiano":

                    key         = json_bd[i]['key_apoyo'];          
                    val         = json_bd[i]['apoyo_cotidiano'];
                    key_input   = json_bd[i]['key_otro_apoyo'];           
                    val_input   = json_bd[i]['otro_apoyo'];

                    $("#"+key).prop("checked", true);

                    if(key == "input_radio_otro_oa"){

                        $("#"+key_input).val(val_input);
                        $("#"+key_input).parent().show();
                    }else{

                        $("#"+key_input).val("");
                        $("#"+key_input).parent().hide();
                    }

                    break;
                    case "medio_transporte":

                    key         = json_bd[i]['key_transoporte'];          
                    val         = json_bd[i]['transporte'];
                    key_input   = json_bd[i]['key_otro_transporte'];           
                    val_input   = json_bd[i]['otro_transporte'];

                    $("#"+key).prop("checked", true);

                    if(key == "input_radio_otro_ot"){

                        $("#"+key_input).val(val_input);
                        $("#"+key_input).parent().show();
                    }else{

                        $("#"+key_input).val("");
                        $("#"+key_input).parent().hide();
                    }

                    break;
                    case "participa_asociacion":

                    key         = json_bd[i]['key_participa'];          
                    val         = json_bd[i]['participa'];
                    key_input   = json_bd[i]['key_asociacion'];           
                    val_input   = json_bd[i]['asociacion'];

                    showOption(key, val, key_input, val_input);

                    break;
                    case "actividades_otros":

                    key         = json_bd[i]['key_realiza'];          
                    val         = json_bd[i]['realiza'];
                    key_input   = json_bd[i]['key_actividad'];           
                    val_input   = json_bd[i]['actividad'];

                    showOption(key, val, key_input, val_input);

                    break;
                    case "apoyo_institucion":

                    key         = json_bd[i]['key_apoya'];          
                    val         = json_bd[i]['apoyo'];
                    key_input   = json_bd[i]['key_apoyo_institu'];           
                    val_input   = json_bd[i]['apoyo_institu'];
                   

                    if(val ==1 ){
                        //Fue seleccionada la opción
                        $("#"+key).prop("checked", true);
                        $("#"+key_input).val(val_input);

                        key_input   = json_bd[i]['key_institucion'];           
                        val_input   = json_bd[i]['institucion'];

                        $("#"+key_input).val(val_input);

                        $("#"+key_input).parent().parent().show();
                    }else {

                    //No fue seleccionada la opción
                    $("#"+key).prop("checked", false);
                    $("#"+key_input).val("");
                    $("#"+key_input).parent().parent().hide();

                    }

                    break;
                }

            }
        }

        function setOptionSelect(key, name, key_otro, val_otro){
            let val;
            $("#"+ key+" option").each(function(){
                if($(this).text() == name){

                 val    =    $(this).attr('value');
                // $("#"+key).val(val);
                // $(this).css("background", "grey");
                $(this).prop("selected", true);
                if(val == "0"){
                    //Opción Otra condicion adquisición
                    $("#"+key_otro).val(val_otro);
                    $("#"+key_otro).parent().show();
                }
                } 
             });
       }

        function showOption(key,val, key_input, val_input){
                
                if(val == 1){
                    //Fue seleccionada la opción
                    $("#"+key).prop("checked", true);
                    $("#"+key_input).val(val_input);
                    $("#"+key_input).parent().show();

                }else{
                    //No fue seleccionada la opción
                    $("#"+key).prop("checked", false);
                    $("#"+key_input).val("");
                    $("#"+key_input).parent().hide();
                }
            
        }

        function showOptionsMult(array, key_select, text_select, key_input, val_input){

            
            for(obj_json in array){
                $("#"+array[obj_json][key_select]).prop("checked", true);
                $("#"+array[obj_json][key_input]).val( array[obj_json][val_input]);
            }

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

        function getIdinstancia() {
            var urlParameters = location.search.split('&');

            for (x in urlParameters) {
                if (urlParameters[x].indexOf('instanceid') >= 0) {
                    var intanceparameter = urlParameters[x].split('=');
                    return intanceparameter[1];
                }
            }
            return 0;
        }

        function getIdcourse() {
            var urlParameters = location.search.split('&');

            for (x in urlParameters) {
                if (urlParameters[x].indexOf('courseid') >= 0) {
                    var intanceparameter = urlParameters[x].split('=');
                    return intanceparameter[1];
                }
            }
            return 0;
        }

        }

    };
});