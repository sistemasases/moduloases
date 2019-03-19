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
        'block_ases/loading_indicator',
        'block_ases/_general_modal_manager'
       ], 
        function($, sweetalert, jqueryui, li, gmm ) {

            $(document).on( "click", "#new-incident", function(){

                let div_incident_box = '\
                <div id="incident_box" style="padding:1em; margin-bottom:1.2em;" >\
                    <input id="new_inc_title" class="form-control" placeholder="Título corto" type="text">\
                    <textarea id="new_inc_detail" class="form-control" name="" placeholder="Descripción detallada de la incidencia" rows="5"></textarea>\
                    <a id="new_inc_registrar" class="btn btn-info" style="float:right; margin-top:0.5em;" href="javascript:void(0)">Registrar</a>\
                </div>\
            ';

                gmm.generate_modal(
                    "modal-new-incident", "Registro de incidencia interna", div_incident_box, "modal-body-new-incident",
                    function(){gmm.show_modal( ".modal-new-incident", 300 )}
                );
            });

            $(document).on("click", "#new_inc_registrar", function(){

                let system_info = "<h1>Incidencia interna</h1>";
                let detail = {
                    title:$("#new_inc_title").val(),
                    commentary:$("#new_inc_detail").val()
                };

                if( (detail.title != "") && (detail.commentary != "") ){
                    li.show();
                    $.ajax({
                        method: "POST",
                        url: "../managers/incident_manager/incident_api.php",
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({"function":"create_incident", "params":[ detail, system_info ]}) ,
                        success: function( response ){
                            li.hide();
                            if( response.status_code === 0 ){
                                swal(
                                    {title:'Éxito',
                                    text: 'Se ha registrado correctamente la incidencia, ticket #' + response.data_response,
                                    type: 'success'},
                                    function(){
                                        location.reload();
                                    }
                                );
                            }else{
                                swal(
                                    'Error!',
                                    'Oops!: ' + response.data_response,
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
                }else{
                    swal(
                        'Oops!',
                        'Verifica que tanto el título como la descripción no estén vacíos.',
                        'warning'
                    );
                }

            });

            function format_date(date) {
                let month_names = [
                  "Enero", "Febrero", "Marzo",
                  "Abril", "Mayo", "Junio", "Julio",
                  "Agosto", "Septiembre", "Octubre",
                  "Noviembre", "Diciembre"
                ];
              
                let day = date.getDate();
                let month_index = date.getMonth();
                let year = date.getFullYear();
                let hour = date.getHours();
                let minutes = date.getMinutes();
                let seconds = date.getSeconds();
              
                return day + '-' + month_names[month_index] + '-' + year + ', ' + hour + ":" + minutes + ":" + seconds;
            }
   
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
                                let datetime = format_date(new Date( JSON.parse(response.data_response).fecha_hora_registro * 1000));

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
                                                {title:'Éxito',
                                                text: 'Se ha cerrado correctamente la incidencia',
                                                type: 'success'},
                                                function(){
                                                    location.reload();
                                                }
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