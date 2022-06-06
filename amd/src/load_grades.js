/**
 * Load grades
 *
 * @module    amd/src/load_grades
 * @author    David Santiago Cortés
 * @copyright 2022 David Santiago Cortés <david.cortes@correounivalle.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/sweetalert','block_ases/mustache'], function($, sweetalert, mustache){

    return {
        init: function () {
            const urlParams = new URLSearchParams(window.location.search);

            let formData = new FormData();
            let grades;

            $("[name='csv_file_loader']").on('change', () => {
                formData.append('file', $("[name='csv_file_loader']")[0].files[0])
            })

			$("#btn-upload-csv").on('click', () => {
                $.ajax({
                    url: "../managers/load_grades/load_grades_api.php",
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: (msg) => {

                        const data = {
                            message: msg.message,
                            type: "success"
                        }

                        grades = msg.data_response

                        showInfo(data)

                        $("#btn-alertas").removeClass("disabled")
                    },
                    error: (msg) => {
                        console.error(msg)
                        swal(
                            "Oops",
                            msg.message,
                            "error"
                        )
                    }
                })
            })

            $("#btn-alertas").on('click', () => {
                $.ajax({
                    url: "../managers/load_grades/load_grades_api.php",
                    type: "POST",
                    data: JSON.stringify({
                        "function": "send_alerts",
                        "params": [grades, urlParams.get('instanceid')],
                    }),
                    cache: "false",
                    success: (msg) => {
                        msg = JSON.parse(msg)
                        const obj = {
                            message: msg.message,
                            type: "none",
                            students: Object.values(msg.data_response)
                        }
                        showInfo(obj)

                        $("#btn-alertas").addClass("disabled")
                    },
                    error: (msg) => {
                        console.error(msg)
                        swal(
                            "Oops",
                            msg.message,
                            "error"
                        )
                    }
                })
            })
        }
    }
    function showInfo(data) {
        $.ajax({
            url: "../templates/load_grades_info_tab.mustache",
            data: null,
            dataType: "text",
            success: (template) => {
                let content = $(mustache.render(template, data))
                $("#div-info").append(content);
            }
        })
    }
});
