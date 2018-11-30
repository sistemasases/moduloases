
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
    'block_ases/select2'
], function ($, jszip, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2) {
    return {
        init: function () {
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

                    },
                    dataType: "json",
                    cache: false,
                    async: true,

                    failure: function (msg) { }
                });


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
                
                        //Items of JSON are decode
                        console.log(msg);
                      
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

            

}

};
});