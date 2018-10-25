/**
 * Course and teacher report
 * @module amd/src/course_and_techar_report
 * @author Luis Gerardo Manrique Cardona
 * @copyright 2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/jquery.dataTables', 'block_ases/sweetalert2'], function($){

    return{
        init: function(){
            $(document).ready(function(){

            });
        },

        load_report: function (data) {
            console.log(data);
            $("#div_table_report").html('');
            $("#div_table_report").fadeIn(1000).append('<table id="tableFinalgradesReport" class="table"' +
                ' cellspacing="0" width="100%"><thead> </thead></table>');

            $("#tableFinalgradesReport").DataTable(data);
        }
    };

});