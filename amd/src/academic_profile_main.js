 /**
 * Academic report management
 * @module amd/src/academic_reports
 * @author Camilo José Cruz rivera
 * @copyright 2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co> 
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/jqueryui'], function($, bootstrap, jqueryui) {
    
    return {

        /**
         * 
         */
        init: function() {

            $(document).ready(function(){
                if($(".bajo").length != 0){
                    $(".bajo").parent().parent().parent().parent().prev().toggleClass('bajo');
                }
                if($(".estimulo").length != 0){
                    $(".estimulo").parent().parent().parent().parent().prev().toggleClass('estimulo');
                }
                if($(".cancelacion").length != 0){
                    $(".cancelacion").parent().parent().parent().parent().prev().toggleClass('cancelacion');
                }
            });           
        }
        
    }
});