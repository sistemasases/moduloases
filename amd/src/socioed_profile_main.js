/**
 * Social-educative tracking
 * @module amd/src/socioed_profile_main
 * @author Jorge Eduardo Mayor Fernández
 * @copyright 2019 Jorge Eduardo Mayor <mayor.jorge@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

    return {

        init: function() {

            $('#button_add_v2_track').on('click', function() {

                $('div').removeClass('regla_incumplida');
                $('#modal_v2_peer_tracking').fadeIn(300);

            });
            
            $('.btn-inasistencia').on('click', function () {
                var data_info = $(this).attr('data-info');
                if (data_info === 'inasistencia') {
                    $('#modal_inasistencia').fadeOut(300);
                    $('#modal_v2_peer_tracking').fadeIn(300);

                } else {
                    $('#modal_v2_peer_tracking').fadeOut(300);
                    $('#modal_inasistencia').fadeIn(300);
                }
            });

            $(document).on("click", ".btn-dphpforms-close", function () {
                $(this).closest('div[class="mymodal"]').fadeOut(300);
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
                $.get( "../managers/dphpforms/dphpforms_forms_core.php?form_id=primer_acercamiento&record_id=" + $(this).attr('data-record-id'), function( data ) {
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
});

