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
        
        return {             
            init: function () {
                window.JSZip = jszip; 
                window.graficas = {};    //Objeto para guardar las gráficas de manera dinámica
                const secciones = ["programa", "facultad", "sexo", "edad", "condExcepcion", "riesgos"];                                                
                
                //Eventos para cargar tablas y gráficas una vez se abra cada panel

                $("#list-students-programa-panel").on('click', function(){                                   
                    getDataGraphicTable("programa");
                });

                $("#list-students-facultad-panel").on('click', function(){
                    getDataGraphicTable("facultad");
                });                                       

                $("#list-students-sexo-panel").on('click', function(){
                    getDataGraphicTable("sexo");
                }); 

                $("#list-students-edad-panel").on('click', function(){
                    getDataGraphicTable("edad");
                }); 

                $("#list-students-condExcepcion-panel").on('click', function(){
                    getDataGraphicTable("condExcepcion");
                }); 

                $("#list-students-riesgos-panel").on('click', function(){
                    getDataGraphicTable("riesgos");
                });              
                
                //Eventos para actualizar tablas y gráficas cada vez que cambien los checkboxes de los filtros

                $('#status_fields').on('change', function () {                    
                    for (i = 0; i < secciones.length; i++) { 
                        
                        if($("#"+secciones[i]).length > 0){
                            getDataGraphicTable(secciones[i]);
                        }
                    }
                });
               
                //Eventos para actualizar tablas y gráficas cada vez que cambie el filtro de cohorte

                $('#conditions').on('change', function () {
                    for (i = 0; i < secciones.length; i++) { 
                        
                        if($("#"+secciones[i]).length > 0){
                            getDataGraphicTable(secciones[i]);
                        }
                    }
                });
            }            
        }

        /**
         * Funcion getDataGraphicTable
         * Recibe un tipo de reporte y realiza una consulta AJAX para obtener los datos de la tabla y gráfica,
         * y posteriormente crearlas.
         * 
         * @param {*} type  tipo de reporte, posibles valores: "programa", "facultad", "sexo", "edad", "condExcepcion", "riesgos"
         */

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

                    //Se crea la gráfica con la función auxiliar correspondiente dependiendo del tipo de gráfica
                    switch(type){
                        case 'programa':                        
                            createBarGraphic(type, msg.data, 'horizontalBar');
                            break;
                        case 'edad':                        
                            createBarGraphic(type, msg.data, 'bar');
                            break;
                        case 'facultad':
                            createPieDoughnutGraphic(type, msg.data, 'doughnut');
                            break;
                        case 'sexo':
                            createPieDoughnutGraphic(type, msg.data, 'pie');
                            break;
                        case 'condExcepcion':
                            createPieDoughnutGraphic(type, msg.data, 'doughnut');
                            break;
                        case 'riesgos':
                            createRisksGraphic(msg.data)
                            break;
                    }                   
                },
                dataType: "json",
                cache: false,
                async: true,

                failure: function (msg) { }
            });
        }

        /**
         * Función createTable
         * Crea o actualiza la tabla de un reporte dependiendo de su tipo
         * 
         * @param {*} type Tipo de tabla: programa, edad, facultad...
         * @param {*} data 
         */

        function createTable(type, data){
            $("#div_table_"+type).empty();
            $("#div_table_"+type).append('<table id="t_' + type + '" class="table" cellspacing="0" width="100%"><thead> </thead></table>');
            $("#t_"+type).DataTable(data); 
        }                
        
        /**
         * Función createBarGraphic         * 
         * Se encarga de crear una gráfica de barras teniendo como parámetros:
         * 
         * @param {*} type Tipo de gráfica: programa, edad, facultad...
         * @param {*} data Información a mostrarse en la gráfica
         * @param {*} type_chart Relacionado con la orientación de la gráfica de barras: 'bar' ó 'horizontalBar'
         */ 

        function createBarGraphic(type, data, type_chart){

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

            $('.horizontal-bar-chart-container').css('height', '1300px');
            $('.horizontal-bar-chart-container').css('width', '100%');

            $('.bar-chart-container').css('height', '800px');
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
            if(!window.graficas["chart_"+type]){
                
                var texto = type;
                if(type ==='condExcepcion'){
                    texto = 'Condición de Excepción'
                }

                window.graficas["chart_"+type] = new Chart(ctx, {               
                    type: type_chart,
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
                            text: 'Cantidad de Estudiantes por ' + texto,
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
                window.graficas["chart_"+type]["data"] = data;
                window.graficas["chart_"+type].update();
            }
        }

        /**
         * Función createPieDoughnutGraphic
         * Se encarga de preparar los datos y contruir un reporte gráfico tipo torta o dona a partir de éstos
         * 
         * @param {*} type Tipo de gráfica: programa, edad, facultad...
         * @param {*} data  Datos a mostrar
         * @param {*} type_graphic 'doughnut' ó 'pie'
         */


        function createPieDoughnutGraphic(type, data, type_graphic){                     

            var labelsGrafica = [], cantidades = [];
            
            for(var x in data){

                nombre = data[x]["nombre"];
                cantidad = data[x]["cantidad"];               
                               
                                                                                    
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
                                          "#CECCCC", "#9191E9", "#99E1D9", "#FFA552", "#BFC8AD", "#0BAA6B", "#EDD892"],
                        borderColor: ["#F56476", "#BFD1E5", "#FFEECF", "#C9A690", "#DF928E", 
                                    "#CECCCC", "#9191E9", "#99E1D9", "#FFA552", "#BFC8AD", "#0BAA6B", "#EDD892"],
                        borderWidth: 1
                        
                    }]
            }

            if(!window.graficas["chart_"+type]){    //Si la gráfica no existe, se crea desde cero  

                var texto = type;
                if(type ==='condExcepcion'){
                    texto = 'Condición de Excepción'
                }
            
                var chart_options = {
                    
                    title: {
                        display: true,
                        text: 'Cantidad de estudiantes por ' + texto,
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
            
                window.graficas["chart_"+type] = new Chart(ctx, {
                    type: type_graphic,
                    data: data,
                    options: chart_options
                });

            }else{ //Si la gráfica ya existe, se actualizan simplemente los datos
                window.graficas["chart_"+type]["data"] = data;
                window.graficas["chart_"+type].update();
            }     
        }

        /**
         * Función createRiskGraphic
         * Se encarga de preparar los datos de la gráfica de riesgos y 
         * crear el reporte gráfico a partir de éstos         
         * 
         * @param {*} data 
         */
        
        function createRisksGraphic(data){

            //Arreglos separados para los labels y los valores de los riesgos            
            var nombres_riesgos = [];
            var riesgos_bajos = [];
            var riesgos_medios = [];
            var riesgos_altos = [];
            var sin_registro = [];

            //Se llenan los arreglos recién creados
            for (riesgo in data){
                nombres_riesgos.push(data[riesgo]["riesgo"]);
                riesgos_bajos.push(data[riesgo]["bajo"]);
                riesgos_medios.push(data[riesgo]["medio"]);
                riesgos_altos.push(data[riesgo]["alto"]);
                sin_registro.push(data[riesgo]["no_registra"]);
            }         
            
            $('.risk-chart-container').css('width', '100%');

            //Preparación de los datos
            var ctx = document.getElementById("grafica_riesgos").getContext('2d');
            var data = {
                labels: nombres_riesgos,
                datasets: [{
                    label: "Riesgos bajos",
                    data: riesgos_bajos,
                    backgroundColor: "rgba(0, 179, 136, 0.4)",
				    borderColor: "rgba(0, 179, 136, 1)",                   
                    borderWidth: 2
                },
                {
                    label: "Riesgos Medios",
                    data: riesgos_medios,
                    backgroundColor: "rgba(255, 136, 17, 0.4)",
				    borderColor: "rgba(255, 136, 17, 1)",                   
                    borderWidth: 2
                },
                {
                    label: "Riesgos Altos",
                    data: riesgos_altos,
                    backgroundColor: "rgba(255, 86, 102, 0.4)",
				    borderColor: "rgba(255, 86, 102, 1)",                   
                    borderWidth: 2
                },
                {
                    label: "N.R.",
                    data: sin_registro,
                    backgroundColor: "rgba(132, 133, 134, 0.4)",
				    borderColor: "rgba(132, 133, 134, 1)",                   
                    borderWidth: 2
                }
                ]
            }

            if(!window.graficas["chart_riesgos"]){ //Si la gráfica no existe, se crea desde cero                     
            
                var chart_options = {
                    
                    title: {
                        display: true,
                        text: 'Cantidad de registros por riesgos',
                        fontSize: 25
                    },
                    legend: {
                        display: true,
                        labels:{
                            fontSize: 15,
                            fontStyle: 'bold'
                        }
                    },
                    starAngle: 0,
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                fontSize: 15,
                                fontStyle: 'bold'
                            }
                        }]
                    },
                    showTooltips: true,
                    showPercentages: true
                };

                Chart.defaults.polarArea.animation.animateScale = false;
            
                window.graficas["chart_riesgos"] = new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: chart_options
                });

            }else{ //Si la gráfica existe, se actualizan sus datos

                window.graficas["chart_riesgos"]["data"] = data;
                window.graficas["chart_riesgos"].update();
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
