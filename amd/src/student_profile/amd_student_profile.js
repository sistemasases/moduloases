requirejs(['jquery', 'bootstrap', 'sweetalert', 'validator'], function($) {

    $(document).ready(function() {

        var modal_peer_tracking = $('#modal_peer_tracking');

        edit_profile_act();
        go_back();
        manage_icetex_status();
        manage_ases_status();
        modal_manage();
        modal_peer_tracking_manage();

        $('#save').click(function() {
            save_profile();
        });

        $("#cancel").click(function() {

            swal({
                    title: "¿Estas seguro/a de cancelar?",
                    text: "Los cambios realizados no serán tomados en cuenta y se perderán",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d51b23",
                    confirmButtonText: "Si!",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                },
                function(isConfirm) {
                    if (isConfirm) {
                        cancel_edition();
                    }
                });
        });

        // Controles para el modal: "Añadir nuevo seguimiento"

        // Despliega el modal de seguimiento
        $('#button_add_track').on('click', function() {
            $('#save_seg').removeClass("hide");
            $('#div_created').addClass('hide');
            $('#upd_seg').addClass('hide');
            $('#myModalLabel').attr('name', 'PARES');

            modal_peer_tracking.show();

            init_form_tracking();
        });
        // Guardar segumiento
        $('#save_tracking_btn').on('click', function() {
            save_tracking_peer();
        });

        // Controles sobre limpiar funcionario
        $('#clean_individual_risk').on('click', function(){
            $('#no_value_individual').prop('checked', true);
        });
        $('#clean_familiar_risk').on('click', function(){
            $('#no_value_familiar').prop('checked', true); 
        });
        $('#clean_academic_risk').on('click', function(){
            $('#no_value_academic').prop('checked', true);
        });
        $('#clean_economic_risk').on('click', function(){
            $('#no_value_economic').prop('checked', true);
        });
        $('#clean_life_risk').on('click', function(){
            $('#no_value_life').prop('checked', true); 
        });
    });

    function edit_profile_act() {
        $("#editar_ficha").click(function() {
            $("#ficha_estudiante").find("input, textarea").prop("readonly", false);
            $("#profesional_ps").prop("readonly", true);
            $("#practicante_ps").prop("readonly", true);
            $("#monitor_ps").prop("readonly", true);
            $("#ficha_estudiante").find("select").prop("disabled", false);
            $(this).hide();
            $("#ficha_estudiante #cancel").fadeIn();
            $("#ficha_estudiante #save").fadeIn();
            $('#ficha_estudiante #codigo').attr('readonly', true);
            $('#ficha_estudiante #search').fadeOut();
        });
    }

    function cancel_edition() {

        $("#ficha_estudiante").find("input, textarea").prop("readonly", true);
        $("#ficha_estudiante").find("select").prop("disabled", true);
        $(this).hide();
        $("#ficha_estudiante #save").fadeOut();
        $('#ficha_estudiante #cancel').fadeOut();
        $("#ficha_estudiante #editar_ficha").fadeIn();
        $('#ficha_estudiante #codigo').attr('readonly', false);
        $('#ficha_estudiante #search').fadeIn();
    }

    function go_back() {
        $("#go_back").on('click', function() {
            var page = 'ases_report.php';
            var search = location.search.split('&');
            location.href = page + search[0] + '&' + search[1];
        });
    }

    function save_profile() {
        var form = $('#ficha_estudiante').serializeArray();
        if (validateFormProfile(form)) {
            var form_json = JSON.stringify(form);
            $.ajax({
                type: "POST",
                data: {
                    func: 'save_profile',
                    form: form
                },
                url: "../managers/student_profile/studentprofile_serverproc.php",
                success: function(msg) {

                    swal(
                        msg.title,
                        msg.msg,
                        msg.status
                    );
                },
                dataType: "json",
                cache: "false",
                error: function(msg) {
                    swal(
                        msg.title,
                        msg.msg,
                        msg.status
                    );
                },
            });
        }

        cancel_edition();
    }

    function validateFormProfile(form) {
        if (has_letters(form[4].value)) {
            swal('Error',
                'El campo "Documento" solo debe contener números',
                'error');
            return 0;
        }
        else if (form[4].value == "") {
            swal('Error',
                'El campo "Documento" no puede estar vacio',
                'error');
            return 0;
        }
        else if (form[6].value == "") {
            swal('Error',
                'El campo "Documento" no puede estar vacio',
                'error');
            return 0;
        }
        else if (has_letters(form[7].value)) {
            swal('Error',
                'El campo "Teléfono 1" solo debe contener números',
                'error');
            return 0;
        }
        else if (has_letters(form[8].value)) {
            swal('Error',
                'El campo "Teléfono 2" solo debe contener números',
                'error');
            return 0;
        }
        else if (has_letters(form[9].value)) {
            swal('Error',
                'El campo "Teléfono 3" solo debe contener números',
                'error');
            return 0;
        }
        else if (form[10].value == "") {
            swal('Error',
                'El campo "Email alternativo" no puede estar vacio',
                'error');
            return 0;
        }
        else if (is_email(form[10].value)) {
            swal('Error',
                'El formato del correo alternativo es incorrecto. \n El formato correcto debería ser ejemplo@ejemplo.com',
                'error');
            return 0;
        }
        else if (form[11].value == "") {
            swal('Error',
                'El campo "Nombre acudiente" no puede estar vacio',
                'error');
            return 0;
        }
        else if (has_letters(form[12].value)) {
            swal('Error',
                'El campo "Teléfono acudiente" solo debe contener números',
                'error');
            return 0;
        }
        else {
            return 1;
        }
    }

    function has_letters(str) {
        var letters = "abcdefghyjklmnñopqrstuvwxyz";
        str = str.toLowerCase();
        for (i = 0; i < str.length; i++) {
            if (letters.indexOf(str.charAt(i), 0) != -1) {
                return 1;
            }
        }
        return 0;
    }

    function has_numbers(str) {
        var numbers = "0123456789";
        for (i = 0; i < str.length; i++) {
            if (numbers.indexOf(str.charAt(i), 0) != -1) {
                return 1;
            }
        }
        return 0;
    }

    function is_email(str) {
        var email_regex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
        if (email_regex.test(str)) {
            return 0;
        }
        else {
            return 1;
        }
    }

    function manage_icetex_status() {
        //validar cambio en estado
        var previous;
        $('#estado').on('focus', function() {
            // se guarda el valor previo con focus
            previous = this.value;
        }).change(function() {
            var newstatus = $(this).val();
        });
    }

    function manage_ases_status() {
        //validar cambio en estado
        var previous;
        $('#estadoAses').on('focus', function() {
            // se guarda el valor previo con focus
            previous = this.value;
        }).change(function() {
            var newstatus = $(this).val();

            if (newstatus == "RETIRADO") {
                $('#modal_dropout').show();
            }
            else if (newstatus == "APLAZADO") {
                $('#modal_dropout').show();
            }
            else {
                swal({
                        title: "¿Está seguro/a de realizar este cambio?",
                        text: "El estado del estudiante pasará de " + previous + " a " + newstatus,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d51b23",
                        confirmButtonText: "Yes",
                        closeOnConfirm: true,
                        allowEscapeKey: false
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            var id_ases = $('#id_ases').val();

                            var result_status = save_ases_status(newstatus, id_ases);

                            swal(
                                result_status.title,
                                result_status.msg,
                                resul_status.status);
                        }
                        else {
                            $('#estado').val(previous);
                        }
                    });
            }
        });
    }

    function save_icetex_status(new_status, id_ases) {
        var data = new Array();

        data.push({
            name: "func",
            value: "save_icetex_status"
        });
        data.push({
            name: 'new_status',
            value: new_status
        });
        data.push({
            name: 'id_ases',
            value: id_ases
        });

        $.ajax({
            type: "POST",
            data: data,
            url: "../../ases/managers/student_profile/studentprofile_serverproc.php",
            success: function(msg) {
                return msg;
            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
                swal(
                    'Error',
                    'No se puede contactar con el servidor.',
                    'error'
                );
            },
        });
    }

    function save_ases_status(new_status, id_ases) {
        var data = new Array();
        var id_ases = $('#id_ases').val();

        data.push({
            name: "func",
            value: "save_ases_status"
        });
        data.push({
            name: 'new_status',
            value: new_status
        });
        data.push({
            name: 'id_ases',
            value: id_ases
        });

        $.ajax({
            type: "POST",
            data: data,
            url: "../../ases/managers/student_profile/studentprofile_serverproc.php",
            success: function(msg) {
                return msg;
            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
                swal(
                    'Error',
                    'El estado no fue actualizado. Error al contactarse con el servidor.',
                    'error');
            },
        });
    }

    //Functions for modal's management

    function modal_manage() {

        // Get the modal
        var modal = $('#modal_dropout');

        // Get the <span> element that closes the modal
        var span_close = $('.mymodal-close');
        var goback_backdrop = $('#goback_backdrop');

        goback_backdrop.on('click', function() {
            modal.hide();
        });

        // When the user clicks on <span> (x), close the modal
        span_close.on('click', function() {
            modal.hide();
        });
    }

    function modal_peer_tracking_manage() {
        // Get the modal
        var modal_peer_tracking = $('#modal_peer_tracking');

        // Get the <span> element that closes the modal
        var span_close = $('.mymodal-close');
        var cancel_button = $('#cancel_peer_tracking');

        cancel_button.on('click', function() {
            modal_peer_tracking.hide();
        });

        // When the user clicks on <span> (x), close the modal
        span_close.on('click', function() {
            modal_peer_tracking.hide();
        });
    }

    function init_form_tracking() {

        var current_date = new Date();
        var current_day = current_date.getDate();
        var current_month = current_date.getMonth() + 1;
        var current_year = current_date.getFullYear();
        var current_min = current_date.getMinutes();
        var current_hour = current_date.getHours();

        //incializar hora
        var hour = "";
        for (var i = 0; i < 24; i++) {
            if (i == current_hour) {
                if (current_hour < 10) {
                    current_hour = "0" + current_hour;
                }
                hour += "<option value=\"" + current_hour + "\" selected>" + current_hour + "</option>";
            }
            else if (i < 10) {
                hour += "<option value=\"0" + i + "\">0" + i + "</option>";
            }
            else {
                hour += "<option value=\"" + i + "\">" + i + "</option>";
            }
        }

        var min = "";
        for (var i = 0; i < 60; i++) {
            if (i == current_min) {
                if (current_min < 10) {
                    current_min = "0" + current_min;
                }
                min += "<option value=\"" + current_min + "\" selected>" + current_min + "</option>";
            }
            else if (i < 10) {
                min += "<option value=\"0" + i + "\">0" + i + "</option>";
            }
            else {
                min += "<option value=\"" + i + "\">" + i + "</option>";
            }
        }

        $('#h_ini').append(hour);
        $('#m_ini').append(min);
        $('#h_fin').append(hour);
        $('#m_fin').append(min);
        $("#infomonitor").addClass('hide');
        $("#seguimiento").find("input:text, textarea").val('');
        $("#seguimiento").find("input:radio,input:checkbox").prop('checked', false);
        $('#upd_seg').attr('disabled', false);
        $('#upd_seg').attr('title', '');
        $('#seguimiento').find('select, textarea, input').attr('disabled', false);
        $('#no_value_individual').hide();
        $('#no_value_familiar').hide();
        $('#no_value_academic').hide();
        $('#no_value_economic').hide();
        $('#no_value_life').hide();
    }

    function save_tracking_peer() {
        var form = $('#tracking_peer_form');
        var data = form.serializeArray();
        var url_parameters = get_url_parameters(document.location.search);

        var result_validation = validate_tracking_peer_form(data);

        if (result_validation != "success") {
            swal({
                title: 'Advertencia',
                text: result_validation,
                type: 'warning',
                html: true
            });
        }else{
            data.push({
                name: "func",
                value: "save_tracking_peer"
            });

            var id_ases = $('#id_ases').val();

            data.push({
                name: "id_ases",
                value: id_ases
            });
            data.push({
                name: "id_instance",
                value: url_parameters.instanceid
            });

            $.ajax({
                type: "POST",
                data: data,
                url: "../../ases/managers/student_profile/studentprofile_serverproc.php",
                success: function(msg) {
                    swal(
                        msg.title,
                        msg.msg,
                        msg.type
                    );
                    modal_peer_tracking.hide();
                },
                dataType: "json",
                cache: "false",
                error: function(msg) {
                    console.log(msg);
                },
            });
        }
    }

    function validate_tracking_peer_form(form) {

        // Validación de los datos generales
        if (form[0].value == "") {
            return "Debe introducir la fecha en la cual se realizó el seguimiento";
        }
        else if (form[1].value == "") {
            return "Debe introducir el lugar donde se realizó el seguimiento";
        }
        else if (form[6].value == "") {
            return "El campo tema se encuentra vacio";
        }
        else if (form[7].value == "") {
            return "El campo objetivos se encuentra vacio";
        }

        //Validación de la hora
        else if (parseInt(form[4].value) < parseInt(form[2].value)) {
            return "La hora de finalización debe ser mayor a la hora inicial";
        }
        else if ((parseInt(form[2].value) == parseInt(form[4].value)) && (parseInt(form[5].value) <= parseInt(form[3].value))) {
            return "La hora de finalización debe ser mayor a la hora inicial";
        }

        // Validación actividades

        // Individual campo
        else if (form[8].value != "" && form[9].value == 0) {
            return "El riesgo asociado al campo Actividad Invidual no está marcado. Si usted escribió información en el campo Actividad Individual debe marcar un nivel de riesgo.";
        }
        // Familiar campo
        else if (form[10].value != "" && form[11].value == 0) {
            return "El riesgo asociado al campo Actividad Familiar no está marcado. Si usted escribió información en el campo Actividad Familiar debe marcar un nivel de riesgo.";
        }
        // Académico campo
        else if(form[12].value != "" && form[13].value == 0){
            return "El riesgo asociado al campo Actividad Académico no está marcado. Si usted escribió información en el campo Actividad Académico debe marcar un nivel de riesgo.";
        }
        // Económico campo
        else if(form[14].value != "" && form[15].value == 0){
            return "El riesgo asociado al campo Actividad Económico no está marcado. Si usted escribió información en el campo Actividad Económico debe marcar un nivel de riesgo.";
        }
        // Vida universitaria y ciudad campo
        else if(form[16].value != "" && form[17].value == 0){
            return "El riesgo asociado al campo Actividad Vida Universitaria no está marcado. Si usted escribió información en el campo Actividad Vida Universitaria debe marcar un nivel de riesgo.";
        }
        // Individual riesgo
        else if(form[8].value == "" && form[9].value != 0){
            return "Usted ha marcado el nivel de riesgo asociado a la actividad Individual, debe digitar información en el campo Actividad Individual. O puede utilizar el icono (<span style='color:gray;' class='glyphicon glyphicon-erase'></span>) de limpiar riesgo.";
        }
        // Familiar riesgo
        else if(form[10].value == "" && form[11].value != 0){
            return "Usted ha marcado el nivel de riesgo asociado a la actividad Familiar, debe digitar información en el campo Actividad Familiar. O puede utilizar el icono (<span style='color:gray;' class='glyphicon glyphicon-erase'></span>) de limpiar riesgo.";
        }
        // Académico riesgo
        else if(form[12].value == "" && form[13].value != 0){
            return "Usted ha marcado el nivel de riesgo asociado a la actividad Académico, debe digitar información en el campo Actividad Académico. O puede utilizar el icono (<span style='color:gray;' class='glyphicon glyphicon-erase'></span>) de limpiar riesgo.";
        }
        // Económico riesgo
        else if(form[14].value == "" && form[15].value != 0){
            return "Usted ha marcado el nivel de riesgo asociado a la actividad Económico, debe digitar información en el campo Actividad Económico. O puede utilizar el icono (<span style='color:gray;' class='glyphicon glyphicon-erase'></span>) de limpiar riesgo.";
        }
        // Vida universitaria y ciudad riesgo
        else if(form[16].value == "" && form[17].value != 0){
            return "Usted ha marcado el nivel de riesgo asociado a la actividad Vida Universitaria, debe digitar información en el campo Actividad Vida Universitaria. O puede utilizar el icono (<span style='color:gray;' class='glyphicon glyphicon-erase'></span>) de limpiar riesgo.";
        }
        // Éxito
        else {
            return "success";
        }
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
