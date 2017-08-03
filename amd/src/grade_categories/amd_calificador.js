requirejs(['jquery', 'bootstrap', 'sweetalert', 'buttons.flash'], function($) {

    $(document).ready(function() {

    });

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
        newDiv.load("../grader/wizard_categories/style/wizard_form.html");
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
                console.log(msg);
                $("#mymodalbody").html(msg);
            },
            dataType: "text",
            cache: "false",
            error: function(msg) {
                console.log(msg);
            },
        });
    }
  
});
