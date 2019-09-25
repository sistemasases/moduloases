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
    'block_ases/Chart',
    'block_ases/loading_indicator',
    'block_ases/jquery.dataTables',
    'block_ases/dataTables.autoFill',
    'block_ases/dataTables.buttons',
    'block_ases/buttons.html5',
    'block_ases/buttons.flash',
    'block_ases/buttons.print'
], function($, Chart, loading_indicator){

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
            var tfoot_total_nochange_title_prefix = 'Activos sin cambio de carrera';
            var tfoot_total_change_title_prefix = 'Activos con cambio de carrera';
            var tfoot_total_active_title_prefix = 'Total activos';
            var tfoot_total_inactive_title_prefix = 'inactivos';
            var tfoot_total_graduated_students_prefix = 'Egresados';
            var tfoot_total_students_prefix = 'Total Estudiantes';
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
                }/*else if (colIndex > 4) {
                    displayProgram();}*/
            });

            var semesters = []; /* Array of strings with the semesters names, ej. ['2015A', '2015B'...]*/
            /** Resume of active semester of all students, of course each property of this object
             * correspond to one semester (see semesters)
             */

            var PercentageResumeReport = (function () {
                function PercentageResumeReport(resume_report /* instance of ResumeReport */, semesters /* Array of strings */) {
                    var total_students = resume_report.total_students;
                    semesters.forEach(semester => {
                            var student_cancel = total_students - (resume_report.semesters[semester][0] + resume_report.semesters[semester][1]);
                            this[semester] = student_cancel * 100 / total_students;
                        }

                    );
                };
                return PercentageResumeReport;
            }());

            var PercentageGradReport = (function () {
                function PercentageGradReport(resume_report /* instance of ResumeReport */, semesters /* Array of strings */) {
                    var total_students = resume_report.total_students;
                    semesters.forEach(semester => {
                            var student_grad = resume_report.semesters[semester][1];
                            this[semester] = student_grad * 100 / total_students;
                        }

                    );
                };
                return PercentageGradReport;
            }());

            var PercentageActiveReport = (function () {
                function PercentageActiveReport(resume_report /* instance of ResumeReport */, semesters /* Array of strings */) {
                    var total_students = resume_report.total_students;
                    semesters.forEach(semester => {
                            var student_active = resume_report.semesters[semester][0];
                            this[semester] = student_active * 100 / total_students;
                        }

                    );
                };
                return PercentageActiveReport;
            }());

            var ResumeReport = (function () {
                /**
                 * Constructor
                 * @param semesters {array} Semester names
                 * @param total_students {number} Students quantity
                 * @constructor
                 */
               function ResumeReport(semesters, total_students) {
                   this.total_students = total_students? total_students: 0;
                   this.semesters = [];
                   var tam = semesters.length;
                   for(var i = 0; i < tam; i++){
                       this.semesters[semesters[i]] = [0, 0, 0, 0];
                   }
               }
               //console.log(semesters);
               ResumeReport.prototype.init_from_data = function init_from_data(data /*DataTable.data*/, semesters) {
                   data.forEach( (item) => {
                       var carrera = '';
                       semesters.forEach( semester => {
                           if(!(item[semester].includes('NO') || item[semester].includes('EGRESADO'))){
                               if(!item[semester].includes(carrera) && carrera !== ''){
                                   this.semesters[semester][2]++;
                               }
                               carrera = item[semester];
                           }
                           if(item[semester].includes('SI')) {
                               this.semesters[semester][0]++;
                           }else if (item[semester].includes('EGRESADO')){
                               this.semesters[semester][1]++;
                           }
                       }) ;
                   });

                   var cambios = 0;

                   semesters.forEach(semester => {
                           cambios = cambios + this.semesters[semester][2];
                       this.semesters[semester][2] = cambios;
                       }

                   );
/*
                    for(var item = 0; item < data.length; item++){
                        var tam = semesters.length;
                        for(var semester = 0; semester < tam; semester++){
                            if(data[item][semester] === 'SI') {
                                console.log('sel');
                                this[semester][0]++;
                            }else console.log('nel');
                        }
                    }*/
                   //console.log(semesters);
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
            function init_tfoot_from_report(resume_report /* Resume Report */, semesters, cohort_id) {
                /* All the cells of tfoot should have no text at start*/
                $('#tableActiveSemesters tfoot th').html('');
                /* Add the first cell of tfoot title */
                $('#tableActiveSemesters tfoot tr.total_students th')[1].textContent = tfoot_total_students_prefix + ' ' + cohort_id;
                $('#tableActiveSemesters tfoot tr.total_nochange th')[1].textContent= tfoot_total_nochange_title_prefix + ' ' + cohort_id;
                $('#tableActiveSemesters tfoot tr.total_change th')[1].textContent= tfoot_total_change_title_prefix + ' ' + cohort_id;
                $('#tableActiveSemesters tfoot tr.total_active th')[1].textContent= tfoot_total_active_title_prefix + ' ' + cohort_id;
                $('#tableActiveSemesters tfoot tr.total_inactive th')[1].textContent = tfoot_total_inactive_title_prefix + ' ' + cohort_id;
                $('#tableActiveSemesters tfoot tr.total_grads th')[1].textContent = tfoot_total_graduated_students_prefix + ' ' + cohort_id;
                /* Add the total active students in each semester at tfoot */
                semesters.forEach(function(semester) {
                    //console.log(resume_report.semesters[semester][1]);
                    //console.log(semester);
                    $('#tableActiveSemesters tfoot tr.total_students th.'+semester).html(resume_report.total_students); //graduated students
                    $('#tableActiveSemesters tfoot tr.total_nochange th.'+semester).html(resume_report.semesters[semester][0]-resume_report.semesters[semester][2]); //active nochange
                    $('#tableActiveSemesters tfoot tr.total_change th.'+semester).html(resume_report.semesters[semester][2]); //active change
                    $('#tableActiveSemesters tfoot tr.total_active th.'+semester).html(resume_report.semesters[semester][0]); //active students
                    $('#tableActiveSemesters tfoot tr.total_inactive th.'+semester).html(resume_report.total_students - (resume_report.semesters[semester][0] + resume_report.semesters[semester][1])); //inactive students
                    $('#tableActiveSemesters tfoot tr.total_grads th.'+semester).html(resume_report.semesters[semester][1]); //graduated students
                });
            }
            var resume_report /* ResumeReport */ = null; // null initialized for now
            var percentage_resume_report /* PercentageResumeReport */ = null; // null initialized for now
            var percentage_grad_report /* PercentageGradReport */ = null; // null initialized for now
            var percentage_active_report /* PercentageGradReport */ = null; // null initialized for now

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

            function init_download_percentage_desertion_element() {
                var graph_image_url =  document.getElementById('active_semesters_chart').toDataURL();
                $('#download_percentage_desertion').click(function() {
                   $(this).attr('href', graph_image_url);
                });
            }
            function init_graph(semesters, percentage_resume_report, percentage_grad_report, percentage_active_report, callback /*PercentageResumeReport*/) {
                var data = [];
                var grads = [];
                var active = [];
                semesters.forEach(semester => {
                   data.push(percentage_resume_report[semester]);
                   grads.push(percentage_grad_report[semester]);
                   active.push(percentage_active_report[semester]);
                });
                var config = {
                    type: 'line',
                    data: {
                        labels: semesters,
                        datasets: [{
                            label: 'Porcentaje de estudiantes activos durante el periodo',
                            backgroundColor: 'green',
                            borderColor: 'green',
                            data: active,
                            fill: false,
                        },
                            {
                            label: 'Porcentaje de estudiantes inactivos durante el periodo',
                            backgroundColor: 'red',
                            borderColor: 'red',
                            data: data,
                            fill: false,
                        },
                            {
                            label: 'Porcentaje de estudiantes graduados durante el periodo',
                            backgroundColor: 'blue',
                            borderColor: 'blue',
                            data: grads,
                            fill: false,
                        }]
                    },
                    options: {
                        animation: {
                            onComplete: function(){
                                callback();
                            }
                         },
                        responsive: true,
                        title: {
                            display: true,
                            text: 'Reporte deserción'
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Periodo'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Porcentaje'
                                }
                            }]
                        }
                    }
                };
                var active_semesters_chart_element = document.getElementById('active_semesters_chart');
                var ctx = active_semesters_chart_element.getContext('2d');
                window.myLine = new Chart(ctx, config);

            }

            function init_datatable (cohort_id) {
                var url = '../managers/report_active_semesters/report_active_semesters_api.php/' + instance_id;
                loading_indicator.show();
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
                        loading_indicator.hide();
                        //console.log(dataFromApi);
                        var dataTable = dataFromApi.dataTable;
                        $('#download_percentage_desertion').css("display", "inline"); //Show the hidden download button
                        semesters = dataFromApi.semesters;
                        var columns = dataTable.columns;
                        var column_names = columns.map( column => column.name );
                        var total_students = dataTable.data.length;
                        console.log(semesters);
                        resume_report = new ResumeReport(semesters, total_students);
                        resume_report.init_from_data(dataTable.data, semesters);
                        percentage_resume_report = new PercentageResumeReport(resume_report, semesters);
                        percentage_grad_report = new PercentageGradReport(resume_report, semesters);
                        percentage_active_report = new PercentageActiveReport(resume_report, semesters);

                        /*Init graph*/
                        init_graph(semesters, percentage_resume_report, percentage_grad_report, percentage_active_report, init_download_percentage_desertion_element);
                        /* Put a class to each cell than have the 'SI' value, see
                        * https://datatables.net/reference/option/rowCallback */
                        dataTable.rowCallback =  function(row, data, index) {
                            $(semesters).each(
                                function (index_, value) {
                                    if (data[value].includes('SI')) {
                                        $("td." + value , row).addClass("active_semester");
                                    }else if (data[value].includes('EGRESADO')){
                                        $("td." + value , row).addClass("graduated_student");
                                    }
                                }
                            );
                        };

                        dataTable.initComplete = function () {
                            $("th:contains('20')").each(
                                function(){
                                    $(this).addClass("Semestre");
                                }
                            );
                            /*Add filter to column headers*/
                            var column_names = dataTable.columns.map(column => column.name ? column.name : null);
                            /* Indexes of the semester columns */
                            /*Filter columns*/
                            var filter_column_names = semesters;
                            filter_column_names.push('num_carreras');
                            console.log(semesters);
                            var filter_column_indexes = get_filter_column_indexes(filter_column_names, column_names);
                            this.api().columns(filter_column_indexes).every(function () {
                                var column = this;


                                var select = $('<select><option value=""></option></select>')
                                    //.appendTo($(column.header()))
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
                        $("#tableActiveSemesters").append(
                            $('<tfoot/>').append( $("#tableActiveSemesters thead tr").clone().addClass('total_students') )//total students
                        );
                        /*Append a t foot with a clone of thead for the totals*/
                        $("#tableActiveSemesters").append(
                            $('<tfoot/>').append( $("#tableActiveSemesters thead tr").clone().addClass('total_nochange') ) //total 1 career
                        );
                        $("#tableActiveSemesters").append(
                            $('<tfoot/>').append( $("#tableActiveSemesters thead tr").clone().addClass('total_change') ) //total multiple career
                        );
                        $("#tableActiveSemesters").append(
                            $('<tfoot/>').append( $("#tableActiveSemesters thead tr").clone().addClass('total_active') ) //total active
                        );
                        $("#tableActiveSemesters").append(
                            $('<tfoot/>').append( $("#tableActiveSemesters thead tr").clone().addClass('total_inactive') )//total inactive
                        );
                        $("#tableActiveSemesters").append(
                            $('<tfoot/>').append( $("#tableActiveSemesters thead tr").clone().addClass('total_grads') )//total graduated_students
                        );

                        /* Init resume in tfoot*/
                        init_tfoot_from_report(resume_report, semesters, cohort_id);
                        if(!validate_known_columns(column_names, known_columns)) {
                            console.error(' Las columnas dadas no coinciden con las columnas conocidas.',
                                'columnas daads:', column_names,
                                'columnas conocidas: ', known_columns);
                        }

                    }

                ).fail(
                    function(error) {
                        loading_indicator.hide();
                    }
                );

            }
            /*After each cohort select change, the table should be updated*/
            $('#cohorts').change(function() {
                var cohort_id = $('#cohorts option:selected').val();
                $('#tableActiveSemesters tfoot th')[0].textContent= tfoot_total_active_title_prefix + ' ' + cohort_id;
                init_datatable(cohort_id);
            });
            /* First table init */
            var cohort_id = $('#cohorts option:selected').val();
            init_datatable(cohort_id);



        },

    };

    /**
     * @method displayProgram
     * @desc Displays the names of the programs from the selected cell
     * @return {void}
     */
/*    function displayProgram() {
        $.ajax({
            type: "POST",
            data: {
                codigo: codigo,
                programa: programa,
                type: "check_loses",
                semestre: semestre,
            },
            url: "../managers/historic_academic_reports/historic_academic_reports_processing.php",
            success: function (msg) {
                console.log(msg);
                swal({
                    title: "Materias Perdidas",
                    type: "info",
                    text: msg,
                    html: true,
                    showCancelButton: false,
                    customClass: 'swal-wide',
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Cerrar",
                    closeOnConfirm: true
                });
                $(".swal-wide").css({ 'width': '600px', 'margin-left': '-300px' });
            },
            dataType: "text",
            cache: "false",
            error: function (msg) {
                console.log(msg);
            },
        });
    }*/

});