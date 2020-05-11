/**
 * Send e-mails to different groups of students or stuff of ASES.
 *
 * @module amd/src/communications
 * @author Jorge Eduardo Mayor Fernández
 * @copyright  2020 Jorge Eduardo Mayor <mayor.jorge@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/select2',], function($, select2){

    return {
        init: function () {
            //$("#send-button").on('click', send_email);
            $('#conditions').attr('multiple', true);

            $("#conditions").select2({

                language: {

                    noResults: function() {

                        return "No hay resultado";
                    },
                    searching: function() {

                        return "Buscando..";
                    }
                },
                dropdownAutoWidth: true,
            });
        }
    }

    function send_email() {
        var cohortes = $('#conditions').val();
        var subject = $('#subject').val();
        var to_users = $('#additional_email').val();
        var message = $('#full_message').val();

        $.ajax({
            type: "POST",
            data: JSON.stringify({
                "function": 'send_email',
                "params": [to_users, cohortes, subject, message],
            }),
            url: "../managers/communications/communications_api.php",
            dataType: "json",
            cache: "false",
            success: function(msg) {
                if(msg.status_code == 0) {
                    swal(
                        msg.title,
                        msg.message,
                        msg.type);
                } else {
                    console.log(msg);
                }
            },
            error: function(msg) {
                console.log(msg);
            }
        });
    }
});