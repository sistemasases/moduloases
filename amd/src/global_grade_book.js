/**
 * @module block_ases/global_grade_book
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui'], function ($, bootstrap, sweetalert, jqueryui) {

    return {

        init: function () {
            var grade;

            $(document).ready(function () {
                var pilos = getIDs();
                deleteNoPilos(pilos);
                bloquearTotales();
                if ($('.gradingerror').length != 0) {
                    new_page = location.origin + "/moodle/grade/report/grader/index.php?id=" + getCourseid();
                    swal({
                        title: "Redireccionando página.",
                        text: "Debido al proceso de actualización del campus virtual se debe realizar este paso.\nSOLO EL DOCENTE ENCARGADO DEL CURSO PUEDE \n Una vez realizado por favor cerrar la ventana y volver a seleccionar su curso en el listado",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Continuar",
                        closeOnConfirm: false
                    },
                        function () {
                            location.href = new_page;
                        });
                }
            });



            $(document).on('blur', '.text', function () {
                if (validateNota($(this))) {
                    var id = $(this).attr('id').split("_");
                    var userid = id[1];
                    var itemid = id[2];
                    var nota = $(this).val();
                    var curso = getCourseid();
                    var data = { user: userid, item: itemid, finalgrade: nota, course: curso };
                    $.ajax({
                        type: "POST",
                        data: data,
                        url: "../managers/grade_categories/grader_processing.php",
                        async: false,
                        success: function (msg) {

                            var updGrade = msg.nota;

                            if (updGrade == true) {
                                console.log("Nota actualizada");

                                if (nota < 3) {
                                    var menMonitor = msg.monitor;
                                    var menPracticante = msg.practicante;
                                    var menProfesional = msg.profesional;

                                    if (menMonitor == true) {
                                        console.log("mensaje al monitor enviado correctamente");
                                    } else {
                                        console.log("error monitor");
                                        swal('Error',
                                            'Error al enviar correo al monitor',
                                            'error');
                                    }

                                    if (menPracticante == true) {
                                        console.log("mensaje al practicante enviado correctamente");
                                    } else {
                                        console.log("error practicante");
                                        swal('Error',
                                            'Error al enviar correo al practicante',
                                            'error');
                                    }

                                    if (menProfesional == true) {
                                        console.log("mensaje al profesional enviado correctamente");
                                    } else {
                                        console.log("error profesional");
                                        swal('Error',
                                            'Error al enviar correo al profesional',
                                            'error');
                                    }
                                }
                            } else {
                                swal('Error',
                                    'Error al actualizar la nota',
                                    'error');
                            }

                        },
                        dataType: "json",
                        cache: "false",
                        error: function (msg) { console.log(msg) },
                    });
                };
            })

            $(document).on('focus', '.text', function () {
                grade = $(this).val();
                //console.log(grade);
            })

            $(document).on('keypress', '.text', function (e) {

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


            $(document).on('click', '.reload', function () {

                location.reload();
            });


            function validateNota(selector) {
                var bool = false;
                var nota = selector.val();

                if (nota > 5 || nota < 0) {
                    swal({
                        title: "Ingrese un valor valido, entre 0 y 5. \n\rUsted ingresó: " + nota,
                        html: true,
                        type: "warning",
                        confirmButtonColor: "#d51b23"
                    });
                    selector.val(grade);
                    bool = false;
                } else if (nota == '' && grade != '') {
                    selector.val('0');
                    bool = true;
                } else if (nota == '' && grade == '' || nota == grade) {
                    bool = false;
                } else {
                    bool = true;
                }



                return bool;
            }

            function bloquearTotales() {
                $('.cat').each(function () {
                    var input = $(this).children().next('.text');
                    input.attr('disabled', true);
                    input.css('font-weight', 'bold')
                })

                $('.course').each(function () {
                    var input = $(this).children().next('.text');
                    input.attr('disabled', true);
                    input.css('font-weight', 'bold');
                    input.css('font-size', 16)
                })

                $('.header').children().each(function () {
                    $(this).removeAttr('href');
                })
            }


            function deleteNoPilos(pilos) {
                $("#user-grades").children().children().each(function () {
                    if ($(this).attr('data-uid') != undefined) {
                        if (!isPilo($(this).attr('data-uid'), pilos)) {
                            $(this).remove();
                        } else {
                            $(this).children('th').children().each(function () {
                                $(this).removeAttr('href');
                                $(this).click(function () {
                                    var id = $(this).parent().parent().attr('data-uid');
                                    var code = $('#idmoodle_' + id).attr('data-code');
                                    var pagina = "student_profile.php";
                                    var url = pagina + location.search + "&student_code=" + code;
                                    //window.open(url, '_blank');
                                })
                            });
                        }
                    };
                })
            }

            function isPilo(id, pilos) {
                for (var i = 0; i < pilos.length; i++) {
                    if (pilos[i].split("_")[1] === id) {
                        return true;
                    }
                }
                return false;
            }

            function getIDs() {
                var pilos = new Array;
                $("#students-pilos").children().each(function () {
                    pilos.push($(this).attr("id"));
                })
                return pilos;
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
 

        }
    }
});

