// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/monitorias_academicas_detalle
 */
define(['jquery',
    'block_ases/jquery.dataTables',
    'block_ases/dataTables.buttons',
    'block_ases/buttons.html5',
    'block_ases/buttons.flash',
    'block_ases/buttons.print',
    'block_ases/_general_modal_manager',
    'block_ases/select2',
    'block_ases/jqueryui',
    'block_ases/loading_indicator',
    'block_ases/sweetalert',
    'extras'
],
    function ($, dataTables, buttons, html5, flash, print,  gmm, select2, jqueryui, loading_indicator, sweetalert, extras) {
        function construir_tabla(data){
            $("#div_table").html('');
            $("#div_table").fadeIn(500).append('<table id="tableResult" class="stripe row-border order-column" cellspacing="0" width="100%"><thead> </thead></table>');
            $("#tableResult").DataTable(data);
        }
        const monitoria_id = (new URLSearchParams(window.location.search)).get("monitoriaid");
        return {
            init: function () {
                 // datepickers
                $.datepicker.setDefaults({
                    closeText: "Cerrar",
                    prevText: "&#x3C;Ant",
                    nextText: "Sig&#x3E;",
                    currentText: "Hoy",
                    monthNames: [ "enero","febrero","marzo","abril","mayo","junio",
                    "julio","agosto","septiembre","octubre","noviembre","diciembre" ],
                    monthNamesShort: [ "ene","feb","mar","abr","may","jun",
                    "jul","ago","sep","oct","nov","dic" ],
                    dayNames: [ "domingo","lunes","martes","miércoles","jueves","viernes","sábado" ],
                    dayNamesShort: [ "dom","lun","mar","mié","jue","vie","sáb" ],
                    dayNamesMin: [ "D","L","M","X","J","V","S" ],
                    weekHeader: "Sm",
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: "" });
                $("#fecha_hasta").datepicker({
                    showWeek: true,
                    dateFormat: "dd/M/y",
                });
                $("#fecha_desde").datepicker({
                    showWeek: true,
                    dateFormat: "dd/M/y",
                });
                $("#fecha_desde").datepicker("setDate", new Date());
                // filtrar
                $("#desplegar-filtrar").click(() => {
                    $("#flecha").toggleClass("abajo");
                    $("#rango-fechas-sesiones").toggle(300);
                });
                $("#rango-fechas-sesiones").submit(consultar_sesiones_por_fecha);
                $("#modificar_monitoria").click(this.mostrar_modificar_monitoria);
                $("#programar_sesiones").click(this.mostrar_programar_sesiones);
            },
            construir_tabla: construir_tabla,
            init_despues_de_tabla: function (){
                // cancelar sesion
                $(".dt-button.buttons-print.eliminar").click(function (e) {
                    let fecha = $(e.target).parent().parent().parent().find("td")[0].innerHTML;
                    swal({
                        title: 'Cancelar sesión',
                        text: "¿Deseas cancelar la sesión programada el "+fecha+"?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Eliminar'
                    }, function (isConfirmed) {
                        if (isConfirmed) {
                            $.ajax({
                                type: "POST",
                                data: JSON.stringify({
                                    "function": 'eliminar_sesion',
                                    "params": e.target.id,
                                }),
                                url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                                dataType: "json",
                                success: function(msg) {
                                    consultar_sesiones_por_fecha();    
                                },
                                error : function(msg) {
                                    swal('Error!', msg, 'error')
                                    console.log(msg)
                                },
                            });
                        }
                    });
                });
            },
            mostrar_modificar_monitoria : function(){
                // cargar mustache
            $.when($.ajax({
                        url: "../templates/monitorias_academicas_anadir.mustache",
                        data: null,
                        dataType: "text",
                        async: true,
                        success: function( template ){
                            let html_to_load = template;
                            //Crear JSON con general_modal_manager
                            gmm.generate_modal("modal_fomulario_anadir", "Modificar monitoría", html_to_load, null, function(){gmm.show_modal(".modal_fomulario_anadir")});

                        },
                        error: function(){
                            console.log( "../templates/monitorias_academicas_anadir.mustache cannot be reached." );
                        }
                    })).done(() =>{

                    listar_materias_select();
                      listar_monitores_select();
                        attach_listeners_botones();
                

                $("#form_anadir input[name=fecha_hasta]")[0].required = false

                $("#seleccionar-fecha-anadir").hide(0);
                $("#input-programar-monitorias-checkbox").hide(0);
                
                // poner valores previos
                var hora = $("#hora").html().split(" ");
                $("#hora_inicio1").val(hora[0]);
                $("#hora_inicio2").val(hora[1]);
                $("#hora_final1").val(hora[3]);
                $("#hora_final2").val(hora[4]);
                $("select[name='dia']").val(parseInt($("#dia").html())).change();

            });
            
                function listar_materias_select(){
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'cargar_materias',
                            "params": [],
                        }),
                        url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                        dataType: "json",
                        success: function(msg) {
                            //$("#div_table").html(msg.responseText);
                            var materias = $.map(msg.data_response, function(a) {return {"id" : a.id, "text" : a.nombre}});
                            $('#materia').select2({
                                "data" : materias,
                                "placeholder": "Seleccionar materia",
                                "templateResult" : renderizar_opcion,
                                "createTag" : agregar_materia_a_select,
                                "tags" : true,
                                "escapeMarkup" : function (markup) {
                                    return markup;
                                },
                            }).on('select2:closing', function (e) {
                                var real_event = e.params.args.originalEvent;
                                if(real_event != null && $(real_event.target).hasClass('boton-eliminar-materia')){
                                        var opcion = e.params.args.originalSelect2Event.data;
                                        // eliminar materia
                                        e.preventDefault();
                                        e.stopPropagation();
                                        $.ajax({
                                            type: "POST",
                                            data: JSON.stringify({
                                                "function": 'eliminar_materia',
                                                "params": [parseInt(opcion.id)],
                                            }),
                                            url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                                            dataType: "json",
                                            success: function(msg) {
                                                if(msg.status_code == 0) {
                                                    $("#materia").find(`option[value=${opcion.id}]`)[0].remove();
                                                    $('#materia').trigger('change');
                                                    $(real_event.target).parent(".opcion-select").parent(".select2-results__option").hide();
                                                } else {
                                                    swal(
                                                            'Error!',
                                                            'Oops!: ' + msg.error_response,
                                                            'error'
                                                        );
                                                }
                                            },
                                            error: function(msg) {
                                                console.log("Error eliminar bd de materia en backend");
                                                $("#debug").html(msg.responseText);
                                            }
                                        });
                                } 
                              }).on('select2:opening', function(event){
                                prevselection = $(event.target).find(':selected');
                                $('#materia').val(null);
                             }).on('select2:select', function(e){
                                 var opcion = e.params.data;
                                if(opcion.id==-1){
                                    // agregar materia
                                    $.ajax({
                                        type: "POST",
                                        data: JSON.stringify({
                                            "function": 'anadir_materia',
                                            "params": [opcion.text],
                                        }),
                                        url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                                        dataType: "json",
                                        success: function(msg) {
                                            if(msg.status_code == 0) {
                                                $('#materia').find("option[value=-1]")[0].value = msg.id;
                                                var nuevo = new Option(opcion.text, msg.id, false, true);
                                                $('#materia').append(nuevo).trigger('change'); 
                                            } else {
                                                swal(
                                                        'Error!',
                                                        'Oops!: ' + msg.data_response,
                                                        'error'
                                                    );
                                            }
                                        },
                                        error: function(msg) {
                                            console.log("Error insercion bd de materia en backend");
                                            $("#debug").html(msg.responseText);
                                        }
                                    });
                                }
                             });
                             $('#materia').val(parseInt($("#materia_id").html())).change();
            
                        },
                        error: function(msg) {
                            console.log("Error consulta BD materias");
                            console.log(msg);
                        }
                    });
                    function renderizar_opcion(m){
                        if(m.hasOwnProperty('newTag') && m.newTag){
                            return $(`<span id="materia_nueva" class="opcion-select">${m.text}<span style="opacity:0.5;margin-left:1ch">(agregar)</span><div class="icono-agregar-materia"><span>`)
                        }
                        var $opcion = $(`<span id="materia_${m.id}" class="opcion-select">${m.text}<div class="boton-eliminar-materia"><span>`);
                        return $opcion;
                    }
                    function agregar_materia_a_select(tag){
                        if ($.trim(tag.term) === '' || $.trim(tag.term).length < 3) {
                            return null;
                          }
        
                          return {
                            id: -1,
                            text: tag.term,
                            newTag: true
                          }
                    }
                }
            
                function listar_monitores_select(){
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'cargar_monitores',
                            "params": [extras.getUrlParams(document.location.search).instanceid],
                        }),
                        url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                        dataType: "json",
                        success: function(msg) {
                            //$("#div_table").html(msg.responseText);
                            var monitores = $.map(msg.data_response, function(a) {return {"id" : a.id, "text" : a.nombre+' '+a.apellido}});
                            $('#monitor').select2({
                                "data" : monitores,
                                "placeholder" : "Seleccionar monitor"
                            });
                            $('#monitor').val(parseInt($("#monitor_id").html())).change();
                            
                        },
                        error: function(msg) {
                            console.log("Error consulta BD monitores");
                            $("#debug").html(msg.responseText);
                        }
                    });
                }
            
           
                function attach_listeners_botones(){
                 
                    // cancelar
                    $("#cancel_monitoria_btn").click(() => {$(".general_modal_close").first().click()});
                    // modificar monitoria
                    $("#form_anadir").submit(function(e){
                        e.preventDefault();
                        loading_indicator.show();
                        var dia = $("select[name='dia']").val();
                        var hora = $("#hora_inicio1 option:selected").text()+" "+$("#hora_inicio2 option:selected").text()+ " - "+ $("#hora_final1 option:selected").text()+" "+$("#hora_final2 option:selected").text();
                        var materia = $('#materia').find(':selected').first().val();
                        var monitor = $('#monitor').find(':selected').first().val();

                        $.ajax({
                            type: "POST",
                            data: JSON.stringify({
                                "function": 'modificar_monitoria',
                                "params": [dia, hora, materia, monitor, monitoria_id],
                            }),
                            url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                            dataType: "json",
                            success: function(msg) {
                                loading_indicator.hide();
                                $(".general_modal_close").first().click();
                                if(msg.status_code == 0) {
                                    swal(
                                        {title:"Monitoria guardada",
                                        text: "Se ha modificado correctamente la monitoria",
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
                                console.log("Error insercion bd de monitoria en backend");
                                $("#debug").html(msg.responseText);
                            }
                        });
                    });
        
               
                }
            },
            mostrar_programar_sesiones : function(){
                $.when($.ajax({
                    url: "../templates/monitorias_academicas_programar.mustache",
                    data: null,
                    dataType: "text",
                    async: true,
                    success: function( template ){
                        gmm.generate_modal("modal_fomulario_programar", "Programar sesiones de monitoría", template, null, function(){gmm.show_modal(".modal_fomulario_programar")});
                        $(".modal_fomulario_programar").find(".general_modal_content").width("40%")
                    },
                    error: function(){
                        console.log( "../templates/monitorias_academicas_programar.mustache cannot be reached." );
                    }
                })).done(() =>{
                    $("#programar_desde").datepicker({
                        minDate: 0,
                        showWeek: true,
                        dateFormat: "dd/M/y"
                    });
                    $("#programar_hasta").datepicker({
                        minDate: 0,
                        showWeek: true,
                        dateFormat: "dd/M/y"
                    });

                    $("#form_programar").submit(function (e) {
                        loading_indicator.show();
                        e.preventDefault()
                        let desde = $("#programar_desde").val();
                        console.log(desde)
                        let hasta = $("#programar_hasta").val();
                        let dia = parseInt($("#dia").html());
                        swal({
                            title: '¿Programar sesiones?',
                            text: "Se programarán sesiones todos los " +["lunes", "martes", "miércoles", "jueves", "viernes", "sábado", "domingo"][dia]+" desde "+desde+" hasta "+hasta+".",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Programar'
                        }, function (isConfirmed) {
                            if (isConfirmed) {
                                $.ajax({
                                    type: "POST",
                                    data: JSON.stringify({
                                        "function": 'programar_sesiones',
                                        "params": [monitoria_id, dia, desde, hasta],
                                    }),
                                    url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                                    dataType: "json",
                                    success: function(msg) {
                                        loading_indicator.hide();
                                        location.reload()
                                    },
                                    error : function(msg) {
                                        swal('Error!', msg.responseText, 'error')
                                        $("#debug").html(msg.responseText);
                                    },
                                });
                            }
                        });
                    });


            });
        }
    }
    function formatear_date_a_int(date){
        function appendLeading0(a){ return a < 10 ? "0"+a.toString() : a.toString()};
        return parseInt(date.getFullYear().toString()+appendLeading0(date.getMonth()+1)+appendLeading0(date.getDate()))
    }
    // TODO: poner como fechas iniciales hoy y 9999999
    function consultar_sesiones_por_fecha(e = null){
        if(e) e.preventDefault();
        loading_indicator.show();
            var desde = formatear_date_a_int(e ? $("#fecha_desde").datepicker("getDate") : new Date());
            var hasta = formatear_date_a_int(e ? $("#fecha_hasta").datepicker("getDate") : new Date(8640000000000000)); // máxima fecha disponible en JS
        $.ajax({
            type: "POST",
            data: JSON.stringify({
                "function": "get_tabla_sesiones",
                "params": [monitoria_id, desde, hasta],
            }),
            url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
            dataType: "json",
            success: function(msg) {
                loading_indicator.hide();
                construir_tabla(msg.data_response);
            },
            error: function(msg) {
                console.log("Error insercion bd de monitoria en backend");
                $("#debug").html(msg.responseText);
            }
        });
    }
    }
);

define('extras', function(){
    return {
        getUrlParams : function (page) {
            var query_string = [];
            var query = page.substring(1);
            var vars = query.split("&");
            for (var i = 0; i < vars.length; i++) {
                var pair = vars[i].split("=");
                query_string[pair[0]] = pair[1];
            }
            return query_string;
        }
    }
});