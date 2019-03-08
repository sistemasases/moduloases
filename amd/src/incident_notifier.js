// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
  * @module block_ases/incident_notifier
  */

 define([
    'jquery',
    'block_ases/loading_indicator',
    'block_ases/sweetalert',
    'block_ases/jscookie'
  ], function($, loading_indicator, sweetalert, jsc) {

    let visible = false;
    let current_ids = [];
    let page_title = $("title").text();
    let open_in_other_tab = false;
    let played = false;

    if (+Cookies.get('tabs') > 0){
        open_in_other_tab = true;
    }else{
        Cookies.set('tabs', 0);
        open_in_other_tab = false;
    };

    Cookies.set('tabs', +Cookies.get('tabs') + 1);

    window.onunload = function () {
        Cookies.set('tabs', +Cookies.get('tabs') - 1);
    };

    $("html").append( '<div id="notification_sound" style="display:none;"></div>' );

    function check_incidents( callback ){
        $.ajax({
            type: "POST",
            url: "../managers/incident_manager/incident_api.php",
            data: JSON.stringify({ "function": "get_ids_open_incidents", "params": [] }),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            async: false,  
            success: function(data){
                if (typeof callback === 'function'){
                    callback( data.data_response );
                }
            },
            failure: function(errMsg) {}
        });
    }

    function add_counter_page( number ){
        if( number > 0 ){
            $("title").text( "(" + number + ")" + " " + page_title );
        }else{
            $("title").text( page_title );
        }
    }

    function playSound(){

        if (+Cookies.get('played') === false){
            let filename = 'notification_sound';
            let mp3Source = '<source src="../resources/' + filename + '.mp3" type="audio/mpeg">';
            let oggSource = '<source src="../resources/' + filename + '.ogg" type="audio/ogg">';
            let embedSource = '<embed hidden="true" autostart="true" loop="false" src="../resources/' + filename +'.mp3">';
            document.getElementById("notification_sound").innerHTML='<audio autoplay="autoplay">' + mp3Source + oggSource + embedSource + '</audio>';
            Cookies.set('played', true);
        };
      }

    console.log( "Incident notifier initialised" );
  
    return {
        init: function() {

            check_incidents(
                function( data ){
                    
                    let number_open_incidents = data.length;
                    add_counter_page( number_open_incidents );

                    if( number_open_incidents > 0 ){
                        visible = true;

                        data.forEach(element => {
                            current_ids.push( element.id )
                        });

                        let menu_incidents_manager = $("#menu_incidents_manager").find(".menu_a");
                        menu_incidents_manager.append( ' <span id="incidents_counter" class="badge badge-secondary">' + data.length + '</span>' );
                    }
                    
                }
            );

            setInterval(() => {
               
                check_incidents(
                    function( data ){
                        
                        let number_open_incidents = data.length;
                        add_counter_page( number_open_incidents );

                        if( ( number_open_incidents > 0 ) && !visible ){
                            visible = true;
                            playSound();
                            Cookies.set('played', false);
                            let menu_incidents_manager = $("#menu_incidents_manager").find(".menu_a");
                            menu_incidents_manager.append( ' <span id="incidents_counter" class="badge badge-secondary">' + data.length + '</span>' );
                        }else if( ( number_open_incidents > 0 ) && visible ){
                            
                            let new_elements = false;
                            let ids = [];
                            data.forEach(element => {
                                ids.push( element.id )
                                if( current_ids.indexOf( element.id ) === -1 ){
                                    new_elements = true;
                                };
                            });

                            if( new_elements ){
                                current_ids = ids;
                                playSound();
                                Cookies.set('played', false);
                                $("#incidents_counter").text( data.length );
                            }


                        }else if( number_open_incidents === 0 ){
                            $("#incidents_counter").remove();
                        }
                        
                    }
                );

            }, 3000);

        }
   };
  });