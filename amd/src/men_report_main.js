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
    'block_ases/loading_indicator',
    'core/config',
    'block_ases/csv'
], function($,bootstrap, datatables, sweetalert, select2, loading_indicator, cfg, csv) {
        var SEMESTER_SELECT_SELECTOR = '#semester-select';
        var CSV_NAME = 'report_men.csv';
        /**
         * Return the Men Report in csv string described by the class `MenReport` at men_report_lib
         * @see MenReport
         * @see men_report_lib
         * @see men_report_api
         * @param semester_name Nombre de el semestre, ejemplos: ['2018A', '2019B']
         * @returns Promise<string>
         */
        var get_report_string = function (semester_name) {
            return  $.ajax({
                type: "POST",
                data: JSON.stringify({semestre: semester_name, function: "create_men_report_csv"}),
                url: "../../../blocks/ases/managers/men_report/men_report_api.php",
            });
        };
        var get_semester_name = function () {
            return $(SEMESTER_SELECT_SELECTOR).val();
        };
        var download_report_csv = function() {
            get_report_string(get_semester_name())
                .then(
                    report_csv_string => {
                        csv.csv_string_to_file_for_download(report_csv_string, CSV_NAME)
                    }
                )
                .catch(err => console.log(err));
        };
    return {
        init: function () {
            $("#report-men-button").click(download_report_csv);
        }
    };    
});