// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_ases/dphpforms_form_builder
 */

define([
    'jquery',
    'block_ases/bootstrap',
    'block_ases/sweetalert',
    'block_ases/jqueryui',
    'block_ases/select2',
    'block_ases/loading_indicator',
    'block_ases/jquery.scrollTo'
], function ($, bootstrap, sweetalert, jqueryui, select2, loading_indicator, JQS) {

    return {
        init: function () {

            var params = get_url_parameters(document.location.search)

            $("input[type=date]").keypress(function (e) {
                e.preventDefault();
            });

            $(document).on("click", ".btn-dphpforms-close", function () {
                $(this).closest('div[class="mymodal"]').fadeOut(300);
            });

            $('.outside').click(function () {
                var outside = $(this);
                swal({
                    title: 'Confirmación de salida',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Salir'
                }, function (isConfirm) {
                    if (isConfirm) {
                        $(outside).parent('.mymodal').fadeOut(300);
                        console.log($(this).parent('.mymodal'));
                    }
                });
            });

            $(document).ready(function () {
                $('.seg_geo_origen').find('input').prop('disabled', true);
            });

            $(document).on('change', $('.seg_geo_vive_zona_riesgo').find('label').find('input'), function () {

                if ($('.seg_geo_vive_zona_riesgo').find('label').find('input').prop('checked')) {
                    $('.seg_geo_origen').find('input').prop('disabled', false);
                } else {
                    $('.seg_geo_origen').find('input').prop('disabled', true);
                    $('.seg_geo_origen').find('input').prop('checked', false);
                }
            });

            $('.seg_geo_vive_zona_riesgo').find('label').find('input').change(function () {
                if ($('.seg_geo_vive_zona_riesgo').find('label').find('input').prop('checked')) {
                    $('.seg_geo_origen').find('input').prop('disabled', false);
                } else {
                    $('.seg_geo_origen').find('input').prop('disabled', true);
                    $('.seg_geo_origen').find('input').prop('checked', false);
                }
            });

            $('#button_update_geographic_track').click(function(){
                $('div').removeClass('regla_incumplida');
                $.get( "../managers/dphpforms/dphpforms_forms_core.php?form_id=seguimiento_geografico&record_id=" + $(this).attr('data-record-id'), function( data ) {
                    $("#seguimiento_geografico_form").html("");
                    $('#seguimiento_geografico_form').append( data );
                    $('#modal_seguimiento_geografico').fadeIn(300);
                    if($('.seg_geo_vive_zona_riesgo').find('label').find('input').prop('checked')) {
                        $('.seg_geo_origen').find('input').prop('disabled', false );
                    }else{
                        $('.seg_geo_origen').find('input').prop('disabled', true );
                        $('.seg_geo_origen').find('input').prop('checked', false );
                    }
                    var count_buttons_dphpforms = $('.dphpforms-record .btn-dphpforms-univalle').length;
                    if( count_buttons_dphpforms == 2 ){
                        $('.dphpforms-record .btn-dphpforms-univalle:eq(0)').css( { 'margin-left' : ( ($('.dphpforms-updater').width()/2) - 72 ) + 'px'  } );
                    }if( count_buttons_dphpforms == 3 ){
                        $('.dphpforms-record .btn-dphpforms-univalle:eq(0)').css( { 'margin-left' : ( ($('.dphpforms-updater').width()/2) - 72 - 30 ) + 'px'  } );
                    }
                });
            });

            function get_url_parameters(url) {
                
                var query_string = [];
                var query = document.location.search.substring(1);
                var vars = query.split("&");
                for (var i = 0; i < vars.length; i++) {
                    var pair = vars[i].split("=");
                    query_string[pair[0]] = pair[1];
                }

                return query_string;
            }

            function get_student_code() {
                return $('#dphpforms_ases_student_code').attr('data-info');
            }

            function get_dphpforms_instance() {
                return $('#dphpforms_block_instance').attr('data-info');
            }

            function check_risks_geo_tracking(flag) {
                var geo_risk = get_checked_risk_value_tracking('.seg_geo_nivel_riesgo');
                var geo_observation = $('.seg_geo_observaciones').find('textarea').val();

                if (
                    (geo_risk == '3')
                ) {

                    var json_risks = {
                        "function": "send_email_dphpforms",
                        "student_code": get_student_code(),
                        "risks": [
                            {
                                "name": "Geográfico",
                                "risk_lvl": geo_risk,
                                "observation": geo_observation
                            }
                        ],
                        "date": $('.fecha').find('input').val(),
                        "url": window.location.href
                    };

                    console.log(JSON.stringify(json_risks));

                    $.ajax({
                        type: "POST",
                        data: JSON.stringify(json_risks),
                        url: "../managers/pilos_tracking/send_risk_email.php",
                        success: function (msg) {
                            console.log(msg);
                        },
                        dataType: "text",
                        cache: "false",
                        error: function (msg) {
                            console.log(msg)
                        }
                    });
                }
            }

            function check_risks_tracking(flag) {

                var individual_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_individual');
                var idv_observation = $('.comentarios_individual').find('textarea').val();
                var familiar_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_familiar');
                var fam_observation = $('.comentarios_familiar').find('textarea').val();
                var academico_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_academico');
                var aca_observation = $('.comentarios_academico').find('textarea').val();
                var economico_risk = get_checked_risk_value_tracking('.puntuacion_riesgo_economico');
                var eco_observation = $('.comentarios_economico').find('textarea').val();
                var vida_univer_risk = get_checked_risk_value_tracking('.puntuacion_vida_uni');
                var vid_observation = $('.comentarios_vida_uni').find('textarea').val();

                if (
                    (individual_risk == '3') || (familiar_risk == '3') ||
                    (academico_risk == '3') || (economico_risk == '3') ||
                    (vida_univer_risk == '3')
                ) {

                    var json_risks = {
                        "function": "send_email_dphpforms",
                        "student_code": get_student_code(),
                        "risks": [
                            {
                                "name": "Individual",
                                "risk_lvl": individual_risk,
                                "observation": idv_observation
                            },
                            {
                                "name": "Familiar",
                                "risk_lvl": familiar_risk,
                                "observation": fam_observation
                            },
                            {
                                "name": "Académico",
                                "risk_lvl": academico_risk,
                                "observation": aca_observation
                            },
                            {
                                "name": "Económico",
                                "risk_lvl": economico_risk,
                                "observation": eco_observation
                            },
                            {
                                "name": "Vida Universitaria",
                                "risk_lvl": vida_univer_risk,
                                "observation": vid_observation
                            }
                        ],
                        "date": $('.fecha').find('input').val(),
                        "url": window.location.href
                    };

                    console.log(JSON.stringify(json_risks));

                    $.ajax({
                        type: "POST",
                        data: JSON.stringify(json_risks),
                        url: "../managers/pilos_tracking/send_risk_email.php",
                        success: function (msg) {
                            console.log(msg);
                        },
                        dataType: "text",
                        cache: "false",
                        error: function (msg) {
                            console.error(msg)
                        }
                    });
                }
            }

            function get_checked_risk_value_tracking(class_id) {
                var value = 0;
                $(class_id).find('.opcionesRadio').find('div').each(function () {
                    if ($(this).find('label').find('input').is(':checked')) {
                        value = $(this).find('label').find('input').val();
                    }
                });
                return value;
            }

            $(document).on("click", ".btn.btn-danger.btn-univalle.btn-card", function () {
                let container = $(this).attr('data-container');
                let height = $('#' + container).height();
                if (height == 0) {
                    $(this).find('span').not(".protected").removeClass('glyphicon-chevron-left');
                    $(this).find('span').not(".protected").addClass('glyphicon-chevron-down');
                    $(this).find('.open-close-icon').removeClass('glyphicon-chevron-left');
                    $(this).find('.open-close-icon').addClass('glyphicon-chevron-down');
                } else {
                    while (height < 0) {
                        height = $('#' + container).height();
                    }
                    $(this).find('span').not(".protected").removeClass('glyphicon-chevron-down');
                    $(this).find('span').not(".protected").addClass('glyphicon-chevron-left');
                    $(this).find('.open-close-icon').removeClass('glyphicon-chevron-down');
                    $(this).find('.open-close-icon').addClass('glyphicon-chevron-left');
                }
            });

            $('.btn-inasistencia').on('click', function () {
                var data_info = $(this).attr('data-info');
                if (data_info == 'inasistencia') {
                    $('#modal_inasistencia').fadeOut(300);
                    $('#modal_v2_peer_tracking').fadeIn(300);
                    $('.id_estudiante').find('input').val(get_student_code());
                    var codigo_monitor = $('#current_user_id').val();
                    $('.id_creado_por').find('input').val(codigo_monitor);
                    $('.id_instancia').find('input').val(get_dphpforms_instance());
                    $('.id_monitor').find('input').val($("#dphpforms_monitor_id").data("info"));
                    $('.id_practicante').find('input').val($("#dphpforms_practicing_id").data("info"));
                    $('.id_profesional').find('input').val($("#dphpforms_professional_id").data("info"));
                    $('.username').find('input').val($("#dphpforms_username").data("info"));

                } else {
                    $('#modal_v2_peer_tracking').fadeOut(300);
                    $('#modal_inasistencia').fadeIn(300);
                    $('.in_id_estudiante').find('input').val(get_student_code());
                    var codigo_monitor = $('#current_user_id').val();
                    $('.in_id_creado_por').find('input').val(codigo_monitor);
                    $('.in_id_instancia').find('input').val(get_dphpforms_instance());
                    $('.in_id_monitor').find('input').val($("#dphpforms_monitor_id").data("info"));
                    $('.in_id_practicante').find('input').val($("#dphpforms_practicing_id").data("info"));
                    $('.in_id_profesional').find('input').val($("#dphpforms_professional_id").data("info"));
                    $('.in_username').find('input').val($("#dphpforms_username").data("info"));
                }
            });


            $('#button_add_groupal_track').on('click', function () {
                $("#modal_v2_edit_groupal_tracking").find("#body_editor").html("");
                $('div').removeClass('regla_incumplida');
                $('#modal_v2_groupal_tracking').fadeIn(300);
                var codigo_monitor = $('#current_user_id').val();
                $('.id_creado_por').find('input').val(codigo_monitor);
                $('.id_estudiante').find('input').val("-");
                $('#list_grupal_seg_consult  input[type=checkbox]').prop('checked', false);

            });

            $('#button_add_geographic_track').on('click', function () {
                $('div').removeClass('regla_incumplida');
                $('#modal_seguimiento_geografico').fadeIn(300);
                $('.seg_geo_id_estudiante').find('input').val(get_student_code());
                var creado_por = $('#current_user_id').val();
                $('.seg_geo_id_creado_por').find('input').val(creado_por);

                if ($('.seg_geo_vive_zona_riesgo').find('label').find('input').prop('checked')) {
                    $('.seg_geo_origen').find('input').prop('disabled', false);
                } else {
                    $('.seg_geo_origen').find('input').prop('disabled', true);
                    $('.seg_geo_origen').find('input').prop('checked', false);
                }
            });

            $('.mymodal-close').click(function () {
                $(this).parent().parent().parent().parent().fadeOut(300);
                $("#list_grupal_seg_consult_1").remove();
            });

            // Controles para editar formulario de pares
            $(document).on('click', ".dphpforms-peer-record", function () {
                if (!$(this).attr("disabled")) {
                    var id_tracking = $(this).attr('data-record-id');
                    load_record_updater('seguimiento_pares', id_tracking, params.instanceid);
                    $('#modal_v2_edit_peer_tracking').fadeIn(300);
                }
            });

            // Controles para editar formulario grupal
            $(document).on('click', '.dphpforms-groupal-record', function () {
                if (!$(this).attr("disabled")) {
                    var id_tracking = $(this).attr('data-record-id');
                    load_record_updater('seguimiento_grupal', id_tracking, params.instanceid);
                    $('#modal_v2_edit_groupal_tracking').fadeIn(300);
                }
            });

            function custom_actions(form, action) {

                if ((form == 'primer_acercamiento') && (action == 'insert')) {

                } else if ((form == 'primer_acercamiento') && (action == 'update')) {

                } else if ((form == 'inasistencia') && (action == 'insert')) {

                } else if ((form == 'inasistencia') && (action == 'update')) {

                    var rev_prof = $('.dphpforms-record').find('.in_revisado_profesional').find('.checkbox').find('input[type=checkbox]').prop('checked');
                    var rev_prac = $('.dphpforms-record').find('.in_revisado_practicante').find('.checkbox').find('input[type=checkbox]').prop('checked');
                    var role_support = $('#dphpforms_role_support').attr('data-info');
                    if ((rev_prof) && (role_support != "sistemas")) {
                        $('.btn-dphpforms-delete-record').remove();
                        $('.btn-dphpforms-update').remove();
                    }
                    if (role_support == "dir_socioeducativo") {
                        $('.btn-dphpforms-delete-record').remove();
                        $('.btn-dphpforms-update').remove();
                    }

                } else if ((form == 'seguimiento_pares') && (action == 'insert')) {

                } else if ((form == 'seguimiento_pares') && (action == 'update')) {

                    var rev_prof = $('.dphpforms-record').find('.revisado_profesional').find('.checkbox').find('input[type=checkbox]').prop('checked');
                    var rev_prac = $('.dphpforms-record').find('.revisado_practicante').find('.checkbox').find('input[type=checkbox]').prop('checked');
                    var role_support = $('#dphpforms_role_support').attr('data-info');
                    if ((rev_prof) && (role_support != "sistemas")) {
                        $('.btn-dphpforms-delete-record').remove();
                        $('.btn-dphpforms-update').remove();
                    }
                    if (role_support == "dir_socioeducativo") {
                        $('.btn-dphpforms-delete-record').remove();
                        $('.btn-dphpforms-update').remove();
                    }


                } else if ((form == 'seguimiento_geografico_') && (action == 'insert')) {
                    $('.seg_geo_origen').find('input').prop('disabled', false);

                } else if ((form == 'seguimiento_geografico_') && (action == 'update')) {

                } else if ((form == 'seguimiento_grupal_') && (action == 'insert')) {

                    var total = $('modal_v2_edit_groupal_tracking').find('input:checked').length;
                    var array_students = "";
                    var create = "";

                    $("#modal_v2_groupal_tracking").find('form').find('.oculto.id_estudiante').find('input').val("");
                    $("#modal_v2_edit_groupal_tracking").find('form').find('.oculto.id_estudiante').find('input').val("");

                    $("#modal_v2_edit_groupal_tracking").find('input:checked').each(function (index) {
                        var complete_code = $(this).parent().parent().find(">:first-child").text();
                        var code = complete_code.split("-");
                        array_students += code[0] + ",";

                    });

                    $("#modal_v2_groupal_tracking").find('input:checked').each(function (index) {
                        var complete_code = $(this).parent().parent().find(">:first-child").text();
                        var code = complete_code.split("-");

                        create += code[0] + ",";
                    });

                    $("#modal_v2_edit_groupal_tracking").find('form').find('.oculto.id_estudiante').find('input').val(array_students.slice(0, -1));
                    $("#modal_v2_groupal_tracking").find('form').find('.oculto.id_estudiante').find('input').val(create.slice(0, -1));

                } else if ((form == 'seguimiento_grupal') && (action == 'update')) {

                    let role_support = $('#dphpforms_role_support').attr('data-info');
                    if ((role_support != "profesional_ps") && (role_support != "sistemas")) {
                        $("#modal_v2_edit_groupal_tracking").find(".btn-dphpforms-delete-record").remove();
                    }
                }
            }

            function check_students_groupal_tracks(students) {

                students_code = students.split(',');
                console.log(".id_estudiante :" + students_code);


                //Get student list of the monitor in an array.

                var student_list_of_monitor = [];

                $('#list_students_attendance_1 > tbody  > tr').each(function (index) {
                    var code = $(this).children('td').first().html().split('-');
                    student_list_of_monitor.push(code[0]);
                });

                console.log("lista total de id_estudiantes :" + student_list_of_monitor);
                console.log("lista de estudiantes seleccin : " + students_code);

                $.each(students_code, function (index, val) {
                    var index_selected = student_list_of_monitor.indexOf(val);
                    if (index_selected != -1) {
                        var row = $("#list_students_attendance_1 tbody>tr:eq('" + index_selected + "')");
                        var column = row.find('td').eq(3).children('input');

                        $("input[type=checkbox][value='" + column.val() + "']").prop('checked', true);
                    }
                });
            }

            // Esta función se encarga de cargar un formulario dinámico
            // para ser actualizado (editable)
            //
            // form_id -> Tipo de formulario (seguimiento_grupal o seguimiento_pares)
            // record_id -> id del formulario en la BD
            // instance_id -> instancia a la que pertenece
            // 
            // NOTA: La url en la petición GET puede parecer que tenga un error
            // cerca al parametro form_id, esto es intencional, NO modificar.
            function load_record_updater(form_id, record_id, instance_id) {

                $('.div').removeClass('regla_incumplida');
                $("#body_editor").html("");
                loading_indicator.show();
                $.get("../managers/dphpforms/dphpforms_forms_core.php?form_id=&record_id=" + record_id + "&instance_id="+instance_id, function (data) {
                    loading_indicator.hide();

                    if (form_id == 'seguimiento_grupal') {

                        $("#modal_v2_edit_groupal_tracking").find("#body_editor").html("");
                        $("#modal_v2_edit_groupal_tracking").find("#body_editor").append(data);
                        var students = $("#modal_v2_edit_groupal_tracking").find('form').find('.oculto.id_estudiante').find('input').val();

                        generate_attendance_table(students);

                    } else {

                        $('#body_editor').append(data);

                        var role_support = $('#dphpforms_role_support').attr('data-info');
                        let current_view = $("#custom_metadata").data("view-name");

                        if (current_view == "report_trackings") {
                            if ((role_support == "sistemas") || (role_support == "profesional_ps") || (role_support == "practicante_ps")) {
                                $(".dphpforms.dphpforms-record.dphpforms-updater").append('<br><br><div class="div-observation col-xs-12 col-sm-12 col-md-12 col-lg-12 comentarios_vida_uni">Observaciones de Practicante/profesional:<br> <textarea id="observation_text" class="form-control " name="observation_text" maxlength="5000"></textarea><br><a id="send_observation" class="btn btn-sm btn-danger btn-dphpforms-univalle btn-dphpforms-send-observation">Enviar observación</a></div>');
                            }
                        }

                        $('button.btn.btn-sm.btn-danger.btn-dphpforms-univalle').attr('id', 'button');
                    }

                    var table = $("#list_grupal_seg_consult").clone().prop('id', 'list_grupal_seg_consult_1');
                    $('#modal_v2_edit_groupal_tracking').find('form').find('h1').after(table);
                    $("#list_grupal_seg_consult_1 > #list_students_attendance").prop('id', 'list_students_attendance_1');

                    $('#list_grupal_seg_consult_1  input[type=checkbox]').prop('checked', false);

                    var div_estudiante = $("#modal_v2_edit_groupal_tracking").find('form').find('.oculto.id_estudiante').find('input');
                    if (div_estudiante.val() != undefined) {
                        check_students_groupal_tracks(div_estudiante.val());
                    }

                    $("#permissions_informationr").html("");

                    var rev_prof = $('.dphpforms-record').find('.revisado_profesional').find('.checkbox').find('input[type=checkbox]').prop('checked');
                    var rev_prac = $('.dphpforms-record').find('.revisado_practicante').find('.checkbox').find('input[type=checkbox]').prop('checked');

                    if (rev_prof || rev_prac) {
                        $('.dphpforms-record').find('.btn-dphpforms-delete-record').remove();
                    }

                    var behaviors = JSON.parse($('#permissions_information').text());

                    for (var x = 0; x < behaviors['behaviors_permissions'].length; x++) {

                        var current_behaviors = behaviors['behaviors_permissions'][x]['behaviors'][0];
                        var behaviors_accessibility = current_behaviors.behaviors_accessibility;

                        for (var z = 0; z < behaviors_accessibility.length; z++) {
                            var disabled = behaviors_accessibility[z]['disabled'];
                            if (disabled == 'true') {
                                disabled = true;
                            } else if (disabled == 'false') {
                                disabled = false;
                            }
                            $('.dphpforms-record').find('#' + behaviors_accessibility[z]['id']).prop('disabled', disabled);
                            $('.dphpforms-record').find('.' + behaviors_accessibility[z]['class']).prop('disabled', disabled);
                        }
                        var behaviors_fields_to_remove = current_behaviors['behaviors_fields_to_remove'];
                        for (var z = 0; z < behaviors_fields_to_remove.length; z++) {
                            $('.dphpforms-record').find('#' + behaviors_fields_to_remove[z]['id']).remove();
                            $('.dphpforms-record').find('.' + behaviors_fields_to_remove[z]['class']).remove();
                        }
                        var limpiar_to_eliminate = current_behaviors['limpiar_to_eliminate'];
                        for (var z = 0; z < limpiar_to_eliminate.length; z++) {
                            $('.dphpforms-record').find('.' + limpiar_to_eliminate[z]['class'] + '.limpiar ').remove();
                        }
                    }

                    let separator = '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> \
                    <hr style="border-color:#424242;margin-bottom:3px;">\
                </div>';

                    $('#dphpforms_record_id[value=' + record_id + ']').parent().parent().append(separator);

                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            function: "get_pretty_record_history",
                            params: [record_id]
                        }),
                        url: "../managers/dphpforms/v2/dphpforms_api.php",
                        async: false,
                        dataType: "json",
                        cache: "false",
                        success: function (data) {
                            let history = JSON.parse(data.data_response);

                            let element_date = '<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="font-size:0.9em;"><strong style="color:#424242;">Fecha y hora</strong></div>';
                            let element_name = '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="font-size:0.9em;"><strong style="color:#424242;">Usuario que realiza la acci&oacute;n</strong></div>';
                            let element_action = '<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="font-size:0.9em;"><strong style="color:#424242;">Acci&oacute;n</strong></div>';
                            let element_status = '<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="font-size:0.9em;"><strong style="color:#424242;">Estado</strong></div>';
                            let tseparator = '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> \
                            <hr style="border-color:#424242; margin-top:0px; margin-bottom:10px;">\
                        </div>';
                            $('#dphpforms_record_id[value=' + record_id + ']').parent().parent().append(element_date + element_name + element_action + element_status + tseparator);

                            history.forEach((item) => {
                                let element_date = '<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">'
                                    + item.fecha_hora_registro +
                                    '</div>';
                                let element_name = '<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">'
                                    + item.usuario_moodle +
                                    '</div>';

                                let action = "";

                                switch (item.accion) {
                                    case "INSERT":
                                        action = "CREA";
                                        break;
                                    case "UPDATE":
                                        action = "ACTUALIZA";
                                        break;
                                    case "DELETE":
                                        action = "ELIMINA";
                                        break;
                                    default:
                                        action = "DESCONOCIDA";
                                        break;
                                }

                                let element_action = '<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">'
                                    + action +
                                    '</div>';
                                let element_status = '<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">'
                                    + (item.cod_retorno !== "0" ? "( No completado )" : "( Completado )") +
                                    '</div>';
                                $('#dphpforms_record_id[value=' + record_id + ']').parent().parent().append('<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"  style="padding:0px;font-size:0.9em;">' + element_date + element_name + element_action + element_status + '</div>');
                            });

                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });

                    $("#permissions_informationr").html("");

                    var is_primer_acercamiento = data.indexOf('primer_acercamiento_');
                    if (is_primer_acercamiento != -1) {
                        custom_actions('primer_acercamiento', 'update');
                    }
                    var is_seguimiento_pares = data.indexOf('seguimiento_de_pares_');
                    if (is_seguimiento_pares != -1) {
                        custom_actions('seguimiento_pares', 'update');
                    }
                    var is_inasistencia = data.indexOf('inasistencia');
                    if (is_inasistencia != -1) {
                        custom_actions('inasistencia', 'update');
                    }
                    var is_inasistencia = data.indexOf('seguimiento_grupal');
                    if (is_inasistencia != -1) {
                        custom_actions('seguimiento_grupal', 'update');
                    }
                    $("input[type=date]").keypress(function (e) {
                        e.preventDefault();
                    });
					
				    // Campos deprecados.
				    const deprecatedFields = [
					    'relacion_pareja',
					    'composicion_familiar',
					    'influencia',
					    'aspec_academicos',
					    'estra_academicas',
					    'inter_autoe_ocupacional',
					    'carac_socioeconomicas',
					    'icetex',
					    'oferta_servicios',
					    'induccion',
					    'vida_universitaria',
					    'retor_ciuda_origen'
				    ];

				    deprecatedFields.forEach( fieldValue => {
				    	$(`input[value=${fieldValue}]`).parent().css("display", "none")
				    });
                });
            }

            $(".limpiar").click(function () {
                $(this).parent().find(".opcionesRadio").find('div').each(function () {
                    $(this).find("label").find("input").prop("checked", false);
                });
            });

            $(document).on('click', '.limpiar', function () {
                $(this).parent().find(".opcionesRadio").find('div').each(function () {
                    $(this).find("label").find("input").prop("checked", false);
                });
            });

            $(document).on('submit', '.dphpforms', function (evt) {

                evt.preventDefault();
                var is_seguimiento_geografico = $(this).attr('id').indexOf('seguimiento_geografico_');
                var is_seguimiento_grupal = $(this).attr('id').indexOf('seguimiento_grupal_');

                if (is_seguimiento_geografico != -1) {
                    custom_actions('seguimiento_geografico_', 'insert');
                }

                if (is_seguimiento_grupal != -1) {
                    $("#modal_v2_edit_groupal_tracking").find('form').find('.oculto.id_estudiante').find('input').val("");
                    custom_actions('seguimiento_grupal_', 'insert');
                }

                $('.seg_geo_origen').find('input').prop('disabled', false);
                $(':disabled').prop('disabled', false);

                var formData = new FormData(this);

                var formulario = $(this);
                var url_processor = formulario.attr('action');
                if (formulario.attr('action') == 'procesador.php') {
                    url_processor = '../managers/dphpforms/procesador.php';
                }

                var record_id = $('#dphpforms_record_id').val();

                $(formulario).find('button').prop("disabled", true);
                $(formulario).find('a').attr("disabled", true);
                loading_indicator.show();
                $.ajax({
                    type: 'POST',
                    url: url_processor,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        loading_indicator.hide();
                        var response = JSON.parse(data);

                        if (response['status'] == 0) {
                            $.get("../managers/pilos_tracking/api_pilos_tracking.php?function=update_last_user_risk&arg=" + get_student_code() + "&rid=" + record_id, function (data) {
                                console.log(data);
                            });
                            var mensaje = '';
                            if (response['message'] == 'Stored') {
                                mensaje = 'Almacenado, se va a descargar una copia de seguridad, por favor consérvela, informar si no se genera la copia.';
                                let record_id = response['data'];
                                let record = null;
                                let record_raw = null;
                                $.ajax({
                                    url: "../managers/dphpforms/dphpforms_get_record.php?record_id=" + record_id,
                                    dataType: 'json',
                                    async: false,
                                    success: function (data) {
                                        record_raw = data;
                                        record = JSON.stringify(data);
                                    }
                                });

                                let k = null;
                                $.ajax({
                                    url: "../managers/dphpforms/v2/dphpforms_api.php",
                                    type: "POST",
                                    data: JSON.stringify({
                                        function: "get_k",
                                        params: [record_id]
                                    }),
                                    dataType: 'json',
                                    async: false,
                                    success: function (data) {
                                        k = data.data_response;
                                    }
                                });

                                let filename = $("#dphpforms_fullname").data("info") + " - " + record_raw.record.alias + "-" + record_id + "-" + "k-" + k + " - Fecha " + record_raw.record.fecha_hora_registro + ".json";

                                var file = new Blob([record], {type: ".json"});
                                if (window.navigator.msSaveOrOpenBlob) // IE10+
                                    window.navigator.msSaveOrOpenBlob(file, filename);
                                else { // Others
                                    var a = document.createElement("a"),
                                        url = URL.createObjectURL(file);
                                    a.href = url;
                                    a.download = filename;
                                    document.body.appendChild(a);
                                    a.click();
                                    setTimeout(function () {
                                        document.body.removeChild(a);
                                        window.URL.revokeObjectURL(url);
                                    }, 0);
                                }

                            } else if (response['message'] == 'Updated') {
                                mensaje = 'Actualizado, las fechas e íconos se actualizarán al recargar la página. ';
                            }
                            check_risks_tracking();
                            check_risks_geo_tracking();
                            swal(
                                {
                                    title: 'Información',
                                    text: mensaje,
                                    type: 'success'
                                },
                                function () {
                                    if (response['message'] == 'Updated') {
                                        $('#dphpforms-peer-record-' + $('#dphpforms_record_id').val()).stop().animate({backgroundColor: 'rgb(175, 255, 173)'}, 400).animate({backgroundColor: '#f5f5f5'}, 4000);
                                        $("#body_editor").html("");
                                    } else {
                                        $('.dphpforms-response').trigger("reset");
                                        $("#body_editor").html("");
                                    }
                                }
                            );

                            $('#modal_v2_edit_peer_tracking').fadeOut(300);
                            $('#modal_v2_peer_tracking').fadeOut(300);
                            $('#modal_primer_acercamiento').fadeOut(300);
                            $('#modal_seguimiento_geografico').fadeOut(300);

                            $(formulario).find('button').prop("disabled", false);
                            $(formulario).find('a').attr("disabled", false);

                        } else if (response['status'] == -6) {
                            $(formulario).find('button').prop("disabled", false);
                            $(formulario).find('a').attr("disabled", false);
                            var mensaje = '';
                            if (response['message'] == 'The value of the field is out of range') {

                                var id_form_pregunta = response['data']['id'];
                                $('div').removeClass('regla_incumplida');
                                $('.div-' + id_form_pregunta).addClass('regla_incumplida');

                                mensaje = 'Ups!, el campo marcado en rojo tiene una fecha por fuera del siguiente rango: ' + response['data']['min'] + " hasta " + response['data']['max'];
                            }
                            $("#modal_v2_edit_peer_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_v2_peer_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_primer_acercamiento").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_seguimiento_geografico").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_v2_groupal_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            swal(
                                'Alerta',
                                mensaje,
                                'warning'
                            );

                        } else if (response['status'] == -5) {
                            $(formulario).find('button').prop("disabled", false);
                            $(formulario).find('a').attr("disabled", false);
                            var mensaje = '';
                            if (response['message'] == 'The field is static and can not be changed') {

                                var id_form_pregunta = response['data'];
                                $('div').removeClass('regla_incumplida');
                                $('.div-' + id_form_pregunta).addClass('regla_incumplida');

                                mensaje = 'Ups!, el campo marcado en rojo está definido como estático y por lo tanto debe mantener el mismo valor, si no logra ver el campo marcado en rojo informe de este incidente.';
                            }
                            $("#modal_v2_edit_peer_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_v2_peer_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_primer_acercamiento").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_seguimiento_geografico").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_v2_groupal_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            swal(
                                'Alerta',
                                mensaje,
                                'warning'
                            );
                        } else if (response['status'] == -4) {
                            $(formulario).find('button').prop("disabled", false);
                            $(formulario).find('a').attr("disabled", false);
                            var mensaje = '';
                            if (response['message'] == 'Field does not match with the regular expression') {

                                var id_form_pregunta = response['data']['id'];
                                $('div').removeClass('regla_incumplida');
                                $('.div-' + id_form_pregunta).addClass('regla_incumplida');

                                mensaje = 'Ups!, el campo marcado en rojo no cumple con el patrón esperado(' + response['data']['human_readable'] + '). Ejemplo: ' + response['data']['example'];
                            }
                            $("#modal_v2_edit_peer_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_v2_peer_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_primer_acercamiento").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_seguimiento_geografico").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_v2_groupal_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            swal(
                                'Alerta',
                                mensaje,
                                'warning'
                            );
                        } else if (response['status'] == -3) {
                            $(formulario).find('button').prop("disabled", false);
                            $(formulario).find('a').attr("disabled", false);
                            var mensaje = '';
                            if (response['message'] == 'Field cannot be null') {

                                var id_form_pregunta = response['data'];
                                $('div').removeClass('regla_incumplida');
                                $('.div-' + id_form_pregunta).addClass('regla_incumplida');

                                mensaje = 'Ups!, los campos que se acaban de colorear en rojo no pueden estar vacíos, si no logra ver ningún campo, informe de este incidente.';
                            }
                            $("#modal_v2_edit_peer_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_v2_peer_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_primer_acercamiento").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_seguimiento_geografico").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_v2_groupal_tracking").scrollTo('.div-' + id_form_pregunta, {
                                duration: 1500,
                                offset: -150
                            });
                            swal(
                                'Alerta',
                                mensaje,
                                'warning'
                            );
                        } else if (response['status'] == -2) {
                            $(formulario).find('button').prop("disabled", false);
                            $(formulario).find('a').attr("disabled", false);
                            var mensaje = '';
                            if (response['message'] == 'Without changes') {
                                mensaje = 'No hay cambios que registrar';
                                $('#modal_v2_edit_peer_tracking').fadeOut(300);
                                $('#modal_v2_peer_tracking').fadeOut(300);
                                $('#modal_primer_acercamiento').fadeOut(300);
                                $('#modal_seguimiento_geografico').fadeOut(300);
                            } else if (response['message'] == 'Unfulfilled rules') {
                                var id_form_pregunta_a = response['data']['id_form_pregunta_a'];
                                var id_form_pregunta_b = response['data']['id_form_pregunta_b'];
                                $('div').removeClass('regla_incumplida');
                                $('.div-' + id_form_pregunta_a).addClass('regla_incumplida');
                                $('.div-' + id_form_pregunta_b).addClass('regla_incumplida');

                                mensaje = 'Ups!, revise los campos que se acaban de colorear en rojo.';
                            }
                            $("#modal_v2_edit_peer_tracking").scrollTo('.div-' + id_form_pregunta_a, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_v2_peer_tracking").scrollTo('.div-' + id_form_pregunta_a, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_primer_acercamiento").scrollTo('.div-' + id_form_pregunta_a, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_seguimiento_geografico").scrollTo('.div-' + id_form_pregunta_a, {
                                duration: 1500,
                                offset: -150
                            });
                            $("#modal_v2_groupal_tracking").scrollTo('.div-' + id_form_pregunta_a, {
                                duration: 1500,
                                offset: -150
                            });
                            swal(
                                'Alerta',
                                mensaje,
                                'warning'
                            );
                        } else if (response['status'] == -1) {
                            console.log(data);
                            swal(
                                'ERROR!',
                                'Ups!, informe de este error',
                                'error'
                            );
                        }
                    },
                    error: function (data) {
                        loading_indicator.hide();
                        console.log(data);
                        swal(
                            'Error!',
                            'Ups!, informe de este error',
                            'error'
                        );
                        $(formulario).find('button').prop("disabled", false);
                        $(formulario).find('a').attr("disabled", false);
                    }
                });
            });

            $(document).on('click', '.btn-dphpforms-delete-record', function () {

                swal({
                    title: 'Confirmación',
                    text: "Está eliminando este registro, ¿desea continuar?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, Eliminar!'
                }, function (isConfirm) {
                    if (isConfirm) {
                        var record_id = $('.btn-dphpforms-delete-record').attr('data-record-id');
                        loading_indicator.show();
                        $.get("../managers/dphpforms/dphpforms_delete_record.php?record_id=" + record_id, function (data) {
                            loading_indicator.hide();
                            var response = data;
                            if (response['status'] == 0) {
                                console.log(response);
                                $.get("../managers/pilos_tracking/api_pilos_tracking.php?function=update_last_user_risk&arg=" + get_student_code() + "&rid=" + record_id, function (datax) {
                                    console.log(datax);
                                });
                                setTimeout(function () {
                                    swal(
                                        {
                                            title: 'Información',
                                            text: 'Eliminado',
                                            type: 'success'
                                        },
                                        function () {
                                            $('.mymodal').fadeOut(300);
                                            $('#dphpforms-peer-record-' + record_id).stop().animate({backgroundColor: 'rgb(175, 255, 173)'}, 400).animate({backgroundColor: '#e32423'}, 4000, function () {
                                                $("#dphpforms-peer-record-" + record_id).remove();
                                            });
                                        }
                                    );
                                }, 500);

                            } else if (response['status'] == -1) {
                                setTimeout(function () {
                                    swal(
                                        'Error!',
                                        response['message'],
                                        'error'
                                    );
                                }, 500);
                            }
                        });
                    }
                });
            });

            function generate_attendance_table(students) {
                loading_indicator.show();
                $.ajax({
                    type: "POST",
                    data: {
                        students: students,
                        type: "consult_students_name"
                    },
                    url: "../../../blocks/ases/managers/pilos_tracking/pilos_tracking_report.php",
                    async: false,
                    success: function (msg) {

                        loading_indicator.hide();
                        if (msg != "") {
                            var table = '<hr style="border-color:red"><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 estudiantes" id="students"><h3>Estudiantes asistentes:</h3><br>' + msg + '<br>';
                            $('#modal_v2_edit_groupal_tracking').find('#students').remove();
                            $('#modal_v2_edit_groupal_tracking').find('form').find('h1').after(table);
                        }

                    },
                    dataType: "text",
                    cache: "false",
                    error: function (msg) {
                        alert("Error al consultar nombres de los estudiantes pertenecientes a un seguimiento grupal");
                        loading_indicator.hide();
                    },
                });
            }
        }
    };
});
