// Standard license block omitted.
/*
 * @package    block_ases/side_menu_script
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */ 
 
 /**
  * @module block_ases/side_menu_script
  */

define(['jquery', 'block_ases/bootstrap'], function($, bootstrap) {  

    return {

        init: function(){

            $('#closebutton').on('click', function(){
                closeNav();
            })
    
            
        }
    };

    function openNav() {
        document.getElementById("mySidenav").style.width = "250px";
    }

    function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
    }
});