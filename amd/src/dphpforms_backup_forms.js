/**
 * Backup  forms report
 * @module amd/src/historic_academic_reports
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
    'block_ases/select2'
], function ($, jszip, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2) {
    return {
        init: function () {

            window.JSZip = jszip;
            $(document).ready(function () {
                $.ajax({

                    type: "POST",
                    data: { loadF: 'loadForms' },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {
                        $("#div_table_forms").empty();
                        $("#div_table_forms").append('<table id="tableBackupForms" class="display" cellspacing="0" width="100%"><thead><thead></table>');
                        var table = $("#tableBackupForms").DataTable(msg);
                        $('#div_table_forms').css('cursor', 'pointer');

                    },
                    dataType: "json",
                    cache: false,
                    async: true,

                    failure: function (msg) { }
                });

            });

            $(document).on('click', '#tableBackupForms tbody tr td', function () {
                var valores = "";

                // Obtenemos todos los valores contenidos en los <td> de la fila
                // seleccionada
               valores =  $(this).parents("tr").find("td:first").html();
                 
               // alert(valores);
                get_only_form(valores);

            });


            $('#generarFiltro').on('click', function () {
                var porId = document.getElementById("filtroCodigoUsuario").value;
                get_id_switch_user(porId);

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
                                    text += input.charAt(i)
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
                                    text += input.charAt(i)
                                    s.pop();
                                } else if (input.charAt(i) == ',' && peek(s) != '\"') {
                                    text += input.charAt(i) + '<br>';
                                    text += indent;
                                } else if (input.charAt(i) == '\n') {
                                } else if (input.charAt(i) == ' ' && peek(s) != '\"') {
                                } else {
                                    text += input.charAt(i)
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
                                var hoverabler = "<div>"
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
                                    text += input.charAt(i)
                                    s.pop();
                                } else if (input.charAt(i) == ',' && peek(s) != '\"') {
                                    text += input.charAt(i);
                                    text += s_html.pop();
                                    text += "<li>" + hoverabler;
                                    s_html.push("</div></li>");
                                } else if (input.charAt(i) == '\n') {
                                } else if (input.charAt(i) == ' ' && peek(s) != '\"') {
                                } else {
                                    text += input.charAt(i)
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
                                var hoverabler = "<div>"
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
                            s_html.push("</ul>")
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


            function get_id_switch_user(cod_user) {
                //Realizar la consulta del estudiante según el codigo ingresado
                //Mostrar los resultados en pantalla
                $.ajax({
                    type: "POST",
                    data: { loadF: 'get_id_user', params: cod_user },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {
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
                            $("#div_name_user").append('<strong>Nombre: </strong>' + msg[0].name_user);
                        }
                    },
                    cache: false,
                    async: true,

                    failure: function (msg) { alert("No encontrado") }
                });
            }


            function get_only_form(id_form) {
                //Get one form switch id
                $.ajax({
                    type: "POST",
                    data: { loadF: 'get_form', params: id_form },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {
                        create_beautifyJSON(msg);
                    },
                    dataType: "json",
                    cache: false,
                    async: true,

                    failure: function (msg) { }
                });
            }

            function create_beautifyJSON(param) {
                //Show beautifyJSON in modal
                $("#div_JSONform").empty();
                var json = JSON.stringify(param);
                $("#div_JSONform").append(json);
                $('#div_JSONform').beautifyJSON({
                    type: "flexible",
                    hoverable: true,
                    collapsible: true,
                    color: true
                });
                $('#modal_JSON').fadeIn(300);
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