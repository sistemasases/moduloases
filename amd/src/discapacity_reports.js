
/**
 * Discapacity report
 * @module amd/src/historic_academic_reports
 * @author Juan Pablo Castro
 * @copyright 2018 Juan Pablo Castro<juan.castro.vasquez@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
    'block_ases/select2', 
    'block_ases/Chart',
    'block_ases/loading_indicator'
], function ($, jszip, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2, Chart, loading_indicator) {
    return {
        init: function () {
            loading_indicator.show();
            $(".bt_students_data").click(function(){


                var icon = $(this).children("span");

                if (icon.hasClass('glyphicon-chevron-right')) {
                    icon.removeClass("glyphicon-chevron-right");
                    icon.addClass("glyphicon-chevron-down");
                } else {
                    icon.addClass("glyphicon-chevron-right");
                    icon.removeClass("glyphicon-chevron-down");
                }

                var target = $($(this).data('target'));
                if (target.css('display') != "none") {
                    target.hide(300);
                } else {
                    target.show(300);
                }

            });

            $(document).on('click', '#tableDiscapacityReports tbody tr td', function () {
                var id = "";

                // Obtenemos la primer columna de la fila seleccionada
                // seleccionada
               id =  $(this).parents("tr").find("td:first").html();
                 
               // alert(valores);
                get_others_data_discapacity(id);

            });

            $(document).ready(function () {
                $.ajax({

                    type: "POST",
                    data: { load: 'loadTableDiscapacityReports' },
                    url: "../managers/discapacity_reports/discapacity_reports_api.php",
                    success: function (msg) {
                        $("#div_table_discapacity_reports").empty();
                        $("#div_table_discapacity_reports").append('<table id="tableDiscapacityReports" class="display" cellspacing="0" width="100%"><thead><thead></table>');
                        var table = $("#tableDiscapacityReports").DataTable(msg);
                        $('#div_table_discapacity_reports').css('cursor', 'pointer');
                        loading_indicator.hide();

                    },
                    error: function(msg){
                        loading_indicator.hide();
                    },
                    dataType: "json",
                    cache: false,
                    async: true,

                    failure: function (msg) { }
                });
                get_data_to_graphic();

        });

            function get_others_data_discapacity(id){
                //Get one form switch id
                $.ajax({
                    type: "POST",
                    data: { load: 'get_others_data_discapacity', params: id },
                    url: "../managers/discapacity_reports/discapacity_reports_api.php",
                    dataType: "json",
                    cache: false,
                    async: true,
                    success: function (msg) {

                        create_modal(msg);
                    },
                    failure: function (msg) { }
                });
            }

            function create_modal(msg){
                //Show beautifyJSON in modal
                $("#div_JSONform").empty();
                let data_html = '';
                data_html += '<div class = "row"> <div class= "col-lg-8 col-md-8 col-sm-8 col-xs-8"><strong> Dificultades permanentes en funciones:</strong> </div>';
                data_html +=  '<div class= "col-lg-4 col-md-4 col-sm-4 col-xs-4">' + msg["cant_dificultad_permanente_funciones"]+'</div></div>';
                data_html += '<div class = "row"> <div class= "col-lg-8 col-md-8 col-sm-8 col-xs-8"><strong> Condiciones de salud a tener en cuenta:</strong> </div>';
                data_html +=  '<div class= "col-lg-4 col-md-4 col-sm-4 col-xs-4">' + msg["cant_condicion_salud_organos"]+'</div></div>';
                data_html += '<div class = "row"> <div class= "col-lg-8 col-md-8 col-sm-8 col-xs-8"><strong> Necesidades diferentes:</strong> </div>';
                data_html +=  '<div class= "col-lg-4 col-md-4 col-sm-4 col-xs-4">' + msg["cant_necesidades_situaciones"]+'</div></div>';
                data_html += '<div class = "row"> <div class= "col-lg-8 col-md-8 col-sm-8 col-xs-8"><strong> Factores de impacto (escenarios universitarios):</strong> </div>';
                data_html += '<div class= "col-lg-4 col-md-4 col-sm-4 col-xs-4">' + msg["cant_factores_impacto"]+'</div></div>';
                data_html += '<div class = "row"> <div class= "col-lg-8 col-md-8 col-sm-8 col-xs-8"><strong> Posibilidades en actividades cotidianas:</strong> </div>';
                data_html +=  '<div class= "col-lg-4 col-md-4 col-sm-4 col-xs-4">' + msg["cant_posibilidad_actividades"]+'</div></div>';
                $("#div_JSONform").append(data_html);
                $('#modal_JSON').fadeIn(300);
            }


            $('.mymodal-close').click(function () {
                $("#modal_JSON").hide();
            });
            $('.btn-danger-close').click(function () {
                $("#modal_JSON").hide();
            });

            $('.outside').click(function(){
                var outside = $(this);
                swal({
                    title: 'Confirmación de salida',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Salir'
                  }, function(isConfirm) {
                    if (isConfirm) {
                        $(outside).parent('.mymodal').fadeOut(300);
                        console.log( $(this).parent('.mymodal') );
                    }
                  });
                
            });

            function get_data_to_graphic(){
                $.ajax({

                    type: "POST",
                    data: { load: 'getDataGraphic' },
                    url: "../managers/discapacity_reports/discapacity_reports_api.php",
                    success: function (msg) {
                        console.log(msg);
                        create_graphic_discapacity(msg);

                    },
                    dataType: "json",
                    cache: false,
                    async: true,

                    failure: function (msg) { }
                });
            }

            function create_graphic_discapacity(data_get){
                var ctx = document.getElementById('grafica_tipos').getContext('2d');
                var data = {
                    labels: ["Cognitiva","Psicosocial","Física","Sensorial","Múltiple", "Otra"],
                    datasets: [{
                        label: 'Número de estudiantes por tipo de discapacidad',
                        data: data_get,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 2
                    }]
                }
            
                var chart_options = {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                };
            
                var radar_chart = new Chart(ctx, {
                    type: 'bar',
                    data: data,
                    options: chart_options
                }
                   
                 

            );

            } 

}

};
});