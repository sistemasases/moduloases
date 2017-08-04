// requirejs(['jquery', 'bootstrap', 'datatables.net', 'datatables.net-buttons', 'buttons.flash', 'jszip', 'pdfmake', 'vfs_fonts', 'buttons.html5', 'buttons.print', 'sweetalert', 'amd_actions'], function($) {

//     var globalArregloPares = [];
//     var globalArregloGrupal = [];
//     var arregloMonitorYEstudiantes = [];
//     var arregloPracticanteYMonitor = [];
//     var arregloImprimirPares = [];
//     var arregloImprimirGrupos = [];
//     var rol = 0;
//     var id = 0;
//     var name = "";
//     var htmltexto = "";
//     var instance = "";
//     var email = "";

//     $(document).ready(function() {
//         //se extrae el id de la instancia en la cual se realizara el reporte
//         var informacionUrl = window.location.search.split("&");
//         for (var i = 0; i < informacionUrl.length; i++) {
//             var elemento = informacionUrl[i].split("=");
//             if (elemento[0] == "?instanceid" || elemento[0] == "instanceid") {
//                 var instance = elemento[1];
//             }
//         }
//         //Se obtiene el nombre para la verificacion de sistemas y administrador.
//         $.ajax({
//             type: "POST",
//             data: {
//                 type: "getName"
//             },
//             url: "../../../blocks/ases/managers/get_info_report.php",
//             async: false,
//             success: function(msg) {
//                 name = msg;
//             },
//             dataType: "text",
//             cache: "false",
//             error: function(msg) {
//                 swal({
//                     title: "error getName",
//                     html: true,
//                     type: "error",
//                     confirmButtonColor: "#d51b23"
//                 });
//             },
//         });

//         //Se obtiene el ID de la persona logueada.
//         $.ajax({
//             type: "POST",
//             data: {
//                 type: "getid"
//             },
//             url: "../../../blocks/ases/managers/get_info_report.php",
//             async: false,
//             success: function(msg) {
//                 id = msg;
//             },
//             dataType: "text",
//             cache: "false",
//             error: function(msg) {
//                 swal({
//                     title: "error getid",
//                     html: true,
//                     type: "error",
//                     confirmButtonColor: "#d51b23"
//                 });
//             },
//         });

//         //Se obtiene el rol de la persona segun la instancia.
//         $.ajax({
//             type: "POST",
//             data: {
//                 type: "getRol",
//                 id: id,
//                 instance: instance
//             },
//             url: "../../../blocks/ases/managers/get_info_report.php",
//             async: false,
//             success: function(msg) {
//                 rol = msg;
//             },
//             dataType: "text",
//             cache: "false",
//             error: function(msg) {
//                 swal({
//                     title: "error getrol",
//                     html: true,
//                     type: "error",
//                     confirmButtonColor: "#d51b23"
//                 });
//             },
//         });

//         //Se obtiene el email de la persona
//         $.ajax({
//             type: "POST",
//             data: {
//                 type: "getEmail"
//             },
//             url: "../../../blocks/ases/managers/get_info_report.php",
//             async: false,
//             success: function(msg) {
//                 email = msg;
//             },
//             dataType: "text",
//             cache: "false",
//             error: function(msg) {
//                 swal({
//                     title: "error getrol",
//                     html: true,
//                     type: "error",
//                     confirmButtonColor: "#d51b23"
//                 });
//             },
//         });

//         name = "";
//         //Se muestra la interfaz correspondiente al usuario.
//         if (rol == 4) {
//             $('#titulo').text("Informacion Estudiantes");
//             htmltexto = monitorUser(id, 0, instance);
//         }
//         else if (rol == 7) {
//             $('#titulo').text("Informacion Monitores");
//             htmltexto = practicanteUser(id, instance);
//         }
//         else if (rol == 3) {

//             $('#titulo').text("Informacion Practicantes");
//             htmltexto = profesionalUser(id, instance);
//         }
//         else if (rol == 6 || name == "administrador" || name == "sistemas1008" || name == "Administrador") {
//             anadirEvento();
//         }

//         //Se reemplaza el texto retornado, este texto corresponde a un conjunto de toogles.
//         $('#reemplazarToogle').html(htmltexto);

//         //Si el usuario cumple con las condiciones, se añade el evento a los botones de observaciones
//         //para enviar mensajes a los correspondientes monitores.

//         if (rol == 7 || (name == "administrador" || name == "sistemas1008" || name == "Administrador")) {
//             $("input[name=profesional]").attr('disabled', true);
//             $("input[name=practicante]").attr('disabled', true);

//             //limpia textarea de todos los tipos de riesgos.
//             $('body').on('click', '.limpiar', function() {
//                 var elemento = $(this).closest("div").attr('id').split("_");
//                 var id = elemento[2].split("div").pop();
//                 switch (elemento[1]) {
//                     case 'individual':
//                         $("#obindividual_" + id).val("");
//                         break;
//                     case 'familiar':
//                         $("#obfamiliar_" + id).val("");
//                         break;
//                     case 'academico':
//                         $("#obacademico_" + id).val("");
//                         break;
//                     case 'economico':
//                         $("#obeconomico_" + id).val("");
//                         break;
//                     case 'universitario':
//                         $("#obuniversitario_" + id).val("");
//                         break;
//                     default:
//                         alert("Dato invalido");
//                         break;
//                 }
//             });

//             //Envia correo al monitor.
//             $(this).on('click', '.btn.btn-info.btn-lg.botonCorreo', function() {
//                 if ($(this).parent().children('textarea').val() == "") {
//                     swal({
//                         title: "Para enviar una observación debe llenar el campo correspondiente",
//                         html: true,
//                         type: "error",
//                         confirmButtonColor: "#d51b23"
//                     });
//                 }
//                 else {
//                     //Se recupera el mensaje y el id del monitor al que se va a enviar el mensaje
//                     var id = $(this).attr("value");
//                     var particionar_informacion = $(this).parent().children('textarea').attr("id").split("-");
//                     var tipo = particionar_informacion[0];
//                     var codigoN1 = particionar_informacion[1];
//                     var codigoN2 = particionar_informacion[2];
//                     var fecha = particionar_informacion[3];
//                     var nombre = particionar_informacion[4];
//                     var mensaje_enviar = $(this).parent().children('textarea').val();

//                     //Se limpia el textarea
//                     $(this).parent().children('textarea').val("");
//                     var respuesta = "";

//                     //Se llama el ajax para enviar el mensaje
//                     $.ajax({
//                         type: "POST",
//                         data: {
//                             type: "send_email_to_user",
//                             tipoSeg: tipo,
//                             codigoEnviarN1: codigoN1,
//                             codigoEnviarN2: codigoN2,
//                             fecha: fecha,
//                             nombre: nombre,
//                             message: mensaje_enviar
//                         },
//                         url: "../../../blocks/ases/managers/get_info_report.php",
//                         async: false,
//                         success: function(msg) {
//                             if (msg == 1) {
//                                 swal({
//                                     title: "Correo enviado",
//                                     html: true,
//                                     type: "success",
//                                     confirmButtonColor: "#d51b23"
//                                 });
//                             }
//                             else {
//                                 swal({
//                                     title: "error al enviar el correo al monitor",
//                                     html: true,
//                                     type: "error",
//                                     confirmButtonColor: "#d51b23"
//                                 });
//                             }
//                         },
//                         dataType: "text",
//                         cache: "false",
//                         error: function(msg) {
//                             swal({
//                                 title: "error al enviar el correo",
//                                 html: true,
//                                 type: "error",
//                                 confirmButtonColor: "#d51b23"
//                             });
//                         },
//                     });
//                 }
//             });

//             //Editar seguimiento determinado.
//             $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
//                 var id = $(this).attr("value");
//                 var $tbody = $(this).parent().parent().parent();
//                 $("input[name=practicante]").attr('disabled', false);
//                 $tbody.find('.editable').removeAttr('readonly');
//                 $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//                 $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//                 $tbody.find('.radio-ocultar').toggleClass('ocultar');
//                 auxiliar_editar(id);
//                 seleccionarButtons(id);
//             });

//             //Cancela la edición de un seguimiento determinado.
//             $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
//                 var id = $(this).attr("value");
//                 var $tbody = $(this).parent().parent().parent();
//                 $tbody.find('.editable').attr('readonly', true);
//                 $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//                 $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//                 $tbody.find('.radio-ocultar').toggleClass('ocultar');
//                 auxiliar_cancelar(id);
//             });

//             // Modifica un seguimiento determinado.
//             $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
//                 var id = $(this).attr("value");
//                 var profesional = "",
//                     practicante = "";
//                 var combo_hora_inicio = document.getElementById("h_ini_" + id);
//                 var combo_hora_fin = document.getElementById("h_fin_" + id);
//                 var combo_min_inicio = document.getElementById("m_ini_" + id);
//                 var combo_min_fin = document.getElementById("m_fin_" + id);
//                 var hora_inicial = combo_hora_inicio.options[combo_hora_inicio.selectedIndex].text;
//                 var hora_final = combo_hora_fin.options[combo_hora_fin.selectedIndex].text;
//                 var min_inicial = combo_min_inicio.options[combo_min_inicio.selectedIndex].text;
//                 var min_final = combo_min_fin.options[combo_min_fin.selectedIndex].text;
//                 var validar = validarHoras(hora_inicial, hora_final, min_inicial, min_final);

//                 if (validar == "") {

//                     if ($("#profesional_" + id).is(':checked')) {
//                         profesional = 1;
//                     }
//                     else {
//                         profesional = 0;
//                     }

//                     if ($("#practicante_" + id).is(':checked')) {
//                         practicante = 1;
//                     }
//                     else {
//                         practicante = 0;
//                     }

//                     var $tbody = $(this).parent().parent().parent();
//                     var idSeguimientoActualizar = $(this).attr('value');
//                     var lugar = $tbody.find("#lugar_" + idSeguimientoActualizar).val();
//                     var tema = $tbody.find("#tema_" + id).val();
//                     var objetivos = $tbody.find("#objetivos_" + id).val();
//                     var fecha = $tbody.find("#fecha_" + id).val();
//                     var h_inicial = hora_inicial + ":" + min_inicial;
//                     var h_final = hora_final + ":" + min_final;

//                     var obindividual = $tbody.find("#obindividual_" + id).val();
//                     var riesgoIndividual = $("input[name='riesgo_individual_" + id + "']:checked").val();
//                     if (riesgoIndividual == undefined) {
//                         riesgoIndividual = "0";
//                     }

//                     var obfamiliar = $tbody.find("#obfamiliar_" + id).val();
//                     var riesgoFamiliar = $("input[name='riesgo_familiar_" + id + "']:checked").val();
//                     if (riesgoFamiliar == undefined) {
//                         riesgoFamiliar = "0";
//                     }

//                     var obacademico = $tbody.find("#obacademico_" + id).val();
//                     var riesgoAcademico = $("input[name='riesgo_academico_" + id + "']:checked").val();
//                     if (riesgoAcademico == undefined) {
//                         riesgoAcademico = "0";
//                     }

//                     var obeconomico = $tbody.find("#obeconomico_" + id).val();
//                     var riesgoEconomico = $("input[name='riesgo_economico_" + id + "']:checked").val();
//                     if (riesgoEconomico == undefined) {
//                         riesgoEconomico = "0";
//                     }

//                     var obuniversitario = $tbody.find("#obuniversitario_" + id).val();
//                     var riesgoUniversitario = $("input[name='riesgo_universitario_" + id + "']:checked").val();
//                     if (riesgoUniversitario == undefined) {
//                         riesgoUniversitario = "0";
//                     }

//                     var observacionesGeneral = $tbody.find("#observacionesGeneral_" + id).val();


//                     if (lugar == "" || tema == "" || objetivos == "") {
//                         swal({
//                             title: "Debe ingresar los datos completamente",
//                             html: true,
//                             type: "warning",
//                             confirmButtonColor: "#d51b23"
//                         });
//                     }
//                     else {
//                         if (!((obindividual != "" && riesgoIndividual == 0) || (obfamiliar != "" && riesgoFamiliar == 0) || (obacademico != "" && riesgoAcademico == 0) || (obeconomico != "" && riesgoEconomico == 0) || (obuniversitario != "" && riesgoUniversitario == 0) || (obindividual == "" && riesgoIndividual > 0) || (obfamiliar == "" && riesgoFamiliar > 0) || (obacademico == "" && riesgoAcademico > 0) || (obeconomico == "" && riesgoEconomico > 0) || (obuniversitario == "" && riesgoUniversitario > 0))) {
//                             $.ajax({
//                                 type: "POST",
//                                 data: {
//                                     id: idSeguimientoActualizar,
//                                     lugar: lugar,
//                                     tema: tema,
//                                     objetivos: objetivos,
//                                     obindividual: obindividual,
//                                     riesgoIndividual: riesgoIndividual,
//                                     obfamiliar: obfamiliar,
//                                     riesgoFamiliar: riesgoFamiliar,
//                                     obacademico: obacademico,
//                                     riesgoAcademico: riesgoAcademico,
//                                     obeconomico: obeconomico,
//                                     riesgoEconomico: riesgoEconomico,
//                                     obuniversitario: obuniversitario,
//                                     riesgoUniversitario: riesgoUniversitario,
//                                     observacionesGeneral: observacionesGeneral,
//                                     practicante: practicante,
//                                     profesional: profesional,
//                                     fecha: fecha,
//                                     h_inicial: h_inicial,
//                                     h_final: h_final,
//                                     type: "actualizar_registro",

