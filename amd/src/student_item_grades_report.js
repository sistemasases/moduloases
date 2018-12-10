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
             * @param est_items_perdidos number  Cantidad de estudiantes con uno o más items perdidos
             * @param est_mas_items_perdidos_que_ganados number Estudiantes con más items perdidos que ganados
             * @param cantidad_estudiantes number Cantidad de estudiantes
             *  que ganados con respecto al total de estudiantes
             * @constructor
             */
            var ResumeReport = /* @class */  (function() {
                function ResumeReport(est_items_perdidos, est_mas_items_perdidos_que_ganados, cantidad_estudiantes) {
                    this.est_items_perdidos = est_items_perdidos? est_items_perdidos: 0;
                    this.cantidad_estudiantes = cantidad_estudiantes? cantidad_estudiantes: 0;
                    this.est_mas_items_perdidos_que_ganados =
                        est_mas_items_perdidos_que_ganados? est_mas_items_perdidos_que_ganados : 0;
                }

                /**
                 * Return the total resume report from data given
                 * @param data array Data instances
                 * @returns {ResumeReport}
                 */
                ResumeReport.return_total_report = function(data) {
                    var resumeReport = new ResumeReport();
                    resumeReport.cantidad_estudiantes = data.length;
                    for( var _i = 0, data_1 = data; _i < data_1.length; _i++) {
                        if(data[_i].cantidad_items_perdidos>=1) {
                            resumeReport.est_items_perdidos++;
                        }
                        if(data[_i].cantidad_items_perdidos > data[_i].cantidad_items_ganados) {
                            resumeReport.est_mas_items_perdidos_que_ganados++;
                        }
                    }
                    return resumeReport;
                };
                return ResumeReport;
            }());
            /** go to ficha general on click **/
            $(document).on('click', '#tableStudentItemGradesReport tbody tr td', function () {
                var pagina = "student_profile.php";
                var table = $("#tableStudentItemGradesReport").DataTable();
                var colIndex = table.cell(this).index().column;
                /* Las columnas (de la 1 a la 4 -- index desde 0) son: numero documento, codigo,
                 apellidos y nombre son links a la ficha estudiante*/
                if (colIndex>=1 && colIndex <=4 ) {
                    var url = pagina + location.search + "&student_code=" + table.cell(table.row(this).index(), 0).data().codigo;
                    var win = window.open(url, '_blank');
                    win.focus();
                }
            });

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
                    paging: false,
                    columns: columns,
                    searching: false,
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

                    row.child('<table align="center" id="student_item_grades_explicit'+student_id+'"></table>').show();
                    $("#student_item_grades_explicit"+student_id).DataTable(dataTable);
                    }

                ).fail(function(err) {
                    tr.removeClass('shown');
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
                    function(dataTable) {
                        $(table_id).html(''); // remove loading image gift
                        var resumeReport = ResumeReport.return_total_report(dataTable.data);
                        templates.render('block_ases/student_item_grades_report_summary', {resume_report: resumeReport})
                            .then(function(html, js) {
                                templates.appendNodeContents('.student_item_grades_report .summary', html, js);
                            }).fail(function(ex) {
                        });
                        console.log(resumeReport);
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



        }

    };

});