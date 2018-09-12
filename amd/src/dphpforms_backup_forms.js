
// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_ases/backup_forms
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
        init: function () {
            window.JSZip = jszip;
            $(document).ready(function () {
                $.ajax({

                    type: "POST",
                    data: { loadF: 'loadForms' },
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {
                        $("#div_table_forms").empty();
                        $("#div_table_forms").append('<table id="tableBackupForms" class="display" cellspacing="0" width="100%"><thead><thead></table>');
                        var table = $("#tableBackupForms").DataTable(msg);
                        $('#div_table_forms').css('cursor', 'pointer');
                    },
                    dataType: "json",
                    cache: false,
                    async: true,

                    failure: function (msg) { }
                });
  
            });

            $(document).on('click', '#tableBackupForms tbody tr td', function () {
                var valores = "";

                // Obtenemos todos los valores contenidos en los <td> de la fila
                // seleccionada
                $(this).parents("tr").find("td").each(function () {
                    valores = $(this).html();
                });

                alert(valores);
                get_only_form(valores);

               // $('#modalFormulario').show();

            });

          
             $('#generarFiltro').on('click', function () {
                    var porId= document.getElementById("filtroCodigoUsuario").value;
                    var codigoUsuario = porId;
                    get_id_switch_user(codigoUsuario);
    
             });
             
            function get_id_switch_user(cod_user){
                $.ajax({
                    type: "POST",
                    data:{loadF: 'get_id_user', param: cod_user},
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {
                        $("#div_table_user").empty();
                        $("#div_table_user").append('<table id="tableUser" class="display" cellspacing="0" width="100%"><thead><thead></table>');
                        var table = $("#tableUser").DataTable(msg);
                        $('#div_table_user').css('cursor', 'pointer');
                           /* swal(
                                "Error",
                                "No se encuentra un estudiante asociado al código ingresado",
                                "error"
                            );*/
                        
                        
                    },
                    dataType: "json",
                    cache: false,
                    async: true,

                    failure: function (msg) { }
                });
            } 

            
          

            function get_only_form(id_form){
                //Get form switch id
                $.ajax({
                    type: "POST",
                    data:{loadF: 'get_form', params: id_form},
                    url: "../managers/dphpforms/dphpforms_dwarehouse_api.php",
                    success: function (msg) {
                        $("#div_table_form").empty();
                        $("#div_table_form").append('<table id="tableBackupForm" class="display" cellspacing="0" width="100%"><thead><thead></table>');
                        var table = $("#tableBackupForm").DataTable(msg);
                        $('#div_table_form').css('cursor', 'pointer');
                    },
                    dataType: "json",
                    cache: false,
                    async: true,

                    failure: function (msg) { }
                });
            }

            modal_manage_form();

            function modal_manage_form() {

                // Get the modal
                var modal = $('#modalFormulario');

                // Get the <span> element that closes the modal
                var span_close = $('.mymodal-close');
                var goback_backdrop = $('#goback_backdrop');

                goback_backdrop.on('click', function () {
                    modal.hide();
                });

                // When the user clicks on <span> (x), close the modal
                span_close.on('click', function () {
                    modal.hide();
                });
            }

        }

    };
});