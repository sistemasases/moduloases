/**
 * Load grades
 *
 * @module    amd/src/load_grades
 * @author    David Santiago Cortés
 * @copyright 2022 David Santiago Cortés <david.cortes@correounivalle.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'block_ases/sweetalert','block_ases/mustache'], function($, swal, mustache){

    return {
        init: function () {
            let formData = new FormData();

            $("[name='csv_file_loader']").on('change', () => {
                formData.append('file', $("[name='csv_file_loader']")[0].files[0])
            })

			$("#btn-upload-csv").on('click', () => {
                console.log("Enviando...")
            })
        }
    }
});
