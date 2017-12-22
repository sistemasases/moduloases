/**
 * @module block_ases/global_grade_book
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/datatables.net', 'block_ases/datatables.net-buttons', 'block_ases/buttons.flash', 'block_ases/jszip', 'block_ases/pdfmake', 'block_ases/buttons.html5', 'block_ases/buttons.print', 'block_ases/sweetalert', 'block_ases/select2', 'block_ases/jqueryui'], function ($, bootstrap, datatablesnet, datatablesnetbuttons, buttonsflash, jszip, pdfmake, buttonshtml5, buttonsprint, sweetalert, select2, jqueryui) {

    return {

        init: function () {
            
            //Metodos de Wizard de crear categorias e items
            $(document).on('click', '#wizard_button', function () {
                $("#modalCategories").modal({
                    backdrop: false
                });
                $('.fondo').show();
                id = getCourseid();
                loadCategories(id);
            });

            $(document).on('click', '.mymodal-close', function () {
                location.reload();
                $('.fondo').hide();
            });

            $(document).on('click', '.edit', function () {
                
                $("#edit").modal({
                    backdrop: false
                });
            });

            $(document).on('click', '.delete', function () {
                var element = $(this).parent().parent().parent().attr('id').split('_');
                var courseid = getCourseid();
                var id = element[1];
                var type = element[0];
                if (type === 'cat') {
                    var tipo = "esta categoría"
                } else {
                    var tipo = "este item"
                }
                var titulo = "Esta seguro que desea eliminar " + tipo + "?";
                swal({
                    title: titulo,
                    text: "Tenga en cuenta que NO SE PODRAN RECUPERAR ninguna de la notas que haya registrado en este elemento una vez borrado!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Aceptar, Borrar " + tipo,
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                    function (isConfirm) {
                        if (isConfirm) {
                            deleteElement(id, courseid, type);
                        } else {
                            swal({
                                title: "Cancelado",
                                type: "error",
                                showCancelButton: false,
                                showConfirmButton: false,
                                timer: 1200,
                            });
                        }
                    });

            });

            $(document).on("click", ".new", function () {
                var maxweight = $(this).parent().parent().children().next('.maxweight').attr('id');
                if (maxweight <= 0) {
                    swal({
                        title: "No se pueden crear mas categorías o ítems en la categoria seleccionada.",
                        text: "\n\r El peso de los elementos dentro de ésta suma 100%.\n\r Para crear un nuevo elemento primero configure los pesos de los demas.",
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });
                    return;
                }
                $('.new').prop('disabled', true);
                $('.delete').prop('disabled', true);
                $('.edit').prop('disabled', true);

                var newDiv = $("<div class = 'divForm'>");
                newDiv.load("../templates/categories_form.html");

                var parent = $(this).parent().parent();
                parent.append('<hr style = "border-top: 1px solid #ddd">');
                parent.append(newDiv);

                window.setTimeout(function () {

                    var agg = parent.attr("id");

                    if (agg == 10) { //if is ponderated
                        $("#divPeso").show();
                        $("#inputValor").on('blur', function () {
                            //se revisa que el elemento digitado cumpla con las restricciones
                            var numero = $(this).val();
                            var maxPeso = parseInt($(this).parent().parent().parent().parent().children().next('.maxweight').attr('id'));
                            //si no cumple con la restriccion de estar entre 0 y 100 entonces se realiza el aviso y se pone el valor en 0
                            if (numero < 0 || numero > maxPeso) {
                                swal({
                                    title: "El valor debe estar entre 0 y el peso máximo: " + maxPeso,
                                    text: "\n\rUsted ingresó: " + numero,
                                    html: true,
                                    type: "warning",
                                    confirmButtonColor: "#d51b23"
                                });
                                $(this).val('');
                            }
                        });
                        $('#inputValor').on('keypress', function (e) {
                            tecla = (document.all) ? e.keyCode : e.which;

                            //Tecla de retroceso para borrar y el punto(.) siempre la permite
                            if (tecla == 8 || tecla == 46) {
                                return true;
                            }
                            // Patron de entrada, en este caso solo acepta numeros
                            patron = /[0-9]/;
                            tecla_final = String.fromCharCode(tecla);
                            return patron.test(tecla_final);

                        });
                    }

                    $("#tipoItem").on('change', function () {
                        var index = $(this).prop('selectedIndex');
                        if (index == 1) {
                            $('#divTipeC').show();
                        }
                        else {
                            $('#divTipeC').hide();
                        }
                    });

                    $('#save').on('click', function () {
                        var agg = $(this).parent().parent().parent().parent().attr("id");
                        var parent = $(this).parent().parent().parent().parent().parent().attr("id");
                        parent = parent.split('_')[1];
                        createElement(agg, parent);
                    });

                    $('#cancel').on('click', function () {
                        var id = getCourseid();
                        loadCategories(id);
                    });
                }, 400);
            });

            function deleteElement(id_e, course_id, type_e) {
                $.ajax({
                    type: "POST",
                    data: {
                        type_ajax: "deleteElement",
                        id: id_e,
                        courseid: course_id,
                        type: type_e
                    },
                    url: "../managers/grade_categories/grader_processing.php",
                    success: function (msg) {
                        if (msg.error) {
                            swal('Error',
                                msg.error,
                                'error');
                        } else {
                            swal({
                                title: "Listo",
                                text: msg.msg,
                                type: "success",
                                showCancelButton: false,
                                showConfirmButton: false,
                                timer: 1200,
                            });
                            loadCategories(course_id);
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function (msg) {
                        console.log(msg);
                    },
                });
            }
            function getCourseid() {
                var informacionUrl = window.location.search.split("&");
                for (var i = 0; i < informacionUrl.length; i++) {
                    var elemento = informacionUrl[i].split("=");
                    if (elemento[0] == "?id_course" || elemento[0] == "id_course") {
                        var curso = elemento[1];
                    }
                }
                return curso;
            }

            function loadCategories(id) {
                $.ajax({
                    type: "POST",
                    data: {
                        course: id,
                        type: "loadCat"
                    },
                    url: "../managers/grade_categories/grader_processing.php",
                    success: function (msg) {
                        $("#mymodalbody").html(msg);
                    },
                    dataType: "text",
                    cache: "false",
                    error: function (msg) {
                        console.log(msg);
                    },
                });
            }

            function createElement(aggParent, idParent) {
                var tipoItem = $("#tipoItem").val();
                var curso = getCourseid();

                if (tipoItem == 'CATEGORÍA') {
                    if (validateDataCat(aggParent)) {
                        var name = $.trim($('#inputNombre').val());
                        var weigth = $('#inputValor').val();
                        var agg = getAggregation($('#tipoCalificacion').prop('selectedIndex'));
                        $.ajax({
                            type: "POST",
                            data: {
                                course: curso,
                                parent: idParent,
                                fullname: name,
                                agregation: agg,
                                tipo: tipoItem,
                                peso: weigth
                            },
                            url: "../managers/grade_categories/grader_processing.php",
                            success: function (msg) {
                                //se recibe el mensaje, si el ingreso fue exitoso entonces se recarga el combo de categorias padre
                                if (msg == 1) {

                                    swal({
                                        title: "Categoria añadida con exito",
                                        html: true,
                                        type: "success",
                                        confirmButtonColor: "#d51b23"
                                    });
                                    loadCategories(curso);
                                }
                                else if (msg == 0) {
                                    //si no fue exitosa la creacion se envia el mensaje de alerta
                                    swal({
                                        title: "Error al añadir la categoria (server error)",
                                        html: true,
                                        type: "error",
                                        confirmButtonColor: "#d51b23"
                                    });
                                }

                            },
                            cache: "false",
                            error: function (msg) {
                                swal({
                                    title: "Error al intentar añadir la categoria",
                                    html: true,
                                    type: "error",
                                    confirmButtonColor: "#d51b23"
                                });
                            },
                        });
                    }
                }
                else if (tipoItem == 'ÍTEM') {
                    if (validateDataIt(aggParent)) {
                        var name = $.trim($('#inputNombre').val());
                        var weigth = $('#inputValor').val();
                        $.ajax({
                            type: "POST",
                            data: {
                                course: curso,
                                parent: idParent,
                                fullname: name,
                                tipo: tipoItem,
                                peso: weigth
                            },
                            url: "../managers/grade_categories/grader_processing.php",
                            success: function (msg) {
                                //se recibe el mensaje, si el ingreso fue exitoso entonces se recarga el combo de categorias padre
                                if (msg == 1) {
                                    swal({
                                        title: "item añadido con exito",
                                        html: true,
                                        type: "success",
                                        confirmButtonColor: "#d51b23"
                                    });
                                    loadCategories(curso);
                                }
                                else if (msg == 0) {
                                    swal({
                                        title: "Error al añadir el item (server error)",
                                        html: true,
                                        type: "error",
                                        confirmButtonColor: "#d51b23"
                                    });
                                }

                            },
                            cache: "false",
                            error: function (msg) {
                                swal({
                                    title: "Error al intentar añadir el item",
                                    html: true,
                                    type: "error",
                                    confirmButtonColor: "#d51b23"
                                });
                            },
                        });
                    }
                }
                else if (tipoItem == 'PARCIAL') {
                    if (validateDataParcial(aggParent)) {
                        var name = $.trim($('#inputNombre').val());
                        var weigth = $('#inputValor').val();
                        $.ajax({
                            type: "POST",
                            data: {
                                course: curso,
                                parent: idParent,
                                fullname: name,
                                agregation: 6,
                                tipo: tipoItem,
                                peso: weigth
                            },
                            url: "../managers/grade_categories/grader_processing.php",
                            success: function (msg) {
                                //se recibe el mensaje, si el ingreso fue exitoso entonces se recarga el combo de categorias padre
                                if (msg == 1) {

                                    swal({
                                        title: "Categoria añadida con exito",
                                        html: true,
                                        type: "success",
                                        confirmButtonColor: "#d51b23"
                                    });
                                    loadCategories(curso);
                                }
                                else if (msg == 0) {
                                    //si no fue exitosa la creacion se envia el mensaje de alerta
                                    swal({
                                        title: "Error al añadir la categoria (server error)",
                                        html: true,
                                        type: "error",
                                        confirmButtonColor: "#d51b23"
                                    });
                                }

                            },
                            cache: "false",
                            error: function (msg) {
                                swal({
                                    title: "Error al intentar añadir la categoria",
                                    html: true,
                                    type: "error",
                                    confirmButtonColor: "#d51b23"
                                });
                            },
                        });
                    }
                }
                else {
                    swal({
                        title: "Seleccione el tipo de elemento que desea crear",
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });
                }

            }

            function getAggregation(index) {
                switch (index) {
                    case 1:
                        return 0;
                        break;
                    case 2:
                        return 10;
                        break;
                }
            }

            function validateDataIt(aggregation) {
                var nombre = $.trim($('#inputNombre').val());
                if (nombre == '') {
                    swal({
                        title: "Ingrese el nombre del ítem que desea crear",
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });
                    return false;
                }
                if (aggregation == 10) {
                    var peso = $('#inputValor').val();
                    if (peso == '') {
                        swal({
                            title: "Ingrese un peso válido entre 0 y 100",
                            html: true,
                            type: "warning",
                            confirmButtonColor: "#d51b23"
                        });
                        return false;
                    }
                }

                return true;
            }

            function validateDataParcial(aggregation) {
                var nombre = $.trim($('#inputNombre').val());
                if (nombre == '') {
                    swal({
                        title: "Ingrese el nombre del parcial que desea crear",
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });
                    return false;
                }
                if (aggregation == 10) {
                    var peso = $('#inputValor').val();
                    if (peso == '') {
                        swal({
                            title: "Ingrese un peso válido entre 0 y 100",
                            html: true,
                            type: "warning",
                            confirmButtonColor: "#d51b23"
                        });
                        return false;
                    }
                }

                return true;
            }

            function validateDataCat(aggregation) {
                if ($('#tipoCalificacion').prop('selectedIndex') == 0) {
                    swal({
                        title: "Seleccione el tipo de calificación de la categoría que desea crear",
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });
                    return false;
                }
                var nombre = $.trim($('#inputNombre').val());
                if (nombre == '') {
                    swal({
                        title: "Ingrese el nombre de la categoría que desea crear",
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });
                    return false;
                }
                if (aggregation == 10) {
                    var peso = $('#inputValor').val();
                    if (peso == '') {
                        swal({
                            title: "Ingrese un peso válido entre 0 y 100",
                            html: true,
                            type: "warning",
                            confirmButtonColor: "#d51b23"
                        });
                        return false;
                    }
                }
                else {

                }
                return true;
            }

            
        }
    }
});