//                                 },
//                                 url: "../../../blocks/ases/managers/get_info_report.php",
//                                 async: false,
//                                 success: function(msg) {
//                                     if (msg == "0") {
//                                         swal({
//                                             title: "error al actualizar registro",
//                                             html: true,
//                                             type: "error",
//                                             confirmButtonColor: "#d51b23"
//                                         });
//                                     }
//                                     else if (msg == "1") {
//                                         swal("¡Hecho!", "El registro ha sido actualizado",
//                                             "success");
//                                         setTimeout('document.location.reload()', 500);

//                                     }
//                                     else {
//                                         swal({
//                                             title: "Debe ingresar correctamente los riesgos",
//                                             html: true,
//                                             type: "warning",
//                                             confirmButtonColor: "#d51b23"
//                                         });
//                                     }
//                                 },
//                                 dataType: "text",
//                                 cache: "false",
//                                 error: function(msg) {},
//                             });


//                         }
//                         else {
//                             swal({
//                                 title: "Debe ingresar correctamente los riesgos",
//                                 html: true,
//                                 type: "warning",
//                                 confirmButtonColor: "#d51b23"
//                             });
//                         }

//                     }
//                 }
//                 else {
//                     swal({
//                         title: validar,
//                         html: true,
//                         type: "warning",
//                         confirmButtonColor: "#d51b23"
//                     });
//                 }
//             });

//             //Borra seguimiento determinado.
//             $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
//                 var id_registro = $(this).attr('value');
//                 swal({
//                         title: "¿Seguro que desea eliminar el registro?",
//                         text: "No podrás deshacer este paso",
//                         type: "warning",
//                         showCancelButton: true,
//                         cancelButtonText: "No",
//                         confirmButtonColor: "#d51b23",
//                         confirmButtonText: "Si",
//                         closeOnConfirm: false
//                     },
//                     function() {
//                         $.ajax({
//                             type: "POST",
//                             data: {
//                                 id: id_registro,
//                                 type: "eliminar_registro",
//                             },
//                             url: "../../../blocks/ases/managers/get_info_report.php",
//                             async: false,
//                             success: function(msg) {
//                                 if (msg == 0) {
//                                     swal({
//                                         title: "error al eliminar registro",
//                                         html: true,
//                                         type: "error",
//                                         confirmButtonColor: "#d51b23"
//                                     });
//                                 }
//                                 else {

//                                     swal("¡Hecho!",
//                                         "El registro ha sido eliminado",
//                                         "success");
//                                     setTimeout('document.location.reload()', 500);
//                                 }
//                             },
//                             dataType: "text",
//                             cache: "false",
//                             error: function(msg) {},
//                         });
//                     });
//             });

//             //se inicia la adicion del evento
//             $(this).on('click', '.btn.btn-info.btn-lg.botonCorreo', function() {
//                 var id = $(this).attr("value");
//                 $("#profesional_" + id).attr("disabled", true);
//                 if ($(this).parent().children('textarea').val() == "") {
//                     swal({
//                         title: "Para enviar una observación debe llenar el campo correspondiente",
//                         html: true,
//                         type: "error",
//                         confirmButtonColor: "#d51b23"
//                     });
//                 }
//                 else {

//                     //se recupera el mensaje y el id del monitor al que se va a enviar el mensaje
//                     var particionar_informacion = $(this).parent().children('textarea').attr("id").split("_");
//                     var tipo = particionar_informacion[0];
//                     var codigoN1 = particionar_informacion[1];
//                     var fecha = particionar_informacion[3];
//                     var nombre = particionar_informacion[4];
//                     var mensaje_enviar = $(this).parent().children('textarea').val();
//                     var codigoN2 = 0;

//                     $.ajax({
//                         type: "POST",
//                         data: {
//                             type: "getProfesional",
//                             id: id,
//                             instance: instance
//                         },
//                         url: "../../../blocks/ases/managers/get_info_report.php",
//                         async: false,
//                         success: function(msg) {
//                             codigoN2 = msg;
//                         },
//                         dataType: "text",
//                         cache: "false",
//                         error: function(msg) {
//                             swal({
//                                 title: "error getrol",
//                                 html: true,
//                                 type: "error",
//                                 confirmButtonColor: "#d51b23"
//                             })
//                         },
//                     });

//                     //se limpia el textarea
//                     $(this).parent.children('textarea').val("");
//                     var respuesta = "";

//                     //se llama el ajax para enviar el mensaje
//                     $.ajax({
//                         type: "POST",
//                         data: {
//                             type: "send_email_to_user",
//                             tipoSeg: tipo,
//                             codigoEnviarN1: codigoN1,
//                             codigoEnviarN2: codigoN2,
//                             fecha: fecha,
//                             nombre: nombre,
//                             message: mensaje_enviar
//                         },
//                         url: "../../../blocks/ases/managers/get_info_report.php",
//                         async: false,
//                         success: function(msg) {
//                             //alert("mensaje");
//                             //alert(msg);
//                             //si el envio del mensaje fue exitoso
//                             if (msg == 1) {
//                                 swal({
//                                     title: "Correo enviado",
//                                     html: true,
//                                     type: "success",
//                                     confirmButtonColor: "#d51b23"
//                                 });
//                             }
//                             else {
//                                 swal({
//                                     title: "error al enviar el correo al monitor",
//                                     html: true,
//                                     type: "error",
//                                     confirmButtonColor: "#d51b23"
//                                 });
//                             }
//                         },
//                         dataType: "text",
//                         cache: "false",
//                         error: function(msg) {
//                             swal({
//                                 title: "error al enviar el correo",
//                                 html: true,
//                                 type: "error",
//                                 confirmButtonColor: "#d51b23"
//                             })
//                         },
//                     });

//                 }
//             });

//             //editar,cancelar,modificar,borrar
//         }
//         else if (rol == 3 || (name == "administrador" || name == "sistemas1008" || name == "Administrador")) {
//             //se inicia la adicion del evento
//             $("input[name=practicante]").attr('disabled', true);
//             $("input[name=profesional]").attr('disabled', true);


//             //Verifica si el profesional desea marcar como revisado el seguimiento.
//             $('input[name="profesional"]').click(function() {
//                 if ($(this).is(':checked')) {
//                     swal({
//                             title: "¿Seguro que desea cambiar estado a revisado?",
//                             text: "En caso de modificar el seguimiento no podrá volverlo a editar",
//                             type: "warning",
//                             showCancelButton: true,
//                             cancelButtonText: "No",
//                             confirmButtonColor: "#d51b23",
//                             confirmButtonText: "Si",
//                             closeOnConfirm: true
//                         },


//                         function(isConfirm) {
//                             if (isConfirm == false) {
//                                 $('input[name="profesional"]').prop('checked', false);
//                             }
//                         });
//                 }
//             });


//             $(this).on('click', '.btn.btn-info.btn-lg.botonCorreo', function() {
//                 var id_registro = $(this).attr('value');
//                 var texto = $("#textarea_" + id_registro);
//                 if (texto.val() == "") {
//                     swal({
//                         title: "Para enviar una observación debe llenar el campo correspondiente",
//                         html: true,
//                         type: "error",
//                         confirmButtonColor: "#d51b23"
//                     });
//                 }
//                 else {
//                     //se recupera el mensaje y el id del monitor al que se va a enviar el mensaje
//                     var particionar_informacion = texto.attr('name').split("_");
//                     //alert(particionar_informacion[4]);
//                     var tipo = particionar_informacion[0];
//                     var codigoN1 = particionar_informacion[1];
//                     var codigoN2 = particionar_informacion[2];
//                     var fecha = particionar_informacion[3];
//                     var nombre = particionar_informacion[4];
//                     var mensaje_enviar = texto.val();

//                     //se limpia el textarea
//                     texto.val("");
//                     var respuesta = "";

//                     //se llama el ajax para enviar el mensaje
//                     $.ajax({
//                         type: "POST",
//                         data: {
//                             type: "send_email_to_user",
//                             tipoSeg: tipo,
//                             codigoEnviarN1: codigoN1,
//                             codigoEnviarN2: codigoN2,
//                             fecha: fecha,
//                             nombre: nombre,
//                             message: mensaje_enviar
//                         },
//                         url: "../../../blocks/ases/managers/get_info_report.php",
//                         async: false,
//                         success: function(msg) {
//                             //si el envio del mensaje fue exitoso
//                             if (msg == 1) {
//                                 swal({
//                                     title: "Correo enviado",
//                                     html: true,
//                                     type: "success",
//                                     confirmButtonColor: "#d51b23"
//                                 });
//                             }
//                             else {
//                                 swal({
//                                     title: "error al enviar el correo al monitor",
//                                     html: true,
//                                     type: "error",
//                                     confirmButtonColor: "#d51b23"
//                                 });
//                             }
//                         },
//                         dataType: "text",
//                         cache: "false",
//                         error: function(msg) {
//                             swal({
//                                 title: "error al enviar el correo",
//                                 html: true,
//                                 type: "error",
//                                 confirmButtonColor: "#d51b23"
//                             });
//                         },
//                     });
//                 }
//             });


//             //limpiar
//             $('body').on('click', '.limpiar', function() {
//                 var elemento = $(this).closest("div").attr('id').split("_");
//                 var id = elemento[2].split("div").pop();
//                 switch (elemento[1]) {
//                     case 'individual':
//                         $("#obindividual_" + id).val("");
//                         auxiliar_limpiar("#riesgo_individual_", id);
//                         break;

//                     case 'familiar':
//                         $("#obfamiliar_" + id).val("");
//                         auxiliar_limpiar("#riesgo_familiar_", id);
//                         break;

//                     case 'academico':
//                         $("#obacademico_" + id).val("");
//                         auxiliar_limpiar("#riesgo_academico_", id);
//                         break;

//                     case 'economico':
//                         $("#obeconomico_" + id).val("");
//                         auxiliar_limpiar("#riesgo_economico_", id);
//                         break;

//                     case 'universitario':
//                         $("#obuniversitario_" + id).val("");
//                         auxiliar_limpiar("#riesgo_universitario_", id);
//                         break;

//                     default:
//                         alert("Dato invalido");
//                         break;
//                 }

//             });
//             //Editar seguimiento determinado.
//             $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
//                 var id = $(this).attr("value");
//                 var $tbody = $(this).parent().parent().parent();
//                 $("input[name=profesional]").attr('disabled', false);
//                 $tbody.find('.editable').removeAttr('readonly');
//                 $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//                 $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//                 $tbody.find('.radio-ocultar').toggleClass('ocultar');
//                 auxiliar_editar(id);
//                 seleccionarButtons(id);
//             });

//             //Cancela la edición de un seguimiento determinado.
//             $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
//                 var id = $(this).attr("value");
//                 $("input[name=profesional]").attr('disabled', true);
//                 var $tbody = $(this).parent().parent().parent();
//                 $tbody.find('.editable').attr('readonly', true);
//                 $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//                 $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//                 $tbody.find('.radio-ocultar').toggleClass('ocultar');
//                 auxiliar_cancelar(id);
//             });

//             // Modifica un seguimiento determinado.
//             $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
//                 var id = $(this).attr("value");
//                 var profesional = "",
//                     practicante = "";
//                 var combo_hora_inicio = document.getElementById("h_ini_" + id);
//                 var combo_hora_fin = document.getElementById("h_fin_" + id);
//                 var combo_min_inicio = document.getElementById("m_ini_" + id);
//                 var combo_min_fin = document.getElementById("m_fin_" + id);
//                 var hora_inicial = combo_hora_inicio.options[combo_hora_inicio.selectedIndex].text;
//                 var hora_final = combo_hora_fin.options[combo_hora_fin.selectedIndex].text;
//                 var min_inicial = combo_min_inicio.options[combo_min_inicio.selectedIndex].text;
//                 var min_final = combo_min_fin.options[combo_min_fin.selectedIndex].text;
//                 var validar = validarHoras(hora_inicial, hora_final, min_inicial, min_final);

//                 if (validar == "") {

//                     if ($("#profesional_" + id).is(':checked')) {
//                         profesional = 1;
//                     }
//                     else {
//                         profesional = 0;
//                     }

//                     if ($("#practicante_" + id).is(':checked')) {
//                         practicante = 1;
//                     }
//                     else {
//                         practicante = 0;
//                     }

//                     var $tbody = $(this).parent().parent().parent();
//                     var idSeguimientoActualizar = $(this).attr('value');
//                     var lugar = $tbody.find("#lugar_" + idSeguimientoActualizar).val();
//                     var tema = $tbody.find("#tema_" + id).val();
//                     var objetivos = $tbody.find("#objetivos_" + id).val();
//                     var fecha = $tbody.find("#fecha_" + id).val();
//                     var h_inicial = hora_inicial + ":" + min_inicial;
//                     var h_final = hora_final + ":" + min_final;

//                     var obindividual = $tbody.find("#obindividual_" + id).val();
//                     var riesgoIndividual = $("input[name='riesgo_individual_" + id + "']:checked").val();
//                     if (riesgoIndividual == undefined) {
//                         riesgoIndividual = "0";
//                     }

