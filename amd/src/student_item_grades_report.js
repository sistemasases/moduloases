/**
 * Student item grades report
 * @module amd/src/course_and_techar_report
 * @author Luis Gerardo Manrique Cardona
 * @copyright 2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @see https://datatables.net/examples/api/multi_filter_select.html
 */
define([
    'jquery',
    'core/notification',
    'core/templates',
    'block_ases/jquery.dataTables'
], function($, notification, templates){

    return {
        init: function () {
            $(document).ready(function () {

            });
        },

        load_report: function (data) {
            /**
             * Class Resume Report
             * @param cursos_al_un_est_ases Cantidad cursos con almenos un estudiante ases matriculado
             * @param cursos_al_item_calif Cantidad de cursos ases con almenos un item calificado
             * @param cursos_al_un_item Cantidad de cursos ases con almenos un item
             * @constructor
             */
            var table_id = '#tableStudentItemGradesReport';
            $(table_id).html('<img class="icon-loading" src="../icon/loading.gif"/>'); // loader image
            var course_id = data.course_id;
            var filter_columns = null;
            var instance_id = data.instance_id;
            var grade_table_border_color = '#cccccc';
            var table = null;
            /**
             * Data: {
             *     table,
             *     course_id,
             *     instance_id
             * }
             *
             * DataTable:
             * {
             *     bsort,
             *     columns,
             *     data,
             *     language,
             *     order
             * }
             * DataTable.data[]:
             * {
             *     curso,
             *     curso_id,
             *     nombre_profesor,
             *     estudiantes_perdiendo,
             *     estudiantes_ganando,
             *     estudiantes_sin_ninguna_nota,
             *     cantidad_estudiantes_ases,
             *     items_con_almenos_una_nota,
             *     cantidad_items
             *     critica,
             * }
             *
             *
             */


            function get_filter_column_indexes(filter_column_names, column_names) {
                var filter_column_indexes = [];
                var i;
                for(i = 0; i < column_names.length; i++) {
                    if(filter_column_names.includes(column_names[i])) {
                        filter_column_indexes.push(i);
                    }

                }
                return filter_column_indexes;
            }
            function add_column_description(columns) {
                // Añadir descripción a las columnas
                $(table_id +' th').each(function(index) {
                    $(this).attr('title', columns[index].description);
                });
            }


            /**
             * Add the controls for extra info in each course, than display the specific student notes
             * when the user open the view in the first column buttons
             */
            function add_extra_course_info_controls() {
                $(table_id + ' tbody').on('click', 'td.details-control', function () {

                    var tr = $(this).closest('tr');
                    var row = table.row(tr);

                    if (row.child.isShown()) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                    }
                    else {
                        // Open this row
                        //  row.child('<img class="icon-loading" src="../icon/loading.gif"/>').show(); // loader image (is replaced when the data arrive)
                        alert('khe');
                        row.child('<br>').show();

                        tr.addClass('shown');
                    }
                });
            }
            function init_datatable () {

                $.ajax({
                    method: "GET",
                    url: '../managers/student_grades/student_item_grades_report_api.php/' + instance_id,
                    dataType: 'json'

                }).done(
                    function (dataTable){
                        $(table_id).html('');
                        table = $(table_id).DataTable(
                            {
                                data: dataTable.data,
                                bsort: dataTable.bsort,
                                columns: dataTable.columns,
                                language: dataTable.language,
                                order: dataTable.order,
                                initComplete: function () {
                                    if (!filter_columns || filter_columns.length==0) {
                                        return;
                                    }
                                    var column_names = dataTable.columns.map(column => column.name ? column.name : null);
                                    var filter_column_indexes = get_filter_column_indexes(filter_columns, column_names);
                                    this.api().columns(filter_column_indexes).every(function () {
                                        var column = this;


                                        var select = $('<select><option value=""></option></select>')
                                            .appendTo($(column.header()))
                                            .on('change', function () {
                                                var val = $.fn.dataTable.util.escapeRegex(
                                                    $(this).val()
                                                );

                                                column
                                                    .search(val ? '^' + val + '$' : '', true, false)
                                                    .draw();
                                            });

                                        column.data().unique().sort().each(function (d, j) {
                                            select.append('<option value="' + d + '">' + d + '</option>');
                                        });
                                    });
                                }
                            }
                        );
                        add_column_description(dataTable.columns);
                        add_extra_course_info_controls();
                    }

                ).fail(
                    function(error) {
                        console.log(error);
                    }
                );

            }
            init_datatable();



        },

    };

});