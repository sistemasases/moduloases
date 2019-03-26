 /**
 * Management - Report (Reporte Ministerio de Educación)
 * @module amd/src/men_report_main 
 * @author Juan Pablo Moreno Muñoz <isabella.serna@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
    ['jquery',
    'block_ases/bootstrap', 
    'block_ases/jquery.dataTables',  
    'block_ases/sweetalert', 
    'block_ases/select2',
    'block_ases/loading_indicator'
], function($,bootstrap, datatables, sweetalert, select2, loading_indicator) {
    return {
        init: function () {
            
            $("#semester-select").onclick( function () {
                var semester = $("#semester-select").val();

                $.ajax({
                    type: "POST",
                    data: {semestre: semester, function: "create_men_report_csv"},
                    url: "../../../blocks/ases/managers/men_report/men_report_api.php",
                    success: function (msg) {

                    }
                });

            });
        }
    };    
});