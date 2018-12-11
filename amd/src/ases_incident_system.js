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
        'block_ases/jqueryui'
       ], 
        function($, sweetalert, jqueryui ) {

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
                        <textarea id="inc_detail" class="form-control" name="" placeholder="Descripción detallada de la incidencia" cols="" rows="3"></textarea>\
                        <a id="inc_registrar" href="javascript:void(0)">Registrar</a>\
                    </div>\
                    <hr style="margin-bottom:5px;margin-top:50px;">\
                    <div id="old_inc">\
                        <span><strong>Incidencias previas</strong></span>\
                    </div>\
                    <hr style="margin:5px;">\
                </div>\
            </div>\
            ';

            $("head").append( "<style>" + style + "</style>" );
            $("body").append( div_ases_incident_system_box );
            $("body").append( div_incident_box );
   
           console.log( "ases_incident_system loaded" );

           const getCircularReplacer = () => {
            const seen = new WeakSet();
            return (key, value) => {
              if (typeof value === "object" && value !== null) {
                if (seen.has(value)) {
                  return;
                }
                seen.add(value);
              }
              return value;
            };
          };

           return {
                init: function(){

                    $(document).ready(function(){

                        setInterval(function(){ 
                            $("#inc_text").fadeOut(700); 
                        }, 3000);

                        $.ajax({
                            method: "POST",
                            url: "../managers/incident_manager/incident_api.php",
                            contentType: "application/json",
                            dataType: "json",
                            data: JSON.stringify({"function":"get_logged_user_incidents", "params":[]}) ,
                            success: function( msg ){
                                console.log(msg);
                            },
                            error: function( XMLHttpRequest, textStatus, errorThrown ) {
                                console.log( "some error " + textStatus + " " + errorThrown );
                                console.log( XMLHttpRequest );
                            }
                        });

                    });

                    $(document).on("click","#ases_incident_system_box", function(){
                        $("#ases_incident_system_box").hide(0);
                        $("#incident_box").show(100);
                    });

                    $(document).on("click","#close_incident_box", function(){
                        $("#incident_box").hide();
                        $("#ases_incident_system_box").show(100);
                    });

                    $(document).on("click", "#inc_registrar", function(){

                        let system_info = $("html").html();
                        let detail = $("#inc_detail").val();

                        $.ajax({
                            method: "POST",
                            url: "../managers/incident_manager/incident_api.php",
                            contentType: "application/json",
                            dataType: "json",
                            data: JSON.stringify({"function":"create_incident", "params":[ detail, system_info ]}) ,
                            success: function( msg ){
                                console.log(msg);
                                if( msg['status_code'] === 0 ){
                                    swal(
                                        'Éxito!',
                                        'Se ha registrado correctamente la incidencia, ticket #20' + msg['data_response'],
                                        'success'
                                    );
                                }else{
                                    swal(
                                        'Error!',
                                        'Oops!: ' + msg.data_response,
                                        'error'
                                    );
                                }
                            },
                            error: function( XMLHttpRequest, textStatus, errorThrown ) {
                                console.log( "some error " + textStatus + " " + errorThrown );
                                console.log( XMLHttpRequest );
                            }
                        });

                    });

                }
           }
       }
);