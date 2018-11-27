 /**
 * Management - View reports
 * @module block_ases/assigned_students_no_trackings_report
 * @author Joan Manuel Tovar Guzmán
 * @copyright 2018 Joan Manuel Tovar Guzmán <joan.tovar@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'block_ases/jszip',
        'block_ases/pdfmake',
        'block_ases/jquery.dataTables',
        'block_ases/dataTables.autoFill',
        'block_ases/dataTables.buttons',
        'block_ases/buttons.html5',
        'block_ases/buttons.flash',
        'block_ases/buttons.print',
        'block_ases/bootstrap',
        'block_ases/sweetalert'
        ],
        function($, jszip, pdfmake, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert) {

	return {

		init: function() {
			
			window.JSZip = jszip;
			load_students_no_trackings_report();

			$(document).on('click', '#tableEstSeguimientos tbody tr td', function () {
				var pagina = "student_profile.php";
				var table = $("#tableEstSeguimientos").DataTable();
				var colIndex = table.cell(this).index().column;

				if (colIndex >= 0 && colIndex <= 3) {
					$("#formulario").each(function () {
						this.reset;
					});
					window.open(pagina + location.search + "&student_code=" + table.cell(table.row(this).index(), 0).data(), '_blank');
				}
			});	

			//Controles para la tabla de estudiantes
			$(document).on('change', '#tableEstSeguimientos thead tr th select', function () {
				var table = $("#tableEstSeguimientos").DataTable();
		
				var colIndex = $(this).parent().index()+1;				
				table.columns( colIndex-1 ).search( this.value ).draw();		
			});
			
				
	/**
	 * @method load_students_no_trackings_report
	 * @desc Loads the report of a student with resolution on a table. Current processing on icetex_reports_processing.php
	 * @return {void}
	 */
	function load_students_no_trackings_report(){
		$.ajax({
			type: "POST",
			data: {loadR: 'loadReport'},
			url: "../managers/no_trackings_report/students_no_trackings_report_processing.php",
			success: function(msg){				
				console.log(msg);
				var table = $("#tableEstSeguimientos").DataTable(msg);
				console.log(table);
				$('#dataTables_scrollBody').css('cursor', 'pointer');
			},
			dataType: "json",
			cache: false,
			error: function(msg){
				swal("Error", "Error al cargar el reporte", "error");
			}
		});
    }	
}

};

});