//                     var obfamiliar = $tbody.find("#obfamiliar_" + id).val();
//                     var riesgoFamiliar = $("input[name='riesgo_familiar_" + id + "']:checked").val();
//                     if (riesgoFamiliar == undefined) {
//                         riesgoFamiliar = "0";
//                     }

//                     var obacademico = $tbody.find("#obacademico_" + id).val();
//                     var riesgoAcademico = $("input[name='riesgo_academico_" + id + "']:checked").val();
//                     if (riesgoAcademico == undefined) {
//                         riesgoAcademico = "0";
//                     }

//                     var obeconomico = $tbody.find("#obeconomico_" + id).val();
//                     var riesgoEconomico = $("input[name='riesgo_economico_" + id + "']:checked").val();
//                     if (riesgoEconomico == undefined) {
//                         riesgoEconomico = "0";
//                     }

//                     var obuniversitario = $tbody.find("#obuniversitario_" + id).val();
//                     var riesgoUniversitario = $("input[name='riesgo_universitario_" + id + "']:checked").val();
//                     if (riesgoUniversitario == undefined) {
//                         riesgoUniversitario = "0";
//                     }

//                     var observacionesGeneral = $tbody.find("#observacionesGeneral_" + id).val();


//                     if (lugar == "" || tema == "" || objetivos == "") {
//                         swal({
//                             title: "Debe ingresar los datos completamente",
//                             html: true,
//                             type: "warning",
//                             confirmButtonColor: "#d51b23"
//                         });
//                     }
//                     else {
//                         if (!((obindividual != "" && riesgoIndividual == 0) || (obfamiliar != "" && riesgoFamiliar == 0) || (obacademico != "" && riesgoAcademico == 0) || (obeconomico != "" && riesgoEconomico == 0) || (obuniversitario != "" && riesgoUniversitario == 0) || (obindividual == "" && riesgoIndividual > 0) || (obfamiliar == "" && riesgoFamiliar > 0) || (obacademico == "" && riesgoAcademico > 0) || (obeconomico == "" && riesgoEconomico > 0) || (obuniversitario == "" && riesgoUniversitario > 0))) {
//                             var seguimiento =new Object();
//                             seguimiento.id = idSeguimientoActualizar;
//                             seguimiento.lugar = lugar;
//                             seguimiento.tema = tema;
//                             seguimiento.objetivos = objetivos;
//                             seguimiento.individual = obindividual;
//                             seguimiento.individual_riesgo= riesgoIndividual;
//                             seguimiento.familiar= obfamiliar;
//                             seguimiento.familiar_riesgo = riesgoFamiliar;
//                             seguimiento.academico = obacademico;
//                             seguimiento.academico_riesgo = riesgoAcademico;
//                             seguimiento.economico = obeconomico;
//                             seguimiento.economico_riesgo = riesgoEconomico;
//                             seguimiento.vida_uni = obuniversitario;
//                             seguimiento.vida_uni_riesgo = riesgoUniversitario;
//                             seguimiento.observaciones = observacionesGeneral;
//                             seguimiento.revisado_practicante = practicante;
//                             seguimiento.revisado_profesional = profesional;
//                             seguimiento.fecha = fecha;
//                             seguimiento.hora_ini = h_inicial;
//                             seguimiento.hora_fin = h_final;
//                             $.ajax({
//                                 type: "POST",
//                                 data: {
//                                     seguimiento:seguimiento,
//                                     type: "actualizar_registro",
//                                 },
//                                 url: "../../../blocks/ases/managers/get_info_report.php",
//                                 async: false,
//                                 success: function(msg) {
//                                     if (msg == "0") {
//                                         swal({
//                                             title: "error al actualizar registro",
//                                             html: true,
//                                             type: "error",
//                                             confirmButtonColor: "#d51b23"
//                                         });
//                                     }
//                                     else if (msg == "1") {
//                                         swal("¡Hecho!", "El registro ha sido actualizado",
//                                             "success");

//                                     }
//                                     else {
//                                         swal({
//                                             title: "Debe ingresar correctamente los riesgos",
//                                             html: true,
//                                             type: "warning",
//                                             confirmButtonColor: "#d51b23"
//                                         });
//                                     }
//                                 },
//                                 error: function(msg) {},
//                             });


//                         }
//                         else {
//                             swal({
//                                 title: "Debe ingresar correctamente los riesgos",
//                                 html: true,
//                                 type: "warning",
//                                 confirmButtonColor: "#d51b23"
//                             });
//                         }

//                     }
//                 }
//                 else {
//                     swal({
//                         title: validar,
//                         html: true,
//                         type: "warning",
//                         confirmButtonColor: "#d51b23"
//                     });
//                 }
//             });
//             //borrar
//             $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
//                 var id_registro = $(this).attr('value');
//                 swal({
//                         title: "¿Seguro que desea eliminar el registro?",
//                         text: "No podrás deshacer este paso",
//                         type: "warning",
//                         showCancelButton: true,
//                         cancelButtonText: "No",
//                         confirmButtonColor: "#d51b23",
//                         confirmButtonText: "Si",
//                         closeOnConfirm: false
//                     },


//                     function() {

//                         $.ajax({
//                             type: "POST",
//                             data: {
//                                 id: id_registro,
//                                 type: "eliminar_registro",
//                             },
//                             url: "../../../blocks/ases/managers/get_info_report.php",
//                             async: false,
//                             success: function(msg) {
//                                 if (msg == 0) {
//                                     swal({
//                                         title: "error al borrar registro",
//                                         html: true,
//                                         type: "error",
//                                         confirmButtonColor: "#d51b23"
//                                     });
//                                 }
//                                 else {

//                                     swal("¡Hecho!",
//                                         "El registro ha sido eliminado",
//                                         "success");
//                                     setTimeout('document.location.reload()', 500);

//                                 }
//                             },
//                             dataType: "text",
//                             cache: "false",
//                             error: function(msg) {},
//                         });
//                     });
//             });

//         }
//         else if (rol == 4 || (name == "administrador" || name == "sistemas1008" || name == "Administrador")) {

//             //limpiar
//             $('body').on('click', '.limpiar', function() {
//                 var elemento = $(this).closest("div").attr('id').split("_");
//                 var id = elemento[2].split("div").pop();
//                 switch (elemento[1]) {
//                     case 'individual':
//                         $("#obindividual_" + id).val("");
//                         auxiliar_limpiar("#riesgo_individual_", id);
//                         break;

//                     case 'familiar':
//                         $("#obfamiliar_" + id).val("");
//                         auxiliar_limpiar("#riesgo_familiar_", id);
//                         break;

//                     case 'academico':
//                         $("#obacademico_" + id).val("");
//                         auxiliar_limpiar("#riesgo_academico_", id);
//                         break;

//                     case 'economico':
//                         $("#obeconomico_" + id).val("");
//                         auxiliar_limpiar("#riesgo_economico_", id);
//                         break;

//                     case 'universitario':
//                         $("#obuniversitario_" + id).val("");
//                         auxiliar_limpiar("#riesgo_universitario_", id);
//                         break;

//                     default:
//                         alert("Dato invalido");
//                         break;
//                 }

//             });


//             //editar


//             $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
//                 var id = $(this).attr("value");
//                 var visto_profesional = $("#profesional_" + id).is(':checked');
//                 if (visto_profesional == false) {

//                     var $tbody = $(this).parent().parent().parent();
//                     $tbody.find('.editable').removeAttr('readonly');
//                     $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//                     $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//                     $tbody.find('.radio-ocultar').toggleClass('ocultar');
//                     $("#borrar_" + id).hide();
//                     $("#editar_" + id).hide();
//                     $("#enviar_" + id).hide();
//                     $("#hora_final_" + id).hide();
//                     $("#hora_inicial_" + id).hide();
//                     $("#titulo_fecha_" + id).show();
//                     $("#mod_hora_final_" + id).show();
//                     $("#mod_hora_ini_" + id).show();

//                     var f1 = $("#h_inicial_texto_" + id).val();
//                     var f2 = $("#h_final_texto_" + id).val();
//                     var array_f1 = f1.split(":");
//                     var array_f2 = f2.split(":");
//                     initFormSeg(id);
//                     //Seleccionamos la hora deacuerdo al sistema
//                     $("#h_ini_" + id + " option[value=" + array_f1[0] + "]").attr("selected", true);
//                     $("#m_ini_" + id + " option[value=" + array_f1[1] + "]").attr("selected", true);
//                     $("#h_fin_" + id + " option[value=" + array_f2[0] + "]").attr("selected", true);
//                     $("#m_fin_" + id + " option[value=" + array_f2[1] + "]").attr("selected", true);
//                     var date = $("label[for='fechatext_" + id + "']").text();
//                     var fecha_formateada = getFormatoFecha(date);
//                     $("#fecha_" + id).val(fecha_formateada);
//                     seleccionarButtons(id);

//                 }
//                 else {
//                     swal("¡Advertencia!",
//                         "No es posible editar el seguimiento, debido a que ya ha sido revisado por un profesional",
//                         "warning");
//                 }
//             });

//             //cancelar
//             $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
//                 var id = $(this).attr("value");
//                 var $tbody = $(this).parent().parent().parent();
//                 $tbody.find('.editable').attr('readonly', true);
//                 $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//                 $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//                 $tbody.find('.radio-ocultar').toggleClass('ocultar');
//                 $("#titulo_fecha_" + id).hide();
//                 $("#borrar_" + id).show();
//                 $("#editar_" + id).show();
//                 $("#enviar_" + id).show();
//                 $("#hora_final_" + id).show();
//                 $("#mod_hora_final_" + id).hide();
//                 $("#hora_inicial_" + id).show();
//                 $("#mod_hora_ini_" + id).hide();
//             });

//             //limpiar
//             $('body').on('click', '.limpiar', function() {
//                 var elemento = $(this).closest("div").attr('id').split("_");
//                 var id = elemento[2].split("div").pop();
//                 switch (elemento[1]) {
//                     case 'individual':
//                         $("#obindividual_" + id).val("");
//                         break;
//                     case 'familiar':
//                         $("#obfamiliar_" + id).val("");
//                         break;
//                     case 'academico':
//                         $("#obacademico_" + id).val("");
//                         break;
//                     case 'economico':
//                         $("#obeconomico_" + id).val("");
//                         break;
//                     case 'universitario':
//                         $("#obuniversitario_" + id).val("");
//                         break;
//                     default:
//                         alert("Dato invalido");
//                         break;
//                 }

//             });

//             //borrar
//             $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
//                 var id_registro = $(this).attr('value');
//                 swal({
//                         title: "¿Seguro que desea eliminar el registro?",
//                         text: "No podrás deshacer este paso",
//                         type: "warning",
//                         showCancelButton: true,
//                         cancelButtonText: "No",
//                         confirmButtonColor: "#d51b23",
//                         confirmButtonText: "Si",
//                         closeOnConfirm: false
//                     },


//                     function() {

//                         $.ajax({
//                             type: "POST",
//                             data: {
//                                 id: id_registro,
//                                 type: "eliminar_registro",
//                             },
//                             url: "../../../blocks/ases/managers/get_info_report.php",
//                             async: false,
//                             success: function(msg) {
//                                 if (msg == 0) {
//                                     swal({
//                                         title: "error al borrar registro",
//                                         html: true,
//                                         type: "error",
//                                         confirmButtonColor: "#d51b23"
//                                     });
//                                 }
//                                 else {

//                                     swal("¡Hecho!",
//                                         "El registro ha sido eliminado",
//                                         "success");
//                                     setTimeout('document.location.reload()', 500);
//                                 }
//                             },
//                             dataType: "text",
//                             cache: "false",
//                             error: function(msg) {},
//                         });
//                     });
//             });



//             // modificar
//             $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
//                 var id = $(this).attr("value");
//                 var profesional = "";
//                 var practicante = "";
//                 var combo_hora_inicio = document.getElementById("h_ini_" + id);
//                 var combo_hora_fin = document.getElementById("h_fin_" + id);
//                 var combo_min_inicio = document.getElementById("m_ini_" + id);
//                 var combo_min_fin = document.getElementById("m_fin_" + id);
//                 var hora_inicial = combo_hora_inicio.options[combo_hora_inicio.selectedIndex].text;
//                 var hora_final = combo_hora_fin.options[combo_hora_fin.selectedIndex].text;
//                 var min_inicial = combo_min_inicio.options[combo_min_inicio.selectedIndex].text;
//                 var min_final = combo_min_fin.options[combo_min_fin.selectedIndex].text;
//                 var validar = validarHoras(hora_inicial, hora_final, min_inicial, min_final);



//                 if (validar == "") {

//                     if ($("#profesional_" + id).is(':checked')) {
//                         profesional = 1;
//                     }
//                     else {
//                         profesional = 0;
//                     }

//                     if ($("#practicante_" + id).is(':checked')) {
//                         practicante = 1;
//                     }
//                     else {
//                         practicante = 0;
//                     }

