// Standard license block omitted.
/* @autor      Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_ases/upload_history_main
 */

define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert'], function ($, bootstrap, sweetalert) {


    return {
        init: function () {


            addHelpMessage();
            $('#selector').on('change', function () {
                addHelpMessage();
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
                    addHelpMessage();
                    return;
                }

                formData.append('file', $('#archivo')[0].files[0]);

                var controler = $('#selector').val() + '_processing.php';
                alert(controler);

                $.ajax({
                    url: '../managers/historic_management/' + controler,
                    data: formData,
                    type: 'POST',
                    dataType: 'json',
                    cache: false,
                    //parametros necesarios para la carga de archivos
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
                        alert("error en el servidor");
                        $('#response').html("");
                        addHelpMessage();
                    }
                    //... Other options like success and etc
                });

            }
            function addHelpMessage() {
                var selector = $('#selector').val();
                $('#informacion').empty();
                switch (selector) {
                    case 'academic':
                        $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información de carga Historico Academico</h4><br><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>username_estudiante</li> </ul> </p><p>Columnas extras aceptadas: <ul> <li>nota</li>  </ul> </p></div>');
                        break;
                    case 'icetex':
                        $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información de carga Historico ICETEX</h4><br> <strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul><li>username_estudiante</li></ul> </p><p>Columnas extras aceptadas: <ul> <li>otro</li>  </ul> </p></div>');
                        break;

                    default:
                    // code
                }
            }


        }
    };
});