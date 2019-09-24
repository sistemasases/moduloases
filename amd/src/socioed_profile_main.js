/**
 * Social-educative tackings
 * @module amd/src/academic_reports
 * @author Jorge Eduardo Mayor Fernández
 * @copyright 2019 Jorge Eduardo Mayor <mayor.jorge@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

    return {

        init: function() {

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

            });

            $('#button_primer_acercamiento').on('click', function() {

                var creado_por = $('#current_user_id').val();

                $('div').removeClass('regla_incumplida');
                $('#modal_primer_acercamiento').fadeIn(300);
                $('.primer_acerca_id_estudiante_field').find('input').val(student_code);
                $('.primer_acerca_id_creado_por_field').find('input').val(creado_por);
            });
        }
    };
});