//                     var $tbody = $(this).parent().parent().parent();
//                     var idSeguimientoActualizar = $(this).attr('value');
//                     var lugar = $tbody.find("#lugar_" + idSeguimientoActualizar).val();
//                     var tema = $tbody.find("#tema_" + id).val();
//                     var objetivos = $tbody.find("#objetivos_" + id).val();
//                     var fecha = $tbody.find("#fecha_" + id).val();
//                     var h_inicial = hora_inicial + ":" + min_inicial;
//                     var h_final = hora_final + ":" + min_final;

//                     var obindividual = $tbody.find("#obindividual_" + id).val();
//                     var riesgoIndividual = $("input[name='riesgo_individual_" + id + "']:checked").val();
//                     if (riesgoIndividual == undefined) {
//                         riesgoIndividual = "0";
//                     }

//                     var obfamiliar = $tbody.find("#obfamiliar_" + id).val();
//                     var riesgoFamiliar = $("input[name='riesgo_familiar_" + id + "']:checked").val();
//                     if (riesgoFamiliar == undefined) {
//                         riesgoFamiliar = "0";
//                     }

//                     var obacademico = $tbody.find("#obacademico_" + id).val();
//                     var riesgoAcademico = $("input[name='riesgo_academico_" + id + "']:checked").val();
//                     if (riesgoAcademico == undefined) {
//                         riesgoAcademico = "0";
//                     }

//                     var obeconomico = $tbody.find("#obeconomico_" + id).val();
//                     var riesgoEconomico = $("input[name='riesgo_economico_" + id + "']:checked").val();
//                     if (riesgoEconomico == undefined) {
//                         riesgoEconomico = "0";
//                     }

//                     var obuniversitario = $tbody.find("#obuniversitario_" + id).val();
//                     var riesgoUniversitario = $("input[name='riesgo_universitario_" + id + "']:checked").val();
//                     if (riesgoUniversitario == undefined) {
//                         riesgoUniversitario = "0";
//                     }

//                     var observacionesGeneral = $tbody.find("#observacionesGeneral_" + id).val();


//                     if (lugar == "" || tema == "" || objetivos == "") {
//                         swal({
//                             title: "Debe ingresar los datos completamente",
//                             html: true,
//                             type: "warning",
//                             confirmButtonColor: "#d51b23"
//                         });
//                     }
//                     else {


//                         if (!((obindividual != "" && riesgoIndividual == 0) || (obfamiliar != "" && riesgoFamiliar == 0) || (obacademico != "" && riesgoAcademico == 0) || (obeconomico != "" && riesgoEconomico == 0) || (obuniversitario != "" && riesgoUniversitario == 0) || (obindividual == "" && riesgoIndividual > 0) || (obfamiliar == "" && riesgoFamiliar > 0) || (obacademico == "" && riesgoAcademico > 0) || (obeconomico == "" && riesgoEconomico > 0) || (obuniversitario == "" && riesgoUniversitario > 0))) {


//                             $.ajax({
//                                 type: "POST",
//                                 data: {
//                                     id: idSeguimientoActualizar,
//                                     lugar: lugar,
//                                     tema: tema,
//                                     objetivos: objetivos,
//                                     obindividual: obindividual,
//                                     riesgoIndividual: riesgoIndividual,
//                                     obfamiliar: obfamiliar,
//                                     riesgoFamiliar: riesgoFamiliar,
//                                     obacademico: obacademico,
//                                     riesgoAcademico: riesgoAcademico,
//                                     obeconomico: obeconomico,
//                                     riesgoEconomico: riesgoEconomico,
//                                     obuniversitario: obuniversitario,
//                                     riesgoUniversitario: riesgoUniversitario,
//                                     observacionesGeneral: observacionesGeneral,
//                                     practicante: practicante,
//                                     profesional: profesional,
//                                     fecha: fecha,
//                                     h_inicial: h_inicial,
//                                     h_final: h_final,
//                                     type: "actualizar_registro",

//                                 },
//                                 url: "../../../blocks/ases/managers/get_info_report.php",
//                                 async: false,
//                                 success: function(msg) {
//                                     if (msg == "0") {
//                                         swal({
//                                             title: "error al actualizar registro",
//                                             html: true,
//                                             type: "error",
//                                             confirmButtonColor: "#d51b23"
//                                         });
//                                     }
//                                     else if (msg == "1") {
//                                         swal("¡Hecho!", "El registro ha sido actualizado",
//                                             "success");
//                                         setTimeout('document.location.reload()', 500);

//                                     }
//                                     else {
//                                         swal({
//                                             title: "Debe ingresar correctamente los riesgos",
//                                             html: true,
//                                             type: "warning",
//                                             confirmButtonColor: "#d51b23"
//                                         });
//                                     }
//                                 },
//                                 dataType: "text",
//                                 cache: "false",
//                                 error: function(msg) {},
//                             });


//                         }
//                         else {
//                             swal({
//                                 title: "Debe ingresar correctamente los riesgos",
//                                 html: true,
//                                 type: "warning",
//                                 confirmButtonColor: "#d51b23"
//                             });
//                         }

//                     }
//                 }
//             });


//         }

//     });




//     //******************************************************************************************************
//     //******************************************************************************************************
//     //******************************************************************************************************
//     //METODOS INICIALES PARA EL PROFESIONAL
//     //******************************************************************************************************
//     //******************************************************************************************************
//     //******************************************************************************************************

//     /*Función que obtiene la información de :
//     1) Número de registros del practicante revisados por el profesional no revisados por el mismo,
//     Número total de registros del practicante.
//     2) Número de registros del monitor revisados por el profesional, no revisados por el mismo,
//     Número total de registros del monitor.
//     3)Número de registros del estudiante revisados por el profesional  no revisados por el mismo,
//     Número total de registros del estudiante.
//     */

//     function get_resumen_registros(id_prof, id_practicante, id_estudiante, instanceid) {
//         $.ajax({
//             type: "POST",
//             data: {
//                 type: "resumen_registros",
//                 id: id_prof
//             },
//             url: "../../../blocks/ases/managers/get_info_report.php",
//             async: false,
//             success: function(msg) {
//                 //se llama el metodo que crea el arreglo global de practicantes de un profesional en la instancia
//                 //    transformarConsultaProfesionalArray(msg, instanceid);
//             },
//             dataType: "json",
//             cache: "false",
//             error: function(msg) {
//                 swal({
//                     title: "error al obtener el resumen de los registros",
//                     html: true,
//                     type: "error",
//                     confirmButtonColor: "#d51b23"
//                 })
//             },
//         });

//     }


//     //funcion que recupera los practicantes de un profesional en la instancia
//     function profesionalUser(id_prof, instanceid) {
//         $.ajax({
//             type: "POST",
//             data: {
//                 type: "info_profesional",
//                 instance: instanceid,
//                 id: id_prof
//             },
//             url: "../../../blocks/ases/managers/get_info_report.php",
//             async: false,
//             success: function(msg) {
//                 //se llama el metodo que crea el arreglo global de practicantes de un profesional en la instancia
//                 transformarConsultaProfesionalArray(msg, instanceid);
//             },
//             dataType: "json",
//             cache: "false",
//             error: function(msg) {
//                 swal({
//                     title: "error info practicante",
//                     html: true,
//                     type: "error",
//                     confirmButtonColor: "#d51b23"
//                 })
//             },
//         });

//         //se retorna la informacion del toogle creado desde el punto del profesional
//         return crearTablaYToggleProfesional(arregloPracticanteYMonitor);

//     }

//     //funcion que transforma el arreglo retornado por la peticion en un arreglo que posteriormente
//     //se usara para la creacion del toogle
//     function transformarConsultaProfesionalArray(arregloPracticantes, instance) {
//         for (var practicante in arregloPracticantes) {
//             var arregloAuxiliar = [];
//             //arreglo[codigo-nombre-html de practicante]
//             arregloAuxiliar.push(arregloPracticantes[practicante][0]);
//             arregloAuxiliar.push(arregloPracticantes[practicante][1]);
//             //se asigna a esta posicion un texto html correspondiente a la informacion del practicante
//             arregloAuxiliar.push(practicanteUser(arregloPracticantes[practicante][0], instance));
//             arregloAuxiliar.push(arregloPracticantes[practicante][2]);
//             arregloAuxiliar.push(arregloPracticantes[practicante][3]);
//             arregloAuxiliar.push(arregloPracticantes[practicante][4]);
//             arregloPracticanteYMonitor.push(arregloAuxiliar);
//         }

//     }

//     //se crea el toogle del profesional el cual tiene cada uno de los practicantesr asignados al profesional
//     function crearTablaYToggleProfesional() {
//         var stringRetornar = "";
//         for (var practicante in arregloPracticanteYMonitor) {
//             stringRetornar += '<div class="panel-group"><div class="panel panel-default" ><div class="panel-heading" style="background-color: #938B8B;"><h4 class="panel-title"><a data-toggle="collapse"  href="#collapse' + arregloPracticanteYMonitor[practicante][0] + '">' + arregloPracticanteYMonitor[practicante][1] + '</a><span> R.P  : <b>' + arregloPracticanteYMonitor[practicante][3] + '</b> - NO R.P : <b>' + arregloPracticanteYMonitor[practicante][4] + '</b> - Total  : <b>' + arregloPracticanteYMonitor[practicante][5] + '</b> </span></h4></div>';
//             stringRetornar += '<div id="collapse' + arregloPracticanteYMonitor[practicante][0] + '" class="panel-collapse collapse"><div class="panel-body">';
//             //en la tercer posicion del arreglo se encuentra un texto html con un formato especifico
//             stringRetornar += arregloPracticanteYMonitor[practicante][2];
//             stringRetornar += '</div></div></div></div>';
//         }

//         return stringRetornar;
//     }

//     //******************************************************************************************************
//     //******************************************************************************************************
//     //******************************************************************************************************
//     //METODOS INICIALES PARA EL PRACTICANTE
//     //******************************************************************************************************
//     //******************************************************************************************************
//     //******************************************************************************************************

//     //funcion que recupera los monitores de un practicante en la instancia
//     function practicanteUser(id_pract, instanceid) {
//         arregloMonitorYEstudiantes = [];

//         $.ajax({
//             type: "POST",
//             data: {
//                 type: "info_practicante",
//                 id: id_pract
//             },
//             url: "../../../blocks/ases/managers/get_info_report.php",
//             async: false,
//             success: function(msg) {
//                 //se llama el metodo que crea el arreglo global de monitores de un practicante en la instancia
//                 transformarConsultaPracticanteArray(msg, instanceid, id_pract);
//             },
//             dataType: "json",
//             cache: "false",
//             error: function(msg) {
//                 swal({
//                     title: "error info practicante",
//                     html: true,
//                     type: "error",
//                     confirmButtonColor: "#d51b23"
//                 })
//             },
//         });

//         //se retorna la informacion del toogle creado desde el punto del practicante
//         return crearTablaYTogglePracticante(arregloMonitorYEstudiantes);
//     }

//     //funcion que transforma el arreglo retornado por la peticion en un arreglo que posteriormente
//     //se usara para la creacion del toogle
//     function transformarConsultaPracticanteArray(arregloMonitores, instance, id_pract) {

//         for (var monitor in arregloMonitores) {
//             var arregloAuxiliar = [];
//             var cantidad = 0;
//             //arreglo[codigo-nombre-html de los monitores]
//             arregloAuxiliar.push(arregloMonitores[monitor][0]);
//             arregloAuxiliar.push(arregloMonitores[monitor][1]);
//                         alert(arregloMonitores[monitor][1]);

//             arregloAuxiliar.push(monitorUser(arregloMonitores[monitor][0], monitor, instance, id_pract));

//             $.ajax({
//                 type: "POST",
//                 data: {
//                     type: "number_seg_monitor",
//                     id: arregloMonitores[monitor][0],
//                     instance: instance
//                 },
//                 url: "../../../blocks/ases/managers/get_info_report.php",
//                 async: false,
//                 success: function(msg) {
//                     var cantidad = JSON.parse(msg);
//                     //se llama el metodo que crea el arreglo global de estudiantes de un monitor en la instancia
//                     revisado_profesional = cantidad[0].count;
//                     no_revisado_profesional = cantidad[1].count;
//                     total_registros = cantidad[2].count;

//                 },
//                 dataType: "text",
//                 cache: "false",
//                 error: function(msg) {
//                     swal({
//                         title: "error info monitor",
//                         html: true,
//                         type: "error",
//                         confirmButtonColor: "#d51b23"
//                     })
//                 },
//             });

//             arregloAuxiliar.push(revisado_profesional);
//             arregloAuxiliar.push(no_revisado_profesional);
//             arregloAuxiliar.push(total_registros);
//             arregloMonitorYEstudiantes.push(arregloAuxiliar); //
//         }

//     }

//     //se crea el toogle del practicante el cual tiene cada uno de los monitores asignados al practicante
//     function crearTablaYTogglePracticante() {
//         var stringRetornar = "";
//         for (var monitor in arregloMonitorYEstudiantes) {
//             stringRetornar += '<div class="panel-group"><div class="panel panel-default" ><div class="panel-heading" style="background-color: #AEA3A3;"><h4 class="panel-title"><a data-toggle="collapse"  href="#collapse' + arregloMonitorYEstudiantes[monitor][0] + '">' + arregloMonitorYEstudiantes[monitor][1] + '<span> R.P : <b>' + arregloMonitorYEstudiantes[monitor][3] + '</b> - NO R.P : <b>' + arregloMonitorYEstudiantes[monitor][4] + '</b> - Total  : <b>' + arregloMonitorYEstudiantes[monitor][5] + '</b> </span></a></h4></div>';
//             stringRetornar += '<div id="collapse' + arregloMonitorYEstudiantes[monitor][0] + '" class="panel-collapse collapse"><div class="panel-body">';
//             //en la tercer posicion del arreglo se encuentra un texto html con un formato especifico
//             stringRetornar += arregloMonitorYEstudiantes[monitor][2];
//             stringRetornar += '</div></div></div></div>';
//         }
//         return stringRetornar;
//     }

