/**
 * Profile for every individual ASES monitor
 *
 * @module block_ases/monitor_profile
 * @author David Santiago Cortés
 * @copyright 2020 David S. Cortés <david.cortes@correounivalle.edu.co>
 * @license GNU GPL v3 or later
 */
define(['jquery', 
        'block_ases/select2', 
        'block_ases/bootstrap',
        'block_ases/sweetalert'], function($, select2, bootstrap) {
    
    return {
        init: function (data_init) {
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
            var self = this;
            
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

            this.editProfile(self);
        },
        editProfile: function(object_function) {
            var unchangedForm = $('#ficha_monitor').serializeArray();
            $("#span-icon-edit").on('click', function() {
                $(this).hide();
                $("#span-icon-save").show();
                $("#span-icon-cancel").show();

                $("#input_num_doc").prop('readonly', false);
                $("#input_email").prop('readonly', false);
                $("#input_phone1").prop('readonly', false);
                $("#input_phone2").prop('readonly', false);
                // Documentos
                $("#input_acuerdo").prop('readonly', false);
                $("#link_acuerdo").removeAttr("href");

                $("#input_d10").prop('readonly', false);
                $("#link_d10").removeAttr("href");

                $("#input_doc").prop('readonly', false);
                $("#link_doc").removeAttr("href");

                $("#input_banco").prop('readonly', false);
                $("#link_banco").removeAttr("href");
            });
            
            $('#span-icon-save').on('click', function () {
                var changedForm = $('#ficha_monitor').serializeArray();
                console.log(object_function);
                var resultValidation = object_function.validateForm(changedForm);
            });

            $('#span-icon-cancel').on('click', { form: unchangedForm }, function(data) {
                swal({
                    title: "¿Desea cancelar la edición?",
                    text: "Los cambios no guardados se perderán",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d51b23",
                    confirmButtonText: "Sí",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    allowEscapeKey: false,

                }, function (isConfirm) {
                    if (isConfirm) {
                        object_function.cancelEdition(); 
                        //object_function.revertChanges(data.data.form);
                    }
                });
            });
        }, validateForm: function (form) {
            
            for (field in form) {
                var msg = new Object();
                var field = form[field];

                msg.title = "Éxito";
                msg.msg = "El formulario fue validado con éxito";
                msg.status = "success";

                switch(field.name) {
                    case "email":
                        let regexemail = /((?:[a-z]+\.)*[a-z]+(?:@correounivalle\.edu\.co))/;
                        let validEmail = regexemail.exec(field.value);

                        if (validEmail !== null) {
                            console.log(validEmail)
                        } else {
                            msg.title = "Error";
                            msg.status = "error";
                            msg.msg = `El campo ${field.name} no cumple con el formato institucional.`;
                            return msg;
                        }
                        break;

                    case "num_doc":
                        if (isNaN(field.name) || isNaN(parseFloat(field.name))) {
                            msg.title = "Error";
                            msg.status = "error";
                            msg.msg = `El campo ${field.name} no es válido.`;
                        }
                        break;

                    case "acuerdo_conf":
                    case "doc":
                    case "d10":
                    case "banco":
                        let urlregex = /^(http|https):\/\//;
                        let validUrl = urlregex.exec(field.value);

                        console.log(validUrl);
                }
            }
            return msg;
        }, cancelEdition: function () {
            $('#span-icon-edit').show();
            $('#span-icon-save').hide();
            $('#span-icon-cancel').hide();
            $("#input_num_doc").prop('readonly', true);
            $("#input_email").prop('readonly', true);
            $("#input_phone1").prop('readonly', true);
            $("#input_phone2").prop('readonly', true);
            // Documentos
            $("#input_acuerdo").prop('readonly', true);
            $("#input_d10").prop('readonly', true);
            $("#input_doc").prop('readonly', true);
            $("#input_banco").prop('readonly', true);
        }
    };

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
