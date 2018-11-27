// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/loading_indicator
  */

 define(['jquery', 'block_ases/sweetalert', 'block_ases/jqueryui'], function($, sweetalert, jqueryui ) {
    
    return {
        init: function() {
            var div = '\
            <div \
                class="loading_indicator"\
                style="\
                    position: fixed;\
                    bottom: 20px;\
                    right: 20px;\
                    z-index: 9999;\
                    width: 70px;\
                    height: 70px;\
                ">\
            </div>\
            ';

            var style='\
              .loading_indicator {\
                border: 16px solid #f3f3f3;\
                border-radius: 50%;\
                border-top: 16px solid red;\
                width: 100px;\
                height: 100px;\
                -webkit-animation: spin 2s linear infinite; /* Safari */\
                animation: spin 2s linear infinite;\
              }\
              @-webkit-keyframes spin {\
                0% { -webkit-transform: rotate(0deg); }\
                100% { -webkit-transform: rotate(360deg); }\
              }\
              @keyframes spin {\
                0% { transform: rotate(0deg); }\
                100% { transform: rotate(360deg); }\
              }\
            ';

            function show_loading_indicator(){
                
                $("head").append( "<style>" + style + "</style>" );
                $("body").append( div );
                $(".loading_indicator").css('z-index', 9999);

            }

            function hide_loading_indicator(){
                
                $(".loading_indicator").hide();
                
            }

        }
    };
      
});