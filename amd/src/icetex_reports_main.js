 /**
 * Management - View reports
 * @module amd/src/icetex_reports_main
 * @author Juan Pablo Moreno Muñoz
 * @copyright 2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
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

			$("#stu_res_button").on('click', function(){
				var check_value = $("#check_no_res").is(':checked');
				load_report_students_resolution(check_value);
			});

			$(document).on('click', '#tableResStudents tbody tr td', function () {
				var pagina = "student_profile.php";
				var table = $("#tableResStudents").DataTable();
				var colIndex = table.cell(this).index().column;

				if (colIndex >= 1 && colIndex <= 4) {
					$("#formulario").each(function () {
						this.reset;
					});
					window.open(pagina + location.search + "&student_code=" + table.cell(table.row(this).index(), 1).data(), '_blank');
				}
			});

			jQuery.fn.dataTable.Api.register( 'sum()', function ( ) {
				var result = 0;
				return this.flatten().reduce( function ( a, b ) {
					if ( typeof a === 'string' ) {
						a = a.replace(/[\$.]/g, '') * 1;
					}
					if ( typeof b === 'string' ) {
						b = b.replace(/[\$.]/g, '') * 1;
					}
			 
					result = parseInt(a, 10) + parseInt(b, 10);
					return result.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
				}, 0 );
			} );

			$("#list-resolutions-panel").on('click', function(){
				load_resolutions();								
			});

			$("#report_button").on('click', function() {
				var cohort = $("#cohort_select select").val();
				if(cohort == ""){
					swal("Información", "Seleccione una cohorte válida", "info");
				}
				else{					
					load_summary_report(cohort);
				}								
			});

			//Controles para la tabla de los estudianes con resolución
			$(document).on('change', '#tableResStudents thead tr th select', function () {
				var table = $("#tableResStudents").DataTable();
		
				var colIndex = $(this).parent().index()+1;
				table.columns( colIndex-1 ).search( this.value ).draw();		
			});

			//Controles para la tabla de resoluciones
			$(document).on('change', '#tableResolutions thead tr th select', function () {
				var table = $("#tableResolutions").DataTable();
		
				var colIndex = $(this).parent().index()+1;
				table.columns( colIndex-1 ).search( this.value ).draw();
			});

	/**
	 * @method load_report_students_resolution
	 * @desc Loads the report of a student with resolution on a table. Current processing on icetex_reports_processing.php
	 * @return {void}
	 */
	function load_report_students_resolution(check_val){
		$("#div_res_students").html('<img class="icon-loading" src="../icon/loading.gif"/>');
		$.ajax({
			type: "POST",
			data: {loadR: 'loadReport', value: check_val},
			url: "../managers/historic_icetex_reports/icetex_reports_processing.php",
			success: function(msg){
				$("#div_res_students").empty();
				$("#div_res_students").fadeIn(1000).append('<table id="tableResStudents" class="display" cellspacing="0" width="80%"><thead><thead></table>');
				var table = $("#tableResStudents").DataTable(msg);
				$('#tableResStudents').css('cursor', 'pointer');
				
			},
			dataType: "json",
			cache: false,
			error: function(msg){
				swal("Error", "Error al cargar el reporte", "error");
			}
		});
	}

	/**
	 * @method compute_resolutions_total_amount
	 * @desc Computes the sum of the current page and all pages for every value in columns three and five of the given table.
	 * @return {void}
	 */
	function compute_resolutions_total_amount(table) {
		var total_current_page = table.column( 3, { page:'current' } ).data().sum();
		var total_all_pages = table.column( 3 ).data().sum();

		var total_stud_amount_page = table.column( 5, { page:'current' } ).data().sum();

		$( table.column( 3 ).footer() ).html(
			'$'+total_current_page + '(Total $' + total_all_pages + ')'
		);

		$( table.column( 5 ).footer() ).html(
			'$'+total_stud_amount_page
		);
	}

	/**
	 * @method load_resolutions
	 * @desc Loads the report of all resolutions on a table. Current processing on resolution_reports_processing.php
	 * @return {void}
	 */
	function load_resolutions(){
		$("#div_resolutions").html('<img class="icon-loading" src="../icon/loading.gif"/>');
		$.ajax({
			type: "POST",
			data: {resR: 'resReport'},
			url: "../managers/historic_icetex_reports/resolution_reports_processing.php",
			success: function(msg){
				$("#div_resolutions").empty();
				$("#div_resolutions").fadeIn(1000).append('<table id="tableResolutions" class="display" cellspacing="0" width="100%"><thead><thead><tfoot id="table_res_foot"><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tfoot></table>');
				var table = $("#tableResolutions").DataTable(msg);
				
				$("#tableResolutions").on( 'page.dt', function () {
					compute_resolutions_total_amount(table);					
				});

				$("#tableResolutions").on('draw.dt', function () {
					compute_resolutions_total_amount(table);					
				});

				compute_resolutions_total_amount(table);
				$('#div_resolutions').css('cursor', 'pointer');

			},
			dataType: "json",
			cache: false,
			error: function(msg){
				swal("Error", "Error al cargar el reporte", "error");
			}
		});

	}

	function load_summary_report(cohort_name){
		$("#div_report_summary").html('<img class="icon-loading" src="../icon/loading.gif"/>');
		$.ajax({
			type: "POST",
			data: {summ: 'summaryR', cohor: cohort_name},
			url: "../managers/historic_icetex_reports/summary_report_processing.php",
			success: function(msg){
				$("#div_report_summary").empty();
				$("#div_report_summary").fadeIn(1000).append('<table id="tableSummary" class="display" cellspacing="0" width="100%"><thead><thead><tfoot id="table_summ_foot"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tfoot></table>');
				var table = $("#tableSummary").DataTable(msg);
				var total_inact_no_res = table.column( 9 ).data().sum();
				var total_act_res = table.column( 3 ).data().sum();
				var total_inact_res = table.column( 5 ).data().sum();
				var total_act_no_res = table.column( 7 ).data().sum();

				$( table.column( 3 ).footer() ).html(
					'$'+total_act_res
				);

				$( table.column( 5 ).footer() ).html(
					'$'+total_inact_res
				);

				$( table.column( 7 ).footer() ).html(
					'$'+total_act_no_res
				);

				$( table.column( 9 ).footer() ).html(
					'$'+total_inact_no_res
				);
				
				$('#div_report_summary').css('cursor', 'pointer');			
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