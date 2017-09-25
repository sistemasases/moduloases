<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> db_management
// requirejs(['jquery', 'jquery-picker', 'sweetalert', 'jqueryui-picker'], function($) {
//     //editar

//     $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
//         var id = $(this).attr("value");
//         var $tbody = $(this).parent().parent().parent();
//         $tbody.find('.editable').removeAttr('readonly');
//         $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//         $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//         $tbody.find('.radio-ocultar').toggleClass('ocultar');
//         $("#titulo_fecha_" + id).show();
//         $("#borrar_" + id).hide();
//         $("#editar_" + id).hide();
//         $("#enviar_" + id).hide();
//         $("#hora_final_" + id).hide();
//         $("#mod_hora_final_" + id).show();
//         $("#hora_inicial_" + id).hide();
//         $("#mod_hora_ini_" + id).show();
//         initFormSeg(id);
//     });


//     //cancelar
//     $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
//         var id = $(this).attr("value");
//         var $tbody = $(this).parent().parent().parent();
//         $tbody.find('.editable').attr('readonly', true);
//         $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//         $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//         $tbody.find('.radio-ocultar').toggleClass('ocultar');
//         $("#titulo_fecha_" + id).hide();
//         $("#borrar_" + id).show();
//         $("#editar_" + id).show();
//         $("#enviar_" + id).show();
//         $("#hora_final_" + id).show();
//         $("#mod_hora_final_" + id).hide();
//         $("#hora_inicial_" + id).show();
//         $("#mod_hora_ini_" + id).hide();
//     });

//     // modificar
//     $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
//         var id = $(this).attr("value");
//         var profesional = "";
//         var practicante = "";
//         var combo_hora_inicio = document.getElementById("h_ini_" + id);
//         var combo_hora_fin = document.getElementById("h_fin_" + id);
//         var combo_min_inicio = document.getElementById("m_ini_" + id);
//         var combo_min_fin = document.getElementById("m_fin_" + id);
//         var hora_inicial = combo_hora_inicio.options[combo_hora_inicio.selectedIndex].text;
//         var hora_final = combo_hora_fin.options[combo_hora_fin.selectedIndex].text;
//         var min_inicial = combo_min_inicio.options[combo_min_inicio.selectedIndex].text;
//         var min_final = combo_min_fin.options[combo_min_fin.selectedIndex].text;
//         var validar = validarHoras(hora_inicial, hora_final, min_inicial, min_final);



//         if (validar == "") {

//             if ($("#profesional_" + id).is(':checked')) {
//                 profesional = 1;
//             }
//             else {
//                 profesional = 0;
//             }

//             if ($("#practicante_" + id).is(':checked')) {
//                 practicante = 1;
//             }
//             else {
//                 practicante = 0;
//             }

//             var $tbody = $(this).parent().parent().parent();
//             var idSeguimientoActualizar = $(this).attr('value');
//             var lugar = $tbody.find("#lugar_" + idSeguimientoActualizar).val();
//             var tema = $tbody.find("#tema_" + id).val();
//             var objetivos = $tbody.find("#objetivos_" + id).val();
//             var obindividual = $tbody.find("#obindividual_" + id).val();
//             var riesgoIndividual = $("input[name='riesgo_individual_" + id + "']:checked").val();
//             var obfamiliar = $tbody.find("#obfamiliar_" + id).val();
//             var riesgoFamiliar = $("input[name='riesgo_familiar_" + id + "']:checked").val();
//             var obacademico = $tbody.find("#obacademico_" + id).val();
//             var riesgoAcademico = $("input[name='riesgo_academico_" + id + "']:checked").val();
//             var obeconomico = $tbody.find("#obeconomico_" + id).val();
//             var riesgoEconomico = $("input[name='riesgo_economico_" + id + "']:checked").val();
//             var obuniversitario = $tbody.find("#obuniversitario_" + id).val();
//             var riesgoUniversitario = $("input[name='riesgo_universitario_" + id + "']:checked").val();
//             var observacionesGeneral = $tbody.find("#observacionesGeneral_" + id).val();
//             var fecha = $tbody.find("#fecha_" + id).val();
//             var h_inicial = hora_inicial + ":" + min_inicial;
//             var h_final = hora_final + ":" + min_final;


