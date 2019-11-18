// Standard license block omitted.
/**
 * General Dphpforms CORE JavaScript module.
 * 
 * @package     block_ases
 * @copyright   ASES
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @module block_ases/dphpforms
 */

define([
    'jquery'
], function (jQuery) {

    const DEV_MODE = true;
    const DEF_PROCESSOR_PATH = '../managers/dphpforms/procesador.php';

    if (DEV_MODE) {
        console.log("Developer Mode Activated!!!");
    }
    
    function get_processor_url( form ){
        return ( 
            form.attr('action') === 'procesador.php' ? 
            DEF_PROCESSOR_PATH : 
            form.attr('action') 
        );
    }

    jQuery(document).on('submit', 'form[data-dphpforms="dphpforms"]', function (evt) {

        evt.preventDefault();
      
        var formData = new FormData(this);

        var form = jQuery(this);
        var url_processor = get_processor_url( form );
        
        jQuery( form ).find('button').prop("disabled", true);
        jQuery( form ).find('input[type="button"]').attr("disabled", true);
        
       
        jQuery.ajax({
            type: 'POST',
            url: url_processor,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                
            },
            error: function (data) {
                
            }
        });
    });

    console.log("Dphpforms CORE initialised");

    return {
        init: () => {
        }
    }
}
);