 /**
 * Management - Tracks (seguimiento de pilos)
 * @module amd/src/pilos_tracking_main 
 * @author Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery','block_ases/Modernizr-v282' ,'block_ases/bootstrap', 'block_ases/datatables',  'block_ases/sweetalert', 'block_ases/select2'], function($,Modernizr,bootstrap, datatables, sweetalert, select2) {

    return {
        init: function() {

            var globalArregloPares = [];
            var globalArregloGrupal = [];
            var arregloMonitorYEstudiantes = [];
            var arregloPracticanteYMonitor = [];
            var arregloImprimirPares = [];
            var arregloImprimirGrupos = [];
            var rol = 0;
            var id = 0;
            var name = "";
            var htmltexto = "";
            var instance = "";
            var email = "";

            $(document).ready(function() {

                ///////////////////////////////////////////////////////////7

                $(".se-pre-con").fadeOut('slow');
                $("#reemplazarToogle").fadeIn("slow");

                var usuario = "";
                //We get the current instance id

                var informacionUrl = window.location.search.split("&");
                for (var i = 0; i < informacionUrl.length; i++) {
                    var elemento = informacionUrl[i].split("=");
                    if (elemento[0] == "?instanceid" || elemento[0] == "instanceid") {
                        var instance = elemento[1];
                    }
                }

                //Disable div from sistemas
                //$(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").hide();

                //Getting information of the logged user such as name, id, email and role
                $.ajax({
                    type: "POST",
                    data: {
                        type: "getInfo",
                        instance: instance
                    },
                    url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg) {
                        $data = $.parseJSON(msg);
                        name = $data.username;
                        id = $data.id;
                        email = $data.email;
                        rol = $data.rol;
                        namerol = $data.name_rol;
                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) {
                        swal({
                            title: "error al obtener información del usuario, getInfo.",
                            html: true,
                            type: "error",
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });

               name = "";
                //Shows the interface acording to the logged user
                if (namerol == "monitor_ps") {
                    usuario = "monitor";

                } else if (namerol == "practicante_ps") {
                    usuario = "practicante";
                } else if (namerol == "profesional_ps") {

                    usuario = "profesional";
                } else if (namerol == "sistemas") {
                    usuario = "sistemas";
                }

                var usuario = [];
                usuario["id"] = id;
                usuario["name"] = name;
                usuario["namerol"] = namerol;


                crear_conteo(usuario);


                // when user is 'practicante' then has permissions
                if (namerol == "practicante_ps") {

                    $("input[name=profesional]").prop('disabled', true);
                    $("input[name=practicante]").prop('disabled', true);

                    limpiar_riesgos();
                    editar_seguimiento(namerol);
                    cancelar_edicion(namerol);
                    borrar_seguimiento(namerol);
                    modificar_seguimiento(id);
                    actualizar_riesgo();
                    enviar_correo(instance);
                    consultar_seguimientos_persona(instance, usuario);
                    send_email_new_form(instance);



                   // when user is 'profesional' then has permissions
                } else if (namerol == "profesional_ps") {
                    //Starts adding event
                    $("input[name=practicante]").prop('disabled', true);
                    $("input[name=profesional]").prop('disabled', true);
                    limpiar_riesgos();
                    cancelar_edicion(namerol);
                    editar_seguimiento(namerol);
                    modificar_seguimiento(id);
                    borrar_seguimiento(namerol);
                    actualizar_riesgo();
                    enviar_correo(instance);
                    consultar_seguimientos_persona(instance, usuario);
                    send_email_new_form(instance);


                    // when user is 'monitor' then has permissions
                } else if (namerol == "monitor_ps") {
                    limpiar_riesgos();
                    editar_seguimiento(namerol);
                    cancelar_edicion(namerol);
                    borrar_seguimiento(namerol);
                    modificar_seguimiento(id);
                    actualizar_riesgo();
                    consultar_seguimientos_persona(instance, usuario);



                    // when user is 'sistemas' then has permissions
                } else if (namerol == "sistemas") {
                    limpiar_riesgos();
                    editar_seguimiento(namerol);
                    cancelar_edicion(namerol);
                    borrar_seguimiento(namerol);
                    modificar_seguimiento(id);
                    actualizar_riesgo();
                    enviar_correo(instance);
                    anadirEvento(instance);
                    send_email_new_form(instance);
                }

            });


            function edit_tracking_new_form(){
            // Controles para editar formulario de pares
            $('.dphpforms-peer-record').on('click', function(){
                var id_tracking = $(this).attr('data-record-id');
                load_record_updater('seguimiento_pares', id_tracking);
                $('#modal_v2_edit_peer_tracking').fadeIn(300);
                  
            });}


            function edit_groupal_tracking_new_form(){
            // Controles para editar formulario grupal
            $('.dphpforms-groupal-record').on('click', function(){
                var id_tracking = $(this).attr('data-record-id');
                load_record_updater('seguimiento_grupal', id_tracking);
               $('#modal_v2_edit_groupal_tracking').fadeIn(300);

            });}






            student_load();
            monitor_load();
            professional_load();
            groupal_tracking_load();

            function professional_load(){

            /*When click on the practicant's name, open the container with the information of 
            the assigned monitors*/

            $('a[class*="practicant"]').click(function() {
                var practicant_code = $(this).attr('href').split("#practicant")[1];
                var practicant_id = $(this).attr('href');
                //Fill container with the information corresponding to the monitor 
                $.ajax({
                    type: "POST",
                    data: {
                        type: "get_practicants_of_professional",
                        practicant_code: practicant_code,
                        instance:get_instance(),
                    },
                    url: "../managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg
                        ) {
                    $(practicant_id + " > div").empty();
                    $(practicant_id + " > div").append(msg);
                    monitor_load();
                    groupal_tracking_load();
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                       swal({
                            title: "Oops !",
                            text: "Se presentó un inconveniente con el practicante seleccionado.",
                            html: true,
                            type: 'warning',
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });
            
            });
            }






            function monitor_load(){

            /*When click on the student's name, open the container with the information of 
            the follow-ups of that date*/

            $('a[class*="monitor"]').click(function() {
                var monitor_code = $(this).attr('href').split("#monitor")[1];
                var monitor_id = $(this).attr('href');
                //Fill container with the information corresponding to the monitor 
                $.ajax({
                    type: "POST",
                    data: {
                        type: "get_monitors_of_practicant",
                        monitor_code: monitor_code,
                        instance:get_instance(),
                    },
                    url: "../managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg
                        ) {
                    $(monitor_id + " > div").empty();
                    $(monitor_id + " > div").append(msg);
                    student_load();
                    groupal_tracking_load();
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                       swal({
                            title: "Oops !",
                            text: "Se presentó un inconveniente con el monitor seleccionado.",
                            html: true,
                            type: 'warning',
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });
            
            });
            }


            /*When click on the "SEGUIMIENTOS GRUPALES", open the container with the information of 
            the follow-ups of that date*/

            function groupal_tracking_load(){

            $('a[class*="groupal"]').click(function() {
                var student_code = $(this).attr('href').split("#groupal")[1];
                var student_id = $(this).attr('href');
                //console.log(student_id);
                //Fill container with the information corresponding to the trackings of the selected student
                $.ajax({
                    type: "POST",
                    data: {
                        type: "get_groupal_trackings",
                        student_code: student_code,
                        instance:instance
                    },
                    url: "../managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg
                        ) {
                    //console.log(msg);
                    $(student_id + " > div").empty();
                    $(student_id + " > div").append(msg);
                    edit_groupal_tracking_new_form();
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        swal({
                            title: "Oops !",
                            text: "Se presentó un inconveniente con los seguimientos grupales seleccionados.",
                            html: true,
                            type: 'warning',
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });
            
            });}


            /*When click on the student's name, open the container with the information of 
            the follow-ups of that date*/

            function student_load(){

            $('a[class*="student"]').click(function() {
               // console.log("student : "+$(this).attr('href').split("#student")[1]);
                var student_code = $(this).attr('href').split("#student")[1];
                var student_id = $(this).attr('href');
                //Fill container with the information corresponding to the trackings of the selected student
                $.ajax({
                    type: "POST",
                    data: {
                        type: "get_student_trackings",
                        student_code: student_code,
                        instance:instance
                    },
                    url: "../managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function(msg
                        ) {
                    $(student_id + " > div").empty();
                    $(student_id + " > div").append(msg);
                    edit_tracking_new_form();
                    edit_groupal_tracking_new_form();
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        swal({
                            title: "Oops !",
                            text: "Se presentó un inconveniente con el estudiante seleccionado.",
                            html: true,
                            type: 'warning',
                            confirmButtonColor: "#d51b23"
                        });
                    },
                });
            
            });}


                 function check_risks_tracking( flag ){
                   

                        var individual_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_individual');
                        var idv_observation = $('.comentarios_individual').find('textarea').val();;
                        var familiar_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_familiar');
                        var fam_observation = $('.comentarios_familiar').find('textarea').val();
                        var academico_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_academico');
                        var aca_observation = $('.comentarios_academico').find('textarea').val();
                        var economico_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_economico');
                        var eco_observation = $('.comentarios_economico').find('textarea').val();
                        var vida_univer_risk = get_checked_risk_value_tracking('.puntuacion_vida_uni');
                        var vid_observation = $('.comentarios_vida_uni').find('textarea').val();

                        if( 
                            ( individual_risk == '3' ) || ( familiar_risk == '3' ) || 
                            ( academico_risk == '3' ) || ( economico_risk == '3' ) || 
                            ( vida_univer_risk == '3' ) 
                        ){

                            var json_risks = {
                                "function": "send_email_dphpforms",
                                "student_code": get_student_code(),
                                "risks": [
                                    {
                                        "name":"Individual",
                                        "risk_lvl": individual_risk,
                                        "observation":idv_observation
                                    },
                                    {
                                        "name":"Familiar",
                                        "risk_lvl": familiar_risk,
                                        "observation":fam_observation
                                    },
                                    {
                                        "name":"Académico",
                                        "risk_lvl": academico_risk,
                                        "observation":aca_observation
                                    },
                                    {
                                        "name":"Económico",
                                        "risk_lvl": economico_risk,
                                        "observation":eco_observation
                                    },
                                    {
                                        "name":"Vida Universitaria",
                                        "risk_lvl": vida_univer_risk,
                                        "observation":vid_observation
                                    }
                                ],
                                "date": $('.fecha').find('input').val(),
                                "url": window.location.href
                            };


                            $.ajax({
                                type: "POST",
                                data: JSON.stringify(json_risks),
                                url: "../managers/pilos_tracking/send_risk_email.php",
                                success: function(msg) {
                                    console.log(msg);
                                },
                                dataType: "text",
                                cache: "false",
                                error: function(msg) {
                                    console.log(msg)
                                }
                            });

                        }

                    
                };

                        function get_checked_risk_value_tracking( class_id ){
                    var value = 0;
                    $( class_id ).find('.opcionesRadio').find('div').each(function(){
                        if($(this).find('label').find('input').is(':checked')){
                            value = $(this).find('label').find('input').val();
                        }
                    });
                    return value;
                };

           $(document).on('click', '.dphpforms > #button' , function(evt) {
                     evt.preventDefault();

                    var formData = new FormData();
                    var formulario = $(this).parent();
                    var url_processor = formulario.attr('action');
                    if(formulario.attr('action') == 'procesador.php'){
                        url_processor = '../managers/dphpforms/procesador.php';
                    }

                    $.ajax({
                        type: 'POST',
                        url: url_processor,
                        data:  $('form.dphpforms').serialize(),
                                dataType: 'json',

                        success: function(data) {
                                //var response = JSON.parse(data);
                                var response = data;
                                
                                if(response['status'] == 0){
                                    var mensaje = '';
                                    if(response['message'] == 'Stored'){
                                        mensaje = 'Almacenado';
                                    }else if(response['message'] == 'Updated'){
                                        mensaje = 'Actualizado';
                                    }
                                    check_risks_tracking();
                                    swal(
                                        {title:'Información',
                                        text: mensaje,
                                        type: 'success'},
                                        function(){
                                            if(response['message'] == 'Updated'){
                                                $('#dphpforms-peer-record-' + $('#dphpforms_record_id').val()).stop().animate({backgroundColor:'rgb(175, 255, 173)'}, 400).animate({backgroundColor:'#f5f5f5'}, 4000);
                                            }
                                        }
                                    );
                                    $('.dphpforms-response').trigger("reset");
                                    $('#modal_v2_edit_peer_tracking').fadeOut(300);
                                    $('#modal_v2_peer_tracking').fadeOut(300);

                                    
                                    
                                }else if(response['status'] == -2){
                                    var mensaje = '';
                                    if(response['message'] == 'Without changes'){
                                        mensaje = 'No hay cambios que registrar';
                                    }else if(response['message'] == 'Unfulfilled rules'){
                                        mensaje = 'Revise los valores ingresados';
                                    }
                                    swal(
                                        'Alerta',
                                        mensaje,
                                        'warning'
                                    );
                                }else if(response['status'] == -1){
                                    swal(
                                        'ERROR!',
                                        'Oops!, informe de este error',
                                        'error'
                                    );
                                };
                            },
                            error: function(data) {
                                swal(
                                    'Error!',
                                    'Oops!, informe de este error',
                                    'error'
                                );
                            }
                            
                     });
                
                     
                });




                $('.mymodal-close').click(function(){
                    $(this).parent().parent().parent().parent().fadeOut(300);
                });

                function generate_attendance_table(students){

                     $.ajax({
                            type: "POST",
                            data: {
                                students: students,
                                type: "consult_students_name"
                            },
                            url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                            async: false,


                            success: function(msg) {

                                if (msg != "") {
                                   var table ='<hr style="border-color:red"><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 estudiantes" id="students"><h3>Estudiantes asistentes:</h3><br>'+msg+'<br>';
                                   $('#modal_v2_edit_groupal_tracking').find('#students').remove(); 
                                   $('#modal_v2_edit_groupal_tracking').find('form').find('h1').after(table);
                                }
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                alert("Error al consultar nombres de los estudiantes pertenecientes a un seguimiento grupal");
                            },
                        });

                    


                }


                function load_record_updater(form_id, record_id){
                    $.get( "../managers/dphpforms/dphpforms_forms_core.php?form_id="+form_id+"&record_id="+record_id, function( data ) {
                         if(form_id =='seguimiento_grupal'){

                            $("#body_editor").html("");
                            $("#modal_v2_edit_groupal_tracking").find("#body_editor").html("");                            
                            $("#modal_v2_edit_groupal_tracking").find("#body_editor").append(data);
                            $("#modal_v2_edit_groupal_tracking").find(".btn-dphpforms-univalle").remove();
                            var students = $("#modal_v2_edit_groupal_tracking").find('form').find('.oculto.id_estudiante').find('input').val();

                            generate_attendance_table(students);


                         }else{
                            $("#modal_v2_edit_groupal_tracking").find("#body_editor").html("");                            
                            $("#body_editor").html("");
                            $('#body_editor').append( data );
                            $(".dphpforms.dphpforms-record.dphpforms-updater").append('<br><br><div class="div-observation col-xs-12 col-sm-12 col-md-12 col-lg-12 comentarios_vida_uni">Observaciones de Practicante/profesional:<br> <textarea id="observation_text" class="form-control " name="observation_text" maxlength="5000"></textarea><br><a id="send_observation" class="btn btn-sm btn-danger btn-dphpforms-univalle btn-dphpforms-send-observation">Enviar observación</a></div>');
                            $('button.btn.btn-sm.btn-danger.btn-dphpforms-univalle').attr('id', 'button');

                         }
                            
                           
                            $("#permissions_informationr").html("");

                            var rev_prof = $('.dphpforms-record').find('.revisado_profesional').find('.checkbox').find('input[type=checkbox]').prop('checked');
                            var rev_prac = $('.dphpforms-record').find('.revisado_practicante').find('.checkbox').find('input[type=checkbox]').prop('checked');
                            
                            if(rev_prof || rev_prac){
                                $('.dphpforms-record').find('.btn-dphpforms-delete-record').remove();
                            }

                            var behaviors = JSON.parse($('#permissions_information').text());
                            
                            for(var x = 0; x < behaviors['behaviors_permissions'].length; x++){
                             
                                var current_behaviors =  behaviors['behaviors_permissions'][x]['behaviors'][0];
                                var behaviors_accessibility = current_behaviors.behaviors_accessibility;
                                
                                for( var z = 0; z <  behaviors_accessibility.length; z++){
                                    var disabled = behaviors_accessibility[z]['disabled'];
                                    if(disabled == 'true'){
                                        disabled = true;
                                    }else if(disabled == 'false'){
                                        disabled = false;
                                    }
                                    $('.dphpforms-record').find('#' + behaviors_accessibility[z]['id']).prop( 'disabled', disabled );
                                    $('.dphpforms-record').find('.' + behaviors_accessibility[z]['class']).prop( 'disabled', disabled );

                                }
                                var behaviors_fields_to_remove = current_behaviors['behaviors_fields_to_remove'];
                                for( var z = 0; z < behaviors_fields_to_remove.length; z++){
                                    $('.dphpforms-record').find('#' + behaviors_fields_to_remove[z]['id']).remove();
                                    $('.dphpforms-record').find('.' + behaviors_fields_to_remove[z]['class']).remove();
                                }
                                var limpiar_to_eliminate = current_behaviors['limpiar_to_eliminate'];
                                for( var z = 0; z <  limpiar_to_eliminate.length; z++){
                                    $('.dphpforms-record').find('.' + limpiar_to_eliminate[z]['class'] + '.limpiar ').remove();
                                }
                                
                            }

                            $("#permissions_informationr").html("");

                    });
                }



            //-------- Page elements --> Listener

            /**
             * @method consultar_seguimientos_persona
             * @desc Obtain track information of a certain user
             * @param {instance} instance current instance
             * @param {object} usuario current user to obtain information
             * @return {void}
             */
            function consultar_seguimientos_persona(instance, usuario) {
                $("#periodos").change(function() {
                    if (namerol != 'sistemas') {
                        var semestre = $("#periodos").val();
                        var id_persona = id;
                        $.ajax({
                            type: "POST",
                            data: {
                                id_persona: id_persona,
                                id_semestre: semestre,
                                instance: instance,
                                otro: true,
                                type: "consulta_sistemas"
                            },
                            url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                            async: false,


                            success: function(msg) {

                                if (msg == "") {
                                    $('#reemplazarToogle').html('<label> No se encontraron registros </label>');
                                    crear_conteo(usuario);



                                } else {
                                    $('#reemplazarToogle').html(msg);
                                    student_load();
                                    monitor_load();
                                    professional_load();
                                    groupal_tracking_load();
                                }
                                $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").slideDown("slow");
                                crear_conteo(usuario);




                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                alert("error al consultar seguimientos de personas");
                            },
                        });
                        edit_tracking_new_form();
                        edit_groupal_tracking_new_form();

                    }


                });
            }


            /**
             * @method anadirEvento
             * @desc Function for 'sistemas' role. Adding an event
             * @param {instance} instance current instance
             * @return {string} message according if there's a period or person to look for
             */
            function anadirEvento(instance) {
                $("#personas").val('').change();

                //Select2 is able when user role is 'sistemas'
                $("#personas").select2({
                    placeholder: "Seleccionar persona",

                    language: {
                        noResults: function() {
                            return "No hay resultado";
                        },
                        searching: function() {
                            return "Buscando..";
                        }
                    }
                });
                $("#periodos").select2({
                    language: {
                        noResults: function() {
                            return "No hay resultado";
                        },
                        searching: function() {
                            return "Buscando..";
                        }
                    }
                });

                consultar_periodos(instance, namerol);


                $('#consultar_persona').on('click', function() {

                    var id_persona = $("#personas").children(":selected").attr("value");
                    var id_semestre = $("#periodos").children(":selected").attr("value");
                    var fechas_epoch = [];


                    if (id_persona == undefined) {
                        swal({
                            title: "Debe escoger una persona para realizar la consulta",
                            html: true,
                            type: "warning",
                            confirmButtonColor: "#d51b23"
                        });
                    } else {
                        $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").show();

                        $(".se-pre-con").show();
                        $("#reemplazarToogle").hide();

                        //Processing in pilos_tracking_report.php
                        $.ajax({
                            type: "POST",
                            data: {
                                id_persona: id_persona,
                                id_semestre: id_semestre,
                                instance: instance,
                                type: "consulta_sistemas"
                            },
                            url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                            async: false,


                            success: function(msg) {



                                //In case there are not records
                                if (msg == "") {
                                    $('#reemplazarToogle').html('<label> No se encontraron registros </label>');

                                } else {
                                    $('#reemplazarToogle').html(msg);
                                    $("input[name=practicante]").prop('disabled', true);
                                    $("input[name=profesional]").prop('disabled', true);
                                }
                                student_load();
                                monitor_load();
                                professional_load();
                                groupal_tracking_load();
                                $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").slideDown("slow");

                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                alert("error al consultar seguimientos de personas");
                            },
                            complete: function(){
                               $(".se-pre-con").hide();
                               $("#reemplazarToogle").fadeIn();
                            }
                        });
                        edit_tracking_new_form();
                        edit_groupal_tracking_new_form();

                    }

                });
            }

            /**
             * @method crear_conteo
             * @desc On a container shows an HTML information about the amount of checked tracks by monitor, 'practicante' or 'profesional'
             * @param {object} usuario
             * @return {void} 
             */
            function crear_conteo(usuario) {
                var periodo = $("#periodos option:selected").text();
                var conteo = 0;
                var contenedor = "";

                if (usuario["namerol"] == 'monitor_ps') {
                    var conteos_monitor = realizar_conteo(usuario);
                    contenedor = '<div class="row"><div class="col-sm-12"><h2>Información monitor - PERIODO :' + periodo + ' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :<label for="revisado_monitor_' + conteo + '">' + conteos_monitor[0] + '</label><b></b> - NO Revisados :<label for="norevisado_monitor_' + conteo + '">' + conteos_monitor[1] + '</label><b></b> - Total  :<label for="total_monitor_' + conteo + '">' + conteos_monitor[2] + '</label> <b></b> </span></h4></div></div></div></div>';
                    $("#conteo_principal").empty();
                    $("#conteo_principal").html(contenedor);


                } else if (usuario["namerol"] == 'practicante_ps') {
                    var conteos_practicante = realizar_conteo(usuario);
                    contenedor = '<div class="row"><div class="col-sm-12"><h2>Información practicante - PERIODO :' + periodo + ' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :' + conteos_practicante[0] + ' <b></b> - NO Revisados :' + conteos_practicante[1] + ' <b></b> - Total  :' + conteos_practicante[2] + ' <b></b> </span></h4></div></div></div></div>';
                    $("#conteo_principal").empty();
                    $("#conteo_principal").html(contenedor);

                } else if (usuario["namerol"] == 'profesional_ps') {
                    var conteos_profesional = realizar_conteo(usuario);
                    contenedor = '<div class="row"><div class="col-sm-12"><h2>Información profesional - PERIODO :' + periodo + ' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :' + conteos_profesional[0] + ' <b></b> - NO Revisados :' + conteos_profesional[1] + ' <b></b> - Total  :' + conteos_profesional[2] + ' <b></b> </span></h4></div></div></div></div>';
                    $("#conteo_principal").empty();
                    $("#conteo_principal").html(contenedor);

                } else if (usuario["namerol"] == 'sistemas') {


                }
            }

            /**
             * @method realizar_conteo
             * @desc Does the counting of tracks, even when there are dependents ('practicantes' or 'profesionales are dependents')
             * @param {object} usuario current user to get the total tracks
             * @param {string} dependiente 
             * @return {integer} amount of tracks (checked and not checked)
             */
            function realizar_conteo(usuario, dependiente) {

                if(dependiente === undefined){
                    dependiente = "ninguno";
                }

                var conteos = [];

                var total_grupal_revisado = 0;
                var total_grupal_norevisado = 0;
                var total_monitor_revisado = 0;
                var total_monitor_norevisado = 0;

                //If the user role is monitor
                if (usuario["namerol"] == 'monitor_ps') {
                    var numero_pares = 0;
                    var numero_grupales = 0;

                    //In case it has no dependents to counting tracks
                    if (dependiente == "ninguno") {
                        numero_pares = $('.panel-heading.pares').children().length;
                        numero_grupales = $('.panel-heading.grupal').children().length;


                    } else {
                        numero_pares = $("#collapse" + usuario["id"] + " .panel-heading.pares").children().length;
                        numero_grupales = $("#collapse" + usuario["id"] + " .panel-heading.grupal").children().length;
                    }
                    $("label[for='norevisado_grupal_" + usuario["id"] + "']").html(numero_grupales);
                    $("label[for='total_grupal_" + usuario["id"] + "']").html(numero_grupales);


                    for (var cantidad = 0; cantidad < numero_pares; cantidad++) {
                        total_monitor_revisado += Number($("label[for='revisado_pares_" + usuario["id"] + "_" + cantidad + "']").text());
                        total_monitor_norevisado += Number($("label[for='norevisado_pares_" + usuario["id"] + "_" + cantidad + "']").text());
                    }

                    for (var cantidad = 0; cantidad < numero_grupales; cantidad++) {
                        total_grupal_revisado += 0;
                        total_grupal_norevisado = numero_grupales;

                    }
                    total = (total_monitor_revisado + total_grupal_revisado) + (total_monitor_norevisado + total_grupal_norevisado);
                    return new Array((total_monitor_revisado + total_grupal_revisado), (total_monitor_norevisado + total_grupal_norevisado), total);

                    //In case the user role is 'practicante'
                } else if (usuario["namerol"] == 'practicante_ps') {
                    var numero_monitores = 0;
                    conteos = [0, 0, 0];
                    var conteos_monitor = [];


                    if (dependiente == "ninguno") {

                        numero_monitores = $('.panel-heading.practicante').children().length;
                        for (var monitor = 0; monitor < numero_monitores; monitor++) {

                            var collapse_name = $(".panel-heading.practicante:eq(" + monitor + ")").find('a').attr('href');
                            var id_monitor = collapse_name.split("#collapse")[1];
                            var usuario_monitor = [];

                            usuario_monitor["id"] = id_monitor;
                            usuario_monitor["namerol"] = "monitor_ps";
                            conteos_monitor = realizar_conteo(usuario_monitor, "practicante");
                            $("label[for='revisado_monitor_" + id_monitor + "']").html(conteos_monitor[0]);
                            $("label[for='norevisado_monitor_" + id_monitor + "']").html(conteos_monitor[1]);
                            $("label[for='total_monitor_" + id_monitor + "']").html(conteos_monitor[2]);


                            conteos[0] += conteos_monitor[0];
                            conteos[1] += conteos_monitor[1];
                            conteos[2] += conteos_monitor[2];

                        }


                        
                    } else {

                        numero_monitores = $("#collapse" + usuario["id"] + " .panel-heading.practicante").children().length;

                        for (var monitor = 0; monitor < numero_monitores; monitor++) {

                            var collapse_name = $("#collapse" + usuario["id"] + " .panel-heading.practicante:eq(" + monitor + ")").find('a').attr('href');
                            var id_monitor = collapse_name.split("#collapse")[1];
                            var usuario_monitor = [];

                            usuario_monitor["id"] = id_monitor;
                            usuario_monitor["namerol"] = "monitor_ps";
                            conteos_monitor = realizar_conteo(usuario_monitor, "practicante");
                            $("label[for='revisado_monitor_" + id_monitor + "']").html(conteos_monitor[0]);
                            $("label[for='norevisado_monitor_" + id_monitor + "']").html(conteos_monitor[1]);
                            $("label[for='total_monitor_" + id_monitor + "']").html(conteos_monitor[2]);


                            conteos[0] += conteos_monitor[0];
                            conteos[1] += conteos_monitor[1];
                            conteos[2] += conteos_monitor[2];

                        }
                    }

                    //Total trackings
                    return conteos;

                    //Otherwise, it is a 'profesional' role
                } else if (usuario["namerol"] == 'profesional_ps') {
                    conteos = [0, 0, 0];
                    var numero_practicantes = $('.panel-heading.profesional').children().length;
                    var conteos_practicantes = [];

                    for (var practicante = 0; practicante < numero_practicantes; practicante++) {
                        var collapse_name = $(".panel-heading.profesional:eq(" + practicante + ")").find('a').attr('href');
                        var id_practicante = collapse_name.split("#collapse")[1];
                        var usuario_practicante = [];
                        usuario_practicante["id"] = id_practicante;
                        usuario_practicante["namerol"] = "practicante_ps";
                        conteos_practicantes = realizar_conteo(usuario_practicante, "practicante");
                        $("label[for='revisado_practicante_" + id_practicante + "']").html(conteos_practicantes[0]);
                        $("label[for='norevisado_practicante_" + id_practicante + "']").html(conteos_practicantes[1]);
                        $("label[for='total_practicante_" + id_practicante + "']").html(conteos_practicantes[2]);
                        conteos[0] += conteos_practicantes[0];
                        conteos[1] += conteos_practicantes[1];
                        conteos[2] += conteos_practicantes[2];
                    }
                    //Total trackings
                    return conteos;

                }




            }

            /**
             * @method send_email_new_form
             * @desc Sends an email to a monitor, given his id, text message, date, name.
             * @param {instance} instance current instance 
             * @return {void}
             */
            function send_email_new_form(instance){

                $('body').on('click', '#send_observation', function() {
                    var form = $("form").serializeArray(),dataObj = {};


                    $(form).each(function(i, field){
                        dataObj[field.name] = field.value;
                    });


                    var id_register = dataObj['id_registro'];
                    var text = $("#observation_text");
                    console.log()


                    if (text.val() == "") {
                        swal({
                            title: "Para enviar una observación debe llenar el campo correspondiente",
                            html: true,
                            type: "error",
                            confirmButtonColor: "#d51b23"
                        });
                    } else {
                        // Gets text message and monitor id to send the email
                        var tracking_type = 'individual';
                        var monitor_code = $('.id_creado_por').find('input').val();
                        var date = $('.fecha').find('input').val();
                        var message_to_send = text.val();
                        var semester=$("#periodos").val();
                        var place = $('.lugar').find('input').val();

                        //Text area is clear again
                        var answer = "";

                        //Ajax function to send message
                        $.ajax({
                            type: "POST",
                            data: {
                                id_tracking: id_register,
                                type: "send_email_to_user",
                                form: "new_form",
                                tracking_type: tracking_type,
                                monitor_code: monitor_code,
                                date: date,
                                message_to_send: message_to_send,
                                semester:semester,
                                instance:instance,
                                place:place
                            },
                            url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                            async: false,
                            success: function(msg) {
                                //If it was successful...
                                console.log(msg);

                                if (msg != "Error") {
                                    swal({
                                        title: "Correo enviado",
                                        html: true,
                                        type: "success",
                                        confirmButtonColor: "#d51b23"
                                    });
                                    text.val("");

                                } else {
                                    console.log("mensaje error : "+msg);
                                    swal({
                                        title: "error al enviar el correo al monitor",
                                        html: true,
                                        type: "error",
                                        confirmButtonColor: "#d51b23"
                                    });
                                }
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                console.log("mensaje error : "+msg);
                                swal({
                                    title: "error al enviar el correo",
                                    html: true,
                                    type: "error",
                                    confirmButtonColor: "#d51b23"
                                });
                            },
                        });
                    }
                });


            }

            /**
             * @method enviar_correo
             * @desc Sends an email to a monitor, given his id, text message, date, name.
             * @param {instance} instance current instance 
             * @return {void}
             */
            function enviar_correo(instance) {

                $('body').on('click', '.btn.btn-info.btn-lg.botonCorreo', function() {

                    var id_registro = $(this).attr('value');
                    var texto = $("#textarea_" + id_registro);
                    if (texto.val() == "") {
                        swal({
                            title: "Para enviar una observación debe llenar el campo correspondiente",
                            html: true,
                            type: "error",
                            confirmButtonColor: "#d51b23"
                        });
                    } else {
                        // Gets text message and monitor id to send the email
                        var particionar_informacion = texto.attr('name').split("_");
                        var tipo = particionar_informacion[0];
                        var codigoN1 = particionar_informacion[1];
                        var codigoN2 = particionar_informacion[2];
                        var fecha = particionar_informacion[3];
                        var nombre = particionar_informacion[4];
                        var mensaje_enviar = texto.val();

                        //Text area is clear again
                        var respuesta = "";

                        //Ajax function to send message
                        $.ajax({
                            type: "POST",
                            data: {
                                type: "send_email_to_user",
                                tipoSeg: tipo,
                                codigoEnviarN1: codigoN1,
                                codigoEnviarN2: codigoN2,
                                fecha: fecha,
                                nombre: nombre,
                                message: mensaje_enviar
                            },
                            url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                            async: false,
                            success: function(msg) {
                                //If it was successful...

                                if (msg != "Error") {
                                    swal({
                                        title: "Correo enviado",
                                        html: true,
                                        type: "success",
                                        confirmButtonColor: "#d51b23"
                                    });
                                    texto.val("");

                                } else {
                                    swal({
                                        title: "error al enviar el correo al monitor",
                                        html: true,
                                        type: "error",
                                        confirmButtonColor: "#d51b23"
                                    });
                                }
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {
                                swal({
                                    title: "error al enviar el correo",
                                    html: true,
                                    type: "error",
                                    confirmButtonColor: "#d51b23"
                                });
                            },
                        });
                    }
                });
            }


            /**
             * @method modificar_seguimiento
             * @desc Function that modifies a specific track, then every field is filled with old information able to modify
             * @param {id} id_usuario 
             * @return {void}
             */
            function modificar_seguimiento(id_usuario) {


                $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
                    //Gets all fields/values to modify
                    var id = $(this).attr("value");
                    var profesional = "",
                        practicante = "";
                    var combo_hora_inicio = document.getElementById("h_ini_" + id);
                    var combo_hora_fin = document.getElementById("h_fin_" + id);
                    var combo_min_inicio = document.getElementById("m_ini_" + id);
                    var combo_min_fin = document.getElementById("m_fin_" + id);
                    var hora_inicial = combo_hora_inicio.options[combo_hora_inicio.selectedIndex].text;
                    var hora_final = combo_hora_fin.options[combo_hora_fin.selectedIndex].text;
                    var min_inicial = combo_min_inicio.options[combo_min_inicio.selectedIndex].text;
                    var min_final = combo_min_fin.options[combo_min_fin.selectedIndex].text;
                    var validar = validarHoras(hora_inicial, hora_final, min_inicial, min_final);
                    send_email(id, id_usuario);
                    if (validar == "") {
                        if ($("#profesional_" + id).is(':checked')) {
                            profesional = 1;
                        } else {
                            profesional = 0;
                        }

                        if ($("#practicante_" + id).is(':checked')) {
                            practicante = 1;
                        } else {
                            practicante = 0;
                        }

                        var $tbody = $(this).parent().parent().parent();
                        var idSeguimientoActualizar = $(this).attr('value');
                        var lugar = $tbody.find("#lugar_" + idSeguimientoActualizar).val();
                        var tema = $tbody.find("#tema_" + id).val();
                        var objetivos = $tbody.find("#objetivos_" + id).val();
                        var fecha = $tbody.find("#fecha_" + id).val();
                        var h_inicial = hora_inicial + ":" + min_inicial;
                        var h_final = hora_final + ":" + min_final;
                        var obindividual = $tbody.find("#obindividual_" + id).val();
                        var riesgoIndividual = $("input[name='riesgo_individual_" + id + "']:checked").val();
                        if (riesgoIndividual == undefined) {
                            riesgoIndividual = "0";
                        }

                        var obfamiliar = $tbody.find("#obfamiliar_" + id).val();
                        var riesgoFamiliar = $("input[name='riesgo_familiar_" + id + "']:checked").val();
                        if (riesgoFamiliar == undefined) {
                            riesgoFamiliar = "0";
                        }

                        var obacademico = $tbody.find("#obacademico_" + id).val();
                        var riesgoAcademico = $("input[name='riesgo_academico_" + id + "']:checked").val();
                        if (riesgoAcademico == undefined) {
                            riesgoAcademico = "0";
                        }

                        var obeconomico = $tbody.find("#obeconomico_" + id).val();
                        var riesgoEconomico = $("input[name='riesgo_economico_" + id + "']:checked").val();
                        if (riesgoEconomico == undefined) {
                            riesgoEconomico = "0";
                        }

                        var obuniversitario = $tbody.find("#obuniversitario_" + id).val();
                        var riesgoUniversitario = $("input[name='riesgo_universitario_" + id + "']:checked").val();
                        if (riesgoUniversitario == undefined) {
                            riesgoUniversitario = "0";
                        }

                        var observacionesGeneral = $tbody.find("#observacionesGeneral_" + id).val();


                        if (lugar == "" || tema == "" || objetivos == "") {
                            swal({
                                title: "Debe ingresar los datos completamente",
                                html: true,
                                type: "warning",
                                confirmButtonColor: "#d51b23"
                            });
                        } else {
                            if (!((obindividual != "" && riesgoIndividual == 0) || (obfamiliar != "" && riesgoFamiliar == 0) || (obacademico != "" && riesgoAcademico == 0) || (obeconomico != "" && riesgoEconomico == 0) || (obuniversitario != "" && riesgoUniversitario == 0) || (obindividual == "" && riesgoIndividual > 0) || (obfamiliar == "" && riesgoFamiliar > 0) || (obacademico == "" && riesgoAcademico > 0) || (obeconomico == "" && riesgoEconomico > 0) || (obuniversitario == "" && riesgoUniversitario > 0))) {
                                var seguimiento = new Object();
                                seguimiento.id = idSeguimientoActualizar;
                                seguimiento.lugar = lugar;
                                seguimiento.tema = tema;
                                seguimiento.objetivos = objetivos;
                                seguimiento.individual = obindividual;
                                seguimiento.individual_riesgo = riesgoIndividual;
                                seguimiento.familiar_desc = obfamiliar;
                                seguimiento.familiar_riesgo = riesgoFamiliar;
                                seguimiento.academico = obacademico;
                                seguimiento.academico_riesgo = riesgoAcademico;
                                seguimiento.economico = obeconomico;
                                seguimiento.economico_riesgo = riesgoEconomico;
                                seguimiento.vida_uni = obuniversitario;
                                seguimiento.vida_uni_riesgo = riesgoUniversitario;
                                seguimiento.observaciones = observacionesGeneral;
                                seguimiento.revisado_practicante = practicante;
                                seguimiento.revisado_profesional = profesional;
                                seguimiento.fecha = fecha;
                                seguimiento.hora_ini = h_inicial;
                                seguimiento.hora_fin = h_final;
                                //Ajax function calls processing at pilos_tracking_report.php
                                $.ajax({
                                    type: "POST",
                                    data: {
                                        seguimiento: seguimiento,
                                        type: "actualizar_registro",
                                    },
                                    url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                                    async: false,
                                    success: function(msg) {
                                        //Something happened
                                        if (msg == "0") {
                                            swal({
                                                title: "error al actualizar registro",
                                                html: true,
                                                type: "error",
                                                confirmButtonColor: "#d51b23"
                                            });
                                            //In case it was successful
                                        } else if (msg == "1") {
                                            swal("¡Hecho!", "El registro ha sido actualizado",
                                                "success");
                                        } else {
                                            //Risks errors
                                            swal({
                                                title: "Debe ingresar correctamente los riesgos",
                                                html: true,
                                                type: "warning",
                                                confirmButtonColor: "#d51b23"
                                            });
                                        }
                                    },
                                    error: function(msg) {},
                                });
                            } else {
                                swal({
                                    title: "Debe ingresar correctamente los riesgos",
                                    html: true,
                                    type: "warning",
                                    confirmButtonColor: "#d51b23"
                                });
                            }

                        }
                    } else {
                        swal({
                            title: validar,
                            html: true,
                            type: "warning",
                            confirmButtonColor: "#d51b23"
                        });

                    }
                });
            }


            /**
             * @method editar_seguimiento
             * @desc Edit a specific track given existents roles
             * @param {role} namerol user role. It could be whether 'profesional', 'practicante', 'profesional' or 'sistemas'
             * @return {void}
             */
            function editar_seguimiento(namerol) {

                $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
                    var id = $(this).attr("value");
                    var $tbody = $(this).parent().parent().parent();

                    var visto_profesional = false;

                    //Ables fields to edit, acording to the logged user role
                    if (namerol == 'monitor_ps') {
                        visto_profesional = $("#profesional_" + id).is(':checked');

                    } else if (namerol == 'practicante_ps') {
                        $("input[name=practicante]").prop('disabled', false);

                    } else if (namerol == 'profesional_ps') {
                        $("input[name=profesional]").prop('disabled', false);

                    } else if (namerol == 'sistemas') {
                        $("input[name=profesional]").prop('disabled', false);
                        $("input[name=practicante]").prop('disabled', false);

                    }


                    if (visto_profesional == false) {

                        $tbody.find('.editable').removeAttr('readonly');
                        $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
                        $tbody.find('.quitar-ocultar').toggleClass('ocultar');
                        $tbody.find('.radio-ocultar').toggleClass('ocultar');
                        auxiliar_editar(id);
                        seleccionarButtons(id);

                        //In case the track had been checked already
                    } else {
                        swal("¡Advertencia!",
                            "No es posible editar el seguimiento, debido a que ya ha sido revisado por un profesional",
                            "warning");
                    }
                });

            }


            /**
             * @method borrar_seguimiento
             * @desc Function that deletes a specific track determining the existing roles
             * @param {role} namerol user role who is deleting a track
             * @return {void}
             */
            function borrar_seguimiento(namerol) {

                $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
                    var id_registro = $(this).attr('value');
                    var visto_profesional = false;

                    //If it's a monitor, visto_profesional value is consulted
                    if (namerol == 'monitor_ps') {
                        visto_profesional = $("#profesional_" + id).is(':checked');

                    } else if (namerol == 'practicante_ps') {


                    } else if (namerol == 'profesional_ps') {

                    }
                    //If the track hasn't been checked by a 'profesional', track could be deleted
                    if (visto_profesional == false) {
                        swal({
                                title: "¿Seguro que desea eliminar el registro?",
                                text: "No podrás deshacer este paso",
                                type: "warning",
                                showCancelButton: true,
                                cancelButtonText: "No",
                                confirmButtonColor: "#d51b23",
                                confirmButtonText: "Si",
                                closeOnConfirm: false
                            },
                            //Ajax function, processing at pilos_tracking_report.php
                            function() {
                                $.ajax({
                                    type: "POST",
                                    data: {
                                        id: id_registro,
                                        type: "eliminar_registro",
                                    },
                                    url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                                    async: false,
                                    success: function(msg) {
                                        swal({
                                            title: msg.title,
                                            html: true,
                                            text: msg.msg,
                                            type: msg.type,
                                            confirmButtonColor: "#d51b23"
                                        });
                                        setTimeout('document.location.reload()', 500);
                                    },
                                    dataType: 'json',
                                    cache: "false",
                                    error: function(msg) {},
                                });
                            });
                    }

                });
            }

            /**
             * @method cancelar_edicion
             * @desc Cancel an edition of a specific track, no matter the user role
             * @param {role} namerol user role who is canceling edition
             * @return {void}
             */
            function cancelar_edicion(namerol) {

                $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
                    var id = $(this).attr("value");

                    if (namerol == 'monitor_ps') {

                    } else if (namerol == 'practicante_ps') {
                        $("input[name=practicante]").prop('disabled', true);

                    } else if (namerol == 'profesional_ps') {
                        $("input[name=profesional]").prop('disabled', true);

                    } else if (namerol == 'sistemas') {
                        $("input[name=profesional]").prop('disabled', true);
                        $("input[name=practicante]").prop('disabled', true);

                    }

                    //read only track
                    var $tbody = $(this).parent().parent().parent();
                    $tbody.find('.editable').attr('readonly', true);
                    $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
                    $tbody.find('.quitar-ocultar').toggleClass('ocultar');
                    $tbody.find('.radio-ocultar').toggleClass('ocultar');
                    auxiliar_cancelar(id);
                });
            }

            /**
             * @method limpiar_riesgos
             * @desc clears risks radio buttons and descriptions
             * @return {void}
             */
            function limpiar_riesgos() {

                $('body').on('click', '.limpiar', function() {
                    var elemento = $(this).closest("div").attr('id').split("_");
                    var id = elemento[2].split("div").pop();
                    switch (elemento[1]) {
                        case 'individual':
                            $("#obindividual_" + id).val("");
                            auxiliar_limpiar("riesgo_individual_", id);
                            break;

                        case 'familiar':
                            $("#obfamiliar_" + id).val("");
                            auxiliar_limpiar("riesgo_familiar_", id);
                            break;

                        case 'academico':
                            $("#obacademico_" + id).val("");
                            auxiliar_limpiar("riesgo_academico_", id);
                            break;

                        case 'economico':
                            $("#obeconomico_" + id).val("");
                            auxiliar_limpiar("riesgo_economico_", id);
                            break;

                        case 'universitario':
                            $("#obuniversitario_" + id).val("");
                            auxiliar_limpiar("riesgo_universitario_", id);
                            break;

                        default:
                            //alert("Dato invalido");
                            $(this).parent().find(".opcionesRadio").find('div').each(function(){
                        $(this).find("label").find("input").prop("checked", false);
                    });
                            break;
                    }
                });
            }


            //--------Auxiliary functions


            /**
             * @method send_email
             * @desc 
             * @param {id} id_seguimiento track id
             * @param {id} id_usuario user id
             * @return {void}
             */
            function send_email(id_seguimiento, id_usuario) {


                var high_risk_array = new Array();
                var observations_array = new Array();

                //Obtains every risk
                var high_individual_risk = $('input:radio[name=riesgo_individual_' + id_seguimiento + ']:checked').val();
                var high_familiar_risk = $('input:radio[name=riesgo_familiar_' + id_seguimiento + ']:checked').val();
                var high_academic_risk = $('input:radio[name=riesgo_academico_' + id_seguimiento + ']:checked').val();
                var high_economic_risk = $('input:radio[name=riesgo_economico_' + id_seguimiento + ']:checked').val();
                var high_life_risk = $('input:radio[name=riesgo_universitario_' + id_seguimiento + ']:checked').val();

                //In case there is any risk = 3 then it's added to a high risk array
                if (high_individual_risk == '3') {
                    high_risk_array.push('Individual');
                    observations_array.push($('#obindividual_' + id_seguimiento).val());
                }
                if (high_familiar_risk == '3') {
                    high_risk_array.push('Familiar');
                    observations_array.push($('#obfamiliar_' + id_seguimiento).val());
                }
                if (high_academic_risk == '3') {
                    high_risk_array.push('Académico');
                    observations_array.push($('#obacademico_' + id_seguimiento).val());
                }
                if (high_economic_risk == '3') {
                    high_risk_array.push('Económico');
                    observations_array.push($('#obeconomico_' + id_seguimiento).val());
                }
                if (high_life_risk == '3') {
                    high_risk_array.push('Vida universitaria');
                    observations_array.push($('#obuniversitario_' + id_seguimiento).val());
                }

                var data_email = new Array();
                data_email.push({
                    name: "function",
                    value: "send_email"
                });
                data_email.push({
                    name: "id_student_moodle",
                    value: id_usuario
                });
                data_email.push({
                    name: "id_student_pilos",
                    value: id_seguimiento
                });
                //High risks are sent via email
                data_email.push({
                    name: "risk_array",
                    value: high_risk_array
                });
                // Observation of every high risk
                data_email.push({
                    name: "observations_array",
                    value: observations_array
                });
                data_email.push({
                    name: "date",
                    value: $('#fecha_' + id_seguimiento).val()
                });
                data_email.push({
                    name: "url",
                    value: window.location
                });



                //In case there's any high risk, call ajax function to send the email. Processing function at send_risk_email.php"
                if (high_risk_array.length != 0) {
                    $.ajax({
                        type: "POST",
                        data: data_email,
                        url: "../managers/pilos_tracking/send_risk_email.php",
                        success: function(msg) {
                            console.log(msg);
                        },
                        dataType: "text",
                        cache: "false",
                        error: function(msg) {
                            console.log(msg)
                        }
                    });
                }
            }


            /**
             * @method consultar_periodos
             * @desc 
             * @param {*} instance 
             * @param {*} namerol 
             * @return {void}
             */
            function consultar_periodos(instance, namerol) {
                $("#periodos").change(function() {
                    var periodo_escogido = $("#periodos").val();
                    $.ajax({
                        type: "POST",
                        data: {
                            id: periodo_escogido,
                            instance: instance,
                            type: "actualizar_personas"
                        },
                        url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                        async: false,
                        success: function(msg) {


                            $('#personas').empty();
                            $("#personas").select2({
                                placeholder: "Seleccionar persona",
                                language: {
                                    noResults: function() {
                                        return "No hay resultado";
                                    },
                                    searching: function() {
                                        return "Buscando..";
                                    }
                                }
                            });
                            if (namerol == 'sistemas') {
                                var inicio = '<option value="">Seleccionar persona</option>';

                                $("#personas").attr('selectedIndex', '-1').find("option:selected").removeAttr("selected");
                                $('#personas').append(inicio + msg);

                            }

                        },
                        dataType: "text",
                        cache: "false",
                        error: function(msg) {
                            alert("error al cargar personas");
                        },
                    });
                });

            }

            /**
             * @method verificar_profesional
             * @desc Verifies if a 'profesional' wants to marks as checked a track
             * @return {void}
             */
            function verificar_profesional() {
                $('input[name="profesional"]').click(function() {
                    if ($(this).is(':checked')) {
                        swal({
                                title: "¿Seguro que desea cambiar estado a revisado?",
                                text: "En caso de modificar el seguimiento no podrá volverlo a editar",
                                type: "warning",
                                showCancelButton: true,
                                cancelButtonText: "No",
                                confirmButtonColor: "#d51b23",
                                confirmButtonText: "Si",
                                closeOnConfirm: true
                            },
                            //Confirms to check the track
                            function(isConfirm) {
                                if (isConfirm == false) {
                                    $('input[name="profesional"]').prop('checked', false);
                                }
                            });
                    }
                });
            }

            
            /**
             * @method validarHoras
             * @desc Validates the input hours for a track are correct
             * @param {integer} h_ini initial hour
             * @param {integer} h_fin final hour
             * @param {integer} m_ini initial minute
             * @param {integer} m_fin final minute
             * @return {string} In case the hours are correct it shows nothing, otherwise deploys a message error (same hour or initial hour is later than final hour)
             */
            function validarHoras(h_ini, h_fin, m_ini, m_fin) {
                var detalle = "";
                if (h_ini > h_fin) {
                    detalle += "* La hora final debe ser mayor a la inicial<br>";
                } else if (h_ini == h_fin) {
                    if (m_ini > m_fin) {
                        isvalid = false;
                        detalle += "* La hora final debe ser mayor a la inicial<br>";
                    } else {
                        if (m_ini == m_fin) {
                            detalle += "* Las horas seleccionadas deben ser diferentes<br>";
                        }
                    }
                }
                //Returns nothinf if ok, message error if not
                return detalle;
            }

            /**
             * @method initFormSeg
             * @desc Function used to initialize selects of hour/min of each track
             * @param {id} id track id
             * @return {void}
             */
            function initFormSeg(id) {
                var date = new Date();
                var minutes = date.getMinutes();
                var hour = date.getHours();
                //initialize hour
                var hora = "";
                for (var i = 0; i < 24; i++) {
                    if (i == hour) {
                        if (hour < 10) hour = "0" + hour;
                        hora += "<option value=\"" + hour + "\" selected>" + hour + "</option>";
                    } else if (i < 10) {
                        hora += "<option value=\"0" + i + "\">0" + i + "</option>";
                    } else {
                        hora += "<option value=\"" + i + "\">" + i + "</option>";
                    }
                }
                var min = "";
                for (var i = 0; i < 60; i++) {

                    if (i == minutes) {
                        if (minutes < 10) minutes = "0" + minutes;
                        min += "<option value=\"" + minutes + "\" selected>" + minutes + "</option>";
                    } else if (i < 10) {
                        min += "<option value=\"0" + i + "\">0" + i + "</option>";
                    } else {
                        min += "<option value=\"" + i + "\">" + i + "</option>";
                    }
                }
                //fill every selector with posible hours and minutes to choose (hour: 0-23, min: 0-59)
                $('#h_ini_' + id).append(hora);
                $('#m_ini_' + id).append(min);
                $('#h_fin_' + id).append(hora);
                $('#m_fin_' + id).append(min);
                $('#seguimiento #m_fin').append(min);
            }

             /**
              * @method actualizar_riesgo
              * @desc Function used to change colour when risk radio buttons is modified
              * @return {void}
              */
            function actualizar_riesgo() {
                $(document).ready(function() {

                    $('input:radio').change(function() {
                        var id = $(this).parent().parent().parent().attr('id');
                        var tipo_riesgo = $(this).attr('value');

                        if (tipo_riesgo == 1) {
                            $("#" + id).removeClass();
                            $("#" + id).addClass('table-info-pilos col-sm-12 riesgo_' + 'bajo');
                        } else if (tipo_riesgo == 2) {
                            $("#" + id).removeClass();
                            $("#" + id).addClass('table-info-pilos col-sm-12 riesgo_' + 'medio');
                        } else if (tipo_riesgo == 3) {
                            $("#" + id).removeClass();
                            $("#" + id).addClass('table-info-pilos col-sm-12 riesgo_' + 'alto');
                        }
                    });
                });
            }

            /**
             * @method auxiliar_cancelar
             * @desc hide and show buttons when 'cancelar' button is clicked
             * @param {id} id  track id 
             * @return {void}
             */

            function auxiliar_cancelar(id) {
                $("#titulo_fecha_" + id).hide();
                $("#borrar_" + id).show();
                $("#editar_" + id).show();
                $("#enviar_" + id).show();
                $("#hora_final_" + id).show();
                $("#mod_hora_final_" + id).hide();
                $("#hora_inicial_" + id).show();
                $("#mod_hora_ini_" + id).hide();
            }

            /**
             * @method auxiliar_editar
             * @desc hide and show buttons, line date and hours up when 'editar' button is clicked
             * @param {id} id track id
             * @return {void}
             */
            function auxiliar_editar(id) {
                $("#borrar_" + id).hide();
                $("#editar_" + id).hide();
                $("#enviar_" + id).hide();
                $("#hora_final_" + id).hide();
                $("#hora_inicial_" + id).hide();
                $("#titulo_fecha_" + id).show();
                $("#mod_hora_final_" + id).show();
                $("#mod_hora_ini_" + id).show();

                var f1 = $("#h_inicial_texto_" + id).val();
                var f2 = $("#h_final_texto_" + id).val();
                var array_f1 = f1.split(":");
                var array_f2 = f2.split(":");

                initFormSeg(id);
                //Get hour from system

                $("#h_ini_" + id + " option[value=" + array_f1[0] + "]").prop("selected", true);
                $("#m_ini_" + id + " option[value=" + array_f1[1] + "]").prop("selected", true);
                $("#h_fin_" + id + " option[value=" + array_f2[0] + "]").prop("selected", true);
                $("#m_fin_" + id + " option[value=" + array_f2[1] + "]").prop("selected", true);

            }


            /**
             * @method auxiliar_limpiar
             * @desc clears risk fields and unmark their priority
             * @param {string} texto text to remove
             * @param {id} id track id
             * @return {void}
             */
            function auxiliar_limpiar(texto, id) {
                $('input:radio[name=' + texto+id + ']').parent().parent().parent().removeClass("riesgo_bajo");
                $('input:radio[name=' + texto+id + ']').parent().parent().parent().removeClass("riesgo_medio");
                $('input:radio[name=' + texto+id + ']').parent().parent().parent().removeClass("riesgo_alto");


                var text = '"' + texto.replace("#", "") + id + '"';
                $('input:radio[name=' + text + ']').each(function(i) {
                    this.checked = false;
                });

            }


            /**
             * @method revisado_profesional
             * @desc In case a track is checked by a 'profesional', edit, delete and observations buttons will be hidden
             * @param {id} id track id
             * @return {void}
             */
            function revisado_profesional(id) {
                if ($("#profesional_" + id).is(':checked')) {
                    $("#borrar_" + id).hide();
                    $("#editar_" + id).hide();
                    $("#enviar_" + id).hide();
                }
            }


            function get_instance(){
                //We get the current instance id

                var informacionUrl = window.location.search.split("&");
                for (var i = 0; i < informacionUrl.length; i++) {
                    var elemento = informacionUrl[i].split("=");
                    if (elemento[0] == "?instanceid" || elemento[0] == "instanceid") {
                        var instance = elemento[1];
                    }
                }
                return instance;
            }

            /**
             * @method seleccionarButtons
             * @desc Select all radiobuttons according to risk priority
             * @param {id} id_seguimiento track id
             * @return {void}
             */
            function seleccionarButtons(id_seguimiento) {


                //individual risk (Riesgo individual)
                if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_bajo')) {
                    $("input[name=riesgo_individual_" + id_seguimiento + "][value=1]").prop('checked', 'checked');


                } else if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_medio')) {
                    $("input[name=riesgo_individual_" + id_seguimiento + "][value=2]").prop('checked', 'checked');
                } else if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_alto')) {
                    $("input[name=riesgo_individual_" + id_seguimiento + "][value=3]").prop('checked', 'checked');
                } else {
                    $("input[name=riesgo_individual_" + id_seguimiento + "][value=0]").prop('checked', 'checked');

                }

                //familiar risk (Riesgo familiar)
                if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_bajo')) {
                    $("input[name=riesgo_familiar_" + id_seguimiento + "][value=1]").prop('checked', 'checked');
                } else if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_medio')) {
                    $("input[name=riesgo_familiar_" + id_seguimiento + "][value=2]").prop('checked', 'checked');
                } else if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_alto')) {
                    $("input[name=riesgo_familiar_" + id_seguimiento + "][value=3]").prop('checked', 'checked');
                } else {
                    $("input[name=riesgo_familiar_" + id_seguimiento + "][value=0]").prop('checked', 'checked');

                }

                //Academic risk(Riesgo academico)
                if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_bajo')) {
                    $("input[name=riesgo_academico_" + id_seguimiento + "][value=1]").prop('checked', 'checked');
                } else if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_medio')) {
                    $("input[name=riesgo_academico_" + id_seguimiento + "][value=2]").prop('checked', 'checked');
                } else if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_alto')) {
                    $("input[name=riesgo_academico_" + id_seguimiento + "][value=3]").prop('checked', 'checked');
                } else {
                    $("input[name=riesgo_academico_" + id_seguimiento + "][value=0]").prop('checked', 'checked');

                }

                //Economic risk(Riesgo economico)
                if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_bajo')) {
                    $("input[name=riesgo_economico_" + id_seguimiento + "][value=1]").prop('checked', 'checked');
                } else if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_medio')) {
                    $("input[name=riesgo_economico_" + id_seguimiento + "][value=2]").prop('checked', 'checked');
                } else if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_alto')) {
                    $("input[name=riesgo_economico_" + id_seguimiento + "][value=3]").prop('checked', 'checked');
                } else {
                    $("input[name=riesgo_economico_" + id_seguimiento + "][value=0]").prop('checked', 'checked');

                }

                //Universitary risk(Riesgo universitario)
                if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_bajo')) {
                    $("input[name=riesgo_universitario_" + id_seguimiento + "][value=1]").prop('checked', 'checked');
                } else if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_medio')) {
                    $("input[name=riesgo_universitario_" + id_seguimiento + "][value=2]").prop('checked', 'checked');
                } else if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_alto')) {
                    $("input[name=riesgo_universitario_" + id_seguimiento + "][value=3]").prop('checked', 'checked');
                } else {
                    $("input[name=riesgo_universitario_" + id_seguimiento + "][value=0]").prop('checked', 'checked');

                }
            }


        }
    };
});