//     //******************************************************************************************************
//     //******************************************************************************************************
//     //******************************************************************************************************
//     //METODOS INICIALES PARA EL MONITOR
//     //******************************************************************************************************
//     //******************************************************************************************************
//     //******************************************************************************************************

//     //funcion que recupera los estudiantes de un monitor en la instancia
//     function monitorUser(codigoMonitor, noMonitor, instanceid, codigoPracticante) {
//         var informacion;

//         $.ajax({
//             type: "POST",
//             data: {
//                 type: "info_monitor",
//                 id: codigoMonitor,
//                 instance: instanceid
//             },
//             url: "../../../blocks/ases/managers/get_info_report.php",
//             async: false,
//             success: function(msg) {
//                 //se llama el metodo que crea el arreglo global de estudiantes de un monitor en la instancia
//                 transformarConsultaMonitorArray(msg);
//             },
//             dataType: "json",
//             cache: "false",
//             error: function(msg) {
//                 swal({
//                     title: "error info monitor",
//                     html: true,
//                     type: "error",
//                     confirmButtonColor: "#d51b23"
//                 })
//             },
//         });

//         //metodo que agrupa informacion de los seguimientos de pares por el codigo
//         arregloImprimirPares = agrupar_informacion(globalArregloPares, 20);

//         //metodo que agrupa informacion de los seguimientos grupales por los codigos
//         arregloImprimirGrupos = agrupar_informacion(globalArregloGrupal, 12);

//         //metodo que deja un solo registro grupal con el mismo codigo y concatena nombres y codigos de los estudiantes
//         arregloImprimirGrupos = agrupar_Seguimientos_grupales(arregloImprimirGrupos);

//         //se ordena los seguimientos de cada estudiante segun la fecha
//         for (var grupo in arregloImprimirPares) {
//             ordenaPorColumna(arregloImprimirPares[grupo], 19);
//         }

//         //se retorna la informacion del toogle creado desde el punto del monitor
//         return crearTablaYToggle(arregloImprimirPares, noMonitor, arregloImprimirGrupos, codigoMonitor, codigoPracticante);
//     }

//     //funcion que transforma el arreglo retornado por la peticion en un arreglo que posteriormente
//     //se usara para la creacion del toogle
//     function transformarConsultaMonitorArray(consulta) {
//         for (var registro in consulta) {
//             //se extrae informacion dependiendo de si el seguimiento es de pares o grupal
//             if (consulta[registro]["tipo"] == "PARES") {
//                 var array_auxiliar = [];

//                 var fecha = transformarFecha(consulta[registro]["fecha"]);
//                 var nombre = consulta[registro]["nombre_estudiante"];
//                 var apellido = consulta[registro]["apellido_estudiante"];
//                 var profesion = consulta[registro]["profesional"];
//                 var practicante = consulta[registro]["practicantee"];

//                 var nombre_enviar = "";
//                 if (apellido == "" || apellido.length == 0) {
//                     nombre_enviar = nombre;
//                 }
//                 else {
//                     nombre_enviar = nombre + " " + apellido;
//                 }

//                 var nombrem = consulta[registro]["nombre_monitor_creo"];
//                 var apellidom = consulta[registro]["apellido_monitor_creo"];
//                 var nombremon_enviar = "";

//                 if (apellidom == "" || apellidom.length == 0) {
//                     nombremon_enviar = nombrem;
//                 }
//                 else {
//                     nombremon_enviar = nombrem + " " + apellidom;
//                 }
//                 //
//                 array_auxiliar.push(nombre_enviar); //0
//                 array_auxiliar.push(fecha); //1
//                 array_auxiliar.push(consulta[registro]["hora_ini"]); //2
//                 array_auxiliar.push(consulta[registro]["hora_fin"]); //3
//                 array_auxiliar.push(consulta[registro]["lugar"]); //4
//                 array_auxiliar.push(consulta[registro]["tema"]); //5
//                 array_auxiliar.push(consulta[registro]["actividades"]); //6
//                 array_auxiliar.push(consulta[registro]["individual"]); //7
//                 array_auxiliar.push(consulta[registro]["individual_riesgo"]); //8
//                 array_auxiliar.push(consulta[registro]["familiar_desc"]); //9
//                 array_auxiliar.push(consulta[registro]["familiar_riesgo"]); //10
//                 array_auxiliar.push(consulta[registro]["academico"]); //11
//                 array_auxiliar.push(consulta[registro]["academico_riesgo"]); //12
//                 array_auxiliar.push(consulta[registro]["economico"]); //13
//                 array_auxiliar.push(consulta[registro]["economico_riesgo"]); //14
//                 array_auxiliar.push(consulta[registro]["vida_uni"]); //15
//                 array_auxiliar.push(consulta[registro]["vida_uni_riesgo"]); //16
//                 array_auxiliar.push(consulta[registro]["observaciones"]); //17
//                 array_auxiliar.push("saltar"); //18 borra
//                 array_auxiliar.push(consulta[registro]["fecha"]); //19
//                 array_auxiliar.push(consulta[registro]["id_estudiante"]); //20 id talentos
//                 array_auxiliar.push(nombremon_enviar); //21
//                 array_auxiliar.push(consulta[registro]["objetivos"]); //22
//                 array_auxiliar.push(consulta[registro]["id_seguimiento"]); //23
//                 array_auxiliar.push(consulta[registro]["registros_estudiantes_revisados"]); //24
//                 array_auxiliar.push(consulta[registro]["registros_estudiantes_norevisados"]); //25
//                 array_auxiliar.push(consulta[registro]["registros_estudiantes_total"]); //26
//                 array_auxiliar.push(consulta[registro]["profesional"]); //27
//                 array_auxiliar.push(consulta[registro]["practicante"]); //28

//                 globalArregloPares.push(array_auxiliar);
//             }
//             else if (consulta[registro]["tipo"] == "GRUPAL") {
//                 var array_auxiliar = [];

//                 var fecha = transformarFecha(consulta[registro]["fecha"]);
//                 var nombre = consulta[registro]["nombre_estudiante"];
//                 var apellido = consulta[registro]["apellido_estudiante"];
//                 var nombre_enviar = "";
//                 if (apellido == "" || apellido.length == 0) {
//                     nombre_enviar = nombre;
//                 }
//                 else {
//                     nombre_enviar = nombre + " " + apellido;
//                 }

//                 var nombrem = consulta[registro]["nombre_monitor_creo"];
//                 var apellidom = consulta[registro]["apellido_monitor_creo"];
//                 var nombremon_enviar = "";

//                 if (apellidom == "" || apellidom.length == 0) {
//                     nombremon_enviar = nombrem;
//                 }
//                 else {
//                     nombremon_enviar = nombrem + " " + apellidom;
//                 }
//                 array_auxiliar.push(nombre_enviar);
//                 array_auxiliar.push(fecha);
//                 array_auxiliar.push(consulta[registro]["hora_ini"]);
//                 array_auxiliar.push(consulta[registro]["hora_fin"]);
//                 array_auxiliar.push(consulta[registro]["lugar"]);
//                 array_auxiliar.push(consulta[registro]["tema"]);
//                 array_auxiliar.push(consulta[registro]["actividades"]);
//                 array_auxiliar.push(consulta[registro]["objetivos"]);
//                 array_auxiliar.push(consulta[registro]["observaciones"]);
//                 array_auxiliar.push("saltar"); //9 borrar
//                 array_auxiliar.push(consulta[registro]["fecha"]); //10
//                 array_auxiliar.push(consulta[registro]["id_estudiante"]); //11
//                 array_auxiliar.push(consulta[registro]["id_seguimiento"]); //12
//                 array_auxiliar.push(nombremon_enviar); //13
//                 array_auxiliar.push(consulta[registro]["registros_estudiantes_revisados_grupal"]); //14
//                 array_auxiliar.push(consulta[registro]["registros_estudiantes_norevisados_grupal"]); //15
//                 array_auxiliar.push(consulta[registro]["registros_estudiantes_total_grupal"]); //16
//                 globalArregloGrupal.push(array_auxiliar); //

//             }
//         }
//     }

//     //se crea el toogle del practicante el cual tiene cada uno de los estudiantes asignados al monitor
//     function crearTablaYToggle(arregloImprimirPares, monitorNo, arregloImprimirGrupos, codigoEnviarN1, codigoEnviarN2) {
//         var stringRetornar = "";

//         //se recorre cada estudiante
//         for (var student in arregloImprimirPares) {

//             stringRetornar += '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading" style="background-color: #D0C4C4;"><h4 class="panel-title"><a data-toggle="collapse" href="#collapse' + monitorNo + arregloImprimirPares[student][0][20] + '">' + arregloImprimirPares[student][0][0] + '<span> R.P  : <b>' + arregloImprimirPares[student][0][24] + '</b> - NO R.P : <b>' + arregloImprimirPares[student][0][25] + '</b> - Total  : <b>' + arregloImprimirPares[student][0][26] + '</b> </span></a></h4></div>';
//             stringRetornar += '<div id="collapse' + monitorNo + arregloImprimirPares[student][0][20] + '" class="panel-collapse collapse"><div class="panel-body">';

//             //se crea un toogle para cada seguimiento que presente dicho estudiante
//             for (var tupla in arregloImprimirPares[student]) {
//                 stringRetornar += '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" href="#collapse_' + monitorNo + arregloImprimirPares[student][0][20] + tupla + '"> <label  for="fechatext_' + arregloImprimirPares[student][tupla][23] + '"/ id="fecha_texto_' + arregloImprimirPares[student][tupla][23] + '"> Registro : ' + arregloImprimirPares[student][tupla][1] + '</label></a></h4></div>';
//                 stringRetornar += '<div id="collapse_' + monitorNo + arregloImprimirPares[student][0][20] + tupla + '" class="panel-collapse collapse"><div class="panel-body hacer-scroll" style="overflow-y"><table class="table table-hover students_table" id="students_table' + arregloImprimirPares[student][0][20] + arregloImprimirPares[student][0][19] + '">';
//                 stringRetornar += '<thead><tr><th></th><th></th><th></th></tr></thead>';
//                 stringRetornar += '<tbody id=' + tupla + '_' + arregloImprimirPares[student][tupla][23] + '>';
//                 stringRetornar += '<div class="table-info-pilos col-sm-12"><div class="col-sm-4" style="display: none" id="titulo_fecha_' + arregloImprimirPares[student][tupla][23] + '"><b>FECHA :</b><input id="fecha_' + arregloImprimirPares[student][tupla][23] + '" type="date" class="no-borde-fondo fecha"  value="' + arregloImprimirPares[student][tupla][1] + '"/></div></div>';
//                 stringRetornar += '<div class"table-info-pilos col-sm-12"><div class="col-sm-4 "><b>LUGAR:</b> <input id="lugar_' + arregloImprimirPares[student][tupla][23] + '"class="no-borde-fondo editable lugar" readonly value="' + arregloImprimirPares[student][tupla][4] + '"></div><div class="col-md-4" id="hora_inicial_' + arregloImprimirPares[student][tupla][23] + '" style="display: "><label for="h_ini" class="select-hour">HORA INICIO</label><input class="no-borde-fondo fecha" readonly id="h_inicial_texto_' + arregloImprimirPares[student][tupla][23] + '" value="' + arregloImprimirPares[student][tupla][2] + ' "></div><div class="col-md-4" id="mod_hora_ini_' + arregloImprimirPares[student][tupla][23] + '" style="display: none"><label for="h_ini" class="form-control-label col-md-4 col-xs-4">HORA INICIO</label><select  class="select-hour" id="h_ini_' + arregloImprimirPares[student][tupla][23] + '" name="h_ini" ></select><label class="col-md-1 col-xs-1" for="m_ini">:</label><select class="select-hour" id="m_ini_' + arregloImprimirPares[student][tupla][23] + '"  name="m_ini"></select></div><div class="col-md-4" id="hora_final_' + arregloImprimirPares[student][tupla][23] + '" style="display: "><label for="h_ini" class="form-control-label col-md-4 col-xs-4">HORA FIN </label><input class="no-borde-fondo fecha" readonly id="h_final_texto_' + arregloImprimirPares[student][tupla][23] + '" value="' + arregloImprimirPares[student][tupla][3] + '"></div><div class="col-md-4" id="mod_hora_final_' + arregloImprimirPares[student][tupla][23] + '" style="display: none"><label for="h_fin" class="form-control-label col-md-4 col-xs-4">HORA FIN</label><select  class="select-hour" id="h_fin_' + arregloImprimirPares[student][tupla][23] + '" name="h_fin" ></select><label class="col-md-1 col-xs-1" for="m_fin">:</label><select class="select-hour" id="m_fin_' + arregloImprimirPares[student][tupla][23] + '"  name="m_fin"></select></div></div>';
//                 stringRetornar += '<div class="table-info-pilos col-sm-12"><b>TEMA:</b><br><input id="tema_' + arregloImprimirPares[student][tupla][23] + '" class="no-borde-fondo editable tema" readonly  value="' + arregloImprimirPares[student][tupla][5] + '"></div>';
//                 stringRetornar += '<div class="table-info-pilos col-sm-12"><b>OBJETIVOS:</b><br><textarea id="objetivos_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly>' + arregloImprimirPares[student][tupla][22] + '</textarea></div></div>';

