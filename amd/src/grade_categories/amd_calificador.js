requirejs(['jquery', 'bootstrap', 'sweetalert', 'buttons.flash'], function($) {

    $(document).ready(function() {
        var pilos = getIDs();
        deleteNoPilos(pilos);
    });

    $(document).on('blur','.text',function(){
        console.log("h")
    })

    $(document).on('keypress','input', function(e) {
        
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

    function deleteNoPilos(pilos){
        $("#user-grades").children().children().each(function(){
            if($(this).attr('data-uid') != undefined){
                if(!isPilo($(this).attr('data-uid'), pilos)){
                    $(this).remove();
                }else{
                    $(this).children('th').children().each(function(){
                        $(this).removeAttr('href');
                        $(this).click(function(){
                            var id = $(this).parent().parent().attr('data-uid');
                            var code = $('#idmoodle_'+id).attr('data-code');
                            var pagina = "student_profile.php";
                            var url = pagina + location.search + "&student_code=" + code;
                            window.open(url, '_blank');
                        })
                });
                }
            };
        })
    }

    function isPilo(id,pilos){
        for (var i = 0; i < pilos.length; i++) {
            if(pilos[i].split("_")[1] === id){
                return true;
            }
        }
        return false;
    }

    function getIDs(){
        var pilos = new Array;
        $("#students-pilos").children().each(function(){
            pilos.push($(this).attr("id"));
        })
        return pilos;
    }

    

//Metodos de Wizard de crear categorias e items
    $(document).on('click', '#wizard_button', function() {
        $("#modalCategories").modal({
            backdrop: false
        });
        $('.fondo').show();
        id = getCourseid();
        loadCategories(id);
    });

    $(document).on('click', '.mymodal-close', function() {
        location.reload();
        $('.fondo').hide();
    });

    $(document).on("click", ".new", function() {
        $('.new').prop('disabled', true);

        var newDiv = $("<div class = 'divForm'>");
        newDiv.load("../templates/categories_form.html");

        var parent = $(this).parent();
        parent.append('<hr style = "border-top: 1px solid #ddd">');
        parent.append(newDiv);

        window.setTimeout(function() {

            var agg = parent.attr("id");

            if (agg == 10) { //if is ponderated
                $("#divPeso").show();
                $("#inputValor").on('blur', function() {
                    //se revisa que el elemento digitado cumpla con las restricciones
                    var numero = $(this).val();
                    //si no cumple con la restriccion de estar entre 0 y 100 entonces se realiza el aviso y se pone el valor en 0
                    if (numero < 0 || numero > 100) {
                        swal({
                            title: "El valor debe estar entre 0 y 100\n\rUsted ingresó: " + numero,
                            html: true,
                            type: "warning",
                            confirmButtonColor: "#d51b23"
                        });
                        $(this).val(0);
                    }
                });
                $('#inputValor').on('keypress', function(e) {
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

            $("#tipoItem").on('change', function() {
                var index = $(this).prop('selectedIndex');
                if (index == 1) {
                    $('#divTipeC').show();
                }
                else {
                    $('#divTipeC').hide();
                }
            });

            $('#save').on('click', function() {
                var agg = $(this).parent().parent().parent().parent().attr("id");
                var parent = $(this).parent().parent().parent().parent().parent().attr("id");
                parent = parent.split('_')[1];
                createElement(agg, parent);
            });

            $('#cancel').on('click', function() {
                var id = getCourseid();
                loadCategories(id);
            });
        }, 400);
    });


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
            url: "../managers/grade_categories/grade_categories_processing.php",
            success: function(msg) {
                $("#mymodalbody").html(msg);
            },
            dataType: "text",
            cache: "false",
            error: function(msg) {
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
                    url: "../managers/grade_categories/grade_categories_processing.php",
                    success: function(msg) {
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
                    error: function(msg) {
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
                    url: "../managers/grade_categories/grade_categories_processing.php",
                    success: function(msg) {
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
                    error: function(msg) {
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
                    url: "../managers/grade_categories/grade_categories_processing.php",
                    success: function(msg) {
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
                    error: function(msg) {
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

  
});