//             $.ajax({
//                 type: "POST",
//                 data: {
//                     id: idSeguimientoActualizar,
//                     lugar: lugar,
//                     tema: tema,
//                     objetivos: objetivos,
//                     obindividual: obindividual,
//                     riesgoIndividual: riesgoIndividual,
//                     obfamiliar: obfamiliar,
//                     riesgoFamiliar: riesgoFamiliar,
//                     obacademico: obacademico,
//                     riesgoAcademico: riesgoAcademico,
//                     obeconomico: obeconomico,
//                     riesgoEconomico: riesgoEconomico,
//                     obuniversitario: obuniversitario,
//                     riesgoUniversitario: riesgoUniversitario,
//                     observacionesGeneral: observacionesGeneral,
//                     practicante: practicante,
//                     profesional: profesional,
//                     fecha: fecha,
//                     h_inicial: h_inicial,
//                     h_final: h_final,
//                     type: "actualizar_registro",

//                 },
//                 url: "../../../blocks/ases/managers/get_info_report.php",
//                 async: false,
//                 success: function(msg) {
//                     if (msg == "0") {
//                         swal({
//                             title: "error al actualizar registro",
//                             html: true,
//                             type: "error",
//                             confirmButtonColor: "#d51b23"
//                         });
//                     }
//                     else if (msg == "1") {
//                         swal("¡Hecho!", "El registro ha sido actualizado",
//                             "success");
//                     }
//                     else {
//                         swal({
//                             title: "Debe ingresar los datos completamente",
//                             html: true,
//                             type: "warning",
//                             confirmButtonColor: "#d51b23"
//                         });
//                     }
//                 },
//                 dataType: "text",
//                 cache: "false",
//                 error: function(msg) {},
//             });

//         }
//         else {
//             swal({
//                 title: validar,
//                 html: true,
//                 type: "warning",
//                 confirmButtonColor: "#d51b23"
//             });
//         }
//     });
//     //borrar
//     $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
//         var id_registro = $(this).attr('value');
//         swal({
//                 title: "¿Seguro que desea eliminar el registro?",
//                 text: "No podrás deshacer este paso",
//                 type: "warning",
//                 showCancelButton: true,
//                 cancelButtonText: "No",
//                 confirmButtonColor: "#d51b23",
//                 confirmButtonText: "Si",
//                 closeOnConfirm: false
//             },


//             function() {

//                 $.ajax({
//                     type: "POST",
//                     data: {
//                         id: id_registro,
//                         type: "eliminar_registro",
//                     },
//                     url: "../../../blocks/ases/managers/get_info_report.php",
//                     async: false,
//                     success: function(msg) {
//                         if (msg == 0) {
//                             swal({
//                                 title: "error al borrar registro",
//                                 html: true,
//                                 type: "error",
//                                 confirmButtonColor: "#d51b23"
//                             });
//                         }
//                         else {

//                             swal("¡Hecho!",
//                                 "El registro ha sido eliminado",
//                                 "success");
//                         }
//                     },
//                     dataType: "text",
//                     cache: "false",
//                     error: function(msg) {},
//                 });
//             });
//     });

//     function validarHoras(h_ini, h_fin, m_ini, m_fin) {
//         var detalle = "";

//         if (h_ini > h_fin) {
//             detalle += "La hora final debe ser mayor a la inicial.";
//         }
//         else if (h_ini == h_fin) {
//             if (m_ini > m_fin) {
//                 isvalid = false;
//                 detalle += "La hora final debe ser mayor a la inicial.";
//             }
//             else {
//                 if (m_ini == m_fin) {
//                     detalle += "Las horas seleccionadas deben ser diferentes.";
//                 }
//             }

//         }
//         return detalle;

//     }




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
//                 hora += "<option value=\"0" + i + "\">0" + i + "</option>";
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
//                 min += "<option value=\"0" + i + "\">0" + i + "</option>";
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
// });
<<<<<<< HEAD
=======
// requirejs(['jquery', 'jquery-picker', 'sweetalert', 'jqueryui-picker'], function($) {
//     //editar

