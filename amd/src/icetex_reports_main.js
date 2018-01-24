 /**
 * Management - Create, update and load periods
 * @module amd/src/icetex_reports_main
 * @author Juan Pablo Moreno Muñoz
 * @copyright 2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/datatables.net', 'block_ases/datatables.net-buttons', 'block_ases/buttons.flash', 'block_ases/jszip', 'block_ases/pdfmake', 'block_ases/buttons.html5', 'block_ases/buttons.print', 'block_ases/sweetalert','block_ases/select2', 'block_ases/jqueryui'], function($, bootstrap, datatablesnet, datatablesnetbuttons, buttonsflash, jszip, pdfmake, buttonshtml5, buttonsprint, sweetalert, select2, jqueryui) {

	return {

		init: function() {

			
			$(document).ready(function() {
                    $("#list-resolution-students-panel").on('click', function(){
					loadReportResolution();
					

                });

			});	

	/**
	 * @method loadReportResolution
	 * @desc Loads the report of a student with resolution on a table. Current processing on icetex_reports_processing.php
	 * @return {void}
	 */
	function loadReportResolution(){
		$.ajax({
			type: "POST",
			data: {loadR: 'loadReport'},
			url: "../managers/historic_icetex_reports/icetex_reports_processing.php",
			success: function(msg){
				$("#div_res_students").empty();
				$("#div_res_students").append('<table id="tableResStudents" class="display" cellspacing="0" width="100%"><thead><thead><tfoot><tr><th colspan="4" style="text-align:right">Total: </th></tr></tfoot></table>');
				var table = $("#tableResStudents").DataTable(msg);
				$('#div_res_students').css('cursor', 'pointer');				
			},
			dataType: "json",
			cache: false,
			error: function(msg){
				swal("Error", "Error al cargar el reporte", "error");
			}
		});

	}
	
	function calculate_total_amount(){
		var table = $("#tableResStudents").DataTable();
	}
	
}

};

});