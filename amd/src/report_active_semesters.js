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
             *     nombre,
             *     [active_semesters]: [semesters codes, example: [2016A, 2016B ...]],
             * }
             *
             */
            /**
             * All cells than have as data 'NO' should have distinct class
             */

            function init_datatable (cohort_id) {
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
                    function (dataTable){
                        dataTable.rowCallback =  function(row, data, index) {
                            var column_names = Object.keys(data);
                            console.log(column_names);
                            $(column_names).each(
                                function (index_, value) {
                                    if (data[value] === "NO") {
                                        $("td." + value , row).addClass("no_active_semester");
                                    }
                                }
                            );
                        };

                        if ( $.fn.dataTable.isDataTable( '#tableActiveSemesters' ) ) {
                            $("#div_table_report").html('');
                            $("#div_table_report").fadeIn(1000).append('<table id="tableActiveSemesters" class="table" cellspacing="0" width="100%"></table>');

                        }
                        table = $("#tableActiveSemesters").DataTable(
                            dataTable
                        );
                    }

                ).fail(
                    function(error) {
                    }
                );

            }
            $('#cohorts').change(function() {
                var cohort_id = $('#cohorts option:selected').val();
                init_datatable(cohort_id);
            });
            var cohort_id = $('#cohorts option:selected').val();
            init_datatable(cohort_id);



        },

    };

});