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

                $('#button_add_test_track').on('click', function() {
                    $('#modal_test_peer_tracking').fadeIn(300);
                });
                $('.mymodal-close').click(function(){
                    $(this).parent().parent().parent().parent().fadeOut(300);
                });

                // Controles para editar formulario de pares
                $('.edit_peer_test_tracking').on('click', function(){
                    var id_tracking = $(this).attr('data-record-id');
                    load_record_updater('88', '27', '7');
                    $('#modal_test_edit_peer_tracking').fadeIn(300);
                });

                function load_record_updater(form_id, record_id, rol){
                    $.get( "../managers/dphpforms/dphpforms_forms_core.php?form_id="+form_id+"&rol="+rol+"&record_id="+record_id, function( data ) {
                            $("#body_editor").html("");
                            $('#body_editor').append( data );
                            var behaviors = JSON.parse($('#permissions_information').text());
                            console.log(behaviors);

                            for(var x = 0; x < behaviors['behaviors_permissions'].length; x++){
                                
                                for(var y = 0; y < behaviors['behaviors_permissions'][x]['behaviors'].length; y++ ){

                                    var current_behaviors = behaviors['behaviors_permissions'][x]['behaviors'][y];
                                    console.log(current_behaviors);
                                    var behaviors_accessibility = current_behaviors['behaviors_accessibility'];
                                    for( var z = 0; z <  behaviors_accessibility.length; z++){
                                        $('#' + behaviors_accessibility[z]['id']).prop( 'disabled', z['disabled'] );
                                        $('.' + behaviors_accessibility[z]['class']).prop( 'disabled', z['disabled'] );
                                    }
                                    var behaviors_fields_to_remove = current_behaviors['behaviors_fields_to_remove'];
                                    for( var z = 0; z < behaviors_fields_to_remove.length; z++){
                                        $('#' + behaviors_fields_to_remove[z]['id']).remove();
                                        $('.' + behaviors_fields_to_remove[z]['class']).remove();
                                    }
                                    var limpiar_to_eliminate = current_behaviors['limpiar_to_eliminate'];
                                    for( var z = 0; z <  limpiar_to_eliminate.length; z++){
                                        $('.' + limpiar_to_eliminate[z]['class'] + '.limpiar ').remove();
                                    }

                                }
                            }

                            /*for( var behavior in behaviors['behaviors_permissions']){
                                
                                var behaviors_accessibility = behavior['behaviors_accessibility'];
                                for( var x in  behaviors_accessibility){
                                    $('#' + x['id']).prop( 'disabled', x['disabled'] );
                                    $('.' + x['class']).prop( 'disabled', x['disabled'] );
                                }
                                var behaviors_fields_to_remove = behavior['behaviors_fields_to_remove'];
                                for( var x in  behaviors_fields_to_remove){
                                    $('#' + x['id']).remove();
                                    $('.' + x['class']).remove();
                                }
                                var limpiar_to_eliminate = behavior['limpiar_to_eliminate'];
                                for( var x in  limpiar_to_eliminate){
                                    $('.' + x['class'] + '.limpiar ').remove();
                                }
                                
                            }*/
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