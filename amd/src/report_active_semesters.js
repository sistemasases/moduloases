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
            console.log(instance_id);
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

            function init_datatable () {
                var cohort_id = $('#cohorts option:selected').val();

                var url = '../managers/report_active_semesters/report_active_semesters_api.php/' + instance_id;
                var post_info = {
                    function: 'data_table',
                    params: {
                        instance_id: instance_id,
                        cohort_id: cohort_id
                    }
                };
                console.log(post_info);
                $.ajax({
                    method: "POST",
                    url: url,
                    data: post_info,
                    dataType: 'json'
                }).done(
                    function (dataTable){
                        console.log(dataTable);
                        $("#tableActiveSemesters").html('');
                        table = $("#tableActiveSemesters").DataTable(
                            {
                                data: dataTable.data,
                                bsort: dataTable.bsort,
                                columns: dataTable.columns,
                                language: dataTable.language,
                                order: dataTable.order
                            }
                        );
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