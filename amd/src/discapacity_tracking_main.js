/**
 * Controls discapacity form
 * @module amd/src/dphpforms_form_discapacity
 * @author Juan Pablo Castro
 * @copyright 2018 Juan Pablo Castro<juan.castro.vasquez@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'block_ases/jszip',
    'block_ases/jquery.dataTables',
    'block_ases/dataTables.autoFill',
    'block_ases/dataTables.buttons',
    'block_ases/buttons.html5',
    'block_ases/buttons.flash',
    'block_ases/buttons.print',
    'block_ases/bootstrap',
    'block_ases/sweetalert',
    'block_ases/jqueryui',
    'block_ases/select2',
    'block_ases/_general_modal_manager',
    'block_ases/mustache',
    'block_ases/loading_indicator'
], function ($, jszip, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2,gmm, mustache, loading_indicator) {
    return {
        init: function(){

                //Load css file to _discapacity_reasonable_adjusment_theme.mustache

                let css_location = "../style/_discapacity_reasonable_adjusment_theme.css";
    
                $('head').append('<link rel="stylesheet" href="' + css_location + '" type="text/css" />');

                //Load _discapacity_reasonable_adjusment_theme.mustache into modal_manager
                $(document).on('click', '#add_discapacity_tracking',function(){
                    load_discapacity_reasonable_adjusment_theme();
                 });

                //Delete table row
                $(document).on('click', '#table_actions_to_discapacity_tracking tbody tr td button', function () {
                    $(this).parent().parent().remove();
                });
    
                /**
                 * Add new row
                */
                $(document).on('click', '#bt_add_action_to_discapacity_tracking', function () {
    
                    let nuevaFila = "";
                    nuevaFila += '<tr><td> <input name="achievement_indicator" class="input_fields_general_tab"  type="text"/></td>';
                    nuevaFila += '<td> <input name="action_performed" class="input_fields_general_tab"  type="text" /></td>';
                    nuevaFila += '<td>  <select name="cars" class="custom-select">';
                    nuevaFila += '<option selected disabled>Seleccione un estado de acción</option>';
                    nuevaFila += '<option value="1">Urgente</option>';
                    nuevaFila += '<option value="2">Realizado</option>';
                    nuevaFila += '<option value="3">En proceso</option>';
                    nuevaFila += '<option value="4">A futuro</option>';
                    nuevaFila += '<option value="5">Descartado</option>';
                    nuevaFila += '</select> </td>';
                    nuevaFila += '<td> <button type ="button" id="bt_delete_action" title="Eliminar acción" name="btn_delete_person" style="visibility:visible;"> </button></td> </tr>';
                    $("#table_actions_to_discapacity_tracking").find("tbody").append(nuevaFila);
    
                });

            /**
            * @author Juan Pablo Castro <juan.castro.vasquez@correounivale.edu.co>
            * @see load_discapacity_reasonable_adjusment_theme()
            * @desc Loads the specified discapacity_reasonable_adjusment_theme.
            */
            function load_discapacity_reasonable_adjusment_theme() {

             loading_indicator.show();

                var id_ases = $('#id_ases').val();
                var theme = "_discapacity_reasonable_adjusment_theme.mustache";

                $.ajax({
                type: "POST",
                data: JSON.stringify({
                            "function": 'load_discapacity_reasonable_adjusment_theme',
                            "params": [id_ases, theme],
                    }),
                    url: "../managers/student_profile/reasonable_adjusment_api.php",
                    success: function(msg) {
                        if(msg.status_code == 0) {
                            $.ajax({
                                url: "../templates/"+theme,
                                data: null,
                                dataType: "text",
                                async: false,
                                success: function( template ){
                                    loading_indicator.hide();
                                    let html_to_load = $(mustache.render( template, msg.data_response ));
                                    //Crear JSON con general_modal_manager
                                    gmm.generate_modal("modal_to_reasonable_adjusment", "Ajustes razonables", html_to_load, null, function(){ gmm.show_modal( ".modal_to_reasonable_adjusment" ) });
                                },
                                error: function(){
                                    loading_indicator.hide();
                                    console.log( "../templates/view_"+tab_name+"_tab_sp.mustache cannot be reached." );
                                }
                            });
                        } else {
                            loading_indicator.hide();
                            console.log(msg);
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        loading_indicator.hide();
                        console.log(msg);
                    }
                });
         }

            /**
            * Function: has_numbers(str)
            * Params: str (String)
            * Result: true or false
            */
            function has_numbers(str) {
                var numbers = "0123456789";
                for (i = 0; i < str.length; i++) {
                    if (numbers.indexOf(str.charAt(i), 0) != -1) {
                        return 1;
                    }
                }
                return 0;
            }

            /**
            * Function: getIdinstancia(str)
            * Params: undefined
            * Result: String
            */
            function getIdinstancia() {
                var urlParameters = location.search.split('&');

                for (x in urlParameters) {
                    if (urlParameters[x].indexOf('instanceid') >= 0) {
                        var intanceparameter = urlParameters[x].split('=');
                        return intanceparameter[1];
                    }
                }
                return 0;
            }

            /**
            * Function: getIdcourse(str)
            * Params: undefined
            * Result: String
            */
            function getIdcourse() {
                var urlParameters = location.search.split('&');

                for (x in urlParameters) {
                    if (urlParameters[x].indexOf('courseid') >= 0) {
                        var intanceparameter = urlParameters[x].split('=');
                        return intanceparameter[1];
                    }
                }
                return 0;
            }

        }

    };
});