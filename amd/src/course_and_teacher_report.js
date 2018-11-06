/**
 * Course and teacher report
 * @module amd/src/course_and_techar_report
 * @author Luis Gerardo Manrique Cardona
 * @copyright 2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @see https://datatables.net/examples/api/multi_filter_select.html
 */
define(['jquery', 'core/notification', 'block_ases/global_grade_book', 'block_ases/jquery.dataTables' ], function($, notification, gg_b){

    return {
        init: function () {
            $(document).ready(function () {

            });
        },

        load_report: function (data) {
            var dataTable = data.table;
            var course_caller_id = data.course_caller_id;
            var course_id = data.course_id;
            var instance_id = data.instance_id;
            /**
             * Data: {
             *     table,
             *     mdl_course_caller_id
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
             */
            var grade_table_border_color = '#cccccc';
            function fix_grades_table_styles() {
                console.log($('.gradeparent').parent());
                $('.gradeparent').parent().css('background', grade_table_border_color);
            }
            var filter_columns = ['critica'];
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
            var table = $("#tableFinalgradesReport").DataTable(
                {
                    data: dataTable.data,
                    bsort: dataTable.bsort,
                    columns: dataTable.columns,
                    language: dataTable.language,
                    order: dataTable.order,
                    initComplete: function () {
                        var column_names = dataTable.columns.map(column => column.name? column.name: null);
                        var filter_column_indexes = get_filter_column_indexes(filter_columns, column_names );
                            this.api().columns(filter_column_indexes).every( function () {
                            var column = this;


                            var select = $('<select><option value=""></option></select>')
                                .appendTo( $(column.header()))
                                .on( 'change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );

                                    column
                                        .search( val ? '^'+val+'$' : '', true, false )
                                        .draw();
                                } );

                            column.data().unique().sort().each( function ( d, j ) {
                                select.append( '<option value="'+d+'">'+d+'</option>' );
                            } );
                        } );
                    }
                }
            );
            function get_course_html( row , callback, tr ) {
                var data = row.data();
                var post_info = {
                    function: 'grade_table',
                    params: {
                        course_id: data.curso_id,
                        course_caller_id: course_caller_id,
                        instance_id: instance_id
                    }
                };
                $.ajax({
                        method: "POST",
                        url: '../managers/course_and_teacher_report/course_and_teacher_report_api.php',
                        data: post_info,
                        dataType: 'html'
                    }
                ).fail(
                    function (response) {
                        notification.alert('Error, debe informar a un programador, adjunte esta información', response.responseText, 'Aceptar');
                        tr.removeClass('shown');
                    }
                ).done(
                    function (response) {
                        callback(row, response);
                    }
                );
            }
            $('#tableFinalgradesReport tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    // Open this row
                    var callback = (row, data_html) => {
                        row.child( data_html ).show();
                        /* Fix styles for ases table*/
                        gg_b.read_only_view_styles();
                        fix_grades_table_styles();
                    };
                    get_course_html(row, callback, tr);
                    /* Fix styles and view for grades loaded */

                    tr.addClass('shown');
                }
            } );
            // Añadir descripción a las columnas
            $("th").each(function(index) {
                if(dataTable.columns[index]) {
                    dataTable.columns[index].description ? $(this).attr('title', dataTable.columns[index].description) : null;
                }

            });
        },

    };

});