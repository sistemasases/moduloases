/**
 * Academic report management
 * @module amd/src/teachers_reports
 * @author Juan Pablo Moreno Muñoz
 * @copyright 2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/datatables', 'block_ases/sweetalert2'], function($){

    return{
        init: function(){
            $(document).ready(function(){

            });
        },

        load_table_report: function (data) {

            $("#div_table_report").html('');
            $("#div_table_report").fadeIn(1000).append('<table id="tableItemsReport" class="table"' +
                ' cellspacing="0" width="100%"><thead> </thead></table>');

            $("#tableItemsReport").DataTable(data);
        }
    };

});