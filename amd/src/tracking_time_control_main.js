// Standard license block omitted.
/*
 * @package    block_ases/tracking_time_control_main
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/tracking_time_control_main
 */
define(['jquery', 'block_ases/bootstrap', 'block_ases/datatables.net', 'block_ases/datatables.net-buttons', 'block_ases/buttons.flash', 'block_ases/jszip', 'block_ases/pdfmake', 'block_ases/buttons.html5', 'block_ases/buttons.print', 'block_ases/sweetalert', 'block_ases/select2'], function($,bootstrap, datatablesnet, datatablesnetbuttons, buttonsflash, jszip, pdfmake, buttonshtml5, buttonsprint, sweetalert, select2) {


    return {

        init: function() {

        $(document).ready(function() {
            $('#table_hours').DataTable();
            } );
        }
    };
});