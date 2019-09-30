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

        $(document).on("click", "#select-visibles", 
            function( event ){
                event.preventDefault();
                $(".user_enrolled[data-visible='true']").find(".remove_check").attr("checked", true);
                $(".user_enrolled[data-visible='true']").find(".ucontainer").addClass( "user-selected" );
            }
        );

        $(document).on("click", "#clear-selection", 
            function( event ){
                event.preventDefault();
                $(".user_enrolled").find(".remove_check").attr("checked", false);
                $(".user_enrolled").find(".ucontainer").removeClass( "user-selected" );
            }
        );

        $(document).on("click", "#remove-selected", 
            function( event ){
                event.preventDefault();
                let uenroll_id = [];
                $(".user_enrolled[data-visible='true']").find(".ucontainer").each( 
                    function( index ){
                        if( $(this).find(".remove_check").prop("checked") == true ){
                            uenroll_id.push( $(this).data("id") );
                        }
                    } 
                );

                //Funciona
                /*$.ajax({
                    type: "POST",
                    data: JSON.stringify( { function:"remove_enrolled_users", params:[ 450299, uenroll_id ] } ),
                    url: "../managers/plugin_status/plugin_status_api.php",
                    dataType: "json",
                    cache: "false",
                    success: function( data ) {
                        loading_indicator.hide();
                        alert( "Eliminado" );
                    },
                    error: function( data ) {
                        loading_indicator.hide();
                        console.log( data );
                    },
                });*/


            }
        );

        $(document).on("click", ".ucontainer", function(){
            let ucontainer = $(this);
            let ucontainer_checkbox = $(this).find(".remove_check");
            if( ucontainer_checkbox.prop("checked") == true ){
                ucontainer_checkbox.attr("checked", false);
                ucontainer.removeClass( "user-selected" );
            }else{
                ucontainer_checkbox.attr("checked", true);
                ucontainer.addClass( "user-selected" );
            };
        });

        $(document).on("click",".mfilter",
            function(){
                $(".ucontainer").find(".remove_check").attr("checked", false);
                $(".ucontainer").removeClass( "user-selected" );
                let filter = $(this);
                $(".mfilter").removeClass( "filter-selected" );
                filter.addClass( "filter-selected" );
                let filter_value = filter.data( "filter" );
                $(".ucontainer").parent().show();
                $(".ucontainer").parent().attr("data-visible", true);
                if( filter_value !== "all" ){
                    $(".ucontainer").not('.ucontainer[data-glist="' + filter_value + '"]').parent().attr("data-visible", false);
                    $(".ucontainer").not('.ucontainer[data-glist="' + filter_value + '"]').parent().hide();
                }
            }
        );

        loading_indicator.show();
        $.ajax({
            type: "POST",
            data: JSON.stringify( { function:"get_users_data_by_instance", params:[ 450299 ] } ),
            url: "../managers/plugin_status/plugin_status_api.php",
            dataType: "json",
            cache: "false",
            success: function( data ) {
                loading_indicator.hide();
                let global_filter = [];

                data.data_response.forEach( 
                    function( elem ){
                        let groups = [];
                        let template = $($("#user_enrolled_template").html());
                        template.find(".ucontainer").find(".fname").text( elem.user.firstname );
                        template.find(".ucontainer").find(".lname").text( elem.user.lastname );
                        template.find(".ucontainer").attr( "data-id", elem.user.id );
                        template.find(".ucontainer").attr( "data-groups", JSON.stringify(elem.groups) );
                        template.find(".ucontainer").attr( "data-groups-number", elem.groups.length );
                        
                        for( let i = 0; i < elem.groups.length; i++ ){
                            let group_name = elem.groups[i].name;
                            groups.push( group_name );
                            if( !global_filter.includes( group_name ) ){
                                global_filter.push( group_name );
                            }
                            template.find(".ucontainer").find(".groups_container").append( '<span class="enrolled_group">' + elem.groups[i].name + '</span>' );
                        }

                        if( elem.groups.length === 0 ){
                            groups.push( "Sin grupo" );
                            template.find(".ucontainer").find(".groups_container").append( '<span class="enrolled_group">Sin grupo</span>' );
                        }

                        template.find(".ucontainer").attr( "data-glist", groups );
                        template.appendTo( "#plugin_members_container" );

                    } 
                );
                global_filter.push( "Sin grupo" );
                global_filter.forEach( 
                    function( element ){
                        $("#step_0_selector").append( '<div class="mfilter" data-filter="' + element + '">' + element + '</div>' );
                    } 
                );
                $("#step_0_selector").append( '<div class="mfilter filter-selected" data-filter="all">Todos (Con varios grupos)</div>' );
            },
            error: function( data ) {
                loading_indicator.hide();
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