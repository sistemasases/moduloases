// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/monitorias_academicas_detalle
 */
define(['jquery',
    'block_ases/jquery.dataTables',
    'block_ases/dataTables.buttons',
    'block_ases/buttons.html5',
    'block_ases/buttons.flash',
    'block_ases/buttons.print',
    'block_ases/_general_modal_manager',
    'block_ases/select2',
    'block_ases/jqueryui',
    'block_ases/loading_indicator',
    'block_ases/sweetalert',
],
    function ($, dataTables, buttons, html5, flash, print,  gmm, select2, jqueryui, loading_indicator, sweetalert) {
        function construir_tabla(data){
            $("#div_table").html('');
            $("#div_table").fadeIn(500).append('<table id="tableResult" class="stripe row-border order-column" cellspacing="0" width="100%"><thead> </thead></table>');
            $("#tableResult").DataTable(data);
        }
        const monitoria_id = (new URLSearchParams(window.location.search)).get("monitoriaid");
        return {
            init: function () {
                 // datepickers
                $.datepicker.setDefaults({
                    closeText: "Cerrar",
                    prevText: "&#x3C;Ant",
                    nextText: "Sig&#x3E;",
                    currentText: "Hoy",
                    monthNames: [ "enero","febrero","marzo","abril","mayo","junio",
                    "julio","agosto","septiembre","octubre","noviembre","diciembre" ],
                    monthNamesShort: [ "ene","feb","mar","abr","may","jun",
                    "jul","ago","sep","oct","nov","dic" ],
                    dayNames: [ "domingo","lunes","martes","miércoles","jueves","viernes","sábado" ],
                    dayNamesShort: [ "dom","lun","mar","mié","jue","vie","sáb" ],
                    dayNamesMin: [ "D","L","M","X","J","V","S" ],
                    weekHeader: "Sm",
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: "" });
                $("#fecha_hasta").datepicker({
                    showWeek: true,
                    dateFormat: "dd/M/y",
                });
                $("#fecha_desde").datepicker({
                    showWeek: true,
                    dateFormat: "dd/M/y",
                });
                $("#fecha_desde").datepicker("setDate", new Date());
                // filtrar
                $("#desplegar-filtrar").click(() => {
                    $("#flecha").toggleClass("abajo");
                    $("#rango-fechas-sesiones").toggle(300);
                });
                $("#rango-fechas-sesiones").submit(consultar_sesiones_por_fecha);
            },
            construir_tabla: construir_tabla,
            init_despues_de_tabla: function (){
                // cancelar sesion
                $(".dt-button.buttons-print.eliminar").click(function (e) {
                    let fecha = $(e.target).parent().parent().parent().find("td")[0].innerHTML;
                    swal({
                        title: 'Cancelar sesión',
                        text: "¿Deseas cancelar la sesión programada el "+fecha+"?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Eliminar'
                    }, function (isConfirmed) {
                        if (isConfirmed) {
                            console.log(e.target.id)
                            $.ajax({
                                type: "POST",
                                data: JSON.stringify({
                                    "function": 'eliminar_sesion',
                                    "params": e.target.id,
                                }),
                                url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
                                dataType: "json",
                                success: function(msg) {
                                    consultar_sesiones_por_fecha();    
                                },
                                error : function(msg) {
                                    swal('Error!', msg, 'error')
                                    console.log(msg)
                                },
                            });
                        }
                    });
                });
            }
        }
    function formatear_date_a_int(date){
        function appendLeading0(a){ return a < 10 ? "0"+a.toString() : a.toString()};
        return parseInt(date.getFullYear().toString()+appendLeading0(date.getMonth()+1)+appendLeading0(date.getDate()))
    }
    // TODO: poner como fechas iniciales hoy y 9999999
    function consultar_sesiones_por_fecha(e){
        e.preventDefault();
        loading_indicator.show();
        var desde = formatear_date_a_int($("#fecha_desde").datepicker("getDate"));
        var hasta = formatear_date_a_int($("#fecha_hasta").datepicker("getDate"));
        console.log(desde+" "+hasta);
        console.log(monitoria_id);
        $.ajax({
            type: "POST",
            data: JSON.stringify({
                "function": "get_tabla_sesiones",
                "params": [monitoria_id, desde, hasta],
            }),
            url: "../managers/asistencia_monitorias/asistencia_monitorias_api.php",
            dataType: "json",
            success: function(msg) {
                loading_indicator.hide();
                construir_tabla(msg.data_response);
            },
            error: function(msg) {
                console.log("Error insercion bd de monitoria en backend");
                $("#debug").html(msg.responseText);
            }
        });
    }
    }
);