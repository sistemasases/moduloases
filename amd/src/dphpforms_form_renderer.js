// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/dphpforms_form_builder
  */

  define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui','block_ases/select2'], function($, bootstrap, sweetalert, jqueryui, select2) {
    
    return {
        init: function() {

                $('#button_actualizar_primer_acercamiento').click(function(){
                    $.get( "../managers/dphpforms/dphpforms_forms_core.php?form_id=primer_acercamiento&record_id=" + $(this).attr('data-record-id'), function( data ) {
                        $("#primer_acercamiento_form").html("");
                        $('#primer_acercamiento_form').append( data );
                        $('#modal_primer_acercamiento').fadeIn(300);
                    });
                });
                

                function get_url_parameters(url){
                    var start_param_position = url.indexOf("?");
                    var params = "";
                    for(var i = start_param_position; i < url.length; i++){
                        params += url[i];
                    }
                    return params.replace(/#[a-zA-z]+_[a-zA-z]+/i, '');
                }

                /*function get_student_code(){
                    var url = location.search;
                    var params = get_url_parameters( url );

                    var student_position = params.indexOf('student_code=') + 13;
                    var codigo_estudiante = '';
                    for(var x = student_position; x < params.length; x++ ){
                        if(student_position[x] == '-'){
                            break;
                        }else{
                            codigo_estudiante += params[x];
                        }
                    }
                    return get_id_instance();
                }*/

                function get_student_code() {
                    var urlParameters = location.search.split('&');
                    for (var x in urlParameters) {
                        if (urlParameters[x].indexOf('student_code') >= 0) {
                            var intanceparameter = urlParameters[x].split('=');
                            return intanceparameter[1].split('-')[0];
                        }
                    }
                    return 0;
                }

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

                            console.log( JSON.stringify(json_risks) );

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

                $('.btn.btn-danger.btn-univalle.btn-card').click(function(){
                    var container = $(this).attr('data-container');
                    var height = $('#' + container).height();

                    

                    if(height == 0){
                        $(this).find('span').removeClass('glyphicon-chevron-left');
                        $(this).find('span').addClass('glyphicon-chevron-down');
                    }else{
                        while(height < 0){
                            height = $('#' + container).height();
                        }
                        $(this).find('span').removeClass('glyphicon-chevron-down');
                        $(this).find('span').addClass('glyphicon-chevron-left');
                    }
                })

                $('#button_add_v2_track').on('click', function() {

                    $('#modal_v2_peer_tracking').fadeIn(300);

                    $('.id_estudiante').find('input').val( get_student_code() );

                    var codigo_monitor = $('#current_user_id').val();
                    $('.id_creado_por').find('input').val(codigo_monitor);

                });

                $('#button_primer_acercamiento').on('click', function() {

                    $('#modal_primer_acercamiento').fadeIn(300);

                    $('.primer_acerca_id_estudiante_field').find('input').val( get_student_code() );

                    var creado_por = $('#current_user_id').val();
                    $('.primer_acerca_id_creado_por_field').find('input').val(creado_por);

                });

                $('.mymodal-close').click(function(){
                    $(this).parent().parent().parent().parent().fadeOut(300);
                });

                // Controles para editar formulario de pares
                $('.dphpforms-peer-record').on('click', function(){
                    var id_tracking = $(this).attr('data-record-id');
                    load_record_updater('seguimiento_pares', id_tracking);
                    $('#modal_v2_edit_peer_tracking').fadeIn(300);
                });

                function load_record_updater(form_id, record_id){
                    $.get( "../managers/dphpforms/dphpforms_forms_core.php?form_id="+form_id+"&record_id="+record_id, function( data ) {
                            $("#body_editor").html("");
                            $('#body_editor').append( data );
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

                $(".limpiar").click(function(){
                    $(this).parent().find(".opcionesRadio").find('div').each(function(){
                        $(this).find("label").find("input").prop("checked", false);
                    });
                });

                $(document).on('click', '.limpiar' , function() {
                    $(this).parent().find(".opcionesRadio").find('div').each(function(){
                        $(this).find("label").find("input").prop("checked", false);
                    });
                 });

                $(document).on('submit', '.dphpforms' , function(evt) {

                    evt.preventDefault();
                    var formData = new FormData(this);
                    var formulario = $(this);
                    var url_processor = formulario.attr('action');
                    if(formulario.attr('action') == 'procesador.php'){
                        url_processor = '../managers/dphpforms/procesador.php';
                    }
                    $(formulario).find('button').prop( "disabled", true );
                    $(formulario).find('a').attr("disabled", true);
                    $.ajax({
                        type: 'POST',
                        url: url_processor,
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                                var response = JSON.parse(data);
                                console.log(data);
                                
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
                                            }else{
                                                $('.dphpforms-response').trigger("reset");
                                                location.reload();
                                            }
                                        }
                                    );
                                    
                                    $('#modal_v2_edit_peer_tracking').fadeOut(300);
                                    $('#modal_v2_peer_tracking').fadeOut(300);
                                    $('#modal_primer_acercamiento').fadeOut(300);
                                    $(formulario).find('button').prop( "disabled", false);
                                    $(formulario).find('a').attr( "disabled", false);

                                    $.get( "../managers/pilos_tracking/api_pilos_tracking.php?function=update_last_user_risk&arg=" + get_student_code(), function( data ) {
                                        console.log( data );
                                    });

                                    
                                    
                                }else if(response['status'] == -2){
                                    $(formulario).find('button').prop( "disabled", false);
                                    $(formulario).find('a').attr( "disabled", false);
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
                                    console.log(data);
                                    swal(
                                        'ERROR!',
                                        'Oops!, informe de este error',
                                        'error'
                                    );
                                };
                            },
                            error: function(data) {
                                console.log(data);
                                swal(
                                    'Error!',
                                    'Oops!, informe de este error',
                                    'error'
                                );
                                $(formulario).find('button').prop( "disabled", false);
                                $(formulario).find('a').attr( "disabled", false);
                            }
                            
                     });
                     
                });

                $(document).on('click', '.btn-dphpforms-delete-record' , function() {

                    swal({
                        title: 'Confirmación',
                        text: "Está eliminando este registro, ¿desea continuar?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Eliminar!'
                      }, function(isConfirm) {
                        if (isConfirm) {
                            var record_id = $('.btn-dphpforms-delete-record').attr('data-record-id');
                            $.get( "../managers/dphpforms/dphpforms_delete_record.php?record_id="+record_id, function( data ) {
                                var response = data;
                                if(response['status'] == 0){
                                    swal(
                                        {title:'Información',
                                        text: 'Eliminado',
                                        type: 'success'},
                                        function(){
                                            $('#modal_v2_edit_peer_tracking').fadeOut( 300 );
                                            //$('#dphpforms-peer-record-' + record_id).remove();
                                            $('#modal_primer_acercamiento').fadeOut( 300 );
                                            location.reload();
                                        }
                                    );
                                }else if(response['status'] == -1){
                                    swal(
                                        'Error!',
                                        response['message'],
                                        'error'
                                    );
                                }
                            });
                        }
                      });
                    
                });

                
                
            }
    };
      
})