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
    'jquery',
    'block_ases/_ases_api',
    'block_ases/sweetalert2',
    'block_ases/loading_indicator',
    'block_ases/jquery.scrollTo'
], (jQuery, asesApi, Swal2, loading_indicator, JQS) => {

    const DEV_MODE = true;
    const DEF_PROCESSOR_PATH = '../managers/dphpforms/procesador.php';

    global_response = [];
    
    if (DEV_MODE) {
        console.log("DphpForms: Developer Mode Activated!!!");
        let dev_style = `
            <style data-origin="dphpforms-dev-mode">
                .oculto{
                    display:block !important;
                }
            </style>
        `;
        jQuery(dev_style).appendTo("head");
    }
    
    // To refact.
    jQuery('.mymodal-close').click(function () {
        jQuery(this).parent().parent().parent().parent().fadeOut(300);
        jQuery("#list_grupal_seg_consult_1").remove();
    });

    function dphpformsJS_get_processor_url(form) {
        return (
                form.attr('action') === 'procesador.php' ?
                DEF_PROCESSOR_PATH :
                form.attr('action')
            );
    }

    function dphpformsJS_render_record(record_id) {

        return asesApi.post(
                "dphpforms", "render_record", [record_id],
                async = false, use_loading_indicator = true, 
                ok_callback = () => {}, error_callback = () => {}, 
                manager_version = 2
        );

    }
    
    function dphpformsJS_get_record( record_id ) {
        
        let record_raw = null;

        // TO UPDATE to dphpformsv2 and ases_api.
        jQuery.ajax({
            url: "../managers/dphpforms/dphpforms_get_record.php?record_id=" + record_id,
            dataType: 'json',
            async: false,
            success: function (data) {
                record_raw = data;
            }
        });
        
        return record_raw;

    }
    
    function dphpformsJS_get_k( record_id ) {
        
        return asesApi.post(
                "dphpforms", "get_k", [record_id],
                async = false, use_loading_indicator = true, 
                ok_callback = () => {}, error_callback = () => {}, 
                manager_version = 2
        ).data_response;

    }
    
    function dphpformsJS_download_record( filename, data ) {

        var file = new Blob([data], {type: ".json"});
        if (window.navigator.msSaveOrOpenBlob) // IE10+
            window.navigator.msSaveOrOpenBlob(file, filename);
        else { // Others
            var a = document.createElement("a"),
                    url = URL.createObjectURL(file);
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            setTimeout(function () {
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            }, 0);
        }

    }
    
    function dphpformsJS_update_risk_lvl(record_id) {
        
        // TO UPDATE to dphpformsv2 and ases_api.
        let api_url = "../managers/pilos_tracking/api_pilos_tracking.php?function=update_last_user_risk&arg=";
        let endpoint = api_url + get_student_code() + "&rid=" + record_id;
        jQuery.get( endpoint , () => {});

    }
    
    function dphpformsJS_scrollTo(uid, question_id, callback = ()=>{}) {

        jQuery(`form[data-uid="${ uid }"]`)
            .closest(".mymodal")
            .scrollTo(
                '.div-' + question_id, 
                {
                    duration: 1500,
                    offset: -150
                },
                {
                    easing:'easeInCubic',
                    onAfter: callback
                }
           );
   
    }
    
    function dphpformsJS_add_error_mark( uid, list_of_question_id ){
        
        jQuery(`form[data-uid="${ uid }"]`)
                .find('div')
                .removeClass('regla_incumplida');
        
        list_of_question_id.forEach( (id) => {
            jQuery(`form[data-uid="${ uid }"]`)
                .find(`.div-${id}`)
                .addClass('regla_incumplida');
        });
        
    }
    
    function dphpformsJS_show_warning(uid, message, questions_with_error = null, scroll_to = null) {

        if( questions_with_error !== null ){
            dphpformsJS_add_error_mark(uid, questions_with_error);
        }

        Swal2.fire({
            icon: 'warning',
            title: 'Alerta',
            text: message,
            onClose: () => {
                if( scroll_to !== null ){
                    dphpformsJS_scrollTo( uid, scroll_to );
                }
            }
        });

    }

    function dphpformsJS_process_response( uid ) {
        
        let response = global_response[uid]['success'];
        let message = "";
        
        if (DEV_MODE) {
            console.log(global_response);
            console.log(response);
        }

        if (response['status'] == 0) {
            
            //dphpformsJS_update_risk_lvl( record_id );
            
            if (response['message'] === 'Stored') {
                
                let record_id = response['data'];
                let record_raw = dphpformsJS_get_record( record_id );
                let record = JSON.stringify( record_raw );
                let k = dphpformsJS_get_k( record_id );
                
                let filename = jQuery("#dphpforms_fullname").data("info") + " - " + record_raw.record.alias + "-" + record_id + "-" + "k-" + k + " - Fecha " + record_raw.record.fecha_hora_registro + ".json";

                dphpformsJS_download_record( filename, record );
                
                Swal2.fire({
                    icon: 'info',
                    title: 'Información',
                    text: 'Almacenado, se va a descargar una copia de seguridad, por favor consérvela. Informe si no se genera la copia.'
                });

            } else if (response['message'] === 'Updated') {
                
                Swal2.fire({
                    icon: 'success',
                    title: 'Alerta',
                    text: 'Actualizado, las fechas e íconos se actualizarán al recargar la página.'
                });
                
            }
            //check_risks_tracking();
            //check_risks_geo_tracking();
            //Callback

        } else if (response['status'] == -6) {
            
            if (response['message'] === 'The value of the field is out of range') {
                
                var id_form_pregunta = response['data']['id'];
                
                message = 
                    'Ups!, el campo marcado en rojo tiene una fecha por fuera del siguiente rango: ' + 
                    response['data']['min'] + " hasta " + 
                    response['data']['max'];
            
                dphpformsJS_show_warning( 
                    uid,  message, [id_form_pregunta],  id_form_pregunta
                );
            
            }
            

        } else if (response['status'] == -5) {
            
            if (response['message'] === 'The field is static and can not be changed') {
                
                var id_form_pregunta = response['data']['id'];
                
                message = 
                    'Ups!, el campo marcado en rojo está definido como \n\
                    estático y por lo tanto debe mantener el mismo valor, \n\
                    si no logra ver el campo marcado en rojo informe de este \n\
                    incidente.';
                                
                dphpformsJS_show_warning( 
                    uid,  message, [id_form_pregunta],  id_form_pregunta
                );
                
            }
            
            
        } else if (response['status'] == -4) {
            
            if (response['message'] === 'Field does not match with the regular expression') {
                
                var id_form_pregunta = response['data']['id'];

                message = 
                    'Ups! El campo marcado en rojo no cumple con el \n\
                    patrón esperado(' + response['data']['human_readable'] + '). \n\
                    Ejemplo: ' + response['data']['example'];
                                
                dphpformsJS_show_warning( 
                    uid,  message, [id_form_pregunta],  id_form_pregunta
                );
            }
            
        } else if (response['status'] == -3) {
            
            if (response['message'] === 'Field cannot be null') {
                
                var id_form_pregunta = response['data'];
                
                message = 
                    'Ups! Los campos que se acaban de colorear en rojo \n\
                    no pueden estar vacíos, si no logra ver ningún campo, \n\
                    informe de este incidente.';
                
                dphpformsJS_show_warning( 
                    uid,  message, [id_form_pregunta],  id_form_pregunta
                );
        
            }
            
        } else if (response['status'] == -2) {
            
            if (response['message'] === 'Without changes') {
                
                dphpformsJS_show_warning( uid,  'No hay cambios que registrar' );
                
            } else if (response['message'] === 'Unfulfilled rules') {
                
                var id_form_pregunta_a = response['data']['id_form_pregunta_a'];
                var id_form_pregunta_b = response['data']['id_form_pregunta_b'];
                                
                dphpformsJS_show_warning( 
                    uid,  'Ups!, revise los campos que se acaban de colorear en rojo.', 
                    [id_form_pregunta_a, id_form_pregunta_b],  id_form_pregunta_a
                );
                
            }
            
        } else if (response['status'] == -1) {
                       
            Swal2.fire({
                icon: 'error',
                title: 'ERROR!',
                text: 'Ups!, informe de este error'
            });
            
        }
    }

    //------------------------------------
    jQuery(document).on(event = 'submit', selector = 'form[data-dphpforms="dphpforms"]', callback = function (evt) {

        loading_indicator.show();

        evt.preventDefault();

        let uid = jQuery(this).data('uid');

        let formData = new FormData(this);

        let form = jQuery(this);
        let url_processor = dphpformsJS_get_processor_url(form);

        //jQuery( form ).find('button').prop("disabled", true);
        //jQuery( form ).find('input[type="button"]').attr("disabled", true);
        
        global_response[uid] = [];

        jQuery.ajax({
            type: 'POST',
            url: url_processor,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            async: false,
            success: function (data) {
                global_response[uid]['success'] = data;
                dphpformsJS_process_response( uid );
            },
            error: function (data) {
                global_response[uid]['error'] = data;
            }
        });
        
        loading_indicator.hide();

    });

    //------------------------------------
    jQuery(document).on(event = 'click', selector = '.dphpf-text-list-add-elem-btn', callback = function (evt) {

        let elem = jQuery(this);

        let block_uuid = elem.data("uid");

        let template = jQuery(jQuery(`div[data-uid='${ block_uuid }'] template`).html());

        template.find("label").text("Extra element:");

        template.appendTo(`div[data-uid='${ block_uuid }'] .dphpf-elements`);

    });

    console.log("Dphpforms CORE initialised");

    return {
        init: () => {
        }
    };
});