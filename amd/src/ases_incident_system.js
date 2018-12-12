// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @author     Jeison Cardona Gómez
 * @copyright  Jeison Cardona Gómez < jeison.cardona@correounivalle.edu.co >
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/ases_incident_system
  */

 define(
    [
        'jquery', 
        'block_ases/sweetalert', 
        'block_ases/jqueryui',
        'block_ases/loading_indicator'
       ], 
        function($, sweetalert, jqueryui, li ) {

            let style='\
            #ases_incident_system_box {\
                padding: 0.5em;\
                color:white;\
                cursor:pointer;\
                background-color:#d51b23;\
                position: fixed;\
                bottom: 0px;\
                left: 0px;\
                z-index: 9999;\
                height: 30px;\
            }\
            #incident_box {\
                position: fixed;\
                bottom: 0px;\
                left: 0px;\
                z-index: 9999;\
                height: 400px;\
                border:1px solid #d51b23;\
                width: 400px;\
                background-color:white;\
                display:none;\
            }\
            .inc_header{\
                height: 30px;\
                background-color:#d51b23;\
                padding: 0.5em;\
                color:white;\
            }\
            .inc_body{\
                padding: 0.7em;\
            }\
            #close_incident_box{\
                float:right;\
                cursor:pointer;\
            }\
            #new_inc{\
                width: 100%;\
            }\
            #inc_detail{\
                width: 100%;\
            }\
            #inc_registrar{\
                background-color:#d51b23;\
                color:white;\
                padding: 0.3em;\
                float:right;\
                margin-top:0.5em;\
            }\
            #inc_registrar:hover{\
                background-color:whitesmoke;\
                color:black;\
            }\
            .inc_item{\
                cursor:pointer;\
            }\
            .inc_item > .status-icon{\
                float:left;\
                margin-right:0.3em;\
            }\
            .inc_item > .remove-icon{\
                float:right;\
                color:gray\
            }\
            .inc_item > .remove-icon:hover{\
                color:black\
            }\
            .inc_item:hover{\
                background-color:whitesmoke;\
            }\
            #old_inc_body{\
                overflow-y: auto;\
                height:130px;\
            }\
          ';

            let div_ases_incident_system_box = '\
            <div \
                id="ases_incident_system_box"\
                title="Reporte de incidencias">\
                <i id="inc_icon" class="glyphicon glyphicon-wrench"></i>\
                <span id="inc_text"><strong>Incidencias</strong></span>\
            </div>\
            ';

            let div_incident_box = '\
            <div id="incident_box" >\
                <div class="inc_header">\
                    <i class="glyphicon glyphicon-wrench"></i>\
                    <span><strong>Incidencias</strong></span>\
                    <i id="close_incident_box" class="glyphicon glyphicon-remove"></i>\
                </div>\
                <div class="inc_body">\
                    <div id="prev_inc_selector">\
                    </div>\
                    <div id="new_inc">\
                        <span><strong>Registro de nueva incidencia</strong></span>\
                        <hr style="margin:5px;">\
                        <input id="inc_title" class="form-control" placeholder="Título corto" type="text">\
                        <textarea id="inc_detail" class="form-control" name="" placeholder="Descripción detallada de la incidencia" cols="" rows="2"></textarea>\
                        <a id="inc_registrar" href="javascript:void(0)">Registrar</a>\
                    </div>\
                    <hr style="margin-bottom:5px;margin-top:50px;">\
                    <div id="old_inc">\
                        <span><strong>Incidencias previas</strong></span>\
                    </div>\
                    <hr style="margin:5px;">\
                    <div id="old_inc_body">\
                    </div>\
                    <hr style="margin:5px;">\
                </div>\
            </div>\
            ';

            $("head").append( "<style>" + style + "</style>" );
            $("body").append( div_ases_incident_system_box );
            $("body").append( div_incident_box );
   
           console.log( "ases_incident_system loaded" );

           return {
                init: function(){

                    function loadIncidents(){
                        li.show();
                        $.ajax({
                            method: "POST",
                            url: "../managers/incident_manager/incident_api.php",
                            contentType: "application/json",
                            dataType: "json",
                            data: JSON.stringify({"function":"get_logged_user_incidents", "params":[]}) ,
                            success: function( response ){
                                li.hide();
                                let inc_list = "";
                                response.data_response.forEach(function(element){

                                    let status_color = {
                                        solved:"#239f07",
                                        waiting:"#ff9a00",
                                        closed:"#000"
                                    };

                                    let status = JSON.parse( element.estados );
                                    let last_status = {
                                        change_order:-1,
                                        status:""
                                    };

                                    status.forEach(function(e){
                                        if( last_status.change_order < parseInt(e.change_order) ){
                                            last_status = e;
                                        }
                                    });

                                    let title = null;
                                    let detail = null;
                                    let comments = JSON.parse( element.comentarios );

                                    comments.forEach(function(e){
                                        if( parseInt( e.message_number ) == 0 ){
                                            title = e.message.title;
                                            detail = e.message.commentary;
                                            return;
                                        }
                                    });


                                    let close_icon = '';

                                    if( last_status.status != "solved" ){
                                        close_icon = '<i class="remove-icon glyphicon glyphicon-remove-sign" data-id="' + element.id + '" title="Eliminar"></i>';
                                    }

                                    inc_list  += '\
                                    <div class="inc_item col-xs-12 col-sm-12 col-md-12 col-lg-12">\
                                        <i class="status-icon glyphicon glyphicon-record" style="color:' + status_color[last_status.status] + '" title="' + last_status.status + '"></i>\
                                        '+ close_icon +'\
                                        <div class="item-title" data-id="' + element.id + '" data-title="' + title + '" data-detail="' + detail + '">#' + element.id + ' - ' + title + '</div>\
                                    </div>\
                                    ';
                                });
                                $("#old_inc_body").html( "" );
                                $("#old_inc_body").append( inc_list );
                            },
                            error: function( XMLHttpRequest, textStatus, errorThrown ) {
                                li.hide();
                                console.log( "some error " + textStatus + " " + errorThrown );
                                console.log( XMLHttpRequest );
                            }
                        });
                    }

                    $(document).ready(function(){

                        loadIncidents();

                        setInterval(function(){ 
                            $("#inc_text").fadeOut(700); 
                        }, 3000);

                    });

                    $(document).on("click","#ases_incident_system_box", function(){
                        $("#ases_incident_system_box").hide(0);
                        $("#incident_box").show(100);
                    });

                    $(document).on("click","#close_incident_box", function(){
                        $("#incident_box").hide();
                        $("#ases_incident_system_box").show(100);
                    });

                    $(document).on("click",".item-title", function(){

                        let ticket_id = $(this).data("id");
                        let _title = $(this).data("title");
                        let detail = $(this).data("detail");

                        swal({
                            html:true,
                            title: 'Ticket #' + ticket_id,
                            text: '<span style="font-size:1.5em;">'+ _title +'</span><br><br>\
                                   <strong>Detalle:</strong>'+ detail +'<br>',
                            type: 'info'
                          });
                    });

                    $(document).on("click",".remove-icon", function(){
                        
                        let incident_id = $(this).data('id');

                        swal({
                            html:true,
                            title: 'Confirmación',
                            text: "<strong>Nota importante!</strong>: Está cerrando una incidencia que aun no está resuelta.",
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
                                        loadIncidents();
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

                    });

                    $(document).on("click", "#inc_registrar", function(){

                        let system_info = $("html").html();
                        let detail = {
                            title:$("#inc_title").val(),
                            commentary:$("#inc_detail").val()
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
                                            'Éxito!',
                                            'Se ha registrado correctamente la incidencia, ticket #' + response.data_response,
                                            'success'
                                        );
                                        loadIncidents();
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

                }
           }
       }
);