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
    'block_ases/select2'
], function ($, jszip, dataTables, autoFill, buttons, html5, flash, print, bootstrap, sweetalert, jqueryui, select2) {
    return {
        init: function(){

            alert("hola");

            function has_numbers(str) {
                var numbers = "0123456789";
                for (i = 0; i < str.length; i++) {
                    if (numbers.indexOf(str.charAt(i), 0) != -1) {
                        return 1;
                    }
                }
                return 0;
            }

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