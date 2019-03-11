// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @author     Jeison Cardona Gómez
 * @copyright  Jeison Cardona Gómez < jeison.cardona@correounivalle.edu.co >
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/ases_incident_manager
  */

 define(
    [
        'jquery', 
        'block_ases/sweetalert', 
        'block_ases/jqueryui',
        'block_ases/loading_indicator'
       ], 
        function($, sweetalert, jqueryui, li ) {
   
           console.log( "ases_incident_manager loaded" );

           return {
                init: function(){

                    $(document).on( "click", ".inc_item", function(){
                            let incident_id = $(this).data("id");
                            load_incident( incident_id );
                        }
                    );

                    $(document).on( "click", "#close_incident", function(){
                            let incident_id = $(this).data("id");
                            close_incident( incident_id );
                        }
                    );

                    function load_incident( incident_id ){
                        li.show();
                        $.ajax({
                            method: "POST",
                            url: "../managers/incident_manager/incident_api.php",
                            contentType: "application/json",
                            dataType: "json",
                            data: JSON.stringify({"function":"get_incident", "params":[ incident_id ]}),
                            async: false,
                            success: function( response ){
                                li.hide();

                                let url_base = $("#extras").data('url-base');

                                let comentarios = JSON.parse(JSON.parse(response.data_response).comentarios);
                                let opened_by = JSON.parse(response.data_response).usuario_registra;
                                let closed_by = JSON.parse(response.data_response).usuario_cierra;
                                let title = comentarios[0].message.title;
                                let detail = comentarios[0].message.commentary;
                                let cerrada = JSON.parse(response.data_response).cerrada;
                                let datetime = JSON.parse(response.data_response).fecha_hora_registro;

                                $(".inc_detail").html( "<strong>Detalle: </strong>" + detail );
                                $(".opened_by").html( "<strong>Abierta por:</strong> " + opened_by.firstname + " " + opened_by.lastname + " - " + opened_by.username );
                                $(".inc_preview").find("a").attr( "href", "ases_incidents_preview.php?incident_id=" + incident_id );
                                $(".record_datetime").html( "<strong>Fecha y hora:</strong> " + datetime );
                                $("#close_incident").attr("data-id", incident_id);
                                $(".send_message").html( '<strong>Mensajes: </strong> [ <a target="_blank" rel="noopener noreferrer" href="' + url_base + "/message/index.php?id=" + opened_by.id + '">Ir al chat con el usuario</a> ].' );
                                $(".inc_preview").show();

                                if( cerrada != 1 ){
                                    $(".inc_title").html( "<h2>Ticket #" + incident_id + " - " + title + "</h2>" );
                                    $(".inc_opc").show();
                                }else{
                                    $(".inc_opc").hide();
                                    $(".inc_title").html( "<h2>Ticket #" + incident_id + " [Cerrada] - " + title + "</h2>" );
                                    $(".closed_by").html( "<strong>Cerrada por:</strong> " + closed_by.firstname + " " + closed_by.lastname );
                                }
                                
                            },
                            error: function( XMLHttpRequest, textStatus, errorThrown ) {
                                li.hide();
                                console.log( "some error " + textStatus + " " + errorThrown );
                                console.log( XMLHttpRequest );
                            }
                        });
                    }

                    function close_incident(incident_id){

                        swal({
                            html:true,
                            title: 'Confirmación',
                            text: "<strong>Nota importante!</strong>: Está cerrando una incidencia.",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, cerrar!'
                          }, function(isConfirm) {
                            if (isConfirm) {
                                li.show();
                                $.ajax({
                                    method: "POST",
                                    url: "../managers/incident_manager/incident_api.php",
                                    contentType: "application/json",
                                    dataType: "json",
                                    data: JSON.stringify({"function":"close_logged_user_incident", "params":[ incident_id ]}) ,
                                    success: function( response ){
                                        li.hide();
                                        if( response.status_code === 0 ){
                                            swal(
                                                'Éxito!',
                                                'Se ha cerrado correctamente la incidencia',
                                                'success'
                                            );
                                        }else{
                                            swal(
                                                'Error!',
                                                'Oops!: Al parece no existe la incidencia',
                                                'error'
                                            );
                                        }
                                    },
                                    error: function( XMLHttpRequest, textStatus, errorThrown ) {
                                        li.hide();
                                        console.log( "some error " + textStatus + " " + errorThrown );
                                        console.log( XMLHttpRequest );
                                    }
                                });
                            }
                        });
                    }

                }
           }
       }
);