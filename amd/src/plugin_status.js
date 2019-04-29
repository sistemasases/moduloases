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