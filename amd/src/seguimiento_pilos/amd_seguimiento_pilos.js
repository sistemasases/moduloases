requirejs(['jquery', 'bootstrap', 'datatables.net', 'datatables.net-buttons', 'buttons.flash', 'jszip', 'pdfmake', 'vfs_fonts', 'buttons.html5', 'buttons.print', 'sweetalert', 'amd_actions'], function($) {

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

    	//Obtenemos el ID de la instancia actual.

        var informacionUrl = window.location.search.split("&");
        for (var i = 0; i < informacionUrl.length; i++) {
            var elemento = informacionUrl[i].split("=");
            if (elemento[0] == "?instanceid" || elemento[0] == "instanceid") {
                var instance = elemento[1];
            }
        }

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
            $('#titulo').text("Informacion Estudiantes");
        }
        else if (namerol == "practicante_ps") {
            $('#titulo').text("Informacion Monitores");
        }
        else if (namerol == "profesional_ps") {

            $('#titulo').text("Informacion Practicantes");
        }
        else if (namerol == "sistemas" || name == "administrador" || name == "sistemas1008" || name == "Administrador") {
        	$('#titulo').text("Informacion Sistemas");

            //anadirEvento();
        }

        

        /*Cuando el usuario sea practicante o administrador */

        if (namerol == "practicante_ps" || (name == "administrador" || name == "sistemas1008" || name == "Administrador")) {

            $("input[name=profesional]").attr('disabled', true);
            $("input[name=practicante]").attr('disabled', true);

            //limpiar
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


            //Envia correo al monitor.
            $(this).on('click', '.btn.btn-info.btn-lg.botonCorreo', function() {
                if ($(this).parent().children('textarea').val() == "") {
                    swal({
                        title: "Para enviar una observación debe llenar el campo correspondiente",
                        html: true,
                        type: "error",
                        confirmButtonColor: "#d51b23"
                    });
                }
                else {
                    //Se recupera el mensaje y el id del monitor al que se va a enviar el mensaje
                    var id = $(this).attr("value");
                    var particionar_informacion = $(this).parent().children('textarea').attr("id").split("-");
                    var tipo = particionar_informacion[0];
                    var codigoN1 = particionar_informacion[1];
                    var codigoN2 = particionar_informacion[2];
                    var fecha = particionar_informacion[3];
                    var nombre = particionar_informacion[4];
                    var mensaje_enviar = $(this).parent().children('textarea').val();

                    //Se limpia el textarea
                    $(this).parent().children('textarea').val("");
                    var respuesta = "";

                    //Se llama el ajax para enviar el mensaje
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
                        url: "../../../blocks/ases/managers/get_info_report.php",
                        async: false,
                        success: function(msg) {
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


            //Editar seguimiento determinado.
            $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
                var id = $(this).attr("value");
                var $tbody = $(this).parent().parent().parent();
                $("input[name=practicante]").attr('disabled', false);
                $tbody.find('.editable').removeAttr('readonly');
                $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
                $tbody.find('.quitar-ocultar').toggleClass('ocultar');
                $tbody.find('.radio-ocultar').toggleClass('ocultar');
                auxiliar_editar(id);
                seleccionarButtons(id);
            });


            //Cancela la edición de un seguimiento determinado.
            $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
                var id = $(this).attr("value");
                $("input[name=practicante]").attr('disabled', true);
                var $tbody = $(this).parent().parent().parent();
                $tbody.find('.editable').attr('readonly', true);
                $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
                $tbody.find('.quitar-ocultar').toggleClass('ocultar');
                $tbody.find('.radio-ocultar').toggleClass('ocultar');
                auxiliar_cancelar(id);
            });
			// Modifica un seguimiento determinado.
            $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
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

                if (validar == "") {

                    if ($("#profesional_" + id).is(':checked')) {
                        profesional = 1;
                    }
                    else {
                        profesional = 0;
                    }

                    if ($("#practicante_" + id).is(':checked')) {
                        practicante = 1;
                    }
                    else {
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
                    }
                    else {
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

            //borrar
            $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
                var id_registro = $(this).attr('value');
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
                                if (msg == 0) {
                                    swal({
                                        title: "error al borrar registro",
                                        html: true,
                                        type: "error",
                                        confirmButtonColor: "#d51b23"
                                    });
                                }
                                else {

                                    swal("¡Hecho!",
                                        "El registro ha sido eliminado",
                                        "success");
                                    setTimeout('document.location.reload()', 500);

                                }
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {},
                        });
                    });
            });

            //se inicia la adicion del evento
            $(this).on('click', '.btn.btn-info.btn-lg.botonCorreo', function() {
                var id = $(this).attr("value");
                $("#profesional_" + id).attr("disabled", true);
                if ($(this).parent().children('textarea').val() == "") {
                    swal({
                        title: "Para enviar una observación debe llenar el campo correspondiente",
                        html: true,
                        type: "error",
                        confirmButtonColor: "#d51b23"
                    });
                }
                else {

                    //se recupera el mensaje y el id del monitor al que se va a enviar el mensaje
                    var particionar_informacion = $(this).parent().children('textarea').attr("id").split("_");
                    var tipo = particionar_informacion[0];
                    var codigoN1 = particionar_informacion[1];
                    var fecha = particionar_informacion[3];
                    var nombre = particionar_informacion[4];
                    var mensaje_enviar = $(this).parent().children('textarea').val();
                    var codigoN2 = 0;

                    $.ajax({
                        type: "POST",
                        data: {
                            type: "getProfesional",
                            id: id,
                            instance: instance
                        },
                        url: "../../../blocks/ases/managers/get_info_report.php",
                        async: false,
                        success: function(msg) {
                            codigoN2 = msg;
                        },
                        dataType: "text",
                        cache: "false",
                        error: function(msg) {
                            swal({
                                title: "error getrol",
                                html: true,
                                type: "error",
                                confirmButtonColor: "#d51b23"
                            })
                        },
                    });

                    //se limpia el textarea
                    $(this).parent.children('textarea').val("");
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
                        url: "../../../blocks/ases/managers/get_info_report.php",
                        async: false,
                        success: function(msg) {
                            //alert("mensaje");
                            //alert(msg);
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
                            })
                        },
                    });

                }
            });

        /*Cuando el usuario sea profesional o administrador */
        }else if (namerol == "profesional_ps" || (name == "administrador" || name == "sistemas1008" || name == "Administrador")) {
            //se inicia la adicion del evento
            $("input[name=practicante]").attr('disabled', true);
            $("input[name=profesional]").attr('disabled', true);


            //Verifica si el profesional desea marcar como revisado el seguimiento.
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


            $(this).on('click', '.btn.btn-info.btn-lg.botonCorreo', function() {
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
                        url: "../../../blocks/ases/managers/get_info_report.php",
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


            //limpiar
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

            //Editar seguimiento determinado.
            $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
                var id = $(this).attr("value");
                var $tbody = $(this).parent().parent().parent();
                $("input[name=profesional]").attr('disabled', false);
                $tbody.find('.editable').removeAttr('readonly');
                $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
                $tbody.find('.quitar-ocultar').toggleClass('ocultar');
                $tbody.find('.radio-ocultar').toggleClass('ocultar');
                auxiliar_editar(id);
                seleccionarButtons(id);
            });

            //Cancela la edición de un seguimiento determinado.
            $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
                var id = $(this).attr("value");
                $("input[name=profesional]").attr('disabled', true);
                var $tbody = $(this).parent().parent().parent();
                $tbody.find('.editable').attr('readonly', true);
                $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
                $tbody.find('.quitar-ocultar').toggleClass('ocultar');
                $tbody.find('.radio-ocultar').toggleClass('ocultar');
                auxiliar_cancelar(id);
            });

            // Modifica un seguimiento determinado.
            $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
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

                if (validar == "") {

                    if ($("#profesional_" + id).is(':checked')) {
                        profesional = 1;
                    }
                    else {
                        profesional = 0;
                    }

                    if ($("#practicante_" + id).is(':checked')) {
                        practicante = 1;
                    }
                    else {
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
                    }
                    else {
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
            //borrar
            $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
                var id_registro = $(this).attr('value');
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
                                if (msg == 0) {
                                    swal({
                                        title: "error al borrar registro",
                                        html: true,
                                        type: "error",
                                        confirmButtonColor: "#d51b23"
                                    });
                                }
                                else {

                                    swal("¡Hecho!",
                                        "El registro ha sido eliminado",
                                        "success");
                                    setTimeout('document.location.reload()', 500);

                                }
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {},
                        });
                    });
            });

        /*Cuando el usuario sea monitor o administrador */
        }else if (namerol == "monitor_ps" || (name == "administrador" || name == "sistemas1008" || name == "Administrador")) {
            
            //limpiar
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


             //Editar seguimiento determinado.
            $('body').on('click', 'span.btn-info.btn-lg.botonesSeguimiento.botonEditarSeguimiento', function() {
                var id = $(this).attr("value");
                var $tbody = $(this).parent().parent().parent();
                var visto_profesional = $("#profesional_" + id).is(':checked');
                if (visto_profesional == false) {
                $tbody.find('.editable').removeAttr('readonly');
                $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
                $tbody.find('.quitar-ocultar').toggleClass('ocultar');
                $tbody.find('.radio-ocultar').toggleClass('ocultar');
                auxiliar_editar(id);
                seleccionarButtons(id);
              }else{
                 swal("¡Advertencia!","No es posible editar el seguimiento, debido a que ya ha sido revisado por un profesional",
                        "warning");}
            });

            //Cancela la edición de un seguimiento determinado.
            $('body').on('click', 'span.btn.btn-info.btn-lg.botonesSeguimiento.botonCancelarSeguimiento', function() {
                var id = $(this).attr("value");
                var $tbody = $(this).parent().parent().parent();
                $tbody.find('.editable').attr('readonly', true);
                $tbody.find('.botonesSeguimiento').toggleClass('ocultar');
                $tbody.find('.quitar-ocultar').toggleClass('ocultar');
                $tbody.find('.radio-ocultar').toggleClass('ocultar');
                auxiliar_cancelar(id);
            });

              //borrar
            $('body').on('click', 'span.btn.btn-info.btn-lg.botonBorrar', function() {
            	var visto_profesional = $("#profesional_" + id).is(':checked');
            	if (visto_profesional == false) {

                var id_registro = $(this).attr('value');
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
                                if (msg == 0) {
                                    swal({
                                        title: "error al borrar registro",
                                        html: true,
                                        type: "error",
                                        confirmButtonColor: "#d51b23"
                                    });
                                }
                                else {

                                    swal("¡Hecho!",
                                        "El registro ha sido eliminado",
                                        "success");
                                    setTimeout('document.location.reload()', 500);

                                }
                            },
                            dataType: "text",
                            cache: "false",
                            error: function(msg) {},
                        });
                    });
                }else{
                 swal("¡Advertencia!","No es posible eliminar el seguimiento, debido a que ya ha sido revisado por un profesional",
                        "warning");}
            });





           // Modifica un seguimiento determinado.
            $('body').on('click', 'span.btn.btn-info.btn-lg.botonModificarSeguimiento', function() {
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

                if (validar == "") {

                    if ($("#profesional_" + id).is(':checked')) {
                        profesional = 1;
                    }
                    else {
                        profesional = 0;
                    }

                    if ($("#practicante_" + id).is(':checked')) {
                        practicante = 1;
                    }
                    else {
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
                    }
                    else {
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

    });



  

    //Funcion que añade un comboBox para los roles especificos y consulta codigos
    //predefinidos
    function anadirEvento() {
        //terminar..

        var selectanadir = '<div class="form-group col-sm-12"><div class="col-sm-3"><label class="control-label" for="email">Seleccione rol :</label><select id="selectProfesional" name="divCategoriaPadre" class="selectPadre col-md-offset-2"><option value=4 selected="selected">Monitor</option><option value=7>Practicante</option><option value=3>Profesional</option></select></div> ';
        selectanadir += '<div class="col-sm-3"><label class="control-label" for="semestre">Seleccione semestre :</label><select id="semestre" name="divCategoriaPadre" class="selectPadre col-md-offset-2"></select></div>';
        selectanadir += '<div class="col-sm-3"><label class="control-label" for="persona">Seleccione persona :</label><select id="persona" name="divCategoriaPadre" class="selectPadre col-md-offset-2"></select></div>';
        selectanadir += '<div class="col-sm-3"><span class="btn btn-primary col-md-offset-2" id="consultarMonitores" class="submit">Consultar</span></div></div>';


        $('#anadir').append(selectanadir);

        $('#consultarMonitores').on('click', function() {
            var v = $('#selectProfesional').val();
            if (v == "inicio") {
                alert("Seleccione una opcion");
            }
            else {
                if (v == 4) {
                    obtenerMonitores();
                    $('#titulo').text("Informacion Estudiantes");
                    //htmltexto=monitorUser(120,0,19,121);
                    //alert("entro m act")
                    // htmltexto=monitorUser(1055,0,534,0);
                }
                else if (v == 7) {
                    $('#titulo').text("Informacion Practicante");
                    // htmltexto=practicanteUser(103132,450299);
                    //alert("entro pract")
                    htmltexto = practicanteUser(1113, 534);
                }
                else if (v == 3) {
                    $('#titulo').text("Informacion Profesional");
                    // htmltexto=profesionalUser(110953,450299);
                    //alert("entro prof")
                    htmltexto = profesionalUser(122, 19);
                }

                $('#reemplazarToogle').html(htmltexto);
            }

        });
    }

    //Obtiene los mensajes de validación de la hora.
    function validarHoras(h_ini, h_fin, m_ini, m_fin) {
        var detalle = "";
        if (h_ini > h_fin) {
            detalle += "* La hora final debe ser mayor a la inicial<br>";
        }
        else if (h_ini == h_fin) {
            if (m_ini > m_fin) {
                isvalid = false;
                detalle += "* La hora final debe ser mayor a la inicial<br>";
            }
            else {
                if (m_ini == m_fin) {
                    detalle += "* Las horas seleccionadas deben ser diferentes<br>";
                }
            }

        }
        return detalle;

    }


    //Inicializa las horas y minutos.
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
                hora += "<option value=\"" + i + "\">0" + i + "</option>";
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
                min += "<option value=\"" + i + "\">0" + i + "</option>";
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

    //funcion que transforma la fecha guardada en el campus en formato epoch a un formato
    //identificable para las personas
    function transformarFecha(fecha) {
        var a = new Date(fecha * 1000);
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var year = a.getFullYear();
        var month = months[a.getMonth()];
        var date = a.getDate();
        var time = date + ' ' + month + ' ' + year;
        return time;
    }

    // Volver a formato fecha
    function getFormatoFecha(fecha) {
        var fecha_array = [];
        var datos = fecha.split("/");
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var mes = datos[0].split("Registro : ");
        var fecha_final = datos[2] + "-" + (months.indexOf(mes[1])+1) + "-" + datos[1];
        if (fecha_final.length == 9 || fecha_final.length == 8) {
            var datos_corregido = fecha_final.split("-");
            if (datos_corregido[1].length == 1) {
                fecha_array[1] = '0' + datos_corregido[1];
            }
            else {
                fecha_array[1] = datos_corregido[1];
            }

            if (datos_corregido[2].length == 1) {
                fecha_array[2] = '0' + datos_corregido[2];

            }
            else {
                fecha_array[2] = datos_corregido[2];
            }
            fecha_array[0] = datos_corregido[0];

            fecha_final = fecha_array[0] + "-" + fecha_array[1] + "-" + fecha_array[2];
        }
        return fecha_final;
    }

    function cantidadSeguimientosMonitor(arreglo) {

        var cantidad = 0;
        for (var estudiante in arreglo) {
            for (seguimiento in estudiante) {
                cantidad++;
            }
        }

        return cantidad;

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
        var date = $("label[for='fechatext_" + id + "']").text();
        var fecha_formateada = getFormatoFecha(date);
        $("#fecha_" + id).val(fecha_formateada);
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
