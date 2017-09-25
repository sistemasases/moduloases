<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> db_management
requirejs(['jquery', 'bootstrap', 'sweetalert', 'datatables.net', 'datatables.net-buttons', 'buttons.flash', 'jszip'
, 'pdfmake', 'vfs_fonts', 'buttons.html5', 'buttons.print'], function($) {

    $(document).ready(function() {
        $("#teachers").DataTable();
    });

    $(document).on('click', '.desplegate', function() {
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

    $(document).on('click', '.ir_curso', function() {
        var id_curso = $(this).attr('id');
        var url = 'calificador.php' + location.search + '&id_course=' + id_curso;
        window.open(url, '_blank');
    });

});
<<<<<<< HEAD
=======
requirejs(['jquery', 'bootstrap', 'sweetalert', 'datatables.net', 'datatables.net-buttons', 'buttons.flash', 'jszip'
, 'pdfmake', 'vfs_fonts', 'buttons.html5', 'buttons.print'], function($) {

    $(document).ready(function() {
        $("#teachers").DataTable();
    });

    $(document).on('click', '.desplegate', function() {
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

    $(document).on('click', '.ir_curso', function() {
        var id_curso = $(this).attr('id');
        var url = 'calificador.php' + location.search + '&id_course=' + id_curso;
        window.open(url, '_blank');
    });

});
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
=======
>>>>>>> db_management