//                 var riesgo = "";
//                 var valor = -1;
//                 //se verifica el tipo de riesgo y asi mismo se añadira
//                 //la clase para la identificacion
//                 if (arregloImprimirPares[student][tupla][8] == 1) {
//                     riesgo = "bajo";
//                     valor = 1;
//                 }
//                 else if (arregloImprimirPares[student][tupla][8] == 2) {
//                     riesgo = "medio";
//                     valor = 2;
//                 }
//                 else if (arregloImprimirPares[student][tupla][8] == 3) {
//                     riesgo = "alto";
//                     valor = 3;
//                 }
//                 else {
//                     riesgo = "no";
//                 }

//                 if (riesgo != "no") {
//                     stringRetornar += '<div class="table-info-pilos col-sm-12 riesgo_' + riesgo + '" id="riesgo_individual_' + arregloImprimirPares[student][tupla][23] + '"><b>INDIVIDUAL:</b><br><textarea id="obindividual_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly>' + arregloImprimirPares[student][tupla][7] + '</textarea><br>RIESGO: ' + riesgo;
//                     stringRetornar += '<div class="col-md-12 radio-ocultar ocultar" id="radio_individual_div' + arregloImprimirPares[student][tupla][23] + '">';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_individual_' + arregloImprimirPares[student][tupla][23] + '"  value="1">Bajo';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_individual_' + arregloImprimirPares[student][tupla][23] + '" value="2">Medio';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_individual_' + arregloImprimirPares[student][tupla][23] + '" value="3">Alto';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
//                     stringRetornar += '</div></div>';
//                     stringRetornar += '</td></tr>';


//                 }
//                 else if (riesgo == "no") {
//                     stringRetornar += '<div class="table-info-pilos col-sm-12 riesgo_' + riesgo + ' quitar-ocultar ocultar individual"><b>INDIVIDUAL:</b><br><textarea id="obindividual_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
//                     stringRetornar += '<div class="col-md-12 radio-ocultar ocultar" id="radio_individual_div' + arregloImprimirPares[student][tupla][23] + '">';
//                     stringRetornar += '<label class="radio-inline hidden" >';
//                     stringRetornar += '<input type="radio" name="riesgo_individual_' + arregloImprimirPares[student][tupla][23] + '"  value="0">No registra';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_individual_' + arregloImprimirPares[student][tupla][23] + '"  value="1">Bajo';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_individual_' + arregloImprimirPares[student][tupla][23] + '" value="2">Medio';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_individual_' + arregloImprimirPares[student][tupla][23] + '" value="3">Alto';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
//                     stringRetornar += '</div></div>';
//                     stringRetornar += '</td></tr>';


//                 }


//                 //se verifica el tipo de riesgo y asi mismo se añadira
//                 //la clase para la identificacion
//                 if (arregloImprimirPares[student][tupla][10] == 1) {
//                     riesgo = "bajo";
//                     valor = 1;
//                 }
//                 else if (arregloImprimirPares[student][tupla][10] == 2) {
//                     riesgo = "medio";
//                     valor = 2;
//                 }
//                 else if (arregloImprimirPares[student][tupla][10] == 3) {
//                     riesgo = "alto";
//                     valor = 3;
//                 }
//                 else {
//                     riesgo = "no";
//                 }

//                 if (riesgo != "no") {
//                     stringRetornar += '<div class="table-info-pilos col-sm-12 riesgo_' + riesgo + '" id="riesgo_familiar_' + arregloImprimirPares[student][tupla][23] + '"><b>FAMILIAR:</b><br><textarea id="obfamiliar_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly>' + arregloImprimirPares[student][tupla][9] + '</textarea><br>RIESGO: ' + riesgo;
//                     stringRetornar += '<div class="col-md-12 radio-ocultar ocultar" id="radio_familiar_div' + arregloImprimirPares[student][tupla][23] + '">';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_familiar_' + arregloImprimirPares[student][tupla][23] + '" value="1">Bajo';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_familiar_' + arregloImprimirPares[student][tupla][23] + '" value="2">Medio';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_familiar_' + arregloImprimirPares[student][tupla][23] + '" value="3">Alto';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
//                     stringRetornar += '</div></div>';
//                     stringRetornar += '</td></tr>';

//                 }
//                 else if (riesgo == "no") {
//                     stringRetornar += '<div class="table-info-pilos col-sm-12 riesgo_' + riesgo + ' quitar-ocultar ocultar"><b>FAMILIAR:</b><br><textarea id="obfamiliar_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
//                     stringRetornar += '<div class="col-md-12 radio-ocultar ocultar" id="radio_familiar_div' + arregloImprimirPares[student][tupla][23] + '">';
//                     stringRetornar += '<label class="radio-inline hidden" >';
//                     stringRetornar += '<input type="radio" name="riesgo_familiar_' + arregloImprimirPares[student][tupla][23] + '"  value="0">No registra';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_familiar_' + arregloImprimirPares[student][tupla][23] + '" value="1">Bajo';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_familiar_' + arregloImprimirPares[student][tupla][23] + '" value="2">Medio';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_familiar_' + arregloImprimirPares[student][tupla][23] + '" value="3">Alto';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
//                     stringRetornar += '</div></div>';
//                     stringRetornar += '</td></tr>';
//                 }

//                 //se verifica el tipo de riesgo y asi mismo se añadira
//                 //la clase para la identificacion
//                 if (arregloImprimirPares[student][tupla][12] == 1) {
//                     riesgo = "bajo";
//                     valor = 1;
//                 }
//                 else if (arregloImprimirPares[student][tupla][12] == 2) {
//                     riesgo = "medio";
//                     valor = 2;
//                 }
//                 else if (arregloImprimirPares[student][tupla][12] == 3) {
//                     riesgo = "alto";
//                     valor = 3;
//                 }
//                 else {
//                     riesgo = "no";
//                 }

//                 if (riesgo != "no") {

//                     stringRetornar += '<div class="table-info-pilos col-sm-12 riesgo_' + riesgo + '"id="riesgo_academico_' + arregloImprimirPares[student][tupla][23] + '"><b>ACADEMICO:</b><br><textarea id="obacademico_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly>' + arregloImprimirPares[student][tupla][11] + '</textarea><br>RIESGO: ' + riesgo;
//                     stringRetornar += '<div class="col-md-12 radio-ocultar ocultar" id="radio_academico_div' + arregloImprimirPares[student][tupla][23] + '">';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_academico_' + arregloImprimirPares[student][tupla][23] + '" value="1">Bajo';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_academico_' + arregloImprimirPares[student][tupla][23] + '" value="2">Medio';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_academico_' + arregloImprimirPares[student][tupla][23] + '" value="3">Alto';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
//                     stringRetornar += '</div></div>';
//                     stringRetornar += '</td></tr>';

//                 }
//                 else if (riesgo == "no") {
//                     stringRetornar += '<div class="table-info-pilos col-sm-12 riesgo_' + riesgo + ' quitar-ocultar ocultar"><b>ACADEMICO:</b><br><textarea id="obacademico_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra</div>';
//                     stringRetornar += '<div class="col-md-12 radio-ocultar ocultar" id="radio_academico_div' + arregloImprimirPares[student][tupla][23] + '">';
//                     stringRetornar += '<label class="radio-inline hidden" >';
//                     stringRetornar += '<input type="radio" name="riesgo_academico_' + arregloImprimirPares[student][tupla][23] + '"  value="0">No registra';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_academico_' + arregloImprimirPares[student][tupla][23] + '" value="1">Bajo';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_academico_' + arregloImprimirPares[student][tupla][23] + '" value="2">Medio';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_academico_' + arregloImprimirPares[student][tupla][23] + '" value="3">Alto';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
//                     stringRetornar += '</div>';
//                     stringRetornar += '</td></tr>';
//                 }

//                 //se verifica el tipo de riesgo y asi mismo se añadira
//                 //la clase para la identificacion
//                 if (arregloImprimirPares[student][tupla][14] == 1) {
//                     riesgo = "bajo";
//                     valor = 1;
//                 }
//                 else if (arregloImprimirPares[student][tupla][14] == 2) {
//                     riesgo = "medio";
//                     valor = 2;
//                 }
//                 else if (arregloImprimirPares[student][tupla][14] == 3) {
//                     riesgo = "alto";
//                     valor = 3;
//                 }
//                 else {
//                     riesgo = "no";
//                 }

//                 if (riesgo != "no") {
//                     stringRetornar += '<div class="table-info-pilos col-sm-12 riesgo_' + riesgo + '" id="riesgo_economico_' + arregloImprimirPares[student][tupla][23] + '"><b>ECONOMICO:</b><br><textarea id="obeconomico_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly>' + arregloImprimirPares[student][tupla][13] + '</textarea><br>RIESGO: ' + riesgo;
//                     stringRetornar += '<div class="col-md-12 radio-ocultar ocultar" id="radio_economico_div' + arregloImprimirPares[student][tupla][23] + '">';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_economico_' + arregloImprimirPares[student][tupla][23] + '" value="1">Bajo';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_economico_' + arregloImprimirPares[student][tupla][23] + '" value="2">Medio';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_economico_' + arregloImprimirPares[student][tupla][23] + '" value="3">Alto';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
//                     stringRetornar += '</div></div>';
//                     stringRetornar += '</td></tr>';


//                 }
//                 else if (riesgo == "no") {
//                     stringRetornar += '<div class="table-info-pilos col-sm-12 riesgo_' + riesgo + ' quitar-ocultar ocultar"><b>ECONOMICO:</b><br><textarea id="obeconomico_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
//                     stringRetornar += '<div class="col-md-12 radio-ocultar ocultar" id="radio_economico_div' + arregloImprimirPares[student][tupla][23] + '">';
//                     stringRetornar += '<label class="radio-inline hidden" >';
//                     stringRetornar += '<input type="radio" name="riesgo_economico_' + arregloImprimirPares[student][tupla][23] + '"  value="0">No registra';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_economico_' + arregloImprimirPares[student][tupla][23] + '" value="1">Bajo';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_economico_' + arregloImprimirPares[student][tupla][23] + '" value="2">Medio';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_economico_' + arregloImprimirPares[student][tupla][23] + '" value="3">Alto';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
//                     stringRetornar += '</div></div>';
//                     stringRetornar += '</td></tr>';
//                 }
//                 //se verifica el tipo de riesgo y asi mismo se añadira
//                 //la clase para la identificacion
//                 if (arregloImprimirPares[student][tupla][16] == 1) {
//                     riesgo = "bajo";
//                     valor = 1;
//                 }
//                 else if (arregloImprimirPares[student][tupla][16] == 2) {
//                     riesgo = "medio";
//                     valor = 2;
//                 }
//                 else if (arregloImprimirPares[student][tupla][16] == 3) {
//                     riesgo = "alto";
//                     valor = 3;
//                 }
//                 else {
//                     riesgo = "no";
//                 }

//                 if (riesgo != "no") {
//                     stringRetornar += '<div class="table-info-pilos col-sm-12 riesgo_' + riesgo + '" id="riesgo_universitario_' + arregloImprimirPares[student][tupla][23] + '"><b>UNIVERSITARIO:</b><br><textarea id="obuniversitario_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly>' + arregloImprimirPares[student][tupla][15] + '</textarea><br>RIESGO: ' + riesgo;
//                     stringRetornar += '<div class="col-md-12 radio-ocultar ocultar" id="radio_universitario_div' + arregloImprimirPares[student][tupla][23] + '">';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_universitario_' + arregloImprimirPares[student][tupla][23] + '" value="1">Bajo';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_universitario_' + arregloImprimirPares[student][tupla][23] + '" value="2">Medio';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_universitario_' + arregloImprimirPares[student][tupla][23] + '" value="3">Alto';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
//                     stringRetornar += '</div></div>';
//                     stringRetornar += '</td></tr>';


//                 }
//                 else if (riesgo == "no") {
//                     stringRetornar += '<div class="table-info-pilos col-sm-12 riesgo_' + riesgo + ' quitar-ocultar ocultar"><b>UNIVERSITARIO:</b><br><textarea id="obuniversitario_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
//                     stringRetornar += '<div class="col-md-12 radio-ocultar ocultar" id="radio_universitario_div' + arregloImprimirPares[student][tupla][23] + '">';
//                     stringRetornar += '<label class="radio-inline hidden" >';
//                     stringRetornar += '<input type="radio" name="riesgo_universitario_' + arregloImprimirPares[student][tupla][23] + '"  value="0">No registra';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_universitario_' + arregloImprimirPares[student][tupla][23] + '" value="1">Bajo';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_universitario_' + arregloImprimirPares[student][tupla][23] + '" value="2">Medio';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" >';
//                     stringRetornar += '<input type="radio" name="riesgo_universitario_' + arregloImprimirPares[student][tupla][23] + '" value="3">Alto';
//                     stringRetornar += '</label>';
//                     stringRetornar += '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
//                     stringRetornar += '</div></div>';
//                     stringRetornar += '</td></tr>';
//                 }

