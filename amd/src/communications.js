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
            $("#send-button").on('click', send_email);
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

    function send_email() {

        var to_users = document.getElementById('additional_email').value;
        var cohortes = $('#conditions').val();
        var subject = document.getElementById('subject').value;
        var message = document.getElementById('full_message').value;
        var course_id = getIdcourse();

        console.log(cohortes);
        console.log(subject);
        console.log(to_users);
        console.log(message);

        $.ajax({
            type: "POST",
            data: JSON.stringify({
                "function": 'send_email',
                "params": [to_users, cohortes, subject, message, course_id],
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