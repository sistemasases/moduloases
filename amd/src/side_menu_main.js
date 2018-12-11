/**
 * Side menu management
 * @module amd/src/
 * @author Juan Pablo Moreno Muñoz
 * @copyright 2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($){
    return{
        init: function (){
            $(document).ready(function() {

                $("#menu_div li").each(function(){
                    var link_li = $("a", this).attr('href');
                    var window_url = window.location.href;
                    if(link_li != undefined){
                        if(link_li == window_url){
                            $("a", this).css('background-color', "#b6161e");
                        }                        
                    }
                });
            });
        }        
    };
});