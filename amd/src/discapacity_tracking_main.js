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

                    loading_indicator.show();

                    $.ajax({
                        url: "../templates/_discapacity_reasonable_adjusment_theme.mustache",
                        data: null,
                        dataType: "text",
                        async: false,
                        success: function( template ){
                            loading_indicator.hide();
                            let html_to_load = template;
                            //Crear JSON con general_modal_manager
                            gmm.generate_modal("modal_to_reasonable_adjusment", "Ajustes razonables", html_to_load, null, function(){ gmm.show_modal( ".modal_to_reasonable_adjusment" ) });
              
                        },
                        error: function(){
                            loading_indicator.hide();
                            console.log( "../templates/_discapacity_reasonable_adjusment_theme.mustache cannot be reached." );
                        }
                    });
                 });

                //Delete table row
                $(document).on('click', '#table_actions_to_discapacity_tracking tbody tr td button', function () {
                    $(this).parent().parent().remove();
                });

                //Insert new record of reasonable adjusment
                $(document).on('click', '#insert_record_reasonable_adjusment', function () {
                    
                    loading_indicator.show();
                    //Function to get record
                    //Function to validate record
                    //Function to insert record
                    insert_record_reasonable_adjusment();
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
                    nuevaFila += '<td style="width: 10px !important;"> <button type ="button" id="bt_delete_action" title="Eliminar acción" name="btn_delete_person" style="visibility:visible;"> </button></td> </tr>';
                    $("#table_actions_to_discapacity_tracking").find("tbody").append(nuevaFila);
    
                });

        
             /**
            * Function: insert_record_reasonable_adjusment()
            * Params: undefined
            * Result: String
            */
           function insert_record_reasonable_adjusment() {

            //loading_indicator.show();

            $.ajax({
                url: '../managers/student_profile/reasonable_adjusment_api.php',
                data: JSON.stringify({
                    "function": 'insert_reasonable_adjusment',
                    "params": [id_ases, record_object]
                }),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST', 
                success: function (msg) {
                    loading_indicator.hide();
                    if(msg.status_code == 0) {
                        loading_indicator.hide();
                        swal(
                            "Éxito",
                            msg.message,
                            "success"
                        );
                    }else {
                        loading_indicator.hide();
                        console.log(msg);
                    }
                   
                },
                error: function (msg) {
                    loading_indicator.hide();
                    swal(
                        "Error",
                        msg.message,
                        "error"
                    );
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
            * Function: getIdinstancia()
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