/**
 * Controls discapacity form
 * @module amd/src/dphpforms_form_others_sp
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
    'block_ases/select2'
], function ($, jszip, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2) {
    return {   
            init: function(){

                if($("#input_economics_saved").val() == "0"){
                    $("#save_economics_data").parent().show();
                }
                if($("#input_economics_saved").val() == "1"){
                    $("#save_economics_data").parent().hide();
                    $("#edit_economics_data").parent().show();
                }


                $("#collapse_econo input[type=checkbox]").not(".check_solvencia").on("change", function(){
                    if($(this).is(":checked")){
                        $(this).parent().next().show();
                        $(this).parent().next().children().children("input").prop("required", true);
                    }else{
                        $(this).parent().next().hide();
                        $(this).parent().next().children().children("input").prop("required", false);
                        $(this).parent().next().children().children("input").val("");
                    }
                   
                });

                $(".select_ocupacion").on("change", function(){
                        $(this).parent().next().show();
                        $(this).parent().next().children("input").prop("required", true);
                        $(this).parent().next().children("input").val("");
                 
                });

            }
     };
});