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
        
        var graficas = {} //Objeto para guardar las gráficas de manera dinámica
        return {
            init: function () {

                window.JSZip = jszip;  
                const secciones = ["programa", "facultad"]                                                
                
                $("#list-students-programa-panel").on('click', function(){                                   
                    getDataGraphicTable("programa");
                });

                $("#list-students-facultad-panel").on('click', function(){
                    getDataGraphicTable("facultad");
                });                                       
                

                $('#status_fields').on('change', function () {                    
                    for (i = 0; i < secciones.length; i++) { 
                        
                        if($("#"+secciones[i]).length > 0){
                            getDataGraphicTable(secciones[i]);
                        }
                    }
                });
               

                $('#conditions').on('change', function () {
                    for (i = 0; i < secciones.length; i++) { 
                        
                        if($("#"+secciones[i]).length > 0){
                            getDataGraphicTable(secciones[i]);
                        }
                    }
                });                      
                

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

        function getDataGraphicTable(type){

            var ases_status = $("#ases_status").is(":checked") ? 1 : 0;
            var icetex_status = $("#icetex_status").is(":checked") ? 1 : 0;
            var program_status = $("#academic_program_status").is(":checked") ? 1 : 0;

            $.ajax({

                type: "POST",
                data: { type: type, cohort: $('#conditions').val(), ases_status: ases_status, icetex_status: icetex_status, program_status: program_status, instance_id: getIdinstancia() },
                url: "../managers/ases_report/asesreport_graphics_processing.php",
                success: function (msg) {                                   

                    createTable(type, msg);
                    //createBarGraphic(type, msg.data);
                    switch(type){
                        case 'programa':
                            createBarGraphic(type, msg.data);
                            break;
                        case 'facultad':
                            createPieDoughnutGraphic(type, msg.data, 'doughnut');
                            break;
                    }                   
                },
                dataType: "json",
                cache: false,
                async: true,

                failure: function (msg) { }
            });
        }

        function createTable(type, msg){
            $("#div_table_"+type).empty();
            $("#div_table_"+type).append('<table id="t_' + type + '" class="table" cellspacing="0" width="100%"><thead> </thead></table>');
            $("#t_"+type).DataTable(msg); 
        }                
        
        //Función que se encarga de crear una gráfica de barras teniendo como parámetros:
        // type: Tipo de gráfica: programa, edad, facultad...
        // data: Información a mostrarse en la gráfica
        function createBarGraphic(type, data){

            //Se preparan los nombres de los labels a mostrar en la gráfica
            var atributos = Object.keys(data[0]);
            var label = atributos[0];
            var labelCantidad = atributos[1];            

            var labelsGrafica = [], cantidades = [];
            
            for(var x in data){

                nombre = data[x][label];
                cantidad = data[x][labelCantidad];  

                if(nombre === 'LICENCIATURA EN EDUCACIÓN BÁSICA CON ÉNFASIS EN CIENCIAS NATURALES Y EDUCACIÓN AMBIENTAL'){
                    nombre = 'LIC. EN EDU. BÁSICA ÉNFASIS EN CIENCIAS NATURALES Y EDU. AMBIENTAL'
                }

                if(nombre === 'LICENCIATURA EN EDUCACIÓN BÁSICA CON ÉNFASIS EN CIENCIAS SOCIALES'){
                    nombre = 'LIC. EN EDU. BÁSICA CON ÉNFASIS EN CIENCIAS SOCIALES'
                } 
                                                                                    
                labelsGrafica.push(nombre);
                cantidades.push(cantidad);               
            }            

            $('.bar-chart-container').css('height', '1300px');
            $('.bar-chart-container').css('width', '100%');
            
            //Se asignan los colores de las barras de la gráfica, alternando los colores
            var backgroundColors = [];
            var borderColors = [];            
            
            for(var i=0; i<labelsGrafica.length; i++){
                if(i%2 == 0){
                    backgroundColors.push('rgba(255, 99, 132, 0.2)');
                    borderColors.push('rgba(255, 99, 132, 1)');
                }else{
                    backgroundColors.push('rgba(130, 130, 130, 0.2)');
                    borderColors.push('rgba(130, 130, 130, 1)');
                }
            }

            //Se crea la gráfica en el elemento "grafica_<type>", ej: grafica_programa
            var ctx = document.getElementById("grafica_"+type).getContext('2d');
            var data = {
                labels: labelsGrafica,
                datasets: [{
                    label: "Cantidad de Estudiantes",
                    data: cantidades,
                    backgroundColor: backgroundColors,
				    borderColor: borderColors,                   
                    borderWidth: 3
                }]
            }        
            
            //Se verifica que la gráfica no exista todavía, para crearla de 0
            //Se maneja una convención para los nombres de la gráficas así: chart_<tipoGrafica>, ej: "chart_programa"
            if(!graficas["chart_"+type]){

                graficas["chart_"+type] = new Chart(ctx, {               
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
                            text: 'No. de Estudiantes por '+type,
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
            else{ //Si la gráfica ya existe, se modifica la información en ella                
                graficas["chart_"+type]["data"] = data;
                graficas["chart_"+type].update();
            }
        }
        
        function createPieDoughnutGraphic(type, data, type_graphic){

            console.log(data);
            var atributos = Object.keys(data[0]);
            var label = atributos[0];
            var labelCantidad = atributos[1];            

            var labelsGrafica = [], cantidades = [];
            
            for(var x in data){

                nombre = data[x][label];
                cantidad = data[x][labelCantidad];                  
                                                                                    
                labelsGrafica.push(nombre);
                cantidades.push(cantidad);               
            }  

            var ctx = document.getElementById("grafica_"+type).getContext('2d');
                var data = {
                    labels: labelsGrafica,
                    datasets: [{
                        label: "Cantidad de Estudiantes",
                        data: cantidades,
                        backgroundColor: ["#F56476", "#BFD1E5", "#FFEECF", "#C9A690", "#DF928E", 
                                          "#CECCCC", "#9191E9", "#99E1D9", "#FFA552", "#BFC8AD"],
                        borderColor: ["#F56476", "#BFD1E5", "#FFEECF", "#C9A690", "#DF928E", 
                        "#CECCCC", "#9191E9", "#99E1D9", "#FFA552", "#BFC8AD"],
                        borderWidth: 1
                        
                    }]
            }

            if(!graficas["chart_"+type]){                
            
                var chart_options = {
                    
                    title: {
                        display: true,
                        text: 'Número de estudiantes por '+type,
                        fontSize: 30
                    },
                    legend: {
                        display: true
                    },
                    starAngle: 0,
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    },
                    showTooltips: true,
                    showPercentages: true


                };

                Chart.defaults.polarArea.animation.animateScale = false;
            
                graficas["chart_"+type] = new Chart(ctx, {
                    type: type_graphic,
                    data: data,
                    options: chart_options
                });

            }else{
                graficas["chart_"+type]["data"] = data;
                graficas["chart_"+type].update();

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
