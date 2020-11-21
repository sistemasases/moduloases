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
            $('#select-periods').on('change', function () {
                $("#btn-consulta-fichas").removeAttr('hidden');
            })
            
            var moodleID = dataInit[0];
            var instanceID = dataInit[1];

            $("#btn-consulta-fichas").one('click', function () {
                let periodID = $("#select-periods").val();
                console.log(periodID);
                loading_indicator.show();
                trackingsCount(moodleID, instanceID, periodID);
                loading_indicator.hide();
            });
        }
    };

    function trackingsCount(moodleID, instanceID, periodID) {

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
                            $("#ases-container-tracking").append(divToAppend);
                        },
                        error: function() {
                            console.log('Cannot reach template.');
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
