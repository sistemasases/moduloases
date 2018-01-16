// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_ases/massmanagement_main
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/datatables.net', 'block_ases/datatables.net-buttons', 'block_ases/buttons.flash', 'block_ases/jszip', 'block_ases/pdfmake', 'block_ases/buttons.html5', 'block_ases/buttons.print', 'block_ases/sweetalert', 'block_ases/select2'], function ($, bootstrap, datatables, sweetalert, select2) {


    return {
        init: function () {

            var val = $('#selector').val();
            addHelpMessage(val);
            $('#selector').on('change', function () {
                var val = $('#selector').val();
                addHelpMessage(val);
            });

            $('#boton_subir').on('click', function () {
                $('#informacion').empty();
                uploadFile();
            });

            function getUrlParams(page) {
                // This function is anonymous, is executed immediately and 
                // the return value is assigned to QueryString!
                var query_string = [];
                var query = document.location.search.substring(1);
                var vars = query.split("&");
                for (var i = 0; i < vars.length; i++) {
                    var pair = vars[i].split("=");
                    query_string[pair[0]] = pair[1];
                }

                return query_string;
            }

            function uploadFile() {

                var urlParameters = getUrlParams(document.location.search); //metodo definido en checrole

                var formData = new FormData();

                formData.append('idinstancia', urlParameters.instanceid);

                if ($('#archivo')[0].files[0] == undefined) {
                    swal({
                        title: "Archivo no registrado.",
                        text: "Seleccione el archivo a subir",
                        html: true,
                        type: "error",
                        confirmButtonColor: "#d51b23"
                    });
                    var val = $('#selector').val();
                    addHelpMessage(val);
                    return;
                }

                formData.append('file', $('#archivo')[0].files[0]);

                var controler = '';

                switch ($('#selector').val()) {
                    case 'monitor_estud':
                        controler = 'mrm_monitor_estud.php'; //
                        break;
                    case 'roles_usuario':
                        controler = 'mrm_roles.php'; //
                        break;
                    default:
                        return 0;
                }

                $.ajax({
                    url: '../managers/mass_management/' + controler,
                    data: formData,
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    // parametros necesarios para la carga de archivos
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('#response').html("<img src='../icon/facebook.gif' />");
                    },
                    success: function (msj) {

                        $('#response').empty();

                        $('#informacion').empty();

                        if (msj.success) {
                            $('#informacion').append('<div class="alert alert-success"><h4 align="center">Información</h4><strong>Exito!</strong> <br><p>' + msj.success + '</p></div>');
                        } else if (msj.warning) {
                            $('#informacion').append('<div class="alert alert-warning"><h4 align="center">Información</h4><strong>Cargado con inconsitencias!</strong> <br>' + msj.warning + '</div>');
                        } else if (msj.error) {
                            $('#informacion').append('<div class="alert alert-danger"><h4 align="center">Información</h4><strong>Error!</strong> <br>' + msj.error + '</div>');
                        }

                        $('#informacion').append(msj.urlzip);
                    },
                    error: function (msj) {
                        alert("error ajax");
                        $('#response').html("");
                    }
                    // ... Other options like success and etc
                });

            }
            function addHelpMessage(selector) {
                $('#informacion').empty();
                switch (selector) {
                    case 'monitor_estud':
                        $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>username_monitor</li><li>username_estudiante</li> </ul> </p></div>');
                        break;
                    case 'roles_usuario':
                        $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>username</li><li>rol(administrativo, reportes,profesional_ps, monitor_ps,  estudiante_t ó practicante_psp)</li> </ul> </p><p>Columnas extras aceptadas: <ul> <li>jefe</li>  </ul> </p></div>');
                        break;

                    default:
                    // code
                }
            }


        }
    };
});