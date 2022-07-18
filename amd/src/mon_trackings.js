/**
 * Trackings tab belonging to the monitor's profile.
 *
 * @module block_ases/mon_trackings
 * @author David Santiago Cortés
 * @copyright 2020 David S. Cortés <david.cortes@correounivalle.edu.co>
 * @license GNU GPL v3 or later
 */

define(['jquery', 
        'block_ases/mustache',
        'block_ases/loading_indicator'], function($, mustache, loading_indicator){

    return {
        init: function(dataInit) {
            //$('#select-periods').on('click', function () {
            //    $("#btn-consulta-fichas").removeAttr('hidden');
            //})
            
            var moodleID = dataInit[0];
            var instanceID = dataInit[1];

            $("button[id^='btn-consulta-fichas-']").on('click', function () {
                if ( $(this).hasClass('ases-info')) {
                    $(this).next().remove();
                    $(this).next().slideUp(500);
                    $(this).removeClass('ases-info');
                    $(this).addClass('ases-danger');
                    $(this).html('Consultar');
                } else {
                    const path = event.path || event.composedPath();
                    trackingsCount(moodleID, instanceID, path[2].id);
                    $(this).html('Ocultar');
                    $(this).removeClass('ases-danger');
                    $(this).addClass('ases-info');
                }
            });

            //$( document ).ajaxStart(function() {
            //    loading_indicator.show();
            //}).ajaxStop(function() {
            //    loading_indicator.hide();
            //});
        }
    };

    function trackingsCount(moodleID, instanceID, periodID, appendLocation) {
        loading_indicator.show();
        $.ajax({
            type: "POST",
            dataType: "json",
            data: JSON.stringify({
                "function": "tracking_count",
                "params": [moodleID, instanceID, periodID],
            }),
            url: "../managers/monitor_profile/monitor_profile_api.php",
            cache: "false",
            success: function (msg) {
                var result = msg;
                if (result.status_code === 0) {
                    $.ajax({
                        url: "../templates/monitor_total_count.mustache",
                        data: null,
                        dataType: "text",
                        cache: "false",
                        async: "false",
                        success: function (template) {
                            let divToAppend = $(mustache.render(template, result.data_response));
                            $(`#count-${periodID}`).append(divToAppend).hide();
                            $(`#count-${periodID}`).slideDown(500);
                        },
                        error: function() {
                            console.log('Cannot reach template.');
                        },
                        complete: function() {
                            loading_indicator.hide();
                        }
                    });
                }
                else {
                    console.log('error', msg);
                }
            },
            error: function (msg) {
               console.log('error', msg); 
                swal(
                    "Error",
                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                    "error"
                );
            },

        });

    }
});
