// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/monitorias_academicas
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
    'extras',
],
    function ($, dataTables, buttons, html5, flash, print,  gmm, select2, jqueryui, loading_indicator, sweetalert, extras) {
        return {
            init: function () {
                // adjuntar listeners
                $(document).on('click', '#anadir_monitoria', iniciar_agregar_monitoria);
                $(document).on('click', '#config-icon', mostrar_config);
                // localizacion
                //// datepicker
                $.datepicker.setDefaults({
                    closeText: "Cerrar",
                    prevText: "&#x3C;Ant",
                    nextText: "Sig&#x3E;",
                    currentText: "Hoy",
                    monthNames: [ "enero","febrero","marzo","abril","mayo","junio",
                    "julio","agosto","septiembre","octubre","noviembre","diciembre" ],
                    monthNamesShort: [ "ene","feb","mar","abr","may","jun",
                    "jul","ago","sep","oct","nov","dic" ],
                    dayNames: ["domingo","lunes","martes","miércoles","jueves","viernes","sábado"],
                    dayNamesShort: [ "dom","lun","mar","mié","jue","vie","sáb" ],
                    dayNamesMin: [ "D","L","M","X","J","V","S" ],
                    weekHeader: "Sm",
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: "" });
                    
                },
        cargar_monitorias_default: function(data){
            $("#div_table").html('');
            $("#div_table").fadeIn(500).append('<table id="tableResult" class="stripe row-border order-column" cellspacing="0" width="100%"><thead> </thead></table>');
            $("#tableResult").DataTable(data);
        },
        continuar_setup_inicial : function(es_monitor){
            if(es_monitor) {
                $(".dt-button.buttons-print.eliminar").toggle();
                $("#config-icon").toggle();
        } else $(".dt-button.buttons-print.eliminar").click(eliminar_monitoria);
        }
        
    }

    function mostrar_config(){
        // cargar mustache
        $.when($.ajax({
            url: "../templates/monitorias_academicas_config.mustache",
            data: null,
            dataType: "text",
            async: true,
            success: function( template ){
                let html_to_load = template;
                //Crear JSON con general_modal_manager
                gmm.generate_modal("modal_config", "Configuración", html_to_load, null, function(){gmm.show_modal(".modal_config")});
            },
            error: function(){
                console.log( "../templates/monitorias_academicas_config.mustache cannot be reached." );
            }
        })).done(() => {        
        // cargar lista de todos los grupos de moodle que pertenezcan al curso
        $.ajax({
            type: "POST",
            data: JSON.stringify({
                "function": 'cargar_grupos',
                "params": [extras.getUrlParams(document.location.search).courseid],
            }),
            url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
            dataType: "json",
            success: function(msg) {
                //$("#div_table").html(msg.responseText);
                var grupos = $.map(msg.data_response, function(a) {return {"id" : a.id, "text" : a.nombre}});
                $('#grupos').select2({
                    "data" : grupos,
                    "placeholder": "Seleccionar grupo"
                });
                $('#grupos').val(msg.seleccionado.id_number).change();
            },
            error: function(msg) {
                    console.log("Error consulta BD grupo");
                    console.log(msg);
                    $("#debug").html(msg.responseText);
                }
            });
            
            // cancelar
            $("#cancel_config").click(() => {$(".general_modal_close").first().click()});

            // guardar 
            $("#form_config").submit(function(e){
                e.preventDefault();
                loading_indicator.show();
                var grupo = $("select[name='grupo']").val();
                $.ajax({
                    type: "POST",
                    data: JSON.stringify({
                        "function": 'actualizar_config',
                        "params": [parseInt(grupo)]
                    }),
                    url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                    dataType: "json",
                    success: function(msg) {
                        loading_indicator.hide();
                        $(".general_modal_close").first().click();
                        if(msg.status_code != 0) {
                            swal(
                                    'Error!',
                                    'Oops!: ' + msg.data_response,
                                    'error'
                                );
                            console.log(msg);
                        }
                    },
                    error: function(msg) {
                        console.log("Error update bd de config en backend");
                        $("#debug").html(msg.responseText);
                    }
                });
            });
        });
    }

    function iniciar_agregar_monitoria(){
        // cargar mustache
            $.ajax({
                url: "../templates/monitorias_academicas_anadir.mustache",
                data: null,
                dataType: "text",
                async: true,
                success: function( template ){
                    let html_to_load = template;
                    //Crear JSON con general_modal_manager
                    gmm.generate_modal("modal_fomulario_anadir", "Añadir monitoria", html_to_load, null, function(){gmm.show_modal(".modal_fomulario_anadir")});
                    mostrar_agregar_monitoria();
                },
                error: function(){
                    console.log( "../templates/monitorias_academicas_anadir.mustache cannot be reached." );
                }
            });
    function mostrar_agregar_monitoria(){

        listar_materias_select();
        listar_monitores_select();
        attach_listeners_botones();
    
        $("#fecha_hasta").datepicker({
            minDate: 0,
            showWeek: true,
            dateFormat: "dd/M/y"
        });
    
        $("#programar_monitorias_chckbx").change(() => {
            $("#fecha_hasta").attr("required", $("#programar_monitorias_chckbx").prop("checked"));
            $("#seleccionar-fecha-anadir").toggle(300);
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
            // guardar monitoria
            $("#form_anadir").submit(function(e){
                e.preventDefault();
                loading_indicator.show();
                var dia = $("select[name='dia']").val();
                var hora = $("#hora_inicio1 option:selected").text()+" "+$("#hora_inicio2 option:selected").text()+ " - "+ $("#hora_final1 option:selected").text()+" "+$("#hora_final2 option:selected").text();
                var materia = $('#materia').find(':selected').first().val();
                var monitor = $('#monitor').find(':selected').first().val();
                var programar = $("#programar_monitorias_chckbx").prop("checked");
                if(programar){
                    var hasta = $("#fecha_hasta").val();
                }else{
                    var hasta = -1;
                }
                $.ajax({
                    type: "POST",
                    data: JSON.stringify({
                        "function": 'anadir_monitoria',
                        "params": [dia, hora, materia, monitor, programar, hasta],
                    }),
                    url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                    dataType: "json",
                    success: function(msg) {
                        loading_indicator.hide();
                        $(".general_modal_close").first().click();
                        if(msg.status_code == 0) {
                            swal(
                                {title:"Monitoria registrada",
                                text: "Se ha agregado correctamente la monitoria",
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
    }
    
}


    function eliminar_monitoria(e){
        let monitoria_info = $(e.target).parent().parent().find("td").toArray().map(td => td.innerHTML)
        swal({
            title: 'Eliminar monitoría',
            text: "¿Deseas eliminar la monitoría de  "+monitoria_info[0]+" programada los "+monitoria_info[1]+" de "+ monitoria_info[2]+"?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Eliminar'
        }, function (isConfirmed) {
            if (isConfirmed) {
                $.ajax({
                    type: "POST",
                    data: JSON.stringify({
                        "function": 'eliminar_monitoria',
                        "params": e.target.id,
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
    }


});


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


/* function manejar_modal_agregar_monitoria() {
    // Get the modal
    var modal_peer_tracking = $('#modal_peer_tracking');

    // Get the <span> element that closes the modal
    var span_close = $('.mymodal-close');
    var cancel_button = $('#cancel_peer_tracking');

    cancel_button.on('click', function () {
        modal_peer_tracking.hide();
    });

    // When the user clicks on <span> (x), close the modal
    span_close.on('click', function () {
        modal_peer_tracking.hide();
    });

    var panel_heading = $('.panel-heading.heading_semester_tracking');



    panel_heading.on('click', function () {
        if ($(this).parent().attr('class') == 'collapsed') {
            $('h4>span', this).removeClass('glyphicon-chevron-left');
            $('h4>span', this).addClass('glyphicon-chevron-down');
        } else {
            $('h4>span', this).removeClass('glyphicon-chevron-down');
            $('h4>span', this).addClass('glyphicon-chevron-left');
        }
    });
} */