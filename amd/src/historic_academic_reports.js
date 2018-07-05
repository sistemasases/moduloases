/**
 * Academic report management
 * @module amd/src/historic_academic_reports
 * @author Camilo José Cruz rivera
 * @copyright 2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/jquery.dataTables', 'block_ases/sweetalert2'], function ($) {

    return {
        /**
         *
         */

        init: function () {
            $(document).ready(function () {

                ////Controles para la tabla historica academica
                $(document).on('change', '#tableResultStudent thead tr th select', function () {
                    var table = $("#tableResultStudent").DataTable();

                    var colIndex = $(this).parent().index() + 1;
                    var selectedText = $(this).parent().find(":selected").text();
                    table.columns(colIndex - 1).search(this.value).draw();
                });

                $(document).on('click', '#tableResultStudent tbody tr td', function () {
                    var table = $("#tableResultStudent").DataTable();
                    var colIndex = table.cell(this).index().column;

                    if (colIndex == 10) {
                        if (table.cell(table.row(this).index(), 10).data() == 'SI') {
                            var codigo = table.cell(table.row(this).index(), 2).data();
                            var programa = table.cell(table.row(this).index(), 6).data();
                            var semestre = table.cell(table.row(this).index(), 5).data();
                            checkEstimulo(codigo, programa, semestre);
                        }
                    }
                });
            });
        },
        load_table_students: function (data) {

            $("#div_table_students").html('');
            $("#div_table_students").fadeIn(1000).append('<table id="tableResultStudent" class="table"' +
                ' cellspacing="0" width="100%"><thead> </thead></table>');

            $("#tableResultStudent").DataTable(data);

        },
        load_total_table: function (data) {

            $("#div_totales_historic").html('');
            $("#div_totales_historic").fadeIn(1000).append('<table id="tableResultTotal" class="table"' +
                ' cellspacing="0" width="100%"><thead> </thead></table>');

            $("#tableResultTotal").DataTable(data);
        }
    };

    function checkEstimulo(codigo, programa, semestre) {
        $.ajax({
            type: "POST",
            data: {
                codigo: codigo,
                programa: programa,
                type: "check_estimulo",
                semestre: semestre,
            },
            url: "../managers/historic_academic_reports/historic_academic_reports_processing.php",
            success: function (msg) {
                console.log(msg);
                swal({
                    title: "Estímulo Académico",
                    type: "info",
                    text: msg,
                    html: true,
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Cerrar",
                    closeOnConfirm: true
                });
            },
            dataType: "text",
            cache: "false",
            error: function (msg) {
                console.log(msg);
            },
        });
    }
});