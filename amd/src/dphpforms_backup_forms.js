/**
 * Backup  forms report
 * @module amd/src/dphpforms_backupforms
 * @author Juan Pablo Castro
 * @copyright 2018 Juan Pablo Castro<juan.castro.vasquez@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'block_ases/jszip',
    'block_ases/jquery.dataTables',
    'block_ases/dataTables.autoFill',
    'block_ases/dataTables.buttons',
    'block_ases/buttons.html5',
    'block_ases/buttons.flash',
    'block_ases/buttons.print',
    'block_ases/bootstrap',
    'block_ases/sweetalert',
    'block_ases/jqueryui',
    'block_ases/select2',
    'block_ases/loading_indicator',
    'block_ases/_general_modal_manager'
], function ($, jszip, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2, li, gmm) {
    return {
        init: function () {


            $(document).on("mousedown", ".slider.round", function (e) {

                let status = $(this).data("status");

                if (status == "off") {

                    $(this).data("status", "on");
                    $("#busqueda-simplificada").hide();
                    $("#busqueda-avanzada").show();

                } else {

                    $(this).data("status", "off");
                    $("#busqueda-avanzada").hide();
                    $("#busqueda-simplificada").show();

                }
            });

            $(document).on("click", "#generarFiltroSimplificado", function () {

                let _username = $("#simple_cod_user").val();
                let _is_student = $("#simple_criteria_select option:selected").val();

                li.show();
                $.ajax({
                    type: "POST",
                    data: { loadF: 'get_records_simple', username: _username, is_student: _is_student },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    cache: false,
                    async: true,
                    success: function (msg) {
                        li.hide();
                        if (msg.length === 0) {
                            swal(
                                'ATTRIBUTE NOT FOUND',
                                'Oooops! Zero results',
                                'warning'
                            );
                        } else {
                            //Filtrar data table
                            $("#div_table_forms").empty();
                            $("#div_table_forms").append('<table id="tableBackupForms" class="display" cellspacing="0" width="100%"><thead><thead></table>');
                            var table = $("#tableBackupForms").DataTable(msg);
                            $('#div_table_forms').css('cursor', 'pointer');
                        }
                    },
                    failure: function (msg) {
                        li.hide();
                        alert("No encontrado");
                    }
                });

            });

            $(document).ready(function () {
                li.show();
                $.ajax({

                    type: "POST",
                    data: { loadF: 'loadForms' },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {
                        li.hide();
                        $("#div_table_forms").empty();
                        $("#div_table_forms").append('<table id="tableBackupForms" class="display" cellspacing="0" width="100%"><thead><thead></table>');
                        var table = $("#tableBackupForms").DataTable(msg);
                        $('#div_table_forms').css('cursor', 'pointer');

                    },
                    dataType: "json",
                    cache: false,
                    async: true,

                    failure: function (msg) { li.hide(); }
                });

                $.ajax({

                    type: "POST",
                    data: { loadF: 'loadGeneralLogs' },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {
                        $("#div_table_general_logs").empty();
                        $("#div_table_general_logs").append('<table id="tableBackupGeneralLogs" class="display" cellspacing="0" width="100%"><thead><thead></table>');
                        var table = $("#tableBackupGeneralLogs").DataTable(msg);
                        $('#div_table_general_logs').css('cursor', 'pointer');

                    },
                    dataType: "json",
                    cache: false,
                    async: true,

                    failure: function (msg) { }
                });

            });

            $(document).on('click', '#tableBackupForms tbody tr td', function () {
                let valores = "";

                // Obtenemos la primer columna de la fila seleccionada
                // seleccionada
                valores = $(this).parents("tr").find("td:first").html();

                // alert(valores);
                get_only_form('get_form', valores);

            });

            $(document).on('click', '#tableBackupGeneralLogs tbody tr td', function () {
                let valores = "";

                // Obtenemos la primer columna de la fila seleccionada
                // seleccionada
                valores = $(this).parents("tr").find("td:first").html();

                // alert(valores);
                get_only_form('get_form_general_logs', valores);

            });


            $('#generarFiltro').on('click', function () {
                var porId = document.getElementById("filtroCodigoUsuario").value;
                var table = document.getElementById("selectTable").value;
                get_id_switch_user(porId, table);

            });

            $('#load_like').on('click', function () {
                var columna = document.getElementById("typecolumn_select").value;
                var cadena = document.getElementById("filtroLike").value;
                //alert (columna);
                get_like_cadena_in_column(cadena, columna);

            });

            $("#typecolumn_select").on("click", function () {
                if ($("#typecolumn_select").val() == "datos_previos" || $("#typecolumn_select").val() == "datos_enviados" || $("#typecolumn_select").val() == "datos_almacenados") {
                    $("#typekey_select").parent().parent().show();
                } else {
                    $("#typekey_select").parent().parent().hide();
                }
            });

            $("#typekey_select").on("click", function () {
                if ($("#typecolumn_select").val() == "datos_enviados") {
                    let cadena_busqueda = '"id":' + '"' + $(this).val() + '"' + ',"valor":"MODIFIQUE ESTE VALOR"';
                    $("#filtroLike").val(cadena_busqueda);
                }
                if ($("#typecolumn_select").val() == "datos_previos" || $("#typecolumn_select").val() == "datos_almacenados") {
                    get_keys_json($(this).val());

                }

            });

            $(".dphpforms-restore").on("click", function () {
                if (!$(this).attr("disabled") && ($(this).attr("data-estado") == 1)) {
                    let dwarehouse_record_id_to_restore = $(this).attr("data-record-id");
                    restore_delete_dphpforms_record(dwarehouse_record_id_to_restore);

                }
            });

            $(".dphpforms-compare").on("click", function () {
                if (!$(this).attr("disabled")) {
                    let datos_previos_para_modal, datos_almacenados_para_modal, json_to_compare, user_monitor, accion_record, date_record, url_request;
                    json_to_compare = JSON.parse($("#json_record_dwarehouse_selected").val());


                    //Create html to modal using json_to_compare
                    //Info general
                    user_monitor = json_to_compare.id_usuario_moodle;
                    accion_record = json_to_compare.accion;
                    date_record = json_to_compare.fecha_hora_registro;
                    url_request = json_to_compare.url_request;

                    //**************************************************************************************************************************************************************************** */
                    //GENERACIÓN DE HTML DE DATOS PREVIOS Y ALMACENADOS
                    //Iterar sobre el json de datos previos para generar html
                    let html_json_prev_content, html_json_content = '', html_json_alm_content;

                    datos_previos_para_modal = json_to_compare.datos_previos;
                    if (datos_previos_para_modal == "" || datos_previos_para_modal == null) {

                        html_json_prev_content = '<div class = "data_estado_json text_json"> No hay datos previos </div>';

                    } else {

                        datos_previos_para_modal = datos_previos_para_modal.record;

                        html_json_prev_content = '<div class="data_estado_json text_json"> Alias formulario:        ' + datos_previos_para_modal.alias + '</div>';
                    }

                    //Iterar sobre el json de datos almacenados para generar html
                    datos_almacenados_para_modal = json_to_compare.datos_almacenados;

                    if (datos_almacenados_para_modal == "" || datos_almacenados_para_modal == null) {

                        html_json_alm_content = '<div class = "data_estado_json text_json"> No hay datos almacenados </div>';

                    } else {

                        datos_almacenados_para_modal = datos_almacenados_para_modal.record;

                        html_json_alm_content = '<div class="data_estado_json text_json"> Alias formulario:        ' + datos_almacenados_para_modal.alias + '</div>';
                    }

                    html_json_content += '<div class="contenedor" >' + html_json_prev_content + html_json_alm_content + '</div>';


                    //Generar html de los campos de ambos json
                    if ((datos_previos_para_modal != "" && datos_previos_para_modal != null ) && (datos_almacenados_para_modal != "" && datos_almacenados_para_modal != null)) {
                        //Puede comparar ambos json y generar html
                        let html_result_compare_json = compare_json(datos_previos_para_modal.campos, datos_almacenados_para_modal.campos);
                        html_json_content += html_result_compare_json;
                    } else {
                        //Ambos, o uno de los dos JSON no tiene información. No es necesario comparar, pero sí generar el html del que tiene información
                        if (datos_previos_para_modal != "" && datos_previos_para_modal != null ) {
                            //Generar html de datos previos
                            html_json_content += generate_html_json(datos_previos_para_modal.campos);
                        } else if (datos_almacenados_para_modal != "" && datos_almacenados_para_modal != null)  {
                            //Generar html de datos almacenados
                            html_json_content += generate_html_json(datos_almacenados_para_modal.campos);
                        }
                    }



                    //**************************************************************************************************************************************************************************** */

                    let html_content = '<div class="grid_dphpforms_compare">';
                    html_content += '<div class="general_info_record_dwarehouse"> ';
                    html_content += '<div class= "title_data">Información del registro </div>';
                    html_content += '<div class="data_general text_json">  Realizado por:        ' + user_monitor + '</div>';
                    html_content += '<div class="data_general text_json">  Acción del registro: <strong style= "font-size: 18px;"> ' + accion_record + '</strong></div>';
                    html_content += '<div class="data_general text_json">  Fecha:                ' + date_record + '</div>';
                    html_content += '<div class="url_students data_general text_json"> </div>';
                    html_content += '                            </div>';
                    html_content += '<div class = "json_data_compare">';
                    html_content += '<div class="contenedor" >';
                    html_content += '<div class="title_data text_json"> Datos previos</div>';
                    html_content += '<div class="title_data text_json"> Datos almacenados</div>';
                    html_content += '</div>';
                    html_content += html_json_content;
                    html_content += '                                    </div>';
                    html_content += '</div>';
                    html_content += '<hr style="background-color: red; height: 1px; border: 0">';

                    //Crear JSON con general_modal_manager
                    gmm.generate_modal("modal_to_compare", "Comparación de estados", html_content, null, function(){ gmm.show_modal( ".modal_to_compare" ) });

                    //GENERACIÓN DE HTML DE URL(S) QUE DIRIGEN A FICHA DE ESTUDIANTE(S)

                    getDataToUrl(json_to_compare.id_registro_respuesta_form, accion_record, json_to_compare);


                }
            });



            $('.outside').click(function () {
                var outside = $(this);
                swal({
                    title: 'Confirmación de salida',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Salir'
                }, function (isConfirm) {
                    if (isConfirm) {
                        $(outside).parent('.mymodal').fadeOut(300);
                        console.log($(this).parent('.mymodal'));
                    }
                });

            });

            (function ($) {

                $.fn.beautifyJSON = function (options) {
                    var defaults = {
                        type: "strict",
                        hoverable: true,
                        collapsible: true,
                        color: true
                    };
                    var settings = jQuery.extend({}, defaults, options);
                    this.each(function () {
                        if (settings.type == "plain") {
                            var INCREMENT = "&ensp;&ensp;&ensp;";
                            var s = [];
                            var indent = "";
                            var input = this.innerHTML;
                            var output = input.split('"').map(function (v, i) {
                                return i % 2 ? v : v.replace(/\s/g, "");
                            }).join('"');
                            var text = "";
                            function peek(stack) {
                                var val = stack.pop();
                                stack.push(val);
                                return val;
                            }
                            for (i = 0; i < input.length; i++) {
                                if (input.charAt(i) == '{') {
                                    s.push(input.charAt(i));
                                    text += input.charAt(i) + '<br>';
                                    indent += INCREMENT;
                                    text += indent;
                                } else if (input.charAt(i) == '\"' && peek(s) != '\"') {
                                    text += input.charAt(i);
                                    s.push(input.charAt(i));
                                } else if (input.charAt(i) == '[' && input.charAt(i + 1) == ']') {
                                    s.push(input.charAt(i));
                                    text += input.charAt(i);
                                    indent += INCREMENT;
                                } else if (input.charAt(i) == '[') {
                                    s.push(input.charAt(i));
                                    text += input.charAt(i) + '<br>';
                                    indent += INCREMENT;
                                    text += indent;
                                } else if (input.charAt(i) == ']') {
                                    indent = indent.substring(0, (indent.length - 18));
                                    text += '<br>' + indent;
                                    text += input.charAt(i);
                                    s.pop();
                                } else if (input.charAt(i) == '}') {
                                    indent = indent.substring(0, (indent.length - 18));
                                    text += '<br>' + indent + input.charAt(i);
                                    s.pop();
                                    if (s.length != 0)
                                        if (peek(s) != '[' && peek(s) != '{') {
                                            text += indent;
                                        }
                                } else if (input.charAt(i) == '\"' && peek(s) == '\"') {
                                    text += input.charAt(i);
                                    s.pop();
                                } else if (input.charAt(i) == ',' && peek(s) != '\"') {
                                    text += input.charAt(i) + '<br>';
                                    text += indent;
                                } else if (input.charAt(i) == '\n') {
                                } else if (input.charAt(i) == ' ' && peek(s) != '\"') {
                                } else {
                                    text += input.charAt(i);
                                }
                            }
                            this.innerHTML = text;
                        } else if (settings.type == "flexible") {
                            var s = [];
                            var s_html = [];
                            var input = this.innerHTML;
                            var text = "";
                            if (settings.collapsible) {
                                var collapser = "<span class='ellipsis'></span><div class='collapser'></div><ul class='array collapsible'>";
                            } else {
                                var collapser = "<div></div><ul class='array'>";
                            }
                            if (settings.hoverable) {
                                var hoverabler = "<div class='hoverable'>";
                            } else {
                                var hoverabler = "<div>";
                            }
                            text += "<div id='json'>";
                            s_html.push("</div>");
                            function peek(stack) {
                                var val = stack.pop();
                                stack.push(val);
                                return val;
                            }
                            for (i = 0; i < input.length; i++) {
                                if (input.charAt(i) == '{') {
                                    s.push(input.charAt(i));
                                    text += input.charAt(i);
                                    text += collapser;
                                    s_html.push("</ul>");
                                    text += "<li>" + hoverabler;
                                    s_html.push("</div></li>");
                                } else if (input.charAt(i) == '\"' && peek(s) != '\"') {
                                    text += input.charAt(i);
                                    s.push(input.charAt(i));
                                } else if (input.charAt(i) == '[' && input.charAt(i + 1) == ']') {
                                    s.push(input.charAt(i));
                                    text += input.charAt(i);
                                    text += collapser;
                                    s_html.push("</ul>");
                                    text += "<li>" + hoverabler;
                                    s_html.push("</div></li>");
                                } else if (input.charAt(i) == '[') {
                                    s.push(input.charAt(i));
                                    text += input.charAt(i);
                                    text += collapser;
                                    s_html.push("</ul>");
                                    text += "<li>" + hoverabler;
                                    s_html.push("</div></li>");
                                } else if (input.charAt(i) == ']') {
                                    text += s_html.pop() + s_html.pop();
                                    text += input.charAt(i);
                                    // text += s_html.pop();
                                    s.pop();
                                } else if (input.charAt(i) == '}') {
                                    text += s_html.pop() + s_html.pop();
                                    text += input.charAt(i);
                                    s.pop();
                                    if (s.length != 0)
                                        if (peek(s) != '[' && peek(s) != '{') {
                                            text += s_html.pop();
                                        }
                                } else if (input.charAt(i) == '\"' && peek(s) == '\"') {
                                    text += input.charAt(i);
                                    s.pop();
                                } else if (input.charAt(i) == ',' && peek(s) != '\"') {
                                    text += input.charAt(i);
                                    text += s_html.pop();
                                    text += "<li>" + hoverabler;
                                    s_html.push("</div></li>");
                                } else if (input.charAt(i) == '\n') {
                                } else if (input.charAt(i) == ' ' && peek(s) != '\"') {
                                } else {
                                    text += input.charAt(i);
                                }
                            }
                            this.innerHTML = text;
                        } else {
                            var text = "";
                            var s_html = [];
                            if (settings.collapsible) {
                                var collapser = "<span class='ellipsis'></span><div class='collapser'></div><ul class='array collapsible'>";
                                var collapser_obj = "<span class='ellipsis'></span><div class='collapser'></div><ul class='obj collapsible'>";
                            } else {
                                var collapser = "<div></div><ul class='array'>";
                                var collapser_obj = "<div></div><ul class='obj'>";
                            }
                            if (settings.hoverable) {
                                var hoverabler = "<div class='hoverable'>";
                            } else {
                                var hoverabler = "<div>";
                            }
                            function peek(stack) {
                                var val = stack.pop();
                                stack.push(val);
                                return val;
                            }
                            function iterateObject(object) {
                                $.each(object, function (index, element) {
                                    if (element == null) {
                                        text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: <span class='type-null'>" + element + "</span></div></li>";
                                    } else if (element instanceof Array) {
                                        text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: " + "[" + collapser;
                                        s_html.push("</li>");
                                        s_html.push("</div>");
                                        s_html.push("</ul>");
                                        iterateArray(element);
                                    } else if (typeof element == 'object') {
                                        text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: " + "{" + collapser_obj;
                                        s_html.push("</li>");
                                        s_html.push("</div>");
                                        s_html.push("</ul>");
                                        iterateObject(element);
                                    } else {
                                        if (typeof element == "number") {
                                            text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: <span class='type-number'>" + element + "</span></div></li>";
                                        } else if (typeof element == "string") {
                                            text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: <span class='type-string'>\"" + element + "\"</span></div></li>";
                                        } else if (typeof element == "boolean") {
                                            text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: <span class='type-boolean'>" + element + "</span></div></li>";
                                        } else {
                                            text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: " + element + "</div></li>";
                                        }
                                    }
                                });
                                text += s_html.pop() + "}" + s_html.pop() + s_html.pop();
                            }
                            function iterateArray(array) {
                                $.each(array, function (index, element) {
                                    if (element == null) {
                                        text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: <span class='type-null'>" + element + "</span></div></li>";
                                    } else if (element instanceof Array) {
                                        text += "<li>" + hoverabler + "[" + collapser;
                                        s_html.push("</li>");
                                        s_html.push("</div>");
                                        s_html.push("</ul>");
                                        iterateArray(element);
                                    } else if (typeof element == 'object') {
                                        text += "<li>" + hoverabler + "{" + collapser_obj;
                                        s_html.push("</li>");
                                        s_html.push("</div>");
                                        s_html.push("</ul>");
                                        iterateObject(element);
                                    } else {
                                        if (typeof element == "number") {
                                            text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: <span class='type-number'>" + element + "</span></div></li>";
                                        } else if (typeof element == "string") {
                                            text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: <span class='type-string'>\"" + element + "\"</span></div></li>";
                                        } else if (typeof element == "boolean") {
                                            text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: <span class='type-boolean'>" + element + "</span></div></li>";
                                        } else {
                                            text += "<li>" + hoverabler + "<span class='property'>" + index + "</span>: " + element + "</div></li>";
                                        }
                                    }
                                });
                                text += s_html.pop() + "]" + s_html.pop() + s_html.pop();
                            }
                            var input = this.innerHTML;
                            var json = jQuery.parseJSON(input);
                            text = "";
                            text += "<div id='json'>";
                            text += hoverabler + "{" + collapser_obj;
                            s_html.push("");
                            s_html.push("</div>");
                            s_html.push("</ul>");
                            iterateObject(json);
                            text += "</ul></div></div>";
                            this.innerHTML = text;
                        }
                        $('.hoverable').hover(function (event) {
                            event.stopPropagation();
                            $('.hoverable').removeClass('hovered');
                            $(this).addClass('hovered');
                        }, function (event) {
                            event.stopPropagation();
                            // $('.hoverable').removeClass('hovered');
                            $(this).addClass('hovered');
                        });
                        $('.collapser').off().click(function (event) {
                            $(this).parent('.hoverable').toggleClass('collapsed');
                        });
                    });
                }
            }(jQuery));


            function get_like_cadena_in_column(cad, column) {
                //Realiza la consulta del atributo según la cadena enviada, y el atributo seleccionado
                //Muestra los resultados en pantalla en el Data Table
                li.show();
                $.ajax({
                    type: "POST",
                    data: { loadF: 'get_like', cadena: cad, atributo: column },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {
                        li.hide();
                        if (msg.length === 0) {
                            swal(
                                'ATTRIBUTE NOT FOUND',
                                'Oooops! Zero results',
                                'warning'
                            );
                        } else {
                            //Filtrar data table
                            $("#div_table_forms").empty();
                            $("#div_table_forms").append('<table id="tableBackupForms" class="display" cellspacing="0" width="100%"><thead><thead></table>');
                            var table = $("#tableBackupForms").DataTable(msg);
                            $('#div_table_forms').css('cursor', 'pointer');
                        }
                    },
                    cache: false,
                    async: true,

                    failure: function (msg) {
                        li.hide();
                        alert("No encontrado");
                    }
                });
            }

            function get_id_switch_user(cod_user, table) {
                //Realizar la consulta del estudiante según el codigo ingresado
                //Mostrar los resultados en pantalla
                let param = [];
                param.push(cod_user);
                param.push(table);
                li.show();
                $.ajax({
                    type: "POST",
                    data: { loadF: 'get_id_user', params: param },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {
                        li.hide();
                        if (msg.length === 0) {
                            swal(
                                'USER NOT FOUND',
                                'Oooops! Incorrect code',
                                'warning'
                            );
                        } else {
                            $("#div_code_user").empty();
                            $("#div_code_user").append('<strong>Usuario: </strong>' + msg[0].cod_user);
                            $("#div_name_user").empty();
                            $("#div_name_user").append( msg[0].name_user + ' '+  msg[0].last_name_user);
                        }
                    },
                    cache: false,
                    async: true,

                    failure: function (msg) {
                        li.hide();
                        alert("No encontrado");
                    }
                });
            }

            function get_keys_json(id_pregunta) {
                //Realizar la consulta de respuesta según id
                li.show();
                $.ajax({
                    type: "POST",
                    data: { loadF: 'get_values', params: id_pregunta },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {
                        li.hide();
                        if (msg.length === 0) {
                            swal(
                                'QUESTION NOT FOUND',
                                'Oooops! Incorrect code',
                                'warning'
                            );
                        } else {
                            opciones = msg[0].options_c;
                            tipo_campo = msg[0].tipo_campo;
                            let cadena_busqueda = '"respuesta":"MODIFIQUE ESTE VALOR","opciones":' + '"' + opciones + '"' + ',"tipo_campo":' + '"' + tipo_campo + '"' + ',"id_pregunta":' + '"' + $("#typekey_select").val() + '"';
                            $("#filtroLike").val(cadena_busqueda);
                        }

                    },
                    cache: false,
                    async: true,

                    failure: function (msg) {
                        li.hide();
                        alert("No encontrado");
                    }
                });
            }

            function get_only_form(func, id_form) {
                //Get one form switch id
                li.show();
                $.ajax({
                    type: "POST",
                    data: { loadF: func, params: id_form },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    dataType: "json",
                    cache: false,
                    async: true,
                    success: function (msg) {

                        li.hide();
                        //msg.datos_previos = JSON.stringify(msg.datos_previos);

                        // console.log(msg[id_form].datos_previos);
                        //Items of JSON are encode


                        //msg is an Object to beautifier
                        //The goal is that any sent object will be embellished, so msg will be modified.
                        //For this, check your JSON structure
                        //Modify msg
                        if (msg[id_form].datos_previos != "") {
                            let data_prev = JSON.parse(msg[id_form].datos_previos);
                            if (func == "get_form") {
                                msg[id_form].datos_previos = data_prev;
                            }
                            if (func == "get_form_general_logs") {
                                if (Array.isArray(data_prev)) {

                                    msg[id_form].datos_previos = data_prev;
                                } else {
                                    let array_data_prev = Array();

                                    for (data in data_prev) {
                                        let key_data_prev = {};
                                        key_data_prev[data] = data_prev[data];
                                        array_data_prev.push(key_data_prev);
                                    }
                                    msg[id_form].datos_previos = array_data_prev;
                                }
                            }

                        }


                        if (msg[id_form].datos_enviados != "") {
                            msg[id_form].datos_enviados = JSON.parse(msg[id_form].datos_enviados);
                        }
                        if (msg[id_form].datos_almacenados != "") {
                            msg[id_form].datos_almacenados = JSON.parse(msg[id_form].datos_almacenados);
                        }

                        // End modify msg    

                        create_beautifyJSON(msg);
                    },
                    failure: function (msg) { li.hide(); }
                });
            }

            function create_beautifyJSON(param) {
                //Show beautifyJSON in modal
                $("#div_JSONform").empty();
                var json1 = JSON.stringify(param);
                //json[0].
                //var json = JSON.parse(param);
                $("#div_JSONform").append(json1);
                $('#div_JSONform').beautifyJSON({
                    type: "flexible",
                    hoverable: true,
                    collapsible: true,
                    color: true
                });

                let dwarehouse_record_id = Object.keys(param)[0];
                let obj = param[dwarehouse_record_id];

                $("#json_record_dwarehouse_selected").attr("value", JSON.stringify(obj));

                if (obj.id_registro_respuesta_form != -1) {
                    // $(".dphpforms-peer-record").attr( 'data-record-id',  obj.id_registro_respuesta_form );
                    // $(".dphpforms-restore").attr( 'data-record-id',  dwarehouse_record_id );
                    // $(".dphpforms-peer-record").attr( 'disabled',  false );
                    // $(".dphpforms-restore").attr( 'disabled',  false );

                    if (obj.accion == "DELETE") {
                        //Si accion es DELETE
                        $(".dphpforms-peer-record").attr('data-record-id', obj.id_registro_respuesta_form);
                        $(".dphpforms-restore").attr('data-record-id', dwarehouse_record_id);
                        $(".dphpforms-restore").attr('data-estado', 1);
                        $(".dphpforms-peer-record").attr('disabled', false);
                        $(".dphpforms-restore").attr('disabled', false);
                        $(".dphpforms-compare").attr('disabled', false);
                        $(".dphpforms-peer-record").hide();
                        $(".dphpforms-restore").show();
                        $(".dphpforms-compare").show();

                    } else if (obj.accion == "INSERT" || obj.accion == "UPDATE" || obj.accion == "RESTORE") {

                        $(".dphpforms-peer-record").attr('data-record-id', obj.id_registro_respuesta_form);
                        $(".dphpforms-peer-record").attr('disabled', false);
                        $(".dphpforms-restore").attr('disabled', true);
                        $(".dphpforms-compare").attr('disabled', false);
                        $(".dphpforms-peer-record").hide();
                        $(".dphpforms-restore").show();
                        $(".dphpforms-compare").show();

                        // if(obj.accion=="INSERT" || obj.accion=="RESTORE"){
                        //     $(".dphpforms-compare").attr( 'disabled',  true );
                        // }


                    } else {
                        $(".dphpforms-peer-record").attr('disabled', true);
                        $(".dphpforms-restore").attr('disabled', true);
                        $(".dphpforms-compare").attr('disabled', true);
                        $(".dphpforms-peer-record").hide();
                        $(".dphpforms-restore").hide();
                        $(".dphpforms-compare").hide();
                    }

                } else {
                    $(".dphpforms-peer-record").attr('disabled', true);
                    $(".dphpforms-restore").attr('disabled', true);
                    $(".dphpforms-compare").attr('disabled', true);
                    $(".dphpforms-peer-record").hide();
                    $(".dphpforms-restore").hide();
                    $(".dphpforms-compare").hide();
                }

                //    if(obj.id_registro_respuesta_form == -1 || obj.accion == "INSERT"){
                //     $(".dphpforms-peer-record").attr( 'disabled',  true );
                //     $(".dphpforms-restore").attr( 'disabled',  true );

                // }else { 
                //     $(".dphpforms-peer-record").attr( 'data-record-id',  obj.id_registro_respuesta_form );
                //     $(".dphpforms-restore").attr( 'data-record-id',  dwarehouse_record_id );
                //     $(".dphpforms-peer-record").attr( 'disabled',  false );
                //     $(".dphpforms-restore").attr( 'disabled',  false );


                // }

                $('#modal_JSON').fadeIn(300);
            }

            function restore_delete_dphpforms_record(dwarehouse_id_form_to_restore) {

                li.show();
                $.ajax({
                    type: "POST",
                    data: { loadF: 'restore_dwarehouse_record', params: dwarehouse_id_form_to_restore },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {

                        li.hide();
                        if (!msg) {
                            swal(
                                'NOT RESTORED SUCCESSFULLY',
                                'Oooops! Error',
                                'warning'
                            );
                        } else {
                            swal(
                                'RESTORED SUCCESSFULLY',
                                'SUCCESS',
                                'success'
                            );
                        }
                    },
                    cache: false,
                    async: true,

                    error: function (msg) {
                        li.hide();
                        swal(
                            'SYSTEM ERROR',
                            'Report to Systems office',
                            'error'
                        );
                    }
                });

            }

            function compare_json(json_a, json_b) {
                // json_b se asume como el JSON que ha cambiado
                // json_a se asume como el JSON con el que se compara el json_b
                // Ejemplo:  json_b = {"alias":"un_alias"} , json_a = {"alias":"otro_alias"}

                let html_campos_b, html_campos_a, length_json_a, count_json_a, html_general_campos;
                html_general_campos = '';
                length_json_a = json_a.length;
                html_campos_b = '';
                html_campos_a = '';
                for (campo_b in json_b) {
                    count_json_a = 0;
                    //Para cada campo_b registrado en el JSON, comparar con cada campo_a
                    for (campo_a in json_a) {
                        count_json_a++;

                        if (json_b[campo_b].local_alias == json_a[campo_a].local_alias) {
                            //Encuentra el campo igual, y compara si son o no diferentes sus respuestas
                            if (json_b[campo_b].respuesta == json_a[campo_a].respuesta) {
                                //Html para campos iguales
                                html_campos_a += '<div data-id-pregunta=' + json_a[campo_a].id_pregunta + ' data-id-relacion-form-pregunta=' + json_a[campo_a].id_relacion_form_pregunta + '  class="data_estado_json data_estado_json_similar" style="border: 1px solid green !important;">  <div class = "text_json">Alias: ' + json_a[campo_a].local_alias + '  </div> ';
                                html_campos_a += '<div class = "text_json">Respuesta: ' + json_a[campo_a].respuesta + '  </div> </div>';

                                html_campos_b += '<div data-id-pregunta=' + json_b[campo_b].id_pregunta + ' data-id-relacion-form-pregunta=' + json_b[campo_b].id_relacion_form_pregunta + ' class="data_estado_json data_estado_json_similar" style="border: 1px solid green !important;">  <div class = "text_json">Alias: ' + json_b[campo_b].local_alias + '  </div> ';
                                html_campos_b += '<div class = "text_json">Respuesta: ' + json_b[campo_b].respuesta + '  </div> </div>';
                            } else {
                                //Html para campos diferentes 
                                html_campos_a += '<div data-id-pregunta=' + json_a[campo_a].id_pregunta + ' data-id-relacion-form-pregunta=' + json_a[campo_a].id_relacion_form_pregunta + '  class="data_estado_json data_estado_json_different" style="border: 1px solid red !important;">  <div class = "text_json" >Alias: ' + json_a[campo_a].local_alias + '  </div> ';
                                html_campos_a += '<div class = "text_json data_estado_json_different" >Respuesta: ' + json_a[campo_a].respuesta + '  </div> </div>';

                                html_campos_b += '<div  data-id-pregunta=' + json_b[campo_b].id_pregunta + ' data-id-relacion-form-pregunta=' + json_b[campo_b].id_relacion_form_pregunta + ' class="data_estado_json data_estado_json_different" style="border: 1px solid red !important;">  <div class = "text_json" >Alias: ' + json_b[campo_b].local_alias + '  </div> ';
                                html_campos_b += '<div class = "text_json data_estado_json_different">Respuesta: ' + json_b[campo_b].respuesta + '  </div> </div>';

                            }

                            break;
                        } else if (count_json_a == length_json_a) {
                            //No hay el campo_b en el json_a

                            html_campos_a += '<div data-id-pregunta=' + json_b[campo_b].id_pregunta + ' data-id-relacion-form-pregunta=' + json_b[campo_b].id_relacion_form_pregunta + '  class="data_estado_json data_estado_json_different" style="border: 1px solid red !important;">  <div class = "text_json" >Alias: No registra en esta versión</div> ';
                            html_campos_a += '<div  class = "text_json">Respuesta: No registra en esta versión</div> </div>';

                            html_campos_b += '<div  data-id-pregunta=' + json_b[campo_b].id_pregunta + ' data-id-relacion-form-pregunta=' + json_b[campo_b].id_relacion_form_pregunta + ' class="data_estado_json data_estado_json_different" style="border: 1px solid red !important;">  <div class = "text_json">Alias: ' + json_b[campo_b].local_alias + '  </div> ';
                            html_campos_b += '<div  class = "text_json">Respuesta: ' + json_b[campo_b].respuesta + '  </div> </div>';
                        }


                    }
                    html_general_campos += '<div class="contenedor" >' + html_campos_a + html_campos_b + '</div>';
                    html_campos_a = '';
                    html_campos_b = '';

                }

                return html_general_campos;
            }

            function generate_html_json(json) {
                let html_to_keys = '';

                for (campo in json) {
                    html_to_keys += '<div class="contenedor" >';
                    html_to_keys += '<div data-id-pregunta=' + json[campo].id_pregunta + ' data-id-relacion-form-pregunta=' + json[campo].id_relacion_form_pregunta + '  class="data_estado_json data_estado_json_different" style="border: 1px solid red !important;">  <div >Alias: No registra en esta versión</div> ';
                    html_to_keys += '<div>Respuesta: No registra en esta versión</div> </div>';
                    html_to_keys += '<div data-id-pregunta=' + json[campo].id_pregunta + ' data-id-relacion-form-pregunta=' + json[campo].id_relacion_form_pregunta + '  class="data_estado_json data_estado_json_similar" style="border: 1px solid green !important;">  <div>Alias: ' + json[campo].local_alias + '  </div> ';
                    html_to_keys += '<div>Respuesta: ' + json[campo].respuesta + '  </div> </div>';
                    html_to_keys += '</div>';
                }

                return html_to_keys;
            }

            function getDataToUrl(id_registro_respuesta_form, accion_record, record_dwarehouse) {

                let param = [];
                param.push(id_registro_respuesta_form);
                param.push(accion_record);
                param.push(JSON.stringify(record_dwarehouse));


                $.ajax({
                    type: "POST",
                    data: { loadF: 'get_student_code_to_url', params: param},
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    cache: false,
                    success: function (msg) {

                        let html_enlaces = '', student_code, name_student, student, courseid, instanceid;
                        let dwarehouse_record_id ;

                         if (msg.length === 0) {
                            html_enlaces = '<strong>Enlace(s) no disponible(s)</strong>';
                            $(".url_students").empty();
                            $(".url_students").append(html_enlaces);
                        } else {


                            for(student_data in msg){

                                   
                                student = msg[student_data];
                                dwarehouse_record_id = Object.keys(student)[0];
                                
                                student_code = student[dwarehouse_record_id].username;
                                name_student = student[dwarehouse_record_id].firstname + ' ' +student[dwarehouse_record_id].lastname;
                                courseid     = student[dwarehouse_record_id].courseid;
                                instanceid     = student[dwarehouse_record_id].instanceid;
                             

                                // let urlHost = location.host;
                                // let urlPathname = location.pathname.split("/");
                                // let pathnameResult ="";
                                let urlFicha;
                                
                                // for(x in urlPathname){
                                //     if(urlPathname[x] == "backup_forms.php"){
                                //         pathnameResult += "student_profile.php";
                                //     }else{
                                //         pathnameResult += urlPathname[x]+"/";
                                //     }
                                    
                                // }

                                urlFicha = "student_profile.php?courseid="+courseid+"&instanceid="+instanceid+"&student_code="+student_code;
                             

                                html_enlaces += '<div> Estudiante: <a href="'+urlFicha+'"  target="_blank" ><strong>'+name_student+ ' '+student_code+'</strong></a></div>';
                               // html_enlaces += '<a href="https://campusvirtual.univalle.edu.co/moodle/blocks/ases/view/student_profile.php?courseid=25643&instanceid=450299&student_code='+ student_code+'">'+name_student+'</a> <br>';
                            }
                            
                            $(".url_students").empty();
                            $(".url_students").append(html_enlaces);
                       
                        }

                        // if (value_id_estudiante[0] !== '-9') {
                        //     for (id in value_id_estudiante) {
                        //         html_enlaces += '<a href="javascript:void(0)" class="data_general text_json" >' + value_code_student[id] + ' </a>';
                        //     }


                        // } else {

                        //     html_enlaces = '<strong>Enlace(s) no disponible(s)</strong>';

                        // }

                        // $(".url_students").empty();
                        // $(".url_students").append(html_enlaces);
                        // callback_create_html_url(alias_formulario, local_alias_campo, accion_record, record_dwarehouse);
                    },

                    error: function (msg) {
                        swal(
                            'TYPE FORM NOT FOUND',
                            'Oooops! Is not a valid form to create url',
                            'warning'
                        );

                    }
                });



            }



            $('.mymodal-close').click(function () {
                $("#modal_JSON").hide();
            });

            $('.btn-danger-close').click(function () {
                $("#modal_JSON").hide();
            });
        }

    };
});