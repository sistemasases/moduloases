/**
 * @module block_ases/global_grade_book
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui'], function ($, bootstrap, sweetalert, jqueryui) {

    return {

        init: function () {
            var grade;

            $(document).ready(function () {
                var pilos = getIDs();
                $(".text").prop('disabled', true);
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

            $(document).on('focus', '.text', function () {
                grade = $(this).val();
                //console.log(grade);
            })

            $(document).on('click', '.reload', function () {

                location.reload();
            });
            
            function bloquearTotales() {
                $('.cat').each(function () {
                    var input = $(this).children().next('.text');
                    //input.attr('disabled', true);
                    input.css('font-weight', 'bold')
                })

                $('.course').each(function () {
                    var input = $(this).children().next('.text');
                    //input.attr('disabled', true);
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

