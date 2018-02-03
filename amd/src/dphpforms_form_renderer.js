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

                $('#socioed_li').click(function(){
                    var codigo_estudiante = $('#codigo').val();
                    var periodo_A = [1, 2, 3, 4, ,5 ,6 ,7];
                    var periodo_B = [8, 9, 10, 11, ,12];
                    $.get( "../managers/dphpforms/dphpforms_records_finder.php?form_id=96&pregunta_id=802&criterio="+codigo_estudiante+"&order=ASC", function( data ) {
                            /*$("#body_editor").html("");
                            $('#body_editor').append( data );*/
                            var seguimientos = data;
                            var detalles_seguimientos = [];
                            var registros_ordenados = [];
                            var fechas_seguimiento = [];
                            var fechas_seguimiento_date = [];
                            for( var i = 0 ; i < seguimientos['results'].length; i++ ){
                                $.get( "../managers/dphpforms/dphpforms_get_record.php?record_id=" + seguimientos['results'][i]['id_registro'], function( data_detalle ) {
                                    var fecha_registro = null;
                                    for(var x = 0; x < data_detalle['record']['campos'].length; x++ ){
                                        if(data_detalle['record']['campos'][x]['local_alias'] == 'fecha'){
                                            fecha_registro = data_detalle['record']['campos'][x]['respuesta'];
                                            break;
                                        }
                                    }
                                    var fecha_registro = new Date(fecha_registro);
                                    fechas_seguimiento_date.push(fecha_registro);
                                    fechas_seguimiento.push( parseInt( Date.parse(fecha_registro)));

                                });
                            }

                            var m = fechas_seguimiento_date.sort(function(a, b){return new Date(a) - new Date(b)});
                            //var m = m.sort(function(a, b){return b-a});
                            console.log(m);
                            console.log(fechas_seguimiento_date);
                            
                    });
                    
                });

                $('#button_add_v2_track').on('click', function() {
                    $('#modal_v2_peer_tracking').fadeIn(300);
                    var codigo_estudiante = $('#codigo').val();
                    $('.id_estudiante').find('input').val(codigo_estudiante);
                    var codigo_monitor = $('#current_user_id').val();
                    $('.id_creado_por').find('input').val(codigo_monitor);
                });
                $('.mymodal-close').click(function(){
                    $(this).parent().parent().parent().parent().fadeOut(300);
                });

                // Controles para editar formulario de pares
                $('.edit_peer_v2_tracking').on('click', function(){
                    var id_tracking = $(this).attr('data-record-id');
                    load_record_updater('96', '23');
                    $('#modal_v2_edit_peer_tracking').fadeIn(300);
                });

                function load_record_updater(form_id, record_id){
                    $.get( "../managers/dphpforms/dphpforms_forms_core.php?form_id="+form_id+"&record_id="+record_id, function( data ) {
                            $("#body_editor").html("");
                            $('#body_editor').append( data );
                            $("#permissions_informationr").html("");
                            var behaviors = JSON.parse($('#permissions_information').text());
                            
                            for(var x = 0; x < behaviors['behaviors_permissions'].length; x++){
                             
                                var current_behaviors =  behaviors['behaviors_permissions'][x]['behaviors'][0];
                                
                                //
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

                 //formulario_prueba_d3_62s
                 $(document).on('submit', '.dphpforms' , function(evt) {
                    evt.preventDefault();
                    var formData = new FormData(this);
                    var formulario = $(this);
                    var url_processor = formulario.attr('action');
                    if(formulario.attr('action') == 'procesador.php'){
                        url_processor = '../managers/dphpforms/procesador.php';
                    }
                    $.ajax({
                        type: 'POST',
                        url: url_processor,
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                                var response = JSON.parse(data);
                                if(response['status'] == 0){
                                    swal(
                                        'Información',
                                        response['message'],
                                        'success'
                                    );
                                    $('.dphpforms-response').trigger("reset");
                                    $('#modal_v2_edit_peer_tracking').fadeOut(300);
                                    $('#modal_v2_peer_tracking').fadeOut(300);
                                }else if(response['status'] == -2){
                                    swal(
                                        'Alerta',
                                        response['message'],
                                        'warning'
                                    );
                                }else if(response['status'] == -1){
                                    swal(
                                        'ERROR!',
                                        response['message'],
                                        'error'
                                    );
                                };
                                
                            },
                            error: function(data) {
                                swal(
                                    'Error!',
                                    data,
                                    'error'
                                );
                            }
                     });
                });
                
            }

    };
      
})