// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
  * @module block_ases/plugin_status
  */

  define(
    [
        'jquery',
        'block_ases/loading_indicator'
    ], 
    function($, loading_indicator) {

        console.log( "Plugin status initialised" );

        $.ajax({
            type: "POST",
            data: JSON.stringify( { function:"get_users_data_by_instance", params:[ 450299 ] } ),
            url: "../managers/plugin_status/plugin_status_api.php",
            dataType: "json",
            cache: "false",
            success: function( data ) {
                data.data_response.forEach( 
                    function( elem ){

                        let template = $($("#user_enrolled_template").html());
                        template.find(".ucontainer").find(".fname").text( elem.user.firstname );
                        template.find(".ucontainer").find(".lname").text( elem.user.lastname );
                        template.find(".ucontainer").attr( "data-groups", JSON.stringify(elem.groups) );
                        template.find(".ucontainer").attr( "data-groups-number", elem.groups.length );

                        for( let i = 0; i < elem.groups.length; i++ ){
                            template.find(".ucontainer").find(".groups_container").append( '<span class="enrolled_group">' + elem.groups[i].name + '</span>' );
                        }

                        if( elem.groups.length === 0 ){
                            template.find(".ucontainer").find(".groups_container").append( '<span class="enrolled_group">N/A</span>' );
                        }
                        

                        template.appendTo( "#plugin_members_container" );

                    } 
                );
            },
            error: function( data ) {
                console.log( data );
            },
        });

        return {
            init: function() {

                $(document).on('click', '[data-toggle="ases-pill"]', function(e){
                    var pill = $( this );
                    var pills = pill.parent();
                    pills.find( "li" ).removeClass( "ases-active" );
                    pill.addClass( "ases-active" );
                    var tab_id = pill.data( "tab" );
                    var selected_tab = $( tab_id );
                    var tabs = selected_tab.parent().find( ".ases-tab-pane" );
                    tabs.removeClass( "ases-tab-active" );
                    tabs.removeClass( "ases-fade" );
                    tabs.removeClass( "ases-in" );
                    selected_tab.addClass( "ases-fade" );
                    selected_tab.addClass( "ases-in" );
                    selected_tab.addClass( "ases-tab-active" );
                });
            }
        };
    }
);