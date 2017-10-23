requirejs(['jquery', 'bootstrap', 'sweetalert'], function($) {
    var grade;

    $(document).ready(function() {
        var pilos = getIDs();
        deleteNoPilos(pilos);
        bloquearTotales();
        if($('.gradingerror').length != 0){
            new_page = location.origin + "/moodle/grade/report/grader/index.php?id="+getCourseid();
            swal({
                title: "Redireccionando página.",
                text: "Debido al proceso de actualización del campus virtual, el Docente encargado del curso debe realizar este paso.\n Una vez realizado por favor cerrar la ventana y volver a seleccionar su curso en el listado",
                type: "warning",
                showCancelButton: false,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Continuar",
                closeOnConfirm: false
              },
              function(){
                location.href = new_page;
              });
        }
    });



    $(document).on('blur','.text',function(){
        if(validateNota($(this))){
            var id = $(this).attr('id').split("_");
            var userid = id[1];
            var itemid = id[2];
            var nota = $(this).val();
            var curso = getCourseid();
            var data ={user: userid,item: itemid, finalgrade: nota,course: curso};
            $.ajax({
            type: "POST",
            data: data,
            url: "../managers/grade_categories/grade_categories_processing.php",
            async: false,
            success: function(msg)
            {
                
                var updGrade = msg.nota;

                if(updGrade == true){
                    console.log("Nota actualizada");

                    if(nota<3){
                        var menMonitor = msg.monitor;
                        var menPracticante = msg.practicante;
                        var menProfesional = msg.profesional;

                        if(menMonitor == true){
                            console.log("mensaje al monitor enviado correctamente");
                        }else{
                            console.log("error monitor");
                            swal('Error',
                         'Error al enviar correo al monitor',
                         'error');
                        }

                        if(menPracticante == true){
                            console.log("mensaje al practicante enviado correctamente");
                        }else{
                            console.log("error practicante");
                            swal('Error',
                         'Error al enviar correo al practicante',
                         'error');
                        }

                        if(menProfesional == true){
                            console.log("mensaje al profesional enviado correctamente");
                        }else{
                            console.log("error profesional");
                            swal('Error',
                         'Error al enviar correo al profesional',
                         'error');
                        }
                    }
                }else{
                    swal('Error',
                         'Error al actualizar la nota',
                         'error');
                }

            },
            dataType: "json",
            cache: "false",
            error: function(msg){console.log(msg)},
            });
        };
    })

    $(document).on('focus','.text',function(){
        grade = $(this).val();
        //console.log(grade);
    })

    $(document).on('keypress','.text', function(e){
        
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


    $(document).on('click','.reload', function(){
        
        location.reload();
    });


    function validateNota(selector){
        var bool = false;
        var nota = selector.val();

        if(nota>5 || nota <0){
            swal({
                title: "Ingrese un valor valido, entre 0 y 5. \n\rUsted ingresó: " + nota,
                html: true,
                type: "warning",
                confirmButtonColor: "#d51b23"
            });
            selector.val(grade);
            bool = false;
        }else if(nota == '' && grade != ''){
            selector.val('0');
            bool = true;
        }else if(nota == '' && grade == '' || nota == grade){
            bool = false;
        }else{
            bool = true;
        }

        

        return bool;
    }

    function bloquearTotales(){
        $('.cat').each(function(){
            var input = $(this).children().next('.text'); 
            input.attr('disabled',true);
            input.css('font-weight','bold')
        })

        $('.course').each(function(){
            var input = $(this).children().next('.text');
            input.attr('disabled',true);
            input.css('font-weight','bold');
            input.css('font-size',16)
        })

        $('.header').children().each(function(){
            $(this).removeAttr('href');
        })
    }

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
                            //window.open(url, '_blank');
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
        var maxweight = $(this).prev().attr('id');
        if(maxweight <= 0){
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
                    var maxPeso= parseInt($(this).parent().parent().parent().parent().children().next('.maxweight').attr('id'));
                    //si no cumple con la restriccion de estar entre 0 y 100 entonces se realiza el aviso y se pone el valor en 0
                    if (numero < 0 || numero > maxPeso) {
                        swal({
                            title: "El valor debe estar entre 0 y el peso máximo: "+maxPeso,
                            text: "\n\rUsted ingresó: " + numero,
                            html: true,
                            type: "warning",
                            confirmButtonColor: "#d51b23"
                        });
                        $(this).val('');
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