//     $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
//         var id = $(this).attr("value");
//         var $tbody = $(this).parent().parent().parent();
//         $tbody.find('.editable').removeAttr('readonly');
//         $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//         $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//         $tbody.find('.radio-ocultar').toggleClass('ocultar');
//         $("#titulo_fecha_" + id).show();
//         $("#borrar_" + id).hide();
//         $("#editar_" + id).hide();
//         $("#enviar_" + id).hide();
//         $("#hora_final_" + id).hide();
//         $("#mod_hora_final_" + id).show();
//         $("#hora_inicial_" + id).hide();
//         $("#mod_hora_ini_" + id).show();
//         initFormSeg(id);
//     });


//     //cancelar
//     $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
//         var id = $(this).attr("value");
//         var $tbody = $(this).parent().parent().parent();
//         $tbody.find('.editable').attr('readonly', true);
//         $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
//         $tbody.find('.quitar-ocultar').toggleClass('ocultar');
//         $tbody.find('.radio-ocultar').toggleClass('ocultar');
//         $("#titulo_fecha_" + id).hide();
//         $("#borrar_" + id).show();
//         $("#editar_" + id).show();
//         $("#enviar_" + id).show();
//         $("#hora_final_" + id).show();
//         $("#mod_hora_final_" + id).hide();
//         $("#hora_inicial_" + id).show();
//         $("#mod_hora_ini_" + id).hide();
//     });

//     // modificar
//     $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
//         var id = $(this).attr("value");
//         var profesional = "";
//         var practicante = "";
//         var combo_hora_inicio = document.getElementById("h_ini_" + id);
//         var combo_hora_fin = document.getElementById("h_fin_" + id);
//         var combo_min_inicio = document.getElementById("m_ini_" + id);
//         var combo_min_fin = document.getElementById("m_fin_" + id);
//         var hora_inicial = combo_hora_inicio.options[combo_hora_inicio.selectedIndex].text;
//         var hora_final = combo_hora_fin.options[combo_hora_fin.selectedIndex].text;
//         var min_inicial = combo_min_inicio.options[combo_min_inicio.selectedIndex].text;
//         var min_final = combo_min_fin.options[combo_min_fin.selectedIndex].text;
//         var validar = validarHoras(hora_inicial, hora_final, min_inicial, min_final);



//         if (validar == "") {

//             if ($("#profesional_" + id).is(':checked')) {
//                 profesional = 1;
//             }
//             else {
//                 profesional = 0;
//             }

//             if ($("#practicante_" + id).is(':checked')) {
//                 practicante = 1;
//             }
//             else {
//                 practicante = 0;
//             }

//             var $tbody = $(this).parent().parent().parent();
//             var idSeguimientoActualizar = $(this).attr('value');
//             var lugar = $tbody.find("#lugar_" + idSeguimientoActualizar).val();
//             var tema = $tbody.find("#tema_" + id).val();
//             var objetivos = $tbody.find("#objetivos_" + id).val();
//             var obindividual = $tbody.find("#obindividual_" + id).val();
//             var riesgoIndividual = $("input[name='riesgo_individual_" + id + "']:checked").val();
//             var obfamiliar = $tbody.find("#obfamiliar_" + id).val();
//             var riesgoFamiliar = $("input[name='riesgo_familiar_" + id + "']:checked").val();
//             var obacademico = $tbody.find("#obacademico_" + id).val();
//             var riesgoAcademico = $("input[name='riesgo_academico_" + id + "']:checked").val();
//             var obeconomico = $tbody.find("#obeconomico_" + id).val();
//             var riesgoEconomico = $("input[name='riesgo_economico_" + id + "']:checked").val();
//             var obuniversitario = $tbody.find("#obuniversitario_" + id).val();
//             var riesgoUniversitario = $("input[name='riesgo_universitario_" + id + "']:checked").val();
//             var observacionesGeneral = $tbody.find("#observacionesGeneral_" + id).val();
//             var fecha = $tbody.find("#fecha_" + id).val();
//             var h_inicial = hora_inicial + ":" + min_inicial;
//             var h_final = hora_final + ":" + min_final;


