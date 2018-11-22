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
define([
    'jquery',
    'core/notification',
    'block_ases/global_grade_book',
    'core/templates',
    'block_ases/jquery.dataTables'
], function($, notification, gg_b, templates){

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
            $("#tableFinalgradesReport").html('<img class="icon-loading" src="../icon/loading.gif"/>'); // loader image
            var ResumeReport = /* @class */ (function   () {
                function ResumeReport(cursos_al_un_est_ases,
                                      cursos_al_item_calif,
                                      cursos_al_un_item) {
                    this.cursos_al_un_est_ases = cursos_al_un_est_ases ? cursos_al_un_est_ases : 0;
                    this.cursos_al_item_calif = cursos_al_item_calif ? cursos_al_item_calif : 0;
                    this.cursos_al_un_item = cursos_al_un_item ? cursos_al_un_item : 0;
                }
                ResumeReport.return_critic_and_no_critic_report = function(data) /* return {critic_report: ResumeReport, no_critic_report: ResumeReport} */  {
                    var no_critic_report = new ResumeReport();
                    var critic_report = new ResumeReport();
                    for( var _i = 0, data_1 = data; _i < data_1.length; _i++) {
                        var report_item = data_1[_i];
                        if(report_item.items_con_almenos_una_nota > 0) {
                            switch (report_item.critica) {
                                case 'SI': critic_report.cursos_al_item_calif++; break;
                                case 'NO': no_critic_report.cursos_al_item_calif++; break;
                            }
                        }
                        if(report_item.cantidad_items > 0) {
                            switch (report_item.critica) {
                                case 'SI': critic_report.cursos_al_un_item++; break;
                                case 'NO': no_critic_report.cursos_al_un_item++; break;
                            }
                        }
                        if(report_item.cantidad_estudiantes_ases > 0) {
                            switch (report_item.critica) {
                                case 'SI': critic_report.cursos_al_un_est_ases++; break;
                                case 'NO': no_critic_report.cursos_al_un_est_ases++; break;
                            }
                        }
                    }
                    return {critic_report: critic_report, no_critic_report: no_critic_report};
                };
                return ResumeReport;
            }());
            var resume_report = {}/*{critic_report: ResumeReport, no_critic_report: ResumeReport}*/;
            var course_id = data.course_id;
            var instance_id = data.instance_id;
            var grade_table_border_color = '#cccccc';
            var filter_columns = ['critica', 'nombre_profesor'];
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
             * ResumeReport {
             *     cantidad_cursos_con_almenos_un_ases,
             *     cantidad_cursos_almenos_un_item_calif,
             *     cantidad_cursos_almenos_un_item
             * }
             *
             */

            function fix_grades_table_styles() {
                $('.gradeparent').parent().css('background', grade_table_border_color);
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
               $('#tableFinalgradesReport th').each(function(index) {
                   $(this).attr('title', columns[index].description);
               });
            }


            /**
             * Add the controls for extra info in each course, than display the specific student notes
             * when the user open the view in the first column buttons
             */
            function add_extra_course_info_controls() {
                $('#tableFinalgradesReport tbody').on('click', 'td.details-control', function () {

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
                        var callback = (row, data_html) => {
                            row.child(data_html).show();
                            /* Fix styles for ases table*/
                            gg_b.read_only_view_styles();
                            fix_grades_table_styles();
                        };
                        get_course_html(row, callback, tr);
                        /* Fix styles and view for grades loaded */

                        tr.addClass('shown');
                    }
                });
            }
            function init_datatable () {

                $.ajax({
                    method: "GET",
                    url: '../managers/course_and_teacher_report/course_and_teacher_report_api.php/' + instance_id,
                    dataType: 'json'

                }).done(
                    function (dataTable){
                    resume_report = ResumeReport.return_critic_and_no_critic_report(dataTable.data);
                    templates.render('block_ases/course_and_teacher_report_summary', {resume_report: resume_report})
                        .then(function(html, js) {
                            templates.appendNodeContents('.course_and_teacher_report .summary', html, js);
                        }).fail(function(ex) {
                    });
                    table = $("#tableFinalgradesReport").DataTable(
                        {
                            data: dataTable.data,
                            bsort: dataTable.bsort,
                            columns: dataTable.columns,
                            language: dataTable.language,
                            order: dataTable.order,
                            initComplete: function () {
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

            function get_course_html( row , callback, tr ) {
                var data = row.data();
                var post_info = {
                    function: 'grade_table',
                    params: {
                        course_id: data.curso_id, // Specific course selected
                        course_caller_id: course_id, // Current course of block ASES instance
                        instance_id: instance_id
                    }
                };
                $.ajax({
                        method: "POST",
                        url: '../managers/grades/grades_api.php',
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


        },

    };

});