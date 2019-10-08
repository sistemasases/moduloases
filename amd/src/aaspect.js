define(['jquery'], function($, config) {

/*!
 * AAspect
 * Licensed under the MIT license
 */

 if (typeof jQuery === 'undefined') {
 	throw new Error('AAspect\'s JavaScript requires jQuery')
 }

//Tab pane control
+function( $ ){
	'use strict';

	function change_tab_to( element ){

		var pill = $( element ).parent();
		var pills = pill.parent().parent();
		pills.find( "li" ).removeClass( "ases-active" );
		pill.addClass( "ases-active" );

		var href = $( element ).attr( "href" ); 
		var tab_id = href.slice( 1 );
		var selected_tab = $( "#" + tab_id );
		var tabs = selected_tab.parent().find( ".ases-tab-pane" );
		tabs.removeClass( "ases-tab-active" );
		tabs.removeClass( "ases-fade" );
		tabs.removeClass( "ases-in" );
		selected_tab.addClass( "ases-fade" );
		selected_tab.addClass( "ases-in" );
		selected_tab.addClass( "ases-tab-active" );
		
	}

	var click_handler = function ( e ) {
	    e.preventDefault();
	    change_tab_to( this );
	}

	$( document ).on( 'click', '[data-toggle="ases-pill"]', click_handler );

}( jQuery );

});
