// Standard license block omitted.
/*
 * @package    block_ases/instanceconfiguration_main
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/instanceconfiguration_main
  */

define(['jquery','block_ases/sweetalert','block_ases/datatables'], function($,sweetalert,datatables) {


  return {
      init: function() {
        

    function get_id_instance() {
        var urlParameters = location.search.split('&');

        for (x in urlParameters) {
            if (urlParameters[x].indexOf('instanceid') >= 0) {
                var intanceparameter = urlParameters[x].split('=');
                return intanceparameter[1];
            }
        }
        return 0;
    }




       }
    };
});