//             $.ajax({
//                 type: "POST",
//                 data: {
//                     id: idSeguimientoActualizar,
//                     lugar: lugar,
//                     tema: tema,
//                     objetivos: objetivos,
//                     obindividual: obindividual,
//                     riesgoIndividual: riesgoIndividual,
//                     obfamiliar: obfamiliar,
//                     riesgoFamiliar: riesgoFamiliar,
//                     obacademico: obacademico,
//                     riesgoAcademico: riesgoAcademico,
//                     obeconomico: obeconomico,
//                     riesgoEconomico: riesgoEconomico,
//                     obuniversitario: obuniversitario,
//                     riesgoUniversitario: riesgoUniversitario,
//                     observacionesGeneral: observacionesGeneral,
//                     practicante: practicante,
//                     profesional: profesional,
//                     fecha: fecha,
//                     h_inicial: h_inicial,
//                     h_final: h_final,
//                     type: "actualizar_registro",

//                 },
//                 url: "../../../blocks/ases/managers/get_info_report.php",
//                 async: false,
//                 success: function(msg) {
//                     if (msg == "0") {
//                         swal({
//                             title: "error al actualizar registro",
//                             html: true,
//                             type: "error",
//                             confirmButtonColor: "#d51b23"
//                         });
//                     }
//                     else if (msg == "1") {
//                         swal("¡Hecho!", "El registro ha sido actualizado",
//                             "success");
//                     }
//                     else {
//                         swal({
//                             title: "Debe ingresar los datos completamente",
//                             html: true,
//                             type: "warning",
//                             confirmButtonColor: "#d51b23"
//                         });
//                     }
//                 },
//                 dataType: "text",
//                 cache: "false",
//                 error: function(msg) {},
//             });

//         }
//         else {
//             swal({
//                 title: validar,
//                 html: true,
//                 type: "warning",
//                 confirmButtonColor: "#d51b23"
//             });
//         }
//     });
//     //borrar
//     $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
//         var id_registro = $(this).attr('value');
//         swal({
//                 title: "¿Seguro que desea eliminar el registro?",
//                 text: "No podrás deshacer este paso",
//                 type: "warning",
//                 showCancelButton: true,
//                 cancelButtonText: "No",
//                 confirmButtonColor: "#d51b23",
//                 confirmButtonText: "Si",
//                 closeOnConfirm: false
//             },


//             function() {

//                 $.ajax({
//                     type: "POST",
//                     data: {
//                         id: id_registro,
//                         type: "eliminar_registro",
//                     },
//                     url: "../../../blocks/ases/managers/get_info_report.php",
//                     async: false,
//                     success: function(msg) {
//                         if (msg == 0) {
//                             swal({
//                                 title: "error al borrar registro",
//                                 html: true,
//                                 type: "error",
//                                 confirmButtonColor: "#d51b23"
//                             });
//                         }
//                         else {

//                             swal("¡Hecho!",
//                                 "El registro ha sido eliminado",
//                                 "success");
//                         }
//                     },
//                     dataType: "text",
//                     cache: "false",
//                     error: function(msg) {},
//                 });
//             });
//     });

//     function validarHoras(h_ini, h_fin, m_ini, m_fin) {
//         var detalle = "";

//         if (h_ini > h_fin) {
//             detalle += "La hora final debe ser mayor a la inicial.";
//         }
//         else if (h_ini == h_fin) {
//             if (m_ini > m_fin) {
//                 isvalid = false;
//                 detalle += "La hora final debe ser mayor a la inicial.";
//             }
//             else {
//                 if (m_ini == m_fin) {
//                     detalle += "Las horas seleccionadas deben ser diferentes.";
//                 }
//             }

//         }
//         return detalle;

//     }




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
//                 hora += "<option value=\"0" + i + "\">0" + i + "</option>";
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
//                 min += "<option value=\"0" + i + "\">0" + i + "</option>";
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
// });
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
=======
>>>>>>> db_management
