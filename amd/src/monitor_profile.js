/**
 * Profile for every individual ASES monitor
 *
 * @module amd/src/monitor_profile
 * @author David Santiago Cortés
 * @copyright 2020 David S. Cortés <david.cortes@correounivalle.edu.co>
 * @license GNU GPL v3 or later
 */
define(['jquery', 
        'block_ases/select2', 
        'block_ases/bootstrap',], function($, select2, bootstrap) {
    
    return {
        init: function () {
            $('#select-monitores').select2({
                language: {
                    noResults: function() {
                        return "No hay resultado";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                },
            });
            
            $("#select-monitores").on('change', function () {
                var code = $('#select-monitores').val();
                var monitorCode = code.split(' ')[0];

                loadMonitor(monitorCode);
            })

            // Load trackings tab on click.
            $("#trackings_li").one('click', {tab_name: 'trackings'}, load_tabs);
            
            $('[data-toggle="tooltip"]').tooltip({
                container : 'body'
            });

            $("#span-icon-edit").on('click', function() {
                console.log("hola");
                $(this).hide();
                $("#span-icon-save").show();
                $("#span-icon-cancel").show();
                $("#input_num_doc").prop('readonly', false);
                $("#input_email").prop('readonly', false);
                $("#input_phone1").prop('readonly', false);
                $("#input_phone2").prop('readonly', false);

                // Links
                $("#input_span_acuerdo").attr('contentEditable', true);
                $("#input_span_d10").attr('contentEditable', true);
                $("#input_span_doc").attr('contentEditable', true);
                $("#input_span_banco").attr('contentEditable', true);

            });
            
        }
    }

    // Loads monitor page
    function loadMonitor(monitorCode) {

        $.ajax({
            type: "POST",
            data: JSON.stringify({
                "function": "is_monitor_ps",
                "params": [monitorCode],
            }),
            url: "../managers/monitor_profile/monitor_profile_api.php",
            dataType: "json",
            cache: "false",
            success: function (msg) {
                console.log(msg);
                var result = msg;

                if (result.status_code === 1) {
                    var parameters = get_url_parameters(document.location.search);
                    var fullUrl = String(document.location);
                    var url = fullUrl.split("?");

                    var newUrl = url[0] + "?courseid=" + parameters['courseid'] + "&instanceid=" + parameters['instanceid'] + "&monitor_code=" + monitorCode;
                    location.href = newUrl;
                } else {
                    swal(
                        "Error",
                        "No se encuentra un monitor ASES asociado al código ingresado",
                        "error"
                    );
                }
            },
            error: function (msg) {
                console.log(msg);
                swal(
                    "Error",
                    "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                    "error"
                );
            },
        });
    }

    //Load a single tab.
    function load_tabs(event) {
        console.log("hola mundo");
        loading_indicator.show(); 
    }


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
