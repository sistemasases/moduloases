// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/monitorias_academicas_inscripcion
 */

 define(['jquery','block_ases/_general_modal_manager','block_ases/loading_indicator','block_ases/sweetalert'], function($, gmm, loading_indicator, sweetalert){
    var USER = {};
    return {
        set_user : (id, email, phone) => USER = {id, email, phone},
        p : () => console.log(USER),
        init : function(){
            loading_indicator.show();
            // Setup: Modal de inscripción a monitoría
            $(".link_inscripcion_monitoria").click(function(e){
                e.preventDefault();
                if(!USER.phone){
                    swal(
                        {title:"Por favor, ingresa tu teléfono",
                        text: "Antes de inscribirte a una monitoría requerimos que ingreses tu número de telefono celular. Puedes hacerlo en la sección \"Tus datos de contacto\".",
                        icon: "warning"});
                    return;
                }
                loading_indicator.show();
                // cargar cuándo es la próxima monitoría
                const id_monitoria = e.target.id;
                $.when($.ajax({
                    type: "POST",
                    data: JSON.stringify({
                            "function": 'get_proxima_sesion_de_monitoria',
                            "params": id_monitoria,
                        }),
                    url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                    dataType: "json",
                    error: function(msg) {
                        $("#debug").html(msg);
                    }
                })).done((sesion) =>{
                // mostrar modal
                let html_to_load = $("#content-modal-inscripcion").html();
                gmm.generate_modal("modal_fomulario_inscripcion", "Inscribirse a monitoría", html_to_load, null, 
                // setup después de mostrar el modal de inscripción
                function() {
                    loading_indicator.hide();
                    gmm.show_modal(".modal_fomulario_inscripcion");
                    $(".modal_fomulario_inscripcion").find(".general_modal_content").addClass("w-50");
                    $(".modal_fomulario_inscripcion").find("#monitoria").val(e.target.innerHTML);
                    $(".modal_fomulario_inscripcion").find("#fecha-monitoria").val(sesion.message.fecha_legible);
                    // setup de listeners
                    //  Asignatura a consultar: Otra ...
                    $(".modal_fomulario_inscripcion").find("#asignatura").change((e) => {
                        if(e.target.value == "-1"){
                            $(".modal_fomulario_inscripcion").find("#nombre-asignatura").val("");
                            $(".modal_fomulario_inscripcion").find("#profesor-asignatura").val("");
                            $(".modal_fomulario_inscripcion").find("#campos-otro").show();
                        } else {
                            $(".modal_fomulario_inscripcion").find("#campos-otro").hide();
                        }
                    });
                    // cancelar
                    $(".modal_fomulario_inscripcion").find("#cancelar-inscripcion-btn").click(() => {$(".general_modal_close").first().click()});
                    // inscribirse
                    $(".modal_fomulario_inscripcion").find("#form-inscripcion").submit(function(e){
                        e.preventDefault();
                        loading_indicator.show();

                        let id_sesion = sesion.message.id;
                        let id_asistente = USER.id;
                        let asignatura_a_consultar = $(".modal_fomulario_inscripcion").find("#asignatura").val();
                        let tematica_a_consultar = $(".modal_fomulario_inscripcion").find("#tematica").val();
                        // Campos si asignatura_a_consultar es "Otra ..."
                        let nombre_asignatura = asignatura_a_consultar == "-1" ? $(".modal_fomulario_inscripcion").find("#nombre-asignatura").val() : "";
                        let profesor = asignatura_a_consultar == "-1" ? $(".modal_fomulario_inscripcion").find("#profesor-asignatura").val() : "";

                        let seguir_inscribiendo = $(".modal_fomulario_inscripcion").find("#seguir-inscribiendo").prop("checked");
                        $.ajax({
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'anadir_asistente_a_sesion_de_monitoria',
                                "params": [id_sesion, id_asistente, asignatura_a_consultar, nombre_asignatura, profesor, tematica_a_consultar, seguir_inscribiendo, id_monitoria],
                            }),
                            url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                            dataType: "json",
                            success: function(msg) {
                                loading_indicator.hide();
                                $(".general_modal_close").first().click();
                                if(msg.status_code == 0) {
                                    swal(
                                        {title:"Inscripción correcta!",
                                        text: "Gracias por inscribirte. Serás contactado por el monitor encargado el día de la monitoría para concretar los detalles.",
                                        icon: "success"},
                                        function (isConfirmed) {
                                            location.reload()
                                        });
                                        
                                } else {
                                    swal(
                                            'Error!',
                                            'Oops!: ' + msg.data_response,
                                            'error'
                                        );
                                    console.log(msg);
                                }
                            },
                            error: function(msg) {
                                console.log("Error de inscripción a monitoria en BD");
                                $("#debug").html(msg.responseText);
                            }
                        });
                    });

                });

            });

            });
            // Setup: eliminar inscripcion
            $(".eliminar-inscripcion").click(function (e) {
                let monitoria = $(e.target).parent().parent().find("td");
                swal({
                    title: 'Cancelar asistencia',
                    text: "¿Deseas desinscribirte de la monitoría de "+monitoria[3].innerHTML+" programada el "+monitoria[0].innerHTML+" "+monitoria[1].innerHTML+" "+monitoria[2].innerHTML+"?",
                    type: 'warning',
                    allowOutsideClick: true,
                    confirmButtonText: 'Cancelar asistencia'
                }, function (isConfirmed) {
                    if (isConfirmed) {
                        $.ajax({
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'eliminar_asistencia',
                                "params": e.target.getAttribute("val"),
                            }),
                            url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                            dataType: "json",
                            success: function(msg) {
                                location.reload(); 
                            },
                            error : function(msg) {
                                swal('Error!', msg, 'error')
                                console.log(msg)
                            },
                        });
                    }
                });
                                });
            // Setup: confirmar asistencia
            $(".confirmar-asistencia").click(function (e) {
                let monitoria = $(e.target).parent().parent().find("td");
                console.log(e.target.getAttribute("val"));
                swal({
                    title: '¿Confirmar asistencia?',
                    text: "Se registrará tu asistencia a la monitoría de "+monitoria[3].innerHTML,
                    type: 'info',
                    allowOutsideClick: true,
                    confirmButtonText: 'Registrar tu asistencia'
                }, function (isConfirmed) {
                    if (isConfirmed) {
                        // Se registra la asistencia cambiando el valor de la columna asistio de la tabla talentospilos_asis_monitoria a la hora en que se confirmó la asistencia. Antes de confirmar, el valor es 0.
                        $.ajax({
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'registrar_asistencia_a_asistente',
                                "params": e.target.getAttribute("val"),
                            }),
                            url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                            dataType: "json",
                            error: function(msg) {
                                console.log("Error edicion BD asistentes monitorias academicas");
                                $("#debug").html(msg.responseText);
                            },
                            success: (msg) => location.reload()
                        });
                    }
                });
            });
            // Setup: editar numero de telefono celular
            {$("#editar-celular").click(function (e) {
                    $("#mostrar-celular").hide();
                    $("#edicion-celular").attr( "style", "display: inline");
                    $("#campo-celular").attr("placeholder", $("#celular").html());
                });
                function terminarDeEditar(e){
                    $("#edicion-celular").hide();
                    $("#mostrar-celular").show();
                }
                $("#cancelar-editar-celular").click(terminarDeEditar);
                $("#guardar-editar-celular").click(function (e) {
                    // guardar el numero celular en la bd
                    //console.log($("#campo-celular").val());
                    $.when($.ajax({
                        type: "POST",
                        data: JSON.stringify({
                                "function": 'modificar_celular_de_usuario',
                                "params": [USER.id, $("#campo-celular").val()],
                            }),
                        url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                        dataType: "json",
                        error: function(msg) {
                            $("#debug").html(msg);
                        },
                        success: function(msg) {
                            USER.phone = $("#campo-celular").val();
                            //console.log(msg)
                        }})).done(terminarDeEditar);
                        $("#celular").html($("#campo-celular").val());
                });
            }

            loading_indicator.hide();
        }
    }
 });