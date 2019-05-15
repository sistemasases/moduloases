// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/ases_graphic_reports
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
    'block_ases/sweetalert2',
    'block_ases/jqueryui',
    'block_ases/select2', 
    'block_ases/Chart',
    'block_ases/loading_indicator',
    'block_ases/chartjs_plugin_datalabels'
],
    
    function ($, jszip, pdfmake, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2, Chart, loading_indicator, chartjs_plugin_datalabels) {
        var radar_chart;
        var table_programs;
        return {
            init: function () {

                window.JSZip = jszip;        
                
               
                $("#list-students-status-panel").on('click', function(){
                    //updateTable();                    
                    get_data_to_graphic();                   					
                });   
                
                $("#save-btn").click(function() {
                //     $("#canvas").get(0).toBlob(function(blob) {
                //        saveAs(blob, "chart_1.png");
                //    });
                });  

                $('#status_fields').on('change', function () {                    
                    get_data_to_graphic();
                });
               

                $('#conditions').on('change', function () {
                    get_data_to_graphic();
                });                                              
                

            },
            load_defaults_students: function (data) {

                // $("#div_table").html('');
                // $("#div_table").fadeIn(1000).append('<table id="tableResult" class="stripe row-border order-column" cellspacing="0" width="100%"><thead> </thead></table>');
                // //console.log(data);
                // $("#tableResult").DataTable(data);

            },
            get_id_instance: function () {
                var urlParameters = location.search.split('&');

                for (x in urlParameters) {
                    if (urlParameters[x].indexOf('instanceid') >= 0) {
                        var intanceparameter = urlParameters[x].split('=');
                        return intanceparameter[1];
                    }
                }
                return 0;
            }
            
        }

        function get_data_to_graphic(){

            var ases_status = $("#ases_status").is(":checked") ? 1 : 0;
            var icetex_status = $("#icetex_status").is(":checked") ? 1 : 0;
            var program_status = $("#academic_program_status").is(":checked") ? 1 : 0;

            $.ajax({

                type: "POST",
                data: { type: 'carrera', cohort: $('#conditions').val(), ases_status: ases_status, icetex_status: icetex_status, program_status: program_status, instance_id: getIdinstancia() },
                url: "../managers/ases_report/asesreport_graphics_processing.php",
                success: function (msg) {
                    var results = Object.keys(msg).map(function(k) { return msg[k] });                    
                    var programas = []; 
                    var cantidades = [];   
                    var nombrePrograma ='';                                      
                    
                    results.sort(function(prog1, prog2,) {
                        return prog2.count - prog1.count;
                    });
                    var data = [];
                    for(var x in results){
                        nombrePrograma = results[x].nombre;
                        cantidadPrograma = results[x].count;

                        nombrePrograma = results[x].nombre;
                        cantidadPrograma = results[x].count;

                        if(nombrePrograma !== 'PLAN TALENTOS PILOS'){                                                        

                            if(nombrePrograma === 'LICENCIATURA EN EDUCACIÓN BÁSICA CON ÉNFASIS EN CIENCIAS NATURALES Y EDUCACIÓN AMBIENTAL'){
                                nombrePrograma = 'LIC. EN EDU. BÁSICA ÉNFASIS EN CIENCIAS NATURALES Y EDU. AMBIENTAL'
                            }

                            if(nombrePrograma === 'LICENCIATURA EN EDUCACIÓN BÁSICA CON ÉNFASIS EN CIENCIAS SOCIALES'){
                                nombrePrograma = 'LIC. EN EDU. BÁSICA CON ÉNFASIS EN CIENCIAS SOCIALES'
                            }                                                                  
                            programas.push(nombrePrograma);
                            cantidades.push(cantidadPrograma);

                            data.push({programa: nombrePrograma, cantidad: cantidadPrograma})
                        }                     
                    }                    

                    creategraphicProgramas(programas, cantidades);
                    createTable(data);
                    
                },
                dataType: "json",
                cache: false,
                async: true,

                failure: function (msg) { }
            });
        }

        function createTable(data){

            if (!table_programs){

                table_programs = $("#table_programs").DataTable(
                    { 
                        "retrieve": true,                          
                        "bsort" : false,
                        "data" : data,                         
                        "columns" : [
                            {
                                "title" : "Programa", 
                                "name" : "programa", 
                                "data" : "programa",
                            },
                            {
                                "title" : "Cantidad de estudiantes", 
                                "name" : "cantidad", 
                                "data" : "cantidad"
                            }],
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
                table_programs.draw();
            }            
            else{
                table_programs.clear();
                table_programs.rows.add(data);
                table_programs.draw();
            }

        }

        function creategraphicProgramas(programas, cantidades){
            $('.chart-container').css('height', '1300px');
            $('.chart-container').css('width', '100%');
            
            var backgroundColors = [];
            var borderColors = [];            
            
            for(var i=0; i<programas.length; i++){
                if(i%2 == 0){
                    backgroundColors.push('rgba(255, 99, 132, 0.2)');
                    borderColors.push('rgba(255, 99, 132, 1)');
                }else{
                    backgroundColors.push('rgba(130, 130, 130, 0.2)');
                    borderColors.push('rgba(130, 130, 130, 1)');
                }
            }

            var ctx = document.getElementById('grafica_tipos').getContext('2d');
            var data = {
                labels: programas,
                datasets: [{
                    label: "Cantidad de Estudiantes",
                    data: cantidades,
                    backgroundColor: backgroundColors,
				    borderColor: borderColors,                   
                    borderWidth: 3
                }]
            }        
            
            if(!radar_chart){

                radar_chart = new Chart(ctx, {               
                    type: 'horizontalBar',
                    data: data,
                    options: {
                        // Elements options apply to all of the options unless overridden in a dataset
                        // In this case, we are setting the border of each horizontal bar to be 2px wide
                        elements: {
                            rectangle: {
                                borderWidth: 2,                            
                            }
                        },
                        responsive: true,
                        legend: {
                            position: 'top',
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'No. de Estudiantes por Programa',
                            fontSize: 30
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    display: true
                                },
                                categoryPercentage: 1.0,
                                barPercentage: 1.0
                            }]                       
                        },
                        maintainAspectRatio: false,
                        plugins: {
                            datalabels: {
                               display: true,
                               align: 'center',
                               anchor: 'center'
                            }
                         }                    
                    }
                }
                );
            }
            else{
                radar_chart["data"] = data;
                radar_chart.update();
            }

        } 

        //Actualización de la tabla 
        function updateTable(){
            //createTable();
            var cohorts = $('#conditions').val();

            if(cohorts == 'TODOS'){
                $('#div-summary-spp').prop('hidden', false);
                $('#div-summary-spe').prop('hidden', false);
                $('#div-summary-3740').prop('hidden', false);
                $('#div-summary-oa').prop('hidden', false);
            }else if(cohorts == 'TODOS-SPP' || cohorts.substring(0, 3) == 'SPP'){
                $('#div-summary-spp').prop('hidden', false);
                $('#div-summary-spe').prop('hidden', true);
                $('#div-summary-3740').prop('hidden', true);
                $('#div-summary-oa').prop('hidden', true);
            }else if(cohorts == 'TODOS-SPE' || cohorts.substring(0, 3) == 'SPE'){
                $('#div-summary-spfunctionp').prop('hidden', true);
                $('#div-summary-spe').prop('hidden', false);
                $('#div-summary-3740').prop('hidden', true);
                $('#div-summary-oa').prop('hidden', true);
            }else if(cohorts == 'TODOS-3740' || cohorts.substring(0, 4) == '3740'){
                $('#div-summary-spp').prop('hidden', true);
                $('#div-summary-spe').prop('hidden', true);
                $('#div-summary-3740').prop('hidden', false);
                $('#div-summary-oa').prop('hidden', true);
            }else if(cohorts == 'TODOS-OTROS'){
                $('#div-summary-spp').prop('hidden', true);
                $('#div-summary-spe').prop('hidden', true);
                $('#div-summary-3740').prop('hidden', true);
                $('#div-summary-oa').prop('hidden', false);
            }
        }
        

        function getIdinstancia() {
            var urlParameters = location.search.split('&');           

            for (x in urlParameters) {
                if (urlParameters[x].indexOf('instanceid') >= 0) {
                    var intanceparameter = urlParameters[x].split('=');
                    return intanceparameter[1];
                }
            }
            return 0;
        }        
    });
