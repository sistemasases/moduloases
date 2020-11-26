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
        'block_ases/sweetalert',
        'block_ases/mustache',
        'block_ases/mon_trackings',
        'block_ases/aaspect',], function($, select2, bootstrap, sweetalert, mustache, mon_trackings, aaspect) {
    
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
            var parameters = get_url_parameters(document.location.search);
            var monitorId = $("#id_moodle")[0].value;

            $("#boss_history_li").one('click', {tab_name: 'history_boss'}, load_tabs);
            $("#trackings_li").one('click', mon_trackings.init([monitorId, parameters.instanceid]));
            
            $('[data-toggle="tooltip"]').tooltip();

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
                $("#link_banco").attr("href", "");
            });
            
            $('#span-icon-save').on('click', function () {
                var changedForm = $('#ficha_monitor').serializeArray();
                var resultValidation = object_function.validateForm(changedForm);

                if (resultValidation.status == "error") {
                    swal(resultValidation.title,
                        resultValidation.msg,
                        resultValidation.status);
                } else {

                    let formOnlyWithChanges = [];
                    changedForm.slice(1,11).forEach((field, i) => {
                        if (field.value != unchangedForm.slice(1,11)[i].value) {
                            formOnlyWithChanges.push(field);
                        }
                    });

                    formOnlyWithChanges.push(changedForm[0]);
                    object_function.saveForm(formOnlyWithChanges, object_function, resultValidation);
                }

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
                        object_function.revertChanges(data.data.form);
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
                        let regexemail = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
                        let validEmail = regexemail.exec(field.value);

                        if (validEmail !== null) {
                            console.log(validEmail)
                        } else {
                            msg.title = "Error";
                            msg.status = "error";
                            msg.msg = `El campo ${field.name} no tiene formato válido.`;
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

                    case "pdf_acuerdo_conf":
                    case "pdf_doc":
                    case "pdf_d10":
                    case "pdf_cuenta_banco":
                        let urlregex = /^(http|https):\/\//;
                        let validUrl = urlregex.exec(field.value);

                        if (validUrl == null) {
                            msg.title = "Error";
                            msg.status = "error";
                            msg.msg = `El campo ${field.name} no es un enlace válido. Recuerde incluir https://.`
                        }
                        break;
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
        
        }, revertChanges: function (form) {

            form.forEach(field => $('[name='+field.name+']').val(field.value));
            
            $("#link_acuerdo").attr("href", $("#input_acuerdo")[0].value);
            $("#link_banco").attr("href", $('#input_banco')[0].value);
            $("#link_d10").attr("href", $("#input_d10")[0].value);
            $('#link_doc').attr('href', $('#input_doc')[0].value);

            location.reload(true);
        }, saveForm: function (form, object_function, resultMsg) {
            $('#span-icon-cancel').hide(); 
            $.ajax({
                type: "POST",
                data: JSON.stringify({
                    "function": 'save_profile',
                    "params": [form]
                }),
                url: '../managers/monitor_profile/monitor_profile_api.php',
                dataType: "json",
                cache: "false",
                error: function (msg) {
                    swal(
                        msg.title,
                        msg.msg,
                        msg.type
                    );
                },
                success: function (msg) {
                    swal(
                        resultMsg.title,
                        msg.message,
                        "success"
                    );
                },
            });
            object_function.cancelEdition();
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

                if (result.status_code === 0) {
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
        var tabName = event.data.tab_name;
        var parameters = get_url_parameters(document.location.search);
        var monitorId = $("#id_moodle")[0].value;

	$.ajax({
            type: "POST",
            url: "../managers/monitor_profile/monitor_profile_api.php",
            dataType: "json",
            cache: "false",
            data: JSON.stringify({
                "function": 'load_tabs',
                "params": [monitorId, parameters.instanceid, tabName],
            }),
	        success: function(msg) {
                if (msg.status_code == 0) {
                    $.ajax({
                        url: "../templates/monitor_view_"+tabName+"_tab.mustache",
                        data: null,
                        dataType: "text",
                        success: function(template) {
                            let tabToLoad = $(mustache.render(template, msg.data_response));
                            $(".ases-tab-content").append(tabToLoad);
                            switch (tabName) {
                                case "history_boss":
                                    $("#general_tab").removeClass("ases-tab-active");
                                    $("#"+tabName+"_tab").addClass("ases-tab-active");
                                    break;
                            }
                        },
                        error: function() {
                            console.log(`../templates/monitor_view_${tabName}_tab.mustache cannot be reached.`);
                        }
                    });
                } else {
                    console.log('error:', msg);
                }
    
            },
            error: function(msg) {
                console.log(msg)
            }
        });
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
