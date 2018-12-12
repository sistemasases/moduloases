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

                                let comentarios = JSON.parse(JSON.parse(response.data_response).comentarios);
                                let info_sistema = JSON.parse(response.data_response).info_sistema.replace(/<script[^>]*>/gi, "&lt;script&rt;").replace(/<\/script[^>]*>/gi, "&lt;/script&rt;");
                                let user = JSON.parse(response.data_response).usuario_registra;
                                let title = comentarios[0].message.title;
                                let detail = comentarios[0].message.commentary;

                                $(".inc_title").html( "<h2>Ticket #" + incident_id + " - " + title + "</h2>" );
                                $(".inc_detail").html( "<p>Detalle: " + detail + "</p>" );
                                $(".opened_by").html( "<strong>Usuario:</strong> " + user.firstname + " " + user.lastname + " - " + user.username );
                                $(".preview").html( "<html>" + info_sistema + "</html>" );
                                $("#close_incident").attr("data-id", incident_id);
                                $(".inc_preview").show();
                                $(".inc_opc").show();
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