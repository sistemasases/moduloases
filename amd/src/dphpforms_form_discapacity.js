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
                    $("#textarea_diagnostico").prop("required", true);
                }else{
                    $("#div_descripcion_diagnostico").hide();
                    $("#textarea_diagnostico").prop("value", "");
                    $("#textarea_diagnostico").prop("required", false);
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
                    descripcion_diagnostico, val_tipo_disc, text_tipo_disc, otro_tipo_disc, val_transporte, otro_transporte,  key_transporte, key_otro_transporte ,key_otra_condicion_adq,
                    val_participa_asociacion, key_participa_asociacion, val_asoc, key_asoc, porcentaje_invalidez, val_act, key_act, key_realiza_act, val_realiza_act, key_descripcion, check_diagnostico,
                    key_check_diagnostico, json_diagnostico,  key_tipo_disc, key_otra_disc,json_tipo_disc,  check_certificado, key_check_certificado, json_certificado, key_porcentaje_inv,
                    val_apoyo_institu, key_apoyo_institu, val_apoyo_tipo, key_apoyo_tipo, val_institu, key_institu, key_cond_adquisicion, json_condicion_adq, json_apoyo_institu,
                    json_dif_permanente, json_cond_salud, json_necesidad, json_factores_impacto, json_posibilidad, json_apoyo_cotidiano, json_transporte, 
                    json_participa_asoc, json_actividades_otros;
               
                    //Variables de las opciones de varias respuestas
                    var key_func_dp, key_dif_dp, funcion_name, dificult_name, key_org_cs, key_cond_cs, organo_name, condicion_name,
                    key_nec_ns, key_sit_ns, key_factor_fi, factor_name, key_otro_factor_fi, otro_factor_name, key_contexto_fc, 
                    key_otro_factor_fc, factor_contexto_name, otro_factor_contexto_name, key_act_pa, actividad_name, posibilidad_name,
                    key_apo_pa, key_apo_pa, tipo_apoyo_name, otra_actividad_key, otra_actividad_name ; 

                    let array_dif_perm = [], array_cond_salud = [], array_necesidades = [], array_factor_impacto = [], array_posibilidades=[];

                    var json_detalle_discapacidad;

                

                //Traer condicion de adquisicion
                val_cond_adquisicion  = $("#cond_adquisicion").val();
                key_cond_adquisicion  = $("#cond_adquisicion").attr("id");
                text_cond_adquisicion = $("#cond_adquisicion").find(":selected").text();

                json_condicion_adq = {key_condicion: key_cond_adquisicion, condicion: text_cond_adquisicion};
                
                //Validar la opción de otra condicion de adquisicion
                if(val_cond_adquisicion == 0){
                    key_otra_condicion_adq = $("#otro_cond_adquisicion").attr("id");
                    otra_cond_adquisicion  = $("#otro_cond_adquisicion").val();
                    json_condicion_adq.key_otra_condicion = key_otra_condicion_adq;
                    json_condicion_adq.otra_condicion     = otra_cond_adquisicion;
                }
                //-------------------------------------------------------------------------------

                //Traer diagnostico 
                check_diagnostico     =  $("#check_diagnostico").is(":checked");
                key_check_diagnostico =  $("#check_diagnostico").attr("id");
                json_diagnostico      =  {key_diagnostico: key_check_diagnostico, tiene_diagnostico: check_diagnostico};
                if(check_diagnostico){
                    descripcion_diagnostico          = $("#textarea_diagnostico").val(); 
                    key_descripcion                  = $("#textarea_diagnostico").attr("id"); 

                    json_diagnostico.key_descripcion = key_descripcion;
                    json_diagnostico.descripcion     = descripcion_diagnostico;
                }
                
               //-------------------------------------------------------------------------------

               //Traer tipo de discapacidad 
                val_tipo_disc   = $("#tipo_discapacidad").val();
                key_tipo_disc   = $("#tipo_discapacidad").attr("id");
                text_tipo_disc  = $("#tipo_discapacidad").find(":selected").text();

                json_tipo_disc  ={key_tipo: key_tipo_disc, tipo_discapacidad: text_tipo_disc};

                //Validar la opción de otro tipo de discapacidad
                if(val_tipo_disc == 0){
                    otro_tipo_disc  = $("#otra_discapacidad").val();
                    key_otra_disc   = $("#otra_discapacidad").attr("id");

                    json_tipo_disc.key_otro_tipo   =  key_otra_disc;
                    json_tipo_disc.otro_tipo       =  otro_tipo_disc;
                }

                
               //-------------------------------------------------------------------------------
               //Traer certificado invalidez
               check_certificado     =  $("#check_certificado_invalidez").is(":checked");
               key_check_certificado =  $("#check_diagnostico").attr("id");
               json_certificado      =  {key_certificado: key_check_certificado, tiene_certificado: check_certificado};
               if(check_certificado){
                key_porcentaje_inv              = $("#input_porcentaje_inv").attr("id"); 
                porcentaje_invalidez            = $("#input_porcentaje_inv").val(); 

                json_certificado.key_porcentaje = key_porcentaje_inv;
                json_certificado.porcentaje     = porcentaje_invalidez;
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
                  

                  //Traer institucion que apoya (?)
                
                  val_apoyo_institu = $("#check_apoyo_institu").is(":checked");
                  key_apoyo_institu = $("#check_apoyo_institu").attr("id");
                  json_apoyo_institu     = {key_apoya: key_apoyo_institu, apoyo: val_apoyo_institu};
  
                  if(val_apoyo_institu){
  
                      val_apoyo_tipo   = $("#input_apoyo").val();
                      key_apoyo_tipo  =  $("#input_apoyo").attr("id");
  
                      json_apoyo_institu.key_apoyo_institu = key_apoyo_tipo;
                      json_apoyo_institu.apoyo_institu     = val_apoyo_tipo;

                      val_institu   = $("#input_institucion").val();
                      key_institu  =  $("#input_institucion").attr("id");
  
                      json_apoyo_institu.key_institucion = key_institu;
                      json_apoyo_institu.institucion     = val_institu;
                  }
  
                  //-------------------------------------------------------------------------------


                                
                //Crear objeto JSON que representa el detalle de discapacidad
                        
                json_detalle_discapacidad = 
                {
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

                if(result_validation.status == "error"){
                    swal(result_validation.title,
                        result_validation.msg,
                        result_validation.status);
                }else{
                    swal("Éxito",
                        "El formulario fue validado con éxito",
                        "success");
                }

                  

                //console.log(json_detalle_discapacidad);
                
                  
              });



        function validate_form(json_detalle){

            var msg = new Object();

            msg.title = "Éxito";
            msg.msg = "El formulario fue validado con éxito";
            msg.status = "success";

            for(i in json_detalle){
                switch(i){
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
                        if(has_numbers(obj_diagnostico[op])){

                            msg.title = "Diagnóstico de discapacidad";
                            msg.status = "error";
                            msg.msg = "El campo "+op+" diagnóstico no debe contener números";
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
                                msg.msg = "El campo "+op+" es obligatorio y no debe contener letras";
                                return msg;  
                            }
                            if(has_letters(obj_cert[op])){

                                msg.title = "Certificado de invalidez";
                                msg.status = "error";
                                msg.msg = "El campo "+op+" no debe contener letras";
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
                            if(has_numbers(obj_dif[op])){

                                msg.title = "Dificultades permanentes";
                                msg.status = "error";
                                msg.msg = "El campo "+op+ " de la opción '"+ obj_dif["funcion"] + "' no debe contener números";
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
                            if(has_numbers(obj_cond[op])){

                                msg.title = "Condiciones de salud";
                                msg.status = "error";
                                msg.msg = "El campo "+op+ " de la opción '"+ obj_cond["organo"] + "' no debe contener números";
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
                            if(has_numbers(obj_nec[op])){

                                msg.title = "Necesidades situaciones";
                                msg.status = "error";
                                msg.msg = "El campo "+op+ " de la opción '"+ obj_nec["situacion"] + "' no debe contener números";
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
                          if(has_numbers(obj_fact[op])){

                              msg.title = "Factores de impacto";
                              msg.status = "error";
                              msg.msg = "El campo "+op+ " de la opción '"+ obj_fact["escenario"] + "' no debe contener números";
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
                          if(has_numbers(obj_pos[op])){

                              msg.title = "Posibilidades en actividades";
                              msg.status = "error";
                              msg.msg = "El campo "+op+ " de la opción '"+ obj_pos["actividad"] + "' no debe contener números";
                              return msg;  
                          }

                      }
                      }
                    }
                    break;


                    case "apoyo_principal_cotidiano":

                    let obj_apoyo= json_detalle[i];
                    for(op in obj_apoyo){
                       if(op == "otro_apoyo"){
                        if(obj_apoyo[op]== ""){

                            msg.title = "Apoyo principal";
                            msg.status = "error";
                            msg.msg = "El campo "+op+" de la opción '"+ obj_apoyo["apoyo_cotidiano"]+"' es obligatorio";
                            return msg;  
                        }
                        if(has_numbers(obj_apoyo[op])){

                            msg.title = "Apoyo principal";
                            msg.status = "error";
                            msg.msg = "El campo "+op+" de la opción '"+ obj_apoyo["apoyo_cotidiano"]+"' no debe contener números";
                            return msg;  
                        }

                       }
                    }

                    break;
                    
                    case "medio_transporte":

                    let obj_transporte= json_detalle[i];
                    for(op in obj_transporte){
                       if(op == "otro_transporte"){
                        if(obj_transporte[op]== ""){

                            msg.title = "Medio de transporte";
                            msg.status = "error";
                            msg.msg = "El campo "+op+" de la opción '"+ obj_transporte["transporte"]+"' es obligatorio";
                            return msg;  
                        }
                        if(has_numbers(obj_transporte[op])){

                            msg.title = "Medio de transporte";
                            msg.status = "error";
                            msg.msg = "El campo "+op+" de la opción '"+ obj_transporte["transporte"]+"' no debe contener números";
                            return msg;  
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
                         if(has_numbers(obj_asoc[op])){
 
                             msg.title = "Participación en organización/asociación";
                             msg.status = "error";
                             msg.msg = "El campo "+op+" no debe contener números";
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
                         if(has_numbers(obj_act[op])){
 
                             msg.title = "Actividades con otros";
                             msg.status = "error";
                             msg.msg = "El campo "+op+" no debe contener números";
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
                         if(has_numbers(obj_inst[op])){
 
                             msg.title = "Apoyo institución";
                             msg.status = "error";
                             msg.msg = "El campo "+op+" no debe contener números";
                             return msg;  
                         }
 
                        }
                     }
                    break;
                }
            }

            return msg;
        } 
                
        // Funciones para la validación de formularios
        function has_letters(str) {
            var letters = "abcdefghyjklmnñopqrstuvwxyz";
            str = str.toLowerCase();
            for (i = 0; i < str.length; i++) {
                if (letters.indexOf(str.charAt(i), 0) != -1) {
                    return 1;
                }
            }
            return 0;
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

        }

    };
});