//                 stringRetornar += '<div class="table-info-pilos col-sm-12"><b>OBSERVACIONES:</b><br><textarea id="observacionesGeneral_' + arregloImprimirPares[student][tupla][23] + '" class ="no-borde-fondo editable" readonly>' + arregloImprimirPares[student][tupla][17] + '</textarea></div>';

//                 stringRetornar += '<div class="table-info-pilos col-sm-12"><b>CREADO POR:</b><br>' + arregloImprimirPares[student][tupla][21] + '</div>';

//                 //----en caso que tenga el rol correspondiente se añade un campo y un boton para
//                 //enviar un mensaje con observaciones tanto al monitor que hizo el seguimiento como al profesional que lo envia
//                 if (rol == 3 || rol == 7 || name == "administrador" || name == "sistemas1008" || name == "Administrador") {
//                     if (arregloImprimirPares[student][tupla][27] != 1) {

//                         stringRetornar += '<div class="table-info-pilos col-sm-12"><b>REPORTAR OBSERVACIÓN</b><br><textarea  id="textarea_' + arregloImprimirPares[student][tupla][23] + '" class="textarea-seguimiento-pilos" name="individual_' + codigoEnviarN1 + '_' + codigoEnviarN2 + '_' + arregloImprimirPares[student][tupla][1] + '_' + arregloImprimirPares[student][tupla][0] + '" rows="4" cols="150"></textarea><br>';

//                     }
//                     if (arregloImprimirPares[student][tupla][27] == 1) {
//                         stringRetornar += '<div class="col-sm-12"><label class="checkbox-inline"><input type="checkbox" name="profesional"  id="profesional_' + arregloImprimirPares[student][tupla][23] + '" value="1" checked>R. profesional</label><label class="checkbox-inline">';
//                     }
//                     else {
//                         stringRetornar += '<div class="col-sm-12"><label class="checkbox-inline"><input type="checkbox" name="profesional" id="profesional_' + arregloImprimirPares[student][tupla][23] + '" value="1">R. profesional</label><label class="checkbox-inline">';
//                     }

//                     if (arregloImprimirPares[student][tupla][28] == 1) {
//                         stringRetornar += '<input type="checkbox" name="practicante" id="practicante_' + arregloImprimirPares[student][tupla][23] + '" value="1" checked>R. practicante</label></div>';
//                     }
//                     else {
//                         stringRetornar += '<input type="checkbox" name="practicante" id="practicante_' + arregloImprimirPares[student][tupla][23] + '" value="1">R. practicante</label></div>';
//                     }
//                     if (arregloImprimirPares[student][tupla][27] != 1) {
//                         stringRetornar += '<div class="col-sm-12"></div><div class="col-sm-4 col" id="enviar_' + arregloImprimirPares[student][tupla][23] + '" style="display: "><span class="btn btn-info btn-lg  botonCorreo" value="' + arregloImprimirPares[student][tupla][23] + '" id="correo_' + arregloImprimirPares[student][tupla][23] + '" type="button">Enviar observaciones</span></div><div class="col-sm-4" id="editar_' + arregloImprimirPares[student][tupla][23] + '" style="display:"><span class="btn btn-info btn-lg botonesSeguimiento botonEditarSeguimiento" value="' + arregloImprimirPares[student][tupla][23] + '" type="button">Editar</span></div><div class="col-sm-4" id="borrar_' + arregloImprimirPares[student][tupla][23] + '" style="display:"><span class="btn btn-info btn-lg botonBorrar"  value="' + arregloImprimirPares[student][tupla][23] + '" type="button">Borrar</span></div></div>';
//                         stringRetornar += '<div class="col-sm-12"><div class="col-sm-5"><span class="btn btn-info btn-lg botonesSeguimiento botonModificarSeguimiento ocultar" value="' + arregloImprimirPares[student][tupla][23] + '" type="button">Guardar</span></div><div class="col-sm-5"><span class="btn btn-info btn-lg botonesSeguimiento botonCancelarSeguimiento ocultar" value="' + arregloImprimirPares[student][tupla][23] + '" type="button">Cancelar</span></div></div><td></tr>';
//                     }
//                     else {

//                     }

//                 }
//                 else {
//                     if (rol == 4) {
//                         if (arregloImprimirPares[student][tupla][27] == 1) {
//                             stringRetornar += '<div class="col-sm-12"><label class="checkbox-inline"><input type="checkbox" class="hide" name="profesional"  id="profesional_' + arregloImprimirPares[student][tupla][23] + '" value="1" checked></label><label class="checkbox-inline">';
//                         }
//                         else {
//                             stringRetornar += '<div class="col-sm-12"><label class="checkbox-inline"><input type="checkbox" class="hide" name="profesional" id="profesional_' + arregloImprimirPares[student][tupla][23] + '" value="1"></label><label class="checkbox-inline">';
//                         }

//                         if (arregloImprimirPares[student][tupla][28] == 1) {
//                             stringRetornar += '<input type="checkbox" name="practicante" class="hide"   id="practicante_' + arregloImprimirPares[student][tupla][23] + '" value="1" checked></label></div>';
//                         }
//                         else {
//                             stringRetornar += '<input type="checkbox" name="practicante" class="hide"  id="practicante_' + arregloImprimirPares[student][tupla][23] + '" value="1"></label></div>';
//                         }
//                         stringRetornar += '<div class="col-sm-12"><div class="col-sm-4" id="editar_' + arregloImprimirPares[student][tupla][23] + '" style="display:"><span class="btn btn-info btn-lg botonesSeguimiento botonEditarSeguimiento" value="' + arregloImprimirPares[student][tupla][23] + '" type="button">Editar</span></div><div class="col-sm-4" id="borrar_' + arregloImprimirPares[student][tupla][23] + '" style="display:"><span class="btn btn-info btn-lg botonBorrar"  value="' + arregloImprimirPares[student][tupla][23] + '" type="button">Borrar</span></div></div>';
//                         stringRetornar += '<div class="col-sm-12"><div class="col-sm-5"><span class="btn btn-info btn-lg botonesSeguimiento botonModificarSeguimiento ocultar" value="' + arregloImprimirPares[student][tupla][23] + '" type="button">Guardar</span></div><div class="col-sm-5"><span class="btn btn-info btn-lg botonesSeguimiento botonCancelarSeguimiento ocultar" value="' + arregloImprimirPares[student][tupla][23] + '" type="button">Cancelar</span></div></div><td></tr>';

//                     }

//                 }


//                 //cerre el colapsable correspondientes
//                 stringRetornar += '</tbody></table></div></div></div></div>';
//             }
//             stringRetornar += '</div></div></div></div>';
//             //alert(   $("#radio_individual_div18597 .radio-inline input:radio").html() );
//         }

//         //si existen seguimiento grupales
//         if (arregloImprimirGrupos.length != 0) {
//             stringRetornar += '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading" style="background-color: #D0C4C4;"><h4 class="panel-title"><a data-toggle="collapse" href="#collapsegroup' + monitorNo + arregloImprimirGrupos[0][11] + '">SEGUIMIENTOS GRUPALES</a><span> R.P  : <b>' + arregloImprimirGrupos[0][14] + '</b> - NO R.P : <b>' + arregloImprimirGrupos[0][15] + '</b> - Total  : <b>' + arregloImprimirGrupos[0][16] + '</b> </span></h4></div>';
//             stringRetornar += '<div id="collapsegroup' + monitorNo + arregloImprimirGrupos[0][11] + '" class="panel-collapse collapse"><div class="panel-body">';
//             for (var grupo in arregloImprimirGrupos) {
//                 stringRetornar += '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" href="#collapsegroup' + monitorNo + grupo + arregloImprimirGrupos[grupo][11] + '">' + arregloImprimirGrupos[grupo][1] + '</a></h4></div>';
//                 stringRetornar += '<div id="collapsegroup' + monitorNo + grupo + arregloImprimirGrupos[grupo][11] + '" class="panel-collapse collapse"><div class="panel-body"><table class="table table-hover" id="grouptable">';
//                 stringRetornar += '<thead><tr><th></th><th></th><th></th></tr></thead>';
//                 stringRetornar += '<tbody id=' + grupo + '_' + arregloImprimirGrupos[grupo][12] + '>';
//                 stringRetornar += '<tr><td>' + arregloImprimirGrupos[grupo][1] + '</td>';
//                 stringRetornar += '<td>LUGAR: ' + arregloImprimirGrupos[grupo][4] + '</td>';
//                 stringRetornar += '<td>HORA: ' + arregloImprimirGrupos[grupo][2] + '-' + arregloImprimirGrupos[grupo][3] + '</td></tr>';

//                 stringRetornar += '<tr><td colspan="3"><b>ESTUDIANTES:</b><br> ' + arregloImprimirGrupos[grupo][17] + '</td></tr>';

//                 stringRetornar += '<tr><td colspan="3"><b>TEMA:</b><br> ' + arregloImprimirGrupos[grupo][5] + '</td></tr>';

//                 stringRetornar += '<tr><td colspan="3"><b>ACTIVIDADES GRUPALES:</b><br> ' + arregloImprimirGrupos[grupo][6] + '</td></tr>';

//                 stringRetornar += '<tr><td colspan="3"><b>OBSERVACIONES:</b><br>' + arregloImprimirGrupos[grupo][7] + '</td></tr>';

//                 stringRetornar += '<tr><td colspan="3"><b>CREADO POR:</b><br>' + arregloImprimirPares[student][tupla][21] + '</td></tr>';


//                 if (rol == 3 || rol == 7 || (name == "administrador" || name == "sistemas1008" || name == "Administrador")) {
//                     stringRetornar += '<tr><td colspan="3"><b>REPORTAR OBSERVACIÓN</b><br><textarea id="grupal_' + codigoEnviarN1 + '_' + codigoEnviarN2 + '_' + arregloImprimirGrupos[grupo][1] + '_' + arregloImprimirGrupos[grupo][14] + '" rows="4" cols="150"></textarea><br><br><span class="btn btn-info btn-lg botonCorreo" value="' + arregloImprimirPares[student][tupla][23] + '" type="button">Enviar observaciones</span><td></tr>';
//                 }

//                 //en caso que tenga el rol correspondiente se añade un campo y un boton para
//                 //enviar un mensaje con observaciones tanto al monitor que hizo el seguimiento como al profesional que lo envia
//                 stringRetornar += '</tbody></table></div></div></div></div>';
//             }
//             stringRetornar += '</div></div></div></div>';

//         }

//         globalArregloPares = [];
//         globalArregloGrupal = [];

//         return stringRetornar;
//     }


//     //******************************************************************************************************
//     //******************************************************************************************************
//     //******************************************************************************************************

//     //funcion para agrupar los seguimientos segun el monitor
//     function agrupar_informacion(infoMonitor, campoComparar) {
//         var nuevoArreglo = [];
//         //se recorren todos los estudiantes
//         for (var unico in infoMonitor) {
//             //se inician variables
//             var confirmarAnanir = "si";
//             var posicion = 0;
//             //si es el primer elemento del arreglo siempre se añadira
//             if (nuevoArreglo.length != 0) {
//                 //si ya hay elementos en el arreglo
//                 for (var actuales in nuevoArreglo) {
//                     //se verifica que no exista otra persona con el mismo nombre
//                     if (infoMonitor[unico][campoComparar] == nuevoArreglo[actuales][0][campoComparar]) {
//                         //si existe entonces no se añadira un nuevo al arreglo sino uno nuevo a la posicion
//                         confirmarAnanir = "no";
//                         posicion = actuales;
//                     }
//                 }
//             }
//             //si se retorna si es decir que no existen registros del estudiante
//             if (confirmarAnanir == "si") {
//                 var arregloEstudiante = [];
//                 //se agrega al arreglo
//                 var tamano = nuevoArreglo.length;
//                 //alert(tamano);
//                 arregloEstudiante.push(infoMonitor[unico]);
//                 nuevoArreglo[tamano] = arregloEstudiante;
//             }
//             else {
//                 var arregloEstudiante = [];
//                 arregloEstudiante = nuevoArreglo[posicion];
//                 arregloEstudiante.push(infoMonitor[unico]);
//                 //si no es prque ya tiene registro asi que se agrega registro al estudiante
//                 nuevoArreglo[posicion] = [];
//                 nuevoArreglo[posicion] = arregloEstudiante;
//             }
//         }
//         return nuevoArreglo;
//     }

//     //funcion para agrupar los seguimientos grupales segun el id
//     function agrupar_Seguimientos_grupales(arreglo) {
//         var NuevoArregloGrupal = [];
//         for (var elementoRevisar in arreglo) {
//             var arregloAuxiliar = arreglo[elementoRevisar][0].slice();
//             var nombres = "";
//             var nombresImpirmir = "";
//             var codigos = "";
//             var contador = 1;

