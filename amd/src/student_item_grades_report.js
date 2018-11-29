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
    'block_ases/ases_jquery_datatable',
    'core/notification',
    'core/templates',
    'block_ases/jquery.dataTables'
], function($, ajdt, notification, templates, ){

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
            var filter_columns = null;
            var instance_id = data.instance_id;
            var grade_table_border_color = '#cccccc';
            var table = null;
            /**
             * Data: {
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
             *     num_doc,
             *     lastname,
             *     firstname,
             *     mdl_user_id,
             *     cantidad_items_ganados,
             *     codigo,
             *     cantidad_items_perdidos
             * }
             *
             *
             */
            function define_student_courses_detail_datatable(data) {
                var columns = [
                    {
                    title: 'Código',
                    name: 'codigo_asignatura',
                    data: 'codigo_asignatura',
                    description: 'Identificador de la asignatura en formato Sede-Codigo-Grupo'
                    }  ,
                    {
                        title: 'Nombre Asignatura',
                        name: 'nombre_asignatura',
                        data: 'nombre_asignatura',
                        description: 'Nombre completo de la asignatura'
                    },
                    {
                        title: 'Profesor',
                        name: 'nombre_profesor',
                        data: 'nombre_profesor',
                        description: 'Nombre completo de el profesor'
                    },
                    {
                        title: 'Notas',
                        name: 'notas',
                        data: 'notas',
                        description: 'Identificador de la asignatura en formato Sede-Codigo-Grupo'
                    },
                    {
                        title: 'Nota Final',
                        name: 'nota_final',
                        data: 'nota_final',
                        description: 'Nota final de la asignatura calculada con las categorias de notas terminadas'
                    }

                ];
                var dataTable = {
                    data: data,
                    bsort: 0,
                    columns: columns,
                    language: ajdt.common_lang_config(),
                    order: 0
                };
                return dataTable;
            }

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

            function get_student_item_grades_explicit(row, tr) {
                var data = row.data();
                var student_id = data.mdl_user_id;
                var url =  '../managers/student_grades/student_item_grades_report_api.php/student_grades/item_summary/' + student_id;
                $.ajax({
                    method: 'get',
                    url: url
                }).done(function(data) {
                    var dataTable = define_student_courses_detail_datatable(data);

                    row.child('<table id="student_item_grades_explicit'+student_id+'"></table>').show();
                    $("#student_item_grades_explicit"+student_id).DataTable(dataTable);
                    console.log(url);
                    }

                ).fail(function(err) {
                    tr.removeClass('shown');
                    console.log(url);
                    console.log(err);
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
                        row.child('<img class="icon-loading" src="../icon/loading.gif"/>').show(); // loader image (is replaced when the data arrive)
                        callback = (html, row) => {
                            row.child(html).show();
                        };
                        get_student_item_grades_explicit(row, tr);
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