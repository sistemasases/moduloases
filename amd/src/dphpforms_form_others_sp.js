/**
 * Controls others form
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
                var id_ases = $("#id_ases").val();

                //Servicio Salud


                $("#edit_health_data").click(function(){
                       
                    //Validar las respuestas obtenidas
                    let respuesta = validateHealthData();


                    if(respuesta.status == "error"){
                        swal(respuesta.title,
                            respuesta.msg,
                            respuesta.status);
                    }else{
                    //Si no hay campos obligatorios vacíos, capturar datos

                    let json_health_data = getHealthData();
                    json_health_data = JSON.stringify(json_health_data);

                    editHealthData(json_health_data, id_ases);
                    

                    }


                 });

                    $("#save_health_data").click(function(){
                       
                    //Validar las respuestas obtenidas
                    let respuesta = validateHealthData();


                        if(respuesta.status == "error"){
                            swal(respuesta.title,
                                respuesta.msg,
                                respuesta.status);
                        }else{
                        //Si no hay campos obligatorios vacíos, capturar datos

                        let json_health_data = getHealthData();
                        json_health_data = JSON.stringify(json_health_data);
                        
                        saveHealthData(json_health_data, id_ases);
                        

                        }
                    });

                    if($("#input_health_saved").val() == "0"){
                    $("#save_health_data").parent().show();
                    }
                    if($("#input_health_saved").val() == "1"){
                    $("#save_health_data").parent().hide();
                    $("#edit_health_data").parent().show();
                    showSavedHealthData();
                    }

                    $("#check_servicio_otro").click(function(){
                        if($(this).is(":checked")){
                            $("#servicio_otro_cual").parent().show();
                            $("#servicio_otro_cual").prop("required", true);
                        }else{
                            $("#servicio_otro_cual").parent().hide();
                            $("#servicio_otro_cual").prop("required", false);
                            $("#servicio_otro_cual").val("");
                        }
                    });

                    $(".servicio_salud").click(function(){

                        if($(this).val()=="input_radio_eps"){

                            $("#input_detalle_servicio").attr("title", "Escriba EPS");
                            $("#input_detalle_servicio").attr("placeholder", "¿Cuál EPS?");
                            $("#input_detalle_servicio").attr("type", "text");
    
                        }else if ($(this).val()=="input_radio_sisben"){

                            $("#input_detalle_servicio").attr("title", "Escriba Nivel");
                            $("#input_detalle_servicio").attr("placeholder", "¿Cuál Nivel?");
                            $("#input_detalle_servicio").attr("type", "number");
                            $("#input_detalle_servicio").attr("min", "1");
                            $("#input_detalle_servicio").attr("max", "3");

                        }else if ($(this).val()=="input_radio_otro_servicio"){

                            $("#input_detalle_servicio").attr("title", "Escriba otro servicio");
                            $("#input_detalle_servicio").attr("placeholder", "¿Cuál servicio de salud?");
                            $("#input_detalle_servicio").attr("type", "text");
    
                        }


                        $("#input_detalle_servicio").parent().show();
                        $("#input_detalle_servicio").attr("required", true);
                        $("#input_detalle_servicio").val("");
                    

                    });


                    //Datos económicos
                
                    if($("#input_economics_saved").val() == "0"){
                        $("#save_economics_data").parent().show();
                    }
                    if($("#input_economics_saved").val() == "1"){
                        $("#save_economics_data").parent().hide();
                        $("#edit_economics_data").parent().show();
                        showSavedEconomicsData();
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
                            let select = document.getElementById($(this).attr("id"));
                            $(this).attr("title", select.options[select.selectedIndex].title);
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
                   

                        saveEconomicsData(json_economics_data, id_ases);
                        

                        }
                    });

                        $("#edit_economics_data").click(function(){
                       
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
    
                            editEconomicsData(json_economics_data, id_ases);
                            
    
                            }


                    });

                //DATOS ECONÓMICOS FUNCIONES    

                function showSavedEconomicsData(){
                    let  key, val,key_input_text, val_input_text, key_input_number, val_input_number, json_interno;

                    let json_economics_data = JSON.parse($("#input_json_economics_saved").val());
                   //Se debe parsear el JSON interno de: prestacion_economica, beca, ayuda_transporte, ayuda_materiales, ocupacion_padres,
                   //                                        nivel_educ_padres, situa_laboral_padres, expectativas_laborales
                   for(i in json_economics_data){

                    switch(i){
                        case "estrato":
                        //Estrato
                        $("#estrato_socioeconomico").val(json_economics_data[i]);
                        break;
                        case "prestacion_economica":
                        //Prestación económica
                         json_interno   = JSON.parse(json_economics_data[i]);
                        key                = json_interno['key_input'];          
                        val                = json_interno['val_input'];
                        key_input_text     = json_interno['key_input_text'];           
                        val_input_text     = json_interno['val_input_text'];
                        key_input_number   = json_interno['key_input_number'];           
                        val_input_number   = json_interno['val_input_number'];


                        showOption(key, val, key_input_text, val_input_text, key_input_number, val_input_number);
                        break;
                        case "beca":
                          //Ayuda beca
                           json_interno   = JSON.parse(json_economics_data[i]);
                          key                = json_interno['key_input'];          
                          val                = json_interno['val_input'];
                          key_input_text     = json_interno['key_input_text'];           
                          val_input_text     = json_interno['val_input_text'];
                          key_input_number   = json_interno['key_input_number'];           
                          val_input_number   = json_interno['val_input_number'];
  
                          showOption(key, val, key_input_text, val_input_text, key_input_number, val_input_number);
                        break;
                        case "ayuda_transporte":
                          //Ayuda transporte
                          json_interno   = JSON.parse(json_economics_data[i]);
                          key                = json_interno['key_input'];          
                          val                = json_interno['val_input'];
                          key_input_text     = json_interno['key_input_text'];           
                          val_input_text     = json_interno['val_input_text'];
                          key_input_number   = json_interno['key_input_number'];           
                          val_input_number   = json_interno['val_input_number'];
  
                          showOption(key, val, key_input_text, val_input_text, key_input_number, val_input_number);
                        break;
                        case "ayuda_materiales":
                          //Ayuda materiales
                          json_interno       = JSON.parse(json_economics_data[i]);
                          key                = json_interno['key_input'];          
                          val                = json_interno['val_input'];
                          key_input_text     = json_interno['key_input_text'];           
                          val_input_text     = json_interno['val_input_text'];
                          key_input_number   = json_interno['key_input_number'];           
                          val_input_number   = json_interno['val_input_number'];
  
  
                          showOption(key, val, key_input_text, val_input_text, key_input_number, val_input_number);
                        break;
                        case "solvencia_econo":
                        //Solvencia económica
                        val                = json_economics_data[i];
                        if(val == 1){
                            //Fue seleccionada la opción
                            $("#check_solvencia").prop("checked", true);
                        }else{
                            //No fue seleccionada la opción
                            $("#check_solvencia").prop("checked", false);
                        }
                        break;
                        case "ocupacion_padres":
                        //Ocupación actual de padres
                        json_interno   = JSON.parse(json_economics_data[i]);
                        for(objeto in json_interno){
                            if(json_interno[objeto]["val_select"] == "option_ninguna"){
                                $("#"+json_interno[objeto]["key_select"] ).val(json_interno[objeto]["val_select"]);
                            }else{
                                $("#"+json_interno[objeto]["key_select"] ).val(json_interno[objeto]["val_select"]);
                                $("#"+json_interno[objeto]["key_select"] ).attr("title", json_interno[objeto]["title_select"] );
                                $(json_interno[objeto]["key_input_select"] ).val(json_interno[objeto]["val_input_select"]);
                                $(json_interno[objeto]["key_input_select"] ).parent().show();
                            }
                            
                            
                        }
                        break;
                        case "nivel_educ_padres":
                        //Nivel educativo de padres
                        json_interno   = JSON.parse(json_economics_data[i]);
                        for(objeto in json_interno){
                     
                        $("#"+json_interno[objeto]["key_select"] ).val(json_interno[objeto]["val_select"]);
                   
                        }
                        break;
                        case "situa_laboral_padres":
                        //Situacion laboral de padres
                        json_interno   = JSON.parse(json_economics_data[i]);
                        for(objeto in json_interno){
                     
                        $("#"+json_interno[objeto]["key_select"] ).val(json_interno[objeto]["val_select"]);
                   
                        }
                        break;
                        case "expectativas_laborales":
                        //Expectativas laborales
                        json_interno   = JSON.parse(json_economics_data[i]);
                        $(json_interno["key_input"]).val(json_interno["val_input"]);
                        break;

                    }
                }
                    
                    
                }

                function editEconomicsData(json_data, ases_id){
                    let json_prev = $("#input_json_economics_saved").val();
                $.ajax({
                    type: "POST",
                    data: {
                        func: 'edit_economics_data',
                        json: json_data, 
                        json_prev: json_prev,
                        instanceid: getIdinstancia(),
                        courseid: getIdcourse(),
                        ases: ases_id
                    },
                    url: "../managers/student_profile/others_tab_api.php",
                    success: function(msg) {
            
                        swal(
                           { title: msg.title,
                            text: msg.msg,
                            type: msg.status
                           },
                           function(){
                            $("html, body").animate({scrollTop:700}, 'slow'); 
                            $("#input_economics_saved").attr("value", "1");
                            $("#input_json_economics_saved").attr("value", json_data);
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
                
                function saveEconomicsData(json_data, ases_id){
                    let json_prev = $("#input_json_economics_saved").val();
                    $.ajax({
                        type: "POST",
                        data: {
                            func: 'save_economics_data',
                            json: json_data, 
                            json_prev: json_prev,
                            instanceid: getIdinstancia(),
                            courseid: getIdcourse(),
                            ases: ases_id
                        },
                        url: "../managers/student_profile/others_tab_api.php",
                        success: function(msg) {
                
                            swal(
                               { title: msg.title,
                                text: msg.msg,
                                type: msg.status
                               },
                               function(){
                                $("html, body").animate({scrollTop:700}, 'slow'); 
                                $("#save_economics_data").parent().hide();
                                $("#edit_economics_data").parent().show();
                                $("#input_economics_saved").attr("value", "1");
                                $("#input_json_economics_saved").attr("value", json_data);
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

                 

                    //Info padre

                    let objeto_select = {};
                    let array_nivel_edu  = [];
                    let array_ocupacion  = [];
                    let array_situacion_lab  = [];

                    obj = document.getElementById('div_info_padre');
                    for (i=0; ele = obj.getElementsByTagName('select')[i]; i++){

                    objeto_select.key_select =   ele.id; 
                    objeto_select.val_select =   ele.value;

                    if(ele.id == "select_nivel_padre" ){
                        array_nivel_edu.push(objeto_select);
                    }

                    if(ele.id == "select_ocupacion_padre" ){
                        if(ele.value != "option_ninguna"){
                            objeto_select.key_input_select  = "#" + $("#"+ele.id).parent().next().children("input").attr("id");
                            objeto_select.val_input_select  = $("#"+ele.id).parent().next().children("input").val();
                            objeto_select.title_select= ele.title; 
                        }
                        array_ocupacion.push(objeto_select);
                    }
                    if(ele.id == "select_situa_padre" ){
                        array_situacion_lab.push(objeto_select);
                    }

                    
                    objeto_select = {};
                    }


                    //Madre

                    obj = document.getElementById('div_info_madre');
                    for (i=0; ele = obj.getElementsByTagName('select')[i]; i++){

                    objeto_select.key_select =   ele.id;
                    objeto_select.val_select =   ele.value; 

                  
                    if(ele.id == "select_nivel_madre" ){
                        array_nivel_edu.push(objeto_select);
                    }
                    if(ele.id == "select_ocupacion_madre" ){
                        if(ele.value != "option_ninguna"){
                            objeto_select.key_input_select  = "#" + $("#"+ele.id).parent().next().children("input").attr("id");
                            objeto_select.val_input_select  = $("#"+ele.id).parent().next().children("input").val();
                            objeto_select.title_select= ele.title; 
                        }
                        array_ocupacion.push(objeto_select);
                    }
                    if(ele.id == "select_situa_madre" ){
                        array_situacion_lab.push(objeto_select);
                    }

                    
                    objeto_select = {};
                    }


                    array_economics_data.push(array_ocupacion);
                    array_economics_data.push(array_nivel_edu);
                    array_economics_data.push(array_situacion_lab);

                    //Expectativas laborales
                       let expectativas_lab = {
                        key_input: "#textarea_expectativas",
                        val_input: $("#textarea_expectativas").val()
                    }

                    array_economics_data.push(expectativas_lab);

                    

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

                //DATOS SERVICIO SALUD FUNCIONES

                function showSavedHealthData(){
                    let  key, val,key_input_text, val_input_text, json_interno;

                    let json_health_data = JSON.parse($("#input_json_health_saved").val());
                   //Se debe parsear el JSON interno de cada opcion
                   for(i in json_health_data){

                    switch(i){
                        case "regimen_salud":
                        //Regimen de salud 
                        json_interno   = JSON.parse(json_health_data[i]);
                        $("#"+json_interno["key_input"]).prop("checked", true);;
                        break;
                
                        case "servicio_salud_vinculado":
                          //Servicio de salud vinculado
                          json_interno   = JSON.parse(json_health_data[i]);
                          key                = json_interno['key_input'];          
                          key_input_text     = json_interno['key_input_text'];           
                          val_input_text     = json_interno['val_input_text'];

                            //Fue seleccionada la opción
                            $("#"+key).prop("checked", true);
                            $("#"+key_input_text).val(val_input_text);
                            $("#"+key_input_text).parent().show();
                            if(key == "input_radio_sisben"){
                                $("#"+key_input_text).attr("title", "Escriba Nivel");
                                $("#"+key_input_text).attr("type", "number");
                                $("#"+key_input_text).attr("min", "1");
                                $("#"+key_input_text).attr("max", "3");
                            }
                            if(key=="input_radio_eps"){

                                $("#"+key_input_text).attr("title", "Escriba EPS");
                                $("#"+key_input_text).attr("placeholder", "¿Cuál EPS?");
                                $("#"+key_input_text).attr("type", "text");
        
                            }else if (key =="input_radio_otro_servicio"){
    
                                $("#"+key_input_text).attr("title", "Escriba otro servicio");
                                $("#"+key_input_text).attr("placeholder", "¿Cuál servicio de salud?");
                                $("#"+key_input_text).attr("type", "text");
        
                            }

                        break;
                        case "servicios_usados":
                        //Servicios de salud usados

                        json_interno   = JSON.parse(json_health_data[i]);
                        for(objeto in json_interno){
                            if(json_interno[objeto]["val_input"] == 1 ){
                                //Fue seleccionado 
                                $("#"+json_interno[objeto]["key_input"]).prop("checked", true);
                                if(json_interno[objeto]["key_input"] == "check_servicio_otro"){
                                     //Mostrar el campo de Otro servicio
                                     $("#"+json_interno[objeto]["key_input_text"]).val(json_interno[objeto]["val_input_text"]);
                                     $("#"+json_interno[objeto]["key_input_text"]).parent().show();

                                }
                                 
                                
                            }else {
                                //No fue seleccionado 
                                $("#"+json_interno[objeto]["key_input"]).prop("checked", false);

                                if(json_interno[objeto]["key_input"] == "check_servicio_otro"){
                                    //Ocultar el campo de Otro servicio
                                    $("#servicio_otro_cual").val("");
                                    $("#servicio_otro_cual").parent().hide();
                                }
                            }
                            
                            
                            
                        }
                        break;
                       

                    }
                }
                    
                    
                }

                function editHealthData(json_data, ases_id){
                    let json_prev = $("#input_json_health_saved").val(); 
                    $.ajax({
                        type: "POST",
                        data: {
                            func: 'edit_health_data',
                            json: json_data, 
                            json_prev: json_prev,
                            instanceid: getIdinstancia(),
                            courseid: getIdcourse(),
                            ases: ases_id
                        },
                        url: "../managers/student_profile/others_tab_api.php",
                        success: function(msg) {
                
                            swal(
                               { title: msg.title,
                                text: msg.msg,
                                type: msg.status
                               },
                               function(){
                                $("html, body").animate({scrollTop:820}, 'slow'); 
                                $("#input_health_saved").attr("value","1");
                                $("#input_json_health_saved").attr("value",json_data);
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

                function saveHealthData(json_data, ases_id){
                    let json_prev = $("#input_json_health_saved").val();
                    $.ajax({
                        type: "POST",
                        data: {
                            func: 'save_health_data',
                            json: json_data, 
                            json_prev: json_prev,
                            instanceid: getIdinstancia(),
                            courseid: getIdcourse(),
                            ases: ases_id
                        },
                        url: "../managers/student_profile/others_tab_api.php",
                        success: function(msg) {
                
                            swal(
                               { title: msg.title,
                                text: msg.msg,
                                type: msg.status
                               },
                               function(){
                                $("html, body").animate({scrollTop:820}, 'slow'); 
                                $("#save_health_data").parent().hide();
                                $("#edit_health_data").parent().show();
                                $("#input_health_saved").attr("value","1");
                                $("#input_json_health_saved").attr("value",json_data);
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

                function getHealthData(){

                    //Crea JSON con todos los datos económicos registrados, así como sus identificadores de tags...

                    let array_health_data = [];
                   
                    //Regimen de salud
                    let val_regimen = $("#opciones_regimen_salud").find(":input[type=radio]:checked").val();
                    let key_regimen = $("#opciones_regimen_salud").find(":input[type=radio]:checked").attr("id");

                    let regimen_salud = {key_input: key_regimen,
                                         val_input: val_regimen
                    };

                    //Servicio de salud (EPS, SISBEN, OTRO)
                    let val_serv_radio    = $("#opciones_servicio_salud").find(":input[type=radio]:checked").val();
                    let key_serv_radio    = $("#opciones_servicio_salud").find(":input[type=radio]:checked").attr("id");
                    let val_serv_text     = $("#input_detalle_servicio").val();
                    let key_serv_text     = "input_detalle_servicio";

                    let servicio_salud= {key_input: key_serv_radio,
                                         val_input: val_serv_radio,
                                         val_input_text: val_serv_text,
                                         key_input_text: key_serv_text
                    };

                    //Servicios de salud usados
                    
                    let array_servicios = [];
                    let objeto_json     = {};

                    $("#options_services input[type=checkbox]").each( function(){

                        objeto_json.key_input = $(this).attr("id");

                        if($(this).is(":checked")){

                            objeto_json.val_input = 1;

                            if(objeto_json.key_input == "check_servicio_otro"){
                                
                                    objeto_json.key_input_text = "servicio_otro_cual";
                                    objeto_json.val_input_text = $("#servicio_otro_cual").val();
                            }
                          
                        }else{
                            objeto_json.val_input = 0;
                        }

                        array_servicios.push(objeto_json);
                        objeto_json = {};
                    });



                    array_health_data.push(regimen_salud);
                    array_health_data.push(servicio_salud);
                    array_health_data.push(array_servicios);

                    

                    return array_health_data;

                }

                function validateHealthData(){

                    var msg = new Object();

                    msg.title  = "Éxito";
                    msg.msg    = "El formulario fue validado con éxito";
                    msg.status = "success";

                    //Servicios 
                        if($("#check_servicio_otro").is(":checked")){
                          
                            let value   =  $("#servicio_otro_cual").val();
                            let op      =  "Otro servicio";

                            if(value == ""){
                                msg.title  = "Datos de servicios de salud";
                                msg.status = "error";
                                msg.msg    = "El campo "+op+" es obligatorio";
                                return msg;  
                            }
                             
                            if(has_numbers(value)){
                                msg.title  = "Datos de servicios de salud";
                                msg.status = "error";
                                msg.msg    = "El campo "+op+" no debe contener números";
                                return msg;  
                            }
                         
                        }
                    
                    //Regimen de salud    
                     let radio_regimen =   $("#opciones_regimen_salud").find(":input[type=radio]:checked").val();
                     if(radio_regimen == undefined){

                        let op     =  "Régimen de salud";
                        msg.title  = "Datos de servicios de salud";
                        msg.status = "error";
                        msg.msg    = "El campo "+op+" es obligatorio";
                        return msg;  
                     }
                    //Servicio de salud  
                    let radio_serv =   $("#opciones_servicio_salud").find(":input[type=radio]:checked").val();
                    if(radio_serv != undefined){

                        let detalle_servicio = $("#input_detalle_servicio").val();
                        let op;
                        
                        switch(radio_serv){
                            case "input_radio_eps":
                            //Validar campo de texto para EPS
                            op     =  "¿Cuál EPS?";

                            if(detalle_servicio == ""){

                                msg.title  = "Datos de servicios de salud";
                                msg.status = "error";
                                msg.msg    = "El campo "+op+" es obligatorio";
                                return msg;  
                            }
                            if(has_numbers(detalle_servicio)){

                                msg.title  = "Datos de servicios de salud";
                                msg.status = "error";
                                msg.msg    = "El campo "+op+" no debe contener números";
                                return msg;  

                            }

                            break;
                            case "input_radio_sisben":
                            //Validar campo de texto para Nivel
                            op     =  "¿Cuál Nivel?";

                            if(detalle_servicio == ""){

                                msg.title  = "Datos de servicios de salud";
                                msg.status = "error";
                                msg.msg    = "El campo "+op+" es obligatorio y numérico";
                                return msg;  
                            }
                            if(detalle_servicio < 1 || detalle_servicio > 3){

                                msg.title  = "Datos de servicios de salud";
                                msg.status = "error";
                                msg.msg    = "El campo "+op+" corresponde a niveles válidos de SISBEN (1,2,3)";
                                return msg;  

                            }
                            break;
                            case "input_radio_otro_servicio":
                            //Validar campo de texto para Otro, ¿cuál?

                            op     =  "¿Cuál servicio de salud?";

                            if(detalle_servicio == ""){

                                msg.title  = "Datos de servicios de salud";
                                msg.status = "error";
                                msg.msg    = "El campo "+op+" es obligatorio";
                                return msg;  
                            }
                            if(has_numbers(detalle_servicio)){

                                msg.title  = "Datos de servicios de salud";
                                msg.status = "error";
                                msg.msg    = "El campo "+op+" no debe contener números";
                                return msg;  

                            }

                            break;
                        }

                     }else{

                        let op     =  "Servicio de salud";
                        msg.title  = "Datos de servicios de salud";
                        msg.status = "error";
                        msg.msg    = "El campo "+op+" es obligatorio";
                        return msg;  
                     }
                    
                    return msg;

                }

              
                function showOption(key, val, key_input_text, val_input_text, key_input_number, val_input_number){
                
                    if(val == 1){
                        //Fue seleccionada la opción
                        $("#"+key).prop("checked", true);
                        $("#"+key_input_text).val(val_input_text);
                        $("#"+key_input_text).parent().parent().show();
                        $("#"+key_input_number).val(val_input_number);
    
                    }else{
                        //No fue seleccionada la opción
                        $("#"+key).prop("checked", false);
                        $("#"+key_input_text).val("");
                        $("#"+key_input_text).parent().parent().hide();
                        $("#"+key_input_number).val("");
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