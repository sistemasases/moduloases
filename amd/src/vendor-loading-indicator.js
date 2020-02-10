// Standard license block omitted.
 /* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @author Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 */

define(
    [
        'jquery',
    ],
    function($) {

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
                    display:none;\
                ">\
            </div>\
            ';

        var style='\
              .loading_indicator {\
                border: 16px solid #bdbdbd;\
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

        $("head").append( "<style>" + style + "</style>" );
        $("body").append( div );
        $(".loading_indicator").css('z-index', 9999);

        var loading_instances = 0;

        console.log( "Loading indicator initialised" );

        var show_loading_indicator =  function(){
            loading_instances++;
            $(".loading_indicator").show();
        }

        var hide_loading_indicator = function(){
            loading_instances--;
            if( loading_instances == 0 ){
                $(".loading_indicator").hide();
            }
        }

        return {
            show:show_loading_indicator,
            hide:hide_loading_indicator
        }
    }
);