//             //funcion que captura tanto los nombres como los codigos y crea un texto
//             //para cada uno los cuales seran usado para ponerse en la tabla
//             for (var tuplaGrupo = 0; tuplaGrupo < arreglo[elementoRevisar].length; tuplaGrupo++) {
//                 if (tuplaGrupo == (arreglo[elementoRevisar].length) - 1) {
//                     nombres += arreglo[elementoRevisar][tuplaGrupo][0];
//                     nombresImpirmir += arreglo[elementoRevisar][tuplaGrupo][0];
//                     codigos += arreglo[elementoRevisar][tuplaGrupo][11];
//                 }
//                 else {
//                     nombres += arreglo[elementoRevisar][tuplaGrupo][0];
//                     nombresImpirmir += arreglo[elementoRevisar][tuplaGrupo][0] + ",";
//                     codigos += arreglo[elementoRevisar][tuplaGrupo][11];
//                 }
//             }

//             //se al arreglo los nombres y los codigos concatenados al final del arreglo
//             arregloAuxiliar[0] = nombres;
//             arregloAuxiliar[11] = codigos;
//             arregloAuxiliar.push(nombresImpirmir);
//             NuevoArregloGrupal.push(arregloAuxiliar);
//         }

//         return NuevoArregloGrupal;
//     }

//     //******************************************************************************************************
//     //******************************************************************************************************
//     //******************************************************************************************************

//     //Funcion que añade un comboBox para los roles especificos y consulta codigos
//     //predefinidos
//     function anadirEvento() {
//         //terminar..

//         var selectanadir = '<div class="form-group col-sm-12"><div class="col-sm-3"><label class="control-label" for="email">Seleccione rol :</label><select id="selectProfesional" name="divCategoriaPadre" class="selectPadre col-md-offset-2"><option value=4 selected="selected">Monitor</option><option value=7>Practicante</option><option value=3>Profesional</option></select></div> ';
//         selectanadir += '<div class="col-sm-3"><label class="control-label" for="semestre">Seleccione semestre :</label><select id="semestre" name="divCategoriaPadre" class="selectPadre col-md-offset-2"></select></div>';
//         selectanadir += '<div class="col-sm-3"><label class="control-label" for="persona">Seleccione persona :</label><select id="persona" name="divCategoriaPadre" class="selectPadre col-md-offset-2"></select></div>';
//         selectanadir += '<div class="col-sm-3"><span class="btn btn-primary col-md-offset-2" id="consultarMonitores" class="submit">Consultar</span></div></div>';


//         $('#anadir').append(selectanadir);

//         $('#consultarMonitores').on('click', function() {
//             var v = $('#selectProfesional').val();
//             if (v == "inicio") {
//                 alert("Seleccione una opcion");
//             }
//             else {
//                 if (v == 4) {
//                     obtenerMonitores();
//                     $('#titulo').text("Informacion Estudiantes");
//                     //htmltexto=monitorUser(120,0,19,121);
//                     //alert("entro m act")
//                     // htmltexto=monitorUser(1055,0,534,0);
//                 }
//                 else if (v == 7) {
//                     $('#titulo').text("Informacion Practicante");
//                     // htmltexto=practicanteUser(103132,450299);
//                     //alert("entro pract")
//                     htmltexto = practicanteUser(1113, 534);
//                 }
//                 else if (v == 3) {
//                     $('#titulo').text("Informacion Profesional");
//                     // htmltexto=profesionalUser(110953,450299);
//                     //alert("entro prof")
//                     htmltexto = profesionalUser(122, 19);
//                 }

//                 $('#reemplazarToogle').html(htmltexto);
//             }

//         });
//     }


//     //Funcion que ordena un arreglo segun la columna definida de menos valor a mayor
//     function ordenaPorColumna(arreglo, col) {

//         var aux;

//         // Recorro la columna selecciona
//         for (var i = 0; i < arreglo.length; i++) {
//             for (var j = i + 1; j < arreglo.length; j++) {
//                 // Verifico si el elemento en la posición [i][col] es mayor que el de la posición [j][col]
//                 if (arreglo[i][col] < arreglo[j][col]) {
//                     // Recorro las filas seleccionadas (i, j) e intercambio los elementos
//                     // Declaro la variable k para controlar la posición (columnas) en la fila
//                     for (var k = 0; k < arreglo[i].length; k++) {
//                         // Intercambio los elementos de las filas seleccionadas columna por columna
//                         aux = arreglo[i][k];
//                         arreglo[i][k] = arreglo[j][k];
//                         arreglo[j][k] = aux;
//                     }
//                 }
//             }
//         }
//     }

//     //Obtiene los mensajes de validación de la hora.
//     function validarHoras(h_ini, h_fin, m_ini, m_fin) {
//         var detalle = "";
//         if (h_ini > h_fin) {
//             detalle += "* La hora final debe ser mayor a la inicial<br>";
//         }
//         else if (h_ini == h_fin) {
//             if (m_ini > m_fin) {
//                 isvalid = false;
//                 detalle += "* La hora final debe ser mayor a la inicial<br>";
//             }
//             else {
//                 if (m_ini == m_fin) {
//                     detalle += "* Las horas seleccionadas deben ser diferentes<br>";
//                 }
//             }

//         }
//         return detalle;

//     }


//     //Inicializa las horas y minutos.
//     function initFormSeg(id) {
//         var date = new Date();
//         var minutes = date.getMinutes();
//         var hour = date.getHours();
//         //incializar hora
//         var hora = "";
//         for (var i = 0; i < 24; i++) {
//             if (i == hour) {
//                 if (hour < 10) hour = "0" + hour;
//                 hora += "<option value=\"" + hour + "\" selected>" + hour + "</option>";
//             }
//             else if (i < 10) {
//                 hora += "<option value=\"" + i + "\">0" + i + "</option>";
//             }
//             else {
//                 hora += "<option value=\"" + i + "\">" + i + "</option>";
//             }
//         }
//         var min = "";
//         for (var i = 0; i < 60; i++) {

//             if (i == minutes) {
//                 if (minutes < 10) minutes = "0" + minutes;
//                 min += "<option value=\"" + minutes + "\" selected>" + minutes + "</option>";
//             }
//             else if (i < 10) {
//                 min += "<option value=\"" + i + "\">0" + i + "</option>";
//             }
//             else {
//                 min += "<option value=\"" + i + "\">" + i + "</option>";
//             }
//         }
//         $('#h_ini_' + id).append(hora);
//         $('#m_ini_' + id).append(min);
//         $('#h_fin_' + id).append(hora);
//         $('#m_fin_' + id).append(min);
//         $('#seguimiento #m_fin').append(min);
//     }

//     //funcion que transforma la fecha guardada en el campus en formato epoch a un formato
//     //identificable para las personas
//     function transformarFecha(fecha) {
//         var a = new Date(fecha * 1000);
//         var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
//         var year = a.getFullYear();
//         var month = months[a.getMonth()];
//         var date = a.getDate();
//         var time = date + ' ' + month + ' ' + year;
//         return time;
//     }

//     // Volver a formato fecha
//     function getFormatoFecha(fecha) {
//         var fecha_array = [];
//         var datos = fecha.split(" ");
//         var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
//         var fecha_final = datos[5] + "-" + (months.indexOf(datos[4]) + 1) + "-" + datos[3];
//         if (fecha_final.length == 9 || fecha_final.length == 8) {
//             var datos_corregido = fecha_final.split("-");
//             if (datos_corregido[1].length == 1) {
//                 fecha_array[1] = '0' + datos_corregido[1];
//             }
//             else {
//                 fecha_array[1] = datos_corregido[1];
//             }

//             if (datos_corregido[2].length == 1) {
//                 fecha_array[2] = '0' + datos_corregido[2];

//             }
//             else {
//                 fecha_array[2] = datos_corregido[2];
//             }
//             fecha_array[0] = datos_corregido[0];

//             fecha_final = fecha_array[0] + "-" + fecha_array[1] + "-" + fecha_array[2];
//         }
//         return fecha_final;
//     }

//     function cantidadSeguimientosMonitor(arreglo) {

//         var cantidad = 0;
//         for (var estudiante in arreglo) {
//             for (seguimiento in estudiante) {
//                 cantidad++;
//             }
//         }

//         return cantidad;

//     }
//     //Oculta y muestra botones al presionar cancelar.
//     function auxiliar_cancelar(id) {
//         $("#titulo_fecha_" + id).hide();
//         $("#borrar_" + id).show();
//         $("#editar_" + id).show();
//         $("#enviar_" + id).show();
//         $("#hora_final_" + id).show();
//         $("#mod_hora_final_" + id).hide();
//         $("#hora_inicial_" + id).show();
//         $("#mod_hora_ini_" + id).hide();
//     }

//     //Oculta y muestra botones al presionar editar, organiza fecha y horas.
//     function auxiliar_editar(id) {
//         $("#borrar_" + id).hide();
//         $("#editar_" + id).hide();
//         $("#enviar_" + id).hide();
//         $("#hora_final_" + id).hide();
//         $("#hora_inicial_" + id).hide();
//         $("#titulo_fecha_" + id).show();
//         $("#mod_hora_final_" + id).show();
//         $("#mod_hora_ini_" + id).show();

//         var f1 = $("#h_inicial_texto_" + id).val();
//         var f2 = $("#h_final_texto_" + id).val();
//         var array_f1 = f1.split(":");
//         var array_f2 = f2.split(":");
//         initFormSeg(id);
//         //Seleccionamos la hora deacuerdo al sistema
//         $("#h_ini_" + id + " option[value=" + array_f1[0] + "]").attr("selected", true);
//         $("#m_ini_" + id + " option[value=" + array_f1[1] + "]").attr("selected", true);
//         $("#h_fin_" + id + " option[value=" + array_f2[0] + "]").attr("selected", true);
//         $("#m_fin_" + id + " option[value=" + array_f2[1] + "]").attr("selected", true);
//         var date = $("label[for='fechatext_" + id + "']").text();
//         var fecha_formateada = getFormatoFecha(date);
//         $("#fecha_" + id).val(fecha_formateada);
//     }


//     //Limpia los campos de riesgos y deschequea su prioridad.
//     function auxiliar_limpiar(texto, id) {
//         $(texto + id).removeClass("riesgo_bajo");
//         $(texto + id).removeClass("riesgo_medio");
//         $(texto + id).removeClass("riesgo_alto");
//         var text = '"' + texto.replace("#", "") + id + '"';
//         $('input:radio[name=' + text + ']').each(function(i) {
//             this.checked = false;
//         });

//     }


//     //En el caso de que el check esté revisado por un profesional 
//     //quita los botones de editar,borrar y observaciones.
//     function revisado_profesional(id) {
//         if ($("#profesional_" + id).is(':checked')) {
//             $("#borrar_" + id).hide();
//             $("#editar_" + id).hide();
//             $("#enviar_" + id).hide();
//         }
//     }

//     //Selecciona los radiobuttons correspondientes con la prioridad del riesgo.
//     function seleccionarButtons(id_seguimiento) {

//         //Riesgo individual
//         if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_bajo')) {
//             $("input[name=riesgo_individual_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
//         }
//         else if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_medio')) {
//             $("input[name=riesgo_individual_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
//         }
//         else if ($("#riesgo_individual_" + id_seguimiento).is('.riesgo_alto')) {
//             $("input[name=riesgo_individual_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
//         }
//         else {
//             $("input[name=riesgo_individual_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

//         }

//         //Riesgo familiar
//         if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_bajo')) {
//             $("input[name=riesgo_familiar_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
//         }
//         else if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_medio')) {
//             $("input[name=riesgo_familiar_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
//         }
//         else if ($("#riesgo_familiar_" + id_seguimiento).is('.riesgo_alto')) {
//             $("input[name=riesgo_familiar_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
//         }
//         else {
//             $("input[name=riesgo_familiar_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

//         }

//         //Riesgo academico
//         if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_bajo')) {
//             $("input[name=riesgo_academico_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
//         }
//         else if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_medio')) {
//             $("input[name=riesgo_academico_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
//         }
//         else if ($("#riesgo_academico_" + id_seguimiento).is('.riesgo_alto')) {
//             $("input[name=riesgo_academico_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
//         }
//         else {
//             $("input[name=riesgo_academico_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

//         }

//         //Riesgo economico
//         if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_bajo')) {
//             $("input[name=riesgo_economico_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
//         }
//         else if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_medio')) {
//             $("input[name=riesgo_economico_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
//         }
//         else if ($("#riesgo_economico_" + id_seguimiento).is('.riesgo_alto')) {
//             $("input[name=riesgo_economico_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
//         }
//         else {
//             $("input[name=riesgo_economico_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

//         }

//         //Riesgo universitario
//         if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_bajo')) {
//             $("input[name=riesgo_universitario_" + id_seguimiento + "][value=1]").attr('checked', 'checked');
//         }
//         else if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_medio')) {
//             $("input[name=riesgo_universitario_" + id_seguimiento + "][value=2]").attr('checked', 'checked');
//         }
//         else if ($("#riesgo_universitario_" + id_seguimiento).is('.riesgo_alto')) {
//             $("input[name=riesgo_universitario_" + id_seguimiento + "][value=3]").attr('checked', 'checked');
//         }
//         else {
//             $("input[name=riesgo_universitario_" + id_seguimiento + "][value=0]").attr('checked', 'checked');

//         }

//     }
// });
