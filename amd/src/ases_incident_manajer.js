// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @author     Jeison Cardona Gómez
 * @copyright  Jeison Cardona Gómez < jeison.cardona@correounivalle.edu.co >
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/ases_incident_manager
  */

 define(
    [
        'jquery', 
        'block_ases/sweetalert', 
        'block_ases/jqueryui',
        'block_ases/loading_indicator'
       ], 
        function($, sweetalert, jqueryui, li ) {
   
           console.log( "ases_incident_manager loaded" );

           return {
                init: function(){}
           }
       }
);