/**
 * Send e-mails to different groups of students or stuff of ASES.
 *
 * @module amd/src/communications
 * @author Jorge Eduardo Mayor Fernández
 * @copyright  2020 Jorge Eduardo Mayor <mayor.jorge@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([jquery], function($){

    return {
        init: function () {
            $("#button-send").on('click', send_email);
        }
    }

    function send_email() {
        $.ajax({
            type: "POST",
            data: JSON.stringify({
                "function": 'send_email',
                "params": [],
            }),
            url: "../managers/student_profile/communications_api.php",
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