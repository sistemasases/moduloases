requirejs(['jquery', 'bootstrap', 'datatables.net', 'datatables.net-buttons', 'buttons.flash', 'jszip', 'pdfmake', 'vfs_fonts', 'buttons.html5', 'buttons.print', 'sweetalert', 'amd_actions','select2'], function($) {

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

        var usuario="";
    	//Obtenemos el ID de la instancia actual.

        var informacionUrl = window.location.search.split("&");
        for (var i = 0; i < informacionUrl.length; i++) {
            var elemento = informacionUrl[i].split("=");
            if (elemento[0] == "?instanceid" || elemento[0] == "instanceid") {
                var instance = elemento[1];
            }
        }

        //Oculta el div de la parte de sistemas.
        //$(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").hide();
        
        //Se obtiene la información correspondiente al nombre,id,email y rol de la persona conectada.
        $.ajax({
            type: "POST",
            data: {
                type: "getInfo",
                instance: instance
            },
            url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
            async: false,
            success: function(msg) {
            	$data= $.parseJSON(msg);
            	name = $data.username;
            	id = $data.id;
            	email = $data.email; 
            	rol = $data.rol;
            	namerol=$data.name_rol;
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
        //Se muestra la interfaz correspondiente al usuario.
        if (namerol == "monitor_ps") {
            usuario = "monitor";

        }
        else if (namerol == "practicante_ps") {
            usuario ="practicante";
        }
        else if (namerol == "profesional_ps") {

            usuario = "profesional";
        }
        else if (namerol == "sistemas") {
            usuario = "sistemas";
        }

        var usuario = [];
        usuario["id"] = id;
        usuario["name"] = name;
        usuario["namerol"]= namerol;


        crear_conteo(usuario);


        /*Cuando el usuario sea practicante = le es permitido */
        if (namerol == "practicante_ps") {

            $("input[name=profesional]").attr('disabled', true);
            $("input[name=practicante]").attr('disabled', true);

            limpiar_riesgos();
            editar_seguimiento(namerol);
            cancelar_edicion(namerol);
            borrar_seguimiento(namerol);
            modificar_seguimiento();
            actualizar_riesgo();
            enviar_correo(instance);
            consultar_seguimientos_persona(instance,usuario);



        /*Cuando el usuario sea profesional = le es permitido */
        }else if (namerol == "profesional_ps") {
            //se inicia la adicion del evento
            $("input[name=practicante]").attr('disabled', true);
            $("input[name=profesional]").attr('disabled', true);
            limpiar_riesgos();
            cancelar_edicion(namerol);
            editar_seguimiento(namerol);
            modificar_seguimiento();
            borrar_seguimiento(namerol);
            actualizar_riesgo();
            enviar_correo(instance);
            consultar_seguimientos_persona(instance,usuario);
            

        /*Cuando el usuario sea monitor = Le es permitido : */
        }else if (namerol == "monitor_ps") {   
            limpiar_riesgos();
            editar_seguimiento(namerol);
            cancelar_edicion(namerol);
            borrar_seguimiento(namerol);
            modificar_seguimiento();
            actualizar_riesgo();
            consultar_seguimientos_persona(instance,usuario);


        
        /*Cuando el usuario sea sistemas = Le es permitido : */
        }else if(namerol == "sistemas"){
            limpiar_riesgos();
            editar_seguimiento(namerol);
            cancelar_edicion(namerol);
            borrar_seguimiento(namerol);
            modificar_seguimiento();
            actualizar_riesgo();
            enviar_correo(instance);
            anadirEvento(instance);




        }

    });



   

//--------LISTENERS DE LOS ELEMENTOS DE LA PÁGINA.
function consultar_seguimientos_persona(instance,usuario){
            $("#periodos").change(function() {
            if (namerol!='sistemas'){
            var semestre =$("#periodos").val();
            var id_persona = id;
            $.ajax({
                 type: "POST",
                 data: {
                    id_persona: id_persona,
                    id_semestre: semestre,
                    instance: instance,
                    otro : true,
                    type: "consulta_sistemas"
                    },
                    url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                    async: false,


                    success: function(msg) {

                    if(msg==""){
                      $('#reemplazarToogle').html('<label> No se encontraron registros </label>' );
                      crear_conteo(usuario);



                    }else{
                    $('#reemplazarToogle').html(msg);
                    }
                    $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").slideDown( "slow" );
                    crear_conteo(usuario);




                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) { alert("error al consultar seguimientos de personas");},
             });

            }

            
        });
}
/*
 * Funcion para el rol sistemas
 *
 */

 function anadirEvento(instance) {
            $("#personas").val('').change();
            
            //Se activa el select2 cuando el usuario es de sistemas.
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

        consultar_periodos(instance,namerol);


        $('#consultar_persona').on('click', function() {

            var id_persona =$("#personas").children(":selected").attr("value");
            var id_semestre =$("#periodos").children(":selected").attr("value");
            var fechas_epoch=[];



            if(id_persona == undefined){
                swal({
                        title: "Debe escoger una persona para realizar la consulta",
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });
            }else{
                $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").show();

            $.ajax({
                 type: "POST",
                 data: {
                    id_persona: id_persona,
                    id_semestre: id_semestre,
                    instance: instance,
                    type: "consulta_sistemas"
                    },
                    url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                    async: false,


                    success: function(msg) {

                    if(msg==""){
                      $('#reemplazarToogle').html('<label> No se encontraron registros </label>' );

                    }else{
                    $('#reemplazarToogle').html(msg);
                    }
                    $(".well.col-md-10.col-md-offset-1.reporte-seguimiento.oculto").slideDown( "slow" );

                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) { alert("error al consultar seguimientos de personas");},
             });

           }

        });
    }



function crear_conteo(usuario){
    var periodo = $("#periodos option:selected").text();
    var conteo=0;
    var contenedor="";
    
    if(usuario["namerol"] == 'monitor_ps'){
        var conteos_monitor =realizar_conteo(usuario);
        contenedor = '<div class="row"><div class="col-sm-12"><h2>Información monitor - PERIODO :'+periodo+' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :<label for="revisado_monitor_'+conteo+'">'+conteos_monitor[0]+'</label><b></b> - NO Revisados :<label for="norevisado_monitor_'+conteo+'">'+conteos_monitor[1]+'</label><b></b> - Total  :<label for="total_monitor_'+conteo+'">'+conteos_monitor[2]+'</label> <b></b> </span></h4></div></div></div></div>';
        $("#conteo_principal").empty();
        $("#conteo_principal").html(contenedor);


    }else if (usuario["namerol"] == 'practicante_ps'){
        var conteos_practicante =realizar_conteo(usuario);
        contenedor = '<div class="row"><div class="col-sm-12"><h2>Información practicante - PERIODO :'+periodo+' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :'+conteos_practicante[0]+' <b></b> - NO Revisados :'+conteos_practicante[1]+' <b></b> - Total  :'+conteos_practicante[2]+' <b></b> </span></h4></div></div></div></div>';
        $("#conteo_principal").empty();
        $("#conteo_principal").html(contenedor);

    }else if (usuario["namerol"] == 'profesional_ps'){
        var conteos_profesional =  realizar_conteo(usuario);
        contenedor = '<div class="row"><div class="col-sm-12"><h2>Información profesional - PERIODO :'+periodo+' </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  :'+conteos_profesional[0]+' <b></b> - NO Revisados :'+conteos_profesional[1]+' <b></b> - Total  :'+conteos_profesional[2]+' <b></b> </span></h4></div></div></div></div>';
        $("#conteo_principal").empty();
        $("#conteo_principal").html(contenedor);

    }else if(usuario["namerol" ] == 'sistemas'){


    }
}
function realizar_conteo(usuario,dependiente="ninguno"){
    var conteos= [];

    var total_grupal_revisado = 0;
    var total_grupal_norevisado = 0;
    var total_monitor_revisado = 0;
    var total_monitor_norevisado = 0;

    if(usuario["namerol"] == 'monitor_ps'){
    var numero_pares=0;
    var numero_grupales=0;

    if (dependiente =="ninguno"){
    numero_pares = $('.panel-heading.pares').children().length;
    numero_grupales = $('.panel-heading.grupal').children().length;


    }else{
    numero_pares = $("#collapse"+usuario["id"]+" .panel-heading.pares").children().length;
    numero_grupales = $("#collapse"+usuario["id"]+" .panel-heading.grupal").children().length;
    }
    $("label[for='norevisado_grupal_"+usuario["id"]+"']").html(numero_grupales);
    $("label[for='total_grupal_"+usuario["id"]+"']").html(numero_grupales);


    for(var cantidad =0; cantidad<numero_pares;cantidad++){
       total_monitor_revisado += Number($("label[for='revisado_pares_"+ usuario["id"]+"_"+cantidad+"']").text());
       total_monitor_norevisado += Number($("label[for='norevisado_pares_"+ usuario["id"]+"_"+cantidad+"']").text());
    }

    for(var cantidad =0; cantidad<numero_grupales;cantidad++){
       total_grupal_revisado += 0;
       total_grupal_norevisado = numero_grupales;

    }
    total = (total_monitor_revisado+total_grupal_revisado) + (total_monitor_norevisado+total_grupal_norevisado);
    return new Array((total_monitor_revisado+total_grupal_revisado),(total_monitor_norevisado+total_grupal_norevisado), total);
    
    }else if (usuario["namerol"] == 'practicante_ps'){
      var numero_monitores=0;
      conteos =[0,0,0];  
      var conteos_monitor =[ ];

      if(dependiente =="ninguno"){
       numero_monitores = $('.panel-heading.practicante').children().length;
       for(var monitor = 0;monitor<numero_monitores;monitor++){

      var collapse_name =$( ".panel-heading.practicante:eq("+monitor+")" ).find('a').attr('href');
      var id_monitor = collapse_name.split("#collapse")[1];
      var usuario_monitor = [];

      usuario_monitor["id"] = id_monitor;
      usuario_monitor["namerol"] ="monitor_ps";
      conteos_monitor = realizar_conteo(usuario_monitor,"practicante");
      $("label[for='revisado_monitor_"+id_monitor+"']").html(conteos_monitor[0]);
      $("label[for='norevisado_monitor_"+id_monitor+"']").html(conteos_monitor[1]);
      $("label[for='total_monitor_"+id_monitor+"']").html(conteos_monitor[2]);


      conteos[0]+=conteos_monitor[0];
      conteos[1]+=conteos_monitor[1];
      conteos[2]+=conteos_monitor[2];

    }

    
      }else{
        numero_monitores = $("#collapse"+usuario["id"]+" .panel-heading.practicante").children().length;
      }
              for(var monitor = 0;monitor<numero_monitores;monitor++){

      var collapse_name =$( "#collapse"+usuario["id"]+" .panel-heading.practicante:eq("+monitor+")" ).find('a').attr('href');
      var id_monitor = collapse_name.split("#collapse")[1];
      var usuario_monitor = [];

      usuario_monitor["id"] = id_monitor;
      usuario_monitor["namerol"] ="monitor_ps";
      conteos_monitor = realizar_conteo(usuario_monitor,"practicante");
      $("label[for='revisado_monitor_"+id_monitor+"']").html(conteos_monitor[0]);
      $("label[for='norevisado_monitor_"+id_monitor+"']").html(conteos_monitor[1]);
      $("label[for='total_monitor_"+id_monitor+"']").html(conteos_monitor[2]);


      conteos[0]+=conteos_monitor[0];
      conteos[1]+=conteos_monitor[1];
      conteos[2]+=conteos_monitor[2];

    }



    return conteos;

    }else if(usuario["namerol"] =='profesional_ps'){
     conteos =[0,0,0];
     var numero_practicantes = $('.panel-heading.profesional').children().length;
     var conteos_practicantes = [];

     for(var practicante=0;practicante<numero_practicantes;practicante++){
      var collapse_name =$(".panel-heading.profesional:eq("+practicante+")" ).find('a').attr('href');
      var id_practicante = collapse_name.split("#collapse")[1];
      var usuario_practicante = [];
      usuario_practicante["id"] = id_practicante;
      usuario_practicante["namerol"] ="practicante_ps";
      conteos_practicantes =realizar_conteo(usuario_practicante,"practicante");
      $("label[for='revisado_practicante_"+id_practicante+"']").html(conteos_practicantes[0]);
      $("label[for='norevisado_practicante_"+id_practicante+"']").html(conteos_practicantes[1]);
      $("label[for='total_practicante_"+id_practicante+"']").html(conteos_practicantes[2]);
      conteos[0]+=conteos_practicantes[0];
      conteos[1]+=conteos_practicantes[1];
      conteos[2]+=conteos_practicantes[2];
     }
     return conteos;

    }




}

/*
 * Funcion para enviar correos.
 *
 */

function enviar_correo(instance){

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
                }else {
                    //se recupera el mensaje y el id del monitor al que se va a enviar el mensaje
                    var particionar_informacion = texto.attr('name').split("_");
                    //alert(particionar_informacion[4]);
                    var tipo = particionar_informacion[0];
                    var codigoN1 = particionar_informacion[1];
                    var codigoN2 = particionar_informacion[2];
                    var fecha = particionar_informacion[3];
                    var nombre = particionar_informacion[4];
                    var mensaje_enviar = texto.val();

                    //se limpia el textarea
                    texto.val("");
                    var respuesta = "";

                    //se llama el ajax para enviar el mensaje
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
                        url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                        async: false,
                        success: function(msg) {
                            //si el envio del mensaje fue exitoso
                            if (msg == 1) {
                                swal({
                                    title: "Correo enviado",
                                    html: true,
                                    type: "success",
                                    confirmButtonColor: "#d51b23"
                                });
                            }
                            else {
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



/*
 * Función para modificar un seguimiento determinado.
 *
 */
function modificar_seguimiento(){


        $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
        var id = $(this).attr("value");
        var profesional = "",practicante = "";
        var combo_hora_inicio = document.getElementById("h_ini_" + id);
        var combo_hora_fin = document.getElementById("h_fin_" + id);
        var combo_min_inicio = document.getElementById("m_ini_" + id);
        var combo_min_fin = document.getElementById("m_fin_" + id);
        var hora_inicial = combo_hora_inicio.options[combo_hora_inicio.selectedIndex].text;
        var hora_final = combo_hora_fin.options[combo_hora_fin.selectedIndex].text;
        var min_inicial = combo_min_inicio.options[combo_min_inicio.selectedIndex].text;
        var min_final = combo_min_fin.options[combo_min_fin.selectedIndex].text;
        var validar = validarHoras(hora_inicial, hora_final, min_inicial, min_final);

        if (validar == "") {
            if ($("#profesional_" + id).is(':checked')) {
                  profesional = 1;
                }else{
                  profesional = 0;
                }

            if ($("#practicante_" + id).is(':checked')) {
                   practicante = 1;
                }else{
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
             }else{
                  if (!((obindividual != "" && riesgoIndividual == 0) || (obfamiliar != "" && riesgoFamiliar == 0) || (obacademico != "" && riesgoAcademico == 0) || (obeconomico != "" && riesgoEconomico == 0) || (obuniversitario != "" && riesgoUniversitario == 0) || (obindividual == "" && riesgoIndividual > 0) || (obfamiliar == "" && riesgoFamiliar > 0) || (obacademico == "" && riesgoAcademico > 0) || (obeconomico == "" && riesgoEconomico > 0) || (obuniversitario == "" && riesgoUniversitario > 0))) {
                    var seguimiento =new Object();
                    seguimiento.id = idSeguimientoActualizar;
                    seguimiento.lugar = lugar;
                    seguimiento.tema = tema;
                    seguimiento.objetivos = objetivos;
                    seguimiento.individual = obindividual;
                    seguimiento.individual_riesgo= riesgoIndividual;
                    seguimiento.familiar_desc= obfamiliar;
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
                    $.ajax({
                            type: "POST",
                             data: {
                                seguimiento:seguimiento,
                                type: "actualizar_registro",
                            },
                             url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
                             async: false,
                                success: function(msg) {
                                    if (msg == "0") {
                                        swal({
                                            title: "error al actualizar registro",
                                            html: true,
                                            type: "error",
                                            confirmButtonColor: "#d51b23"
                                        });
                                    }
                                    else if (msg == "1") {
                                        swal("¡Hecho!", "El registro ha sido actualizado",
                                            "success");
                                    }
                                    else {
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
                        }
                        else {
                            swal({
                                title: "Debe ingresar correctamente los riesgos",
                                html: true,
                                type: "warning",
                                confirmButtonColor: "#d51b23"
                            });
                        }

                    }
                }
                else {
                    swal({
                        title: validar,
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });

                }
            });
}



 /*
 * Función para editar un seguimiento determinado dado los roles existentes.
 *
 */
function editar_seguimiento(namerol){

    $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
        var id = $(this).attr("value");
        var $tbody = $(this).parent().parent().parent();

        var visto_profesional = false;

        if(namerol == 'monitor_ps'){
         visto_profesional = $("#profesional_" + id).is(':checked');

        }else if (namerol == 'practicante_ps'){
          $("input[name=practicante]").attr('disabled', false);

        }else if (namerol =='profesional_ps'){
          $("input[name=profesional]").attr('disabled', false);

        }


        if(visto_profesional == false){

        $tbody.find('.editable').removeAttr('readonly');
        $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
        $tbody.find('.quitar-ocultar').toggleClass('ocultar');
        $tbody.find('.radio-ocultar').toggleClass('ocultar');
        auxiliar_editar(id);
        seleccionarButtons(id);

        }else{
            swal("¡Advertencia!",
                "No es posible editar el seguimiento, debido a que ya ha sido revisado por un profesional",
                "warning");}
        });

    }


/*
 * Función para borrar un seguimiento determinado dado los roles existentes.
 *
 */
function borrar_seguimiento(namerol){

    $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
    var id_registro = $(this).attr('value');
    var visto_profesional = false;

    if(namerol == 'monitor_ps'){
      visto_profesional = $("#profesional_" + id).is(':checked');

    }else if (namerol == 'practicante_ps'){

    
    }else if (namerol =='profesional_ps'){
    
    } 
    if (visto_profesional  == false){
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
        function() {
        $.ajax({
            type: "POST",
            data: {
                    id: id_registro,
                    type: "eliminar_registro",
                   },
            url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
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


 /*
 * Función para cancelar la edición de un seguimiento determinado dado cualquiera de los roles existentes.
 *
 */
function cancelar_edicion(namerol){

        $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
            var id = $(this).attr("value");

        if(namerol == 'monitor_ps'){

        }else if (namerol == 'practicante_ps'){
          $("input[name=practicante]").attr('disabled', true);

        }else if (namerol =='profesional_ps'){
          $("input[name=profesional]").attr('disabled', true);

        }
             var $tbody = $(this).parent().parent().parent();
             $tbody.find('.editable').attr('readonly', true);
             $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
             $tbody.find('.quitar-ocultar').toggleClass('ocultar');
             $tbody.find('.radio-ocultar').toggleClass('ocultar');
             auxiliar_cancelar(id);
         });
}

/*
 * Función para limpiar la descripción de los riesgos y los radiobuttons seleccionados.
 *
 */
    function limpiar_riesgos(){

    $('body').on('click', '.limpiar', function() {
                var elemento = $(this).closest("div").attr('id').split("_");
                var id = elemento[2].split("div").pop();
                switch (elemento[1]) {
                    case 'individual':
                        $("#obindividual_" + id).val("");
                        auxiliar_limpiar("#riesgo_individual_", id);
                        break;

                    case 'familiar':
                        $("#obfamiliar_" + id).val("");
                        auxiliar_limpiar("#riesgo_familiar_", id);
                        break;

                    case 'academico':
                        $("#obacademico_" + id).val("");
                        auxiliar_limpiar("#riesgo_academico_", id);
                        break;

                    case 'economico':
                        $("#obeconomico_" + id).val("");
                        auxiliar_limpiar("#riesgo_economico_", id);
                        break;

                    case 'universitario':
                        $("#obuniversitario_" + id).val("");
                        auxiliar_limpiar("#riesgo_universitario_", id);
                        break;

                    default:
                        alert("Dato invalido");
                        break;
                }
            });
    }


//--------FUNCIONES AUXILIARES.



function consultar_periodos(instance,namerol){
            $("#periodos").change(function() {
            var periodo_escogido = $( "#periodos" ).val();
              $.ajax({
                 type: "POST",
                 data: {
                    id: periodo_escogido,
                    instance: instance,
                    type: "actualizar_personas"
                    },
                    url: "../../../blocks/ases/managers/seguimiento_pilos/seguimientopilos_report.php",
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
                    if(namerol =='sistemas'){
                    var inicio = '<option value="">Seleccionar persona</option>';

                     $("#personas").attr('selectedIndex', '-1').find("option:selected").removeAttr("selected");
                     $('#personas').append(inicio+msg);
                    
                    }

                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) { alert("error al cargar personas");},
                        });
                    });

}


//Verifica si el profesional desea marcar como revisado el seguimiento.

function verificar_profesional(){
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
        function(isConfirm) {
            if (isConfirm == false) {
             $('input[name="profesional"]').prop('checked', false);
                 }
              });
           }
    });
  }



/*
*  Función que obtiene los mensajes de validación de la hora.
*/
    function validarHoras(h_ini, h_fin, m_ini, m_fin) {
        var detalle = "";
        if (h_ini > h_fin) {
            detalle += "* La hora final debe ser mayor a la inicial<br>";
        }else if (h_ini == h_fin) {
         if (m_ini > m_fin) {
            isvalid = false;
            detalle += "* La hora final debe ser mayor a la inicial<br>";
        }else {
         if (m_ini == m_fin) {
            detalle += "* Las horas seleccionadas deben ser diferentes<br>";
          }
        }
      }
    return detalle;
    }


/*
* Función usada para inicializar los selects de las horas/minutos finales e iniciales de cada seguimiento.
*/
    function initFormSeg(id) {
        var date = new Date();
        var minutes = date.getMinutes();
        var hour = date.getHours();
        //incializar hora
        var hora = "";
        for (var i = 0; i < 24; i++) {
            if (i == hour) {
                if (hour < 10) hour = "0" + hour;
                hora += "<option value=\"" + hour + "\" selected>" + hour + "</option>";
            }
            else if (i < 10) {
                hora += "<option value=\"0" + i + "\">0" + i + "</option>";
            }
            else {
                hora += "<option value=\"" + i + "\">" + i + "</option>";
            }
        }
        var min = "";
        for (var i = 0; i < 60; i++) {

            if (i == minutes) {
                if (minutes < 10) minutes = "0" + minutes;
                min += "<option value=\"" + minutes + "\" selected>" + minutes + "</option>";
            }
            else if (i < 10) {
                min += "<option value=\"0" + i + "\">0" + i + "</option>";
            }
            else {
                min += "<option value=\"" + i + "\">" + i + "</option>";
            }
        }
        $('#h_ini_' + id).append(hora);
        $('#m_ini_' + id).append(min);
        $('#h_fin_' + id).append(hora);
        $('#m_fin_' + id).append(min);
        $('#seguimiento #m_fin').append(min);
    }


   /*
   * Función usada para cambiar color cuando se cambie el radiobutton de riesgo.
   */

   function actualizar_riesgo(){
$(document).ready(function() {

        $('input:radio').change(function() {
        var id =$(this).parent().parent().parent().attr('id');
        var tipo_riesgo = $(this).attr('value');
        
        if(tipo_riesgo == 1){
           $("#" + id).removeClass();
           $("#"+id).addClass('table-info-pilos col-sm-12 riesgo_'+'bajo');
        }else if(tipo_riesgo == 2){
           $("#" + id).removeClass();
           $("#"+id).addClass('table-info-pilos col-sm-12 riesgo_'+'medio');
        }else if(tipo_riesgo == 3){
           $("#" + id).removeClass();
           $("#"+id).addClass('table-info-pilos col-sm-12 riesgo_'+'alto');
           }
        });});
   }


    //Oculta y muestra botones al presionar cancelar.
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

    //Oculta y muestra botones al presionar editar, organiza fecha y horas.
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
        //Seleccionamos la hora deacuerdo al sistema

        $("#h_ini_" + id + " option[value=" + array_f1[0] + "]").attr("selected", true);
        $("#m_ini_" + id + " option[value=" + array_f1[1] + "]").attr("selected", true);
        $("#h_fin_" + id + " option[value=" + array_f2[0] + "]").attr("selected", true);
        $("#m_fin_" + id + " option[value=" + array_f2[1] + "]").attr("selected", true);
    }


    //Limpia los campos de riesgos y deschequea su prioridad.
    function auxiliar_limpiar(texto, id) {
        $(texto + id).removeClass("riesgo_bajo");
        $(texto + id).removeClass("riesgo_medio");
        $(texto + id).removeClass("riesgo_alto");
        var text = '"' + texto.replace("#", "") + id + '"';
        $('input:radio[name=' + text + ']').each(function(i) {
            this.checked = false;
        });

    }


    //En el caso de que el check esté revisado por un profesional 
    //quita los botones de editar,borrar y observaciones.
    function revisado_profesional(id) {
        if ($("#profesional_" + id).is(':checked')) {
            $("#borrar_" + id).hide();
            $("#editar_" + id).hide();
            $("#enviar_" + id).hide();
        }
    }

    //Selecciona los radiobuttons correspondientes con la prioridad del riesgo.
    function seleccionarButtons(id_seguimiento) {


        //Riesgo individual
        if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_bajo')) {
        $("input[name=riesgo_individual_" + id_seguimiento + "][value=1]").attr('checked', 'checked');


        }
        else if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_individual_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_individual_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_individual_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo familiar
        if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_familiar_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo academico
        if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_academico_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo economico
        if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_economico_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

        //Riesgo universitario
        if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_bajo')) {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
        }
        else if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_medio')) {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
        }
        else if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_alto')) {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
        }
        else {
            $("input[name=riesgo_universitario_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

        }

    }
});
