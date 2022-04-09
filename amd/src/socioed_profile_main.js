button_add_v2_track/**
 * Social-educative tracking
 * @module amd/src/socioed_profile_main
 * @author Jorge Eduardo Mayor Fernández
 * @copyright 2019 Jorge Eduardo Mayor <mayor.jorge@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'block_ases/loading_indicator',
        'block_ases/mustache'], 
        function($, loading_indicator, mustache) {

    return {

        init: function() {
            
            const params = get_url_parameters(document.location.search)

            var student_code = $('#dphpforms_ases_student_code').attr('data-info');

            $('#button_add_v2_track').on('click', function() {

                var codigo_monitor = $('#current_user_id').val();
                var dphpforms_instance = $('#dphpforms_block_instance').attr('data-info');

                $('div').removeClass('regla_incumplida');
                $('#modal_v2_peer_tracking').fadeIn(300);
                $('.id_estudiante').find('input').val(student_code);
                $('.id_creado_por').find('input').val(codigo_monitor);
                $('.id_instancia').find('input').val(dphpforms_instance);
                $('.id_monitor').find('input').val( $("#dphpforms_monitor_id").data("info") );
                $('.id_practicante').find('input').val( $("#dphpforms_practicing_id").data("info") );
                $('.id_profesional').find('input').val( $("#dphpforms_professional_id").data("info") );
                $('.username').find('input').val( $("#dphpforms_username").data("info") );
                
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
            $('#button_carga_Hisotricos').on('click', function() {
                loading_indicator.show();
                var id_ases = $('#id_ases').val();
                var id_instance = document.querySelector('#dphpforms_block_instance').dataset.info;
                $.ajax({
                    type: "POST",
                    data: JSON.stringify({
                        "function": 'cargar_historicos',
                        "params": [id_ases, id_instance],
                    }),
                    url: "../managers/student_profile/studentprofile_api.php",
                    success: function(msg) {
                        if(msg.status_code == 0) {
                            $.ajax({
                                url: "../templates/socioed_historic.mustache",
                                data: null,
                                dataType: "text",
                                async: false,
                                success: function( template ){
                                    loading_indicator.hide();
                                    let tab_to_load = $(mustache.render( template, msg.data_response ));
                                    $("#socioed_historic").append( tab_to_load );
                                },
                                error: function(msg) {
                                    loading_indicator.hide();
                                    console.log(msg);
                                }
                            });
                        }else {
                            console.log(msg);
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        loading_indicator.hide();
                        console.log(msg);
                    }
                });
                loading_indicator.hide();
                $('#button_carga_Hisotricos').prop('disabled',true)
            });

            $('#button_primer_acercamiento').on('click', function() {

                var creado_por = $('#current_user_id').val();

                $('div').removeClass('regla_incumplida');
                $('#modal_primer_acercamiento').fadeIn(300);
                $('.primer_acerca_id_estudiante_field').find('input').val(student_code);
                $('.primer_acerca_id_creado_por_field').find('input').val(creado_por);
            });

            $('#button_actualizar_primer_acercamiento').click(function(){
                $('div').removeClass('regla_incumplida');


                $.get( "../managers/dphpforms/dphpforms_forms_core.php?form_id=primer_acercamiento&instance_id="+params.instanceid+"&record_id=" + $(this).attr('data-record-id'), function( data ) {
                    $("#primer_acercamiento_form").html("");
                    $('#primer_acercamiento_form').append( data );
                    $('#modal_primer_acercamiento').fadeIn(300);

                    var id_creado_por = $('#modal_primer_acercamiento').find('.pa_id_creado_por').find('input').val();

                    $.get( "../managers/user_management/api_user.php?function=get_user_information&arg=" + id_creado_por, function( response ) {

                        var registered_by = response.firstname + ' ' + response.lastname;

                        $('#modal_primer_acercamiento').find('h1').after('<hr style="border-color:#444;"><h3>Registrado por: <strong>' + registered_by + '</strong></h3>');

                        var count_buttons_dphpforms = $('.dphpforms-record .btn-dphpforms-univalle').length;
                        if( count_buttons_dphpforms == 2 ){
                            $('.dphpforms-record .btn-dphpforms-univalle:eq(0)').css( { 'margin-left' : ( ($('.dphpforms-updater').width()/2) - 72 ) + 'px'  } );
                        }else if( count_buttons_dphpforms == 3 ){
                            $('.dphpforms-record .btn-dphpforms-univalle:eq(0)').css( { 'margin-left' : ( ($('.dphpforms-updater').width()/2) - 72 - 30 ) + 'px'  } );
                        }
                    });
                });
            });
        }
    };

    function get_url_parameters(page) {
        // This function is anonymous, is executed immediately and
        // the return value is assigned to QueryString!
        var query_string = [];
        var query = document.location.search.substring(1);
        var vars = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            query_string[pair[0]] = pair[1];
        }

        return query_string;
    }
});

