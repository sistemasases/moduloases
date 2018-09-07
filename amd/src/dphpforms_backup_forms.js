
// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/backup_forms
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
], function($, jszip, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2) {
  return {
        init: function (){
            window.JSZip = jszip;
           $(document).ready(function(){
                    $.ajax({
                        type: "POST",
                        url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                        data: JSON.stringify({ "function": "get_list_forms" , "params": [0]}),
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        async: true,  
                        success: function(data){
                           
                           prueba(data);


                        },
                        failure: function(errMsg) {alert("Cuidad 2");}
                    });
                });

            function prueba(data){ 
                var respuesta = data['data_response'];
                $("#div_table_report").DataTable(
                    { 
                        "bsort" : false,
                        "data": [
                            {
                                "op":55,
                                "casa": 0
                            },
                            {
                                "casa":5,
                                "op": 14
                            }
                        ],
                        "columns" : [
                            {
                                "title" : "t", 
                                "name" : "casita", 
                                "data" : "casa",
                            },
                            {
                                "title" : "op", 
                                "name" : "op", 
                                "data" : "op",
                            },
                        ],
                        "dom":"lifrtpB",
                        "buttons" : [
                            {
                                "extend" : "print",
                                "text" : 'Imprimir'
                            },{
                                "extend" : "csv",
                                "text" : 'CSV'
                            },{
                                "extend" : "excel",
                                "text" : 'Excel',
                                "className" : 'buttons-excel',
                                "filename" : 'Export excel',
                                "extension" : '.xls'
                            }   
                        ]
                    }
                );
            };
                
        }

  };
});