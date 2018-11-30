
/**
 * Discapacity report
 * @module amd/src/historic_academic_reports
 * @author Juan Pablo Castro
 * @copyright 2018 Juan Pablo Castro<juan.castro.vasquez@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'block_ases/jszip',
    'block_ases/jquery.dataTables',
    'block_ases/dataTables.autoFill',
    'block_ases/dataTables.buttons',
    'block_ases/buttons.html5',
    'block_ases/buttons.flash',
    'block_ases/buttons.print',
    'block_ases/bootstrap',
    'block_ases/sweetalert',
    'block_ases/jqueryui',
    'block_ases/select2'
], function ($, jszip, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2) {
    return {
        init: function () {


        $(document).ready(function () {
                $.ajax({

                    type: "POST",
                    data: { load: 'loadTableDiscapacityReports' },
                    url: "../managers/discapacity_reports/discapacity_reports_api.php",
                    success: function (msg) {
                        $("#div_table_discapacity_reports").empty();
                        $("#div_table_discapacity_reports").append('<table id="tableDiscapacityReports" class="display" cellspacing="0" width="100%"><thead><thead></table>');
                        var table = $("#tableDiscapacityReports").DataTable(msg);
                        $('#div_table_discapacity_reports').css('cursor', 'pointer');

                    },
                    dataType: "json",
                    cache: false,
                    async: true,

                    failure: function (msg) { }
                });

            });

}

};
});