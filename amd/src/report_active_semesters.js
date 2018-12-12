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
        init: function (data) {
            var instance_id = data.instance_id;
            var table = null;
            /**
             * Data: {
             *     instance_id
             * }
             *
             * DataFromAPI: {
             *     semesters, # Array with the semester names
             *     dataTable: DataTable
             * }
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
             *     codigo,
             *     ases_user_id,
             *     nombre,
             *     [active_semesters]: [semesters codes, example: [2016A, 2016B ...]],
             * }
             *
             */
            /**
             * All cells than have as data 'NO' should have distinct class
             */
            var CODIGO_COLUMN = 'codigo';
            var NOMBRE_COLUMN= 'nombre';
            var NUM_DOC_COLUMN = 'num_doc';
            var tfoot_first_row_title_prefix = 'Total activos SRA';
            var known_columns = [CODIGO_COLUMN, NOMBRE_COLUMN, NUM_DOC_COLUMN];
            /** go to ficha general on click **/
            $(document).on('click', '#tableActiveSemesters tbody tr td', function () {
                var pagina = "student_profile.php";
                var table = $("#tableActiveSemesters").DataTable();
                var colIndex = table.cell(this).index().column;
                /* Las columnas (de la 0 a la 2 -- index desde 0) son: codigo, nombre,
                 y numero de documento, estos son links a la ficha estudiante*/
                if (colIndex>=0 && colIndex <=2) {
                    var url = pagina + location.search + "&student_code=" + table.cell(table.row(this).index(), 0).data();
                    var win = window.open(url, '_blank');
                    win.focus();
                }
            });

            var semesters = []; /* Array of strings with the semesters names, ej. ['2015A', '2015B'...]*/
            /** Resume of active semester of all students, of course each property of this object
             * correspond to one semester (see semesters)
             */
            var ResumeReport = (function () {
               function ResumeReport(semesters) {
                   semesters.forEach((value) => {
                        this[value] = 0;
                   });
               }
               ResumeReport.prototype.init_from_data = function init_from_data(data /*DataTable.data*/, semesters) {
                   data.forEach( (item) => {
                      semesters.forEach( semester => {
                         if(item[semester] === 'SI') {
                             this[semester]++;
                         }
                      }) ;
                   });
               };
               return ResumeReport;
            }());
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
            function init_tfoot_from_report(resume_report /* Resume Report */, semesters, cohort_name) {
                /* Init first cell of tfoot*/
                $('#tableActiveSemesters tfoot th')[0].textContent= tfoot_first_row_title_prefix + ' ' + cohort_name;
                /* In column name, and num_doc nothing should be was showed*/
                $('#tableActiveSemesters tfoot th.'+NOMBRE_COLUMN).html('');
                $('#tableActiveSemesters tfoot th.'+NUM_DOC_COLUMN).html('');
                semesters.forEach(function(semester) {
                    $('#tableActiveSemesters tfoot th.'+semester).html(resume_report[semester]);
                });
            }
            var resume_report /* ResumeReport */ = null; // I no initialized for now

            /**
             * Validate the given columns with a known columns
             * @param columns array of string with column names
             * @param known_columns array of string with known column names
             * @returns {boolean}
             */
            function validate_known_columns(columns, known_columns) {
                /* Check tan all the knowed columns are in the given columns*/
                return columns.filter(value => -1 !== known_columns.indexOf(value)).length === known_columns.length;
            }
            function init_datatable (cohort_id, cohort_name) {
                var url = '../managers/report_active_semesters/report_active_semesters_api.php/' + instance_id;
                var post_info = {
                    function: 'data_table',
                    params: {
                        instance_id: instance_id,
                        cohort_id: cohort_id
                    }
                };
                $.ajax({
                    method: "POST",
                    url: url,
                    data: post_info,
                    dataType: 'json'
                }).done(
                    function (dataFromApi /*instance of DataFromAPI*/){
                        var dataTable = dataFromApi.dataTable;
                        semesters = dataFromApi.semesters;
                        var column_names = dataTable.columns.map( column => column.name );
                        resume_report = new ResumeReport(semesters);
                        resume_report.init_from_data(dataTable.data, semesters);
                        console.log(resume_report['2015A']);
                        /* Put a class to each cell than have the 'SI' value, see
                        * https://datatables.net/reference/option/rowCallback */
                        dataTable.rowCallback =  function(row, data, index) {
                            $(semesters).each(
                                function (index_, value) {
                                    if (data[value] === "SI") {
                                        $("td." + value , row).addClass("active_semester");
                                    }
                                }
                            );
                        };
                        dataTable.initComplete = function () {
                            /*Add filter to column headers*/
                            var column_names = dataTable.columns.map(column => column.name ? column.name : null);
                            /* Indexes of the semester columns */
                            var filter_column_indexes = get_filter_column_indexes(semesters, column_names);
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

                        /* After each cohort change remove the datatable and put a empty table*/
                        if ( $.fn.dataTable.isDataTable( '#tableActiveSemesters' ) ) {
                            $("#div_table_report").html('');
                            $("#div_table_report").fadeIn(1000).append('<table id="tableActiveSemesters" class="table" cellspacing="0" width="100%"></table>');

                        }
                        table = $("#tableActiveSemesters").DataTable(
                            dataTable
                        );
                        /*Append a t foot with a clone of thead*/
                        $("#tableActiveSemesters").append(
                            $('<tfoot/>').append( $("#tableActiveSemesters thead tr").clone() )
                        );
                        /* Init resume in tfoot*/
                        init_tfoot_from_report(resume_report, semesters, cohort_name);
                        if(!validate_known_columns(column_names, known_columns)) {
                            console.error(' Las columnas dadas no coinciden con las columnas conocidas.',
                                'columnas daads:', column_names,
                                'columnas conocidas: ', known_columns);
                        }

                    }

                ).fail(
                    function(error) {
                    }
                );

            }
            /*After each cohort select change, the table should be updated*/
            $('#cohorts').change(function() {
                var cohort_id = $('#cohorts option:selected').val();
                var cohort_name = $('#cohorts option:selected').text();
                $('#tableActiveSemesters tfoot th')[0].textContent= tfoot_first_row_title_prefix + ' ' + cohort_name;
                init_datatable(cohort_id, cohort_name);
            });
            /* First table init */
            var cohort_id = $('#cohorts option:selected').val();
            var cohort_name = $('#cohorts option:selected').text();
            init_datatable(cohort_id, cohort_name);



        },

    };

});