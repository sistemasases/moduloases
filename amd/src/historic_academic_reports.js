/**
 * Academic report management
 * @module amd/src/historic_academic_reports
 * @author Camilo José Cruz rivera
 * @copyright 2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/jquery.dataTables', 'block_ases/sweetalert2'], function ($) {

    return {
        /**
         *
         */

        init: function () {
            $(document).ready(function () {

            });
        },
        load_table_students: function (data) {

            $("#div_table_students").html('');
            $("#div_table_students").fadeIn(1000).append('<table id="tableResultStudent" class="table"' +
                ' cellspacing="0" width="100%"><thead> </thead></table>');

            $("#tableResultStudent").DataTable(data);

        },
        load_total_table: function (data) {

            $("#div_totales_historic").html('');
            $("#div_totales_historic").fadeIn(1000).append('<table id="tableResultTotal" class="table"' +
                ' cellspacing="0" width="100%"><thead> </thead></table>');

            $("#tableResultTotal").DataTable(data);
        }
    };
});