/**
 * @module block_ases/grade_categories
 */


define(['jquery', 'block_ases/bootstrap', 'block_ases/datatables.net', 'block_ases/datatables.net-buttons', 'block_ases/buttons.flash', 'block_ases/jszip', 'block_ases/pdfmake', 'block_ases/buttons.html5', 'block_ases/buttons.print', 'block_ases/sweetalert', 'block_ases/jqueryui'], function ($, bootstrap, datatablesnet, datatablesnetbuttons, buttonsflash, jszip, pdfmake, buttonshtml5, buttonsprint, sweetalert, jqueryui) {

    return {

        init: function () {

            $(document).ready(function () {
                $("#teachers").DataTable();
            });

            $(document).on('click', '.desplegate', function () {
                var parent = $(this).parent().parent();
                var id = parent.attr('id').split("_")[1];
                var curso = '#curso_' + id;
                var profe = '#profe_' + id;

                if (parent.hasClass('cerrado')) {
                    $(this).children().removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-down');
                    $(curso).appendTo(profe);
                }
                else {
                    $(this).children().removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-left');
                    $(curso).appendTo('#courses_info');
                }
                parent.toggleClass('cerrado');

            });

            $(document).on('click', '.ir_curso', function () {
                var id_curso = $(this).attr('id');
                var url = 'global_grade_book.php' + location.search + '&id_course=' + id_curso;
                window.open(url, '_blank');
            });



        }
    }
});
