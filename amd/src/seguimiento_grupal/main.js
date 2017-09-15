requirejs(['jquery', 'validator', 'bootstrap', 'sweetalert', 'checkrole', 'amd_ficha'], function($) {

    $(document).ready(function() {
        load_students();
        loadAll_seg();

        $('#socioedu_add_grupal').click(function() {
            $('#save_seg').removeClass("hide");
            $('#div_created').addClass('hide');
            $('#upd_seg').addClass('hide');
            $('#myModalLabel').attr('name', 'GRUPAL');
            initFormSeg();
            load_attendance_list();
        });

        $('#close_seg').on('click', function() {
            $('#edit_seg').addClass('hide');
        });

        $('#go_back').on('click', function() {
            window.history.back();
        });

        $('#edit_seg').click(function() {
            var students_id = new Array();
            var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
            var id_seg = $(this).prop('name');

            $('#seguimiento input[id=asistencia]:checked').each(
                function() {
                    var id = $(this).val();
                    students_id.push(id);
                }
            );
            $('#myModalLabel').attr('name', 'GRUPAL');
            var tipo = $('#myModalLabel').attr('name');
            var data = $('#seguimiento').serializeArray();
            data.push({
                name: "function",
                value: "update"
            });
            data.push({
                name: "tipo",
                value: tipo
            });
            data.push({
                name: "id_seg",
                value: id_seg
            });
            data.push({
                name: "idtalentos",
                value: students_id
            });
            data.push({
                name: "idinstancia",
                value: parameters.instanceid
            });
            data.push({
                name: "idmonitor",
                value: 120
            });


            var validation = validateModal(data);
            if (validation.isvalid) {
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/seguimiento.php",
                    success: function(msg) {
                        var error = msg.error;
                        if (!error) {
                            swal({
                                title: "Actualizado con exito!!!",
                                html: true,
                                type: "success",
                                text: msg.msg,
                                confirmButtonColor: "#d51b23"
                            });
                            $('#myModal').modal('toggle');
                            $('#myModal').modal('toggle');
                            $('#save_seg').addClass('hide');
                            $('.modal-backdrop').remove();
                            loadAll_seg();
                        }
                        else {
                            swal({
                                title: error,
                                html: true,
                                type: "error",
                                text: msg.msg,
                                confirmButtonColor: "#D3D3D3"
                            });
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("error al actualizar seguimiento");
                    },
                });

            }
            else {
                swal({
                    title: "Error",
                    html: true,
                    type: "warning",
                    text: "Detalles del error:<br>" + validation.detalle,
                    confirmButtonColor: "#D3D3D3"
                });
            }
        });

        $('#save_seg').click(function() {
            var students_id = new Array();
            var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

            $('#seguimiento input[id=asistencia]:checked').each(
                function() {
                    var id = $(this).val();
                    students_id.push(id);
                }
            );

            var tipo = $('#myModalLabel').attr('name');
            var data = $('#seguimiento').serializeArray();
            data.push({
                name: "function",
                value: "new"
            });
            data.push({
                name: "tipo",
                value: tipo
            });
            data.push({
                name: "idtalentos",
                value: students_id
            });
            data.push({
                name: "idinstancia",
                value: parameters.instanceid
            });
            data.push({
                name: "idmonitor",
                value: 120
            });

            var validation = validateModal(data);
            if (validation.isvalid) {
                $.ajax({
                    type: "POST",
                    data: data,
                    url: "../managers/user_management/seguimiento.php",
                    success: function(msg) {
                        var error = msg.error;
                        if (!error) {
                            swal({
                                title: "Actualizado con exito!!",
                                html: true,
                                type: "success",
                                text: msg.msg,
                                confirmButtonColor: "#d51b23"
                            });
                            $('#myModal').modal('toggle');
                            $('#myModal').modal('toggle');
                            $('#save_seg').addClass('hide');
                            $('.modal-backdrop').remove();
                            loadAll_seg();
                        }
                        else {
                            swal({
                                title: error,
                                html: true,
                                type: "error",
                                text: msg.msg,
                                confirmButtonColor: "#D3D3D3"
                            });
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("error al guardar seguimiento");
                    },
                });

            }
            else {
                swal({
                    title: "Error",
                    html: true,
                    type: "warning",
                    text: "Detalles del error:<br>" + validation.detalle,
                    confirmButtonColor: "#D3D3D3"
                });
            }
        });

        $('#upd_seg').click(function() {
            var id_seg = $(this).attr('name');
            $("#seguimiento :input").prop("disabled", false);
            var estudiantes = obtener_estudiantes();
            load_attendance_list(estudiantes, true);
            $('#edit_seg').removeClass('hide');
            $('#upd_seg').addClass("hide");
        });
    });

    function obtener_estudiantes() {
        var estudiantes = [];
        $('#mytable_consult > tbody  > tr > td').each(function() {
            if ($(this).attr('id') == "talentos") {
                estudiantes.push($(this).html());
            }
        });
        return estudiantes;
    }

    function getUrlParams(page) {
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


    function load_students() {
        var data = new Array();
        var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
        data.push({
            name: "function",
            value: "load_grupal"
        });
        data.push({
            name: "idinstancia",
            value: parameters.instanceid
        });

        $.ajax({
            type: "POST",
            data: data,
            url: "../managers/user_management/seguimiento.php",
            success: function(msg) {
                $('#mytable tbody').html('');
                if (msg.rows != 0) {

                    var content = msg.content;
                    for (x in content) {

                        $('#mytable tbody').append("<tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td class=\"hide\">" + content[x].idtalentos + "</td> </tr>");
                    }

                }
                else {
                    $('#list_grupal_seg').append("<a>No registra ningun estudiante a su cargo.Por favor dirigete a la oficina de Sistemas de talentos pilos para gestionar tu situación</a>");
                }
            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
                alert("error al cargar estudiantes");
            },
        });
    }


    function load_attendance_list(list = null, editable = null) {
        var data = new Array();
        var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

        data.push({
            name: "function",
            value: "load_grupal"
        });
        data.push({
            name: "idinstancia",
            value: parameters.instanceid
        });

        $.ajax({
            type: "POST",
            data: data,
            url: "../managers/user_management/seguimiento.php",
            success: function(msg) {
                $('#seguimiento #mytable_consult tbody').html('');

                if (msg.rows != 0) {

                    var content = msg.content;
                    if (!list) {
                        for (x in content) {
                            $('#seguimiento #mytable_consult tbody').append("<tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td><input type=\"checkbox\" id=\"asistencia\" name=\"asistencia\" value=\"" + content[x].idtalentos + "\"/></td>  </tr>");
                        }
                    }
                    else {
                        for (x in content) {

                            if (list.indexOf(x) != -1) {
                                $('#seguimiento #mytable_consult tbody').append(" <tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td><input type=\"checkbox\" checked=\"checked\" id=\"asistencia\" name=\"asistencia\"  value=\"" + content[x].idtalentos + "\"/></td>  </tr>");
                            }
                            else {
                                $('#seguimiento #mytable_consult tbody').append("<tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td><input type=\"checkbox\" id=\"asistencia\" name=\"asistencia\" value=\"" + content[x].idtalentos + "\"/></td>  </tr>");
                            }
                        }
                    }
                }
                else {
                    $('#seguimiento #list_grupal_seg').append("<a>No registra ningun estudiante a su cargo.Por favor dirigete a la oficina de Sistemas de talentos pilos para gestionar tu situación</a>");
                }
                var id_seg = $('#upd_seg').prop('name');
                $("#edit_seg").prop('name', id_seg);

            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
                alert("error al cargar listado de asistencia");
            },
        });
    }

    function update_seg_grupal(id_seg) {

        var students_id = new Array();

        $('#seguimiento input[id=asistencia]:checked').each(
            function() {
                var id = $(this).val();
                students_id.push(id);
            }
        );

        var data = $('#myModal #seguimiento').serializeArray();

        data.push({
            name: "id_seg",
            value: id_seg
        });
        data.push({
            name: "function",
            value: "update"
        });
        data.push({
            name: "tipo",
            value: "GRUPAL"
        });
        data.push({
            name: "idtalentos",
            value: students_id
        });


        $.each(data, function(i, item) {
            if (item.name == "optradio") {
                item.value = $('#seguimiento input[name=optradio]:checked').parent().attr('id');
            }
        });

        // var  result = "";
        // $.each(data, function(i, item) {
        //     result += item.name+" = "+item.value+"\n";
        // });
        // alert(result);


        $.ajax({
            type: "POST",
            data: data,
            url: "../managers/user_management/seguimiento.php",
            success: function(msg) {
                var error = msg.error;
                if (!error) {
                    swal({
                        title: "Actualizado con exito!!",
                        html: true,
                        type: "success",
                        text: msg.msg,
                        confirmButtonColor: "#d51b23"
                    });
                    $('#myModal').modal('toggle');
                    $('#myModal').modal('toggle');
                    $('#upd_seg').addClass('hide');
                    $('.modal-backdrop').remove();
                    loadAll_seg();
                }
                else {
                    swal({
                        title: error,
                        html: true,
                        type: "error",
                        text: msg.msg,
                        confirmButtonColor: "#D3D3D3"
                    });
                }


            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
                alert(msg);
            },
        });
    }

    function initFormSeg() {

        var date = new Date();
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        var minutes = date.getMinutes();
        var hour = date.getHours();

        //   // inicializar fecha

        //incializar hora
        var hora = "";
        for (var i = 0; i < 24; i++) {
            if (i == hour) {
                if (hour < 10) hour = "0" + hour;
                hora += "<option value=\"" + hour + "\" selected>" + hour + "</option>";
            }
            else if (i < 10) {
                hora += "<option value=\"0" + i + "\">0" + i + "</option>";
            }
            else {
                hora += "<option value=\"" + i + "\">" + i + "</option>";
            }
        }

        var min = "";
        for (var i = 0; i < 60; i++) {

            if (i == minutes) {
                if (minutes < 10) minutes = "0" + minutes;
                min += "<option value=\"" + minutes + "\" selected>" + minutes + "</option>";
            }
            else if (i < 10) {
                min += "<option value=\"0" + i + "\">0" + i + "</option>";
            }
            else {
                min += "<option value=\"" + i + "\">" + i + "</option>";
            }
        }


        $('#seguimiento #h_ini').append(hora);
        $('#seguimiento #m_ini').append(min);

        $('#seguimiento #h_fin').append(hora);
        $('#seguimiento #m_fin').append(min);

        $("#seguimiento").find("input:text, textarea").val('');
        $('#seguimiento #infomonitor').addClass('hide');
        $('#upd_seg').attr('disabled', false);
        $('#upd_seg').attr('title', '');
        $('#seguimiento').find('select, textarea, input').attr('disabled', false);

    }

    function loadAll_seg() {
        $('#list_grupal').html('');
        var data = new Array();
        var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js

        data.push({
            name: "function",
            value: "loadSegMonitor"
        });
        data.push({
            name: "tipo",
            value: "GRUPAL"
        });
        data.push({
            name: "idinstancia",
            value: parameters.instanceid
        });
        $.ajax({
            type: "POST",
            data: data,
            url: "../managers/user_management/seguimiento.php",
            success: function(msg) {
                var error = msg.error;
                if (!error) {

                    var result = msg.result;
                    var rows = msg.rows;
                    if (rows > 0) {
                        for (x in result) {

                            $('#list_grupal').append('<div class="container well col-md-12"> <div class="container-fluid col-md-10" name="info"><div class="row"><label class="col-md-3" for="fecha_des">Fecha</label><label class="col-md-9" for="tema_des">Tema</label> </div> <div class="row"> <input type="text" class="col-md-3" value=' + result[x].fecha + ' id="fecha_seg" name="fecha_seg" disabled> <input type="text" class="col-md-9" value=' + result[x].tema + ' id="tema_seg" name="tema_seg" disabled> </div></div> <div id=' + result[x].id + ' class="col-md-2" name="div_button_seg"> <span class="btn btn-danger" id="consult_grupal" name="consult_grupal" data-toggle="modal" data-target="#myModal">Detalle</span><span class="btn btn-warning" id="delete_grupal" name="delete_grupal">Borrar</span> </div></div>');
                        }
                        $('#list_grupal').on('click', '#consult_grupal', function() {
                            var id_seg = $(this).parent().attr('id');
                            $('#upd_seg').removeClass('hide');
                            $("#upd_seg").prop('name', id_seg);

                            get_seguimiento(id_seg, 'GRUPAL');
                        });

                        $('#list_grupal').on('click', '#delete_grupal', function() {
                            var id_seg = $(this).parent().attr('id');
                            delete_seguimiento(id_seg);

                        });



                    }
                    else {
                        $('#list_grupal').append("<label>No registra</label><br>");
                    }

                }
                else {
                    swal({
                        title: error,
                        html: true,
                        type: "error",
                        text: msg.msg,
                        confirmButtonColor: "#D3D3D3"
                    });
                }
            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
                alert("error al cargar seguimiento monitor");
            },
        });
    }


    function delete_seguimiento(id) {
        swal({
                title: "¿Seguro que desea eliminar el registro?",
                text: "No podrás deshacer este paso",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "No",
                confirmButtonColor: "#d51b23",
                confirmButtonText: "Si",
                closeOnConfirm: false
            },


            function() {

                $.ajax({
                    type: "POST",
                    data: {
                        id: id,
                        "function": "delete",
                    },
                    url: "../../../blocks/ases/managers/user_management/seguimiento.php",
                    async: false,
                    success: function(msg) {
                        if (msg == 0) {
                            swal({
                                title: "error al borrar registro",
                                html: true,
                                type: "error",
                                confirmButtonColor: "#d51b23"
                            });
                        }
                        else {

                            setTimeout('document.location.reload()', 500);

                            swal("¡Hecho!",
                                "El registro ha sido eliminado",
                                "success");
                            //

                        }
                    },
                    dataType: "text",
                    cache: "false",
                    error: function(msg) {alert("error al eliminar seguimiento")},
                });
            });
    }

    function get_seguimiento(id_seg, tipo, instancia) {
        var data = new Array();
        var parameters = getUrlParams(document.location.search); //metodo definido en chekrole.js
        $('#save_seg').addClass("hide");


        initFormSeg();

        data.push({
            name: "function",
            value: "getSeguimiento"
        });
        data.push({
            name: "id",
            value: id_seg
        });
        data.push({
            name: "idinstancia",
            value: parameters.instanceid
        });
        data.push({
            name: "tipo",
            value: tipo
        });
        $.ajax({
            type: "POST",
            data: data,
            url: "../managers/user_management/seguimiento.php",
            success: function(msg) {
                var error = msg.error;
                if (!error) {
                    $("#place").val(msg.seguimiento.lugar);
                    $("#tema").val(msg.seguimiento.tema);
                    $("#objetivos").val(msg.seguimiento.objetivos);
                    $("#actividades").val(msg.seguimiento.actividades);
                    $("#observaciones").val(msg.seguimiento.observaciones);
                    $("#h_ini option[value=" + msg.hour.h_ini + "]").attr("selected", true);
                    $("#m_ini option[value=" + msg.hour.m_ini + "]").attr("selected", true);
                    $("#h_fin option[value=" + msg.hour.h_fin + "]").attr("selected", true);
                    $("#m_fin option[value=" + msg.hour.m_fin + "]").attr("selected", true);
                    $("#date").val(msg.hour.seguimiento.fecha);
                    $("#seguimiento :input").prop("disabled", true);
                    $('#mytable_consult tbody').html('');
                    if (msg.rows != 0) {
                        var content = msg.content;
                        for (x in content) {
                            $('#mytable_consult tbody').append("<tr> <td>" + content[x].username + "</td> <td>" + content[x].firstname + "</td> <td>" + content[x].lastname + "</td>  <td id=\"talentos\"  class = \"hide\">" + content[x].idtalentos + "</td> </tr>");
                        }
                    }
                    else {
                        $('#list_grupal_seg_consult').append("<a>No registra ningun estudiante a su cargo.Por favor dirigete a la oficina de Sistemas de talentos pilos para gestionar tu situación</a>");
                    }
                }
                else {
                    swal({
                        title: error,
                        html: true,
                        type: "error",
                        text: msg.msg,
                        confirmButtonColor: "#D3D3D3"
                    });
                }
            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
                alert("error al ver detalles de seguimientos");
            },
        });
    }


    function validateModal(data) {
        var isvalid = true;
        var detalle = "";
        var date, h_ini, m_ini, h_fin, m_fin, tema, objetivos, idtalentos, place;
        $.each(data, function(i, field) {
            switch (field.name) {
                case 'date':
                    date = field.value;
                    break;
                case 'place':
                    place = field.value;
                    break;

                case 'h_ini':
                    h_ini = field.value;
                    break;

                case 'm_ini':
                    m_ini = field.value;
                    break;

                case 'h_fin':
                    h_fin = field.value;
                    break;
                case 'm_fin':
                    m_fin = field.value;
                    break;
                case 'tema':
                    tema = field.value;
                    break;
                case 'objetivos':
                    objetivos = field.value;
                    break;
                case 'actividades':
                    actividades = field.value;
                    break;
                case 'observaciones':
                    observaciones = field.value;
                    break;
                case 'idtalentos':
                    idtalentos = field.value;
                    break;
            }
        });
        if (!date) {
            isvalid = false;
            detalle += "* Selecciona una Fecha de seguimiento valida: date<br>";
        }


        if (place == undefined || place == "") {
            detalle += "* Seleccione el lugar : lugar<br>";
            isvalid = false;
        }

        if (actividades == undefined || actividades == "") {
            detalle += "* Seleccione las actividades que se realizaron : actividades<br>";
            isvalid = false;
        }

        if (observaciones == undefined || observaciones == "") {
            detalle += "* Seleccione las observaciones : observaciones<br>";
            isvalid = false;
        }

        if (h_ini > h_fin) {
            isvalid = false;
            detalle += "* La hora final debe ser mayor a la inicial<br>";
        }
        else if (h_ini == h_fin) {
            if (m_ini > m_fin) {
                isvalid = false;
                detalle += "* La hora final debe ser mayor a la inicial<br>";
            }
        }

        if (idtalentos.length === 0) {
            isvalid = false;
            detalle += "* Selecciona los estudiantes que asistieron al seguimiento: " + idtalentos.length + "<br>";
        }


        if (tema == "") {
            isvalid = false;
            detalle += "* La informacion de \"observaciones\" es obligatoria :" + tema + "<br>";
        }

        if (objetivos == "") {
            isvalid = false;
            detalle += "* La informacion de \"Objetivos\" es obligatoria:" + objetivos + "<br>";
        }

        var result = {
            isvalid: isvalid,
            detalle: detalle
        };


        return result;
    }



});
