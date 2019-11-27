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

    jQuery(document).on( event = 'submit',  selector = 'form[data-dphpforms="dphpforms"]', callback = function (evt) {

        evt.preventDefault();
      
        let formData = new FormData(this);

        let form = jQuery(this);
        let url_processor = get_processor_url( form );
        
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
        
    jQuery(document).on( event = 'click', selector = '.dphpf-text-list-add-elem-btn', callback = function (evt) {

        let elem = jQuery(this);
    
        let block_uuid = elem.data( "uid" );
        
        let template = jQuery( jQuery( `div[data-uid='${ block_uuid }']` ).find("template").html() );
        
        template.appendTo( `div[data-uid='${ block_uuid }']` );
        
        console.log( template );
        
    });

    console.log("Dphpforms CORE initialised");

    return {
        init: () => {
        }
    };
});