 /**
  * @module block_ases/side_menu_script
  */

define(['jquery', 'block_ases/bootstrap'], function($, bootstrap) {  

    return {

        init: function(){
    
            function openNav() {
                document.getElementById("mySidenav").style.width = "250px";
            }

            function closeNav() {
                document.getElementById("mySidenav").style.width = "0";
            }
        }
    };
});