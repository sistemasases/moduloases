// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/geographic_main
  */

define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui'], function($, bootstrap, sweetalert, jqueryui) {

    return {

        init: function(){

            var id_ases = $('#id_ases').val()

            $('#button_edit_geographic').on('click', function(){
                $('#button_edit_geographic').attr('hidden', true);
                $('#div_save_buttons').removeAttr('hidden');
                $('#select_neighborhood').removeAttr('disabled');
                $('#select_geographic_risk').removeAttr('disabled');
                $('#latitude').removeAttr('disabled');
                $('#longitude').removeAttr('disabled');
            });

            $('#button_cancel_geographic').on('click', function(){
                $('#button_edit_geographic').removeAttr('hidden');
                $('#div_save_buttons').attr('hidden', true);
                $('#select_neighborhood').attr('disabled', true);
                $('#select_geographic_risk').attr('disabled', true);
                $('#latitude').attr('disabled', true);
                $('#longitude').attr('disabled', true);
            });

            load_geographic_info(id_ases);
        }

    }

    function load_geographic_info(id_ases){
        $.ajax({
            type: "POST",
            data: {
                func: 'load_geographic_info',
                id_ases: id_ases
            },
            url: "../managers/student_profile/geographic_serverproc.php",
            success: function(msg) {

                console.log(msg);
            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
                console.log(msg);
            },
        });
    }

})