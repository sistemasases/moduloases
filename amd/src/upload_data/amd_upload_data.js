requirejs(['jquery', 'bootstrap', 'sweetalert', 'validator'], function($) {

    $(document).ready(function() {
        mostrarArchivos();
        var form = document.getElementById('upload_data_form');
        $("#load_button").on('click', function() {
            subir_archivos(form);
        });
        $("#archivos_subidos").on('click', '.eliminar_archivo', function() {
            var archivo = $(this).parents('.row').eq(0).find('span').text();
            archivo = $.trim(archivo);
            eliminarArchivos(archivo);
        });
        $("#respuesta").on('click', '.continue', function() {
            setTimeout('document.location.reload()', 500);
        });
    });

    /*
        Carga de archivos
    */
    function subir_archivos(form) {

        let status_bar = form.children[2].children[0].children[0],
            span = status_bar.children[0],
            goback_button = form.children[4].children[1];

        response_div = document.getElementById('response_div');
        span_div = document.getElementById('response_span');

        span_div.innerHTML = "";
        response_div.setAttribute('hidden', 'hidden');

        status_bar.classList.remove('green_bar', 'red_bar');

        //Ajax request
        let request = new XMLHttpRequest();

        // Progress bar

        request.upload.addEventListener("progress", (event) => {
            let percent = Math.round((event.loaded / event.total) * 100);

            status_bar.style.width = percent + '%';
            span.innerHTML = percent + '%';

        });

        request.addEventListener("load", () => {
            if (request.response == '1') {
                status_bar.classList.add('green_bar');
                span.innerHTML = "Éxito";
                response_div.removeAttribute('hidden');
                response_span.classList.add('response_success');
                response_span.innerHTML += 'El contenido del archivo ha sido cargado con éxito en la base de datos.';
            }
            else {
                status_bar.classList.add('red_bar');
                span.innerHTML = "Error";
                response_div.removeAttribute('hidden');
                response_span.classList.add('response_error');
                response_span.innerHTML += request.response;
            }

        });

        request.open('post', '../managers/subir_archivo.php');

        request.send(new FormData(form));

        goback_button.addEventListener('click', () => {
            request.abort();
            status_bar.classList.remove('green_bar');
            status_bar.classList.add('red_bar');
            span.innerHTML = "Proceso cancelado";
        });
    }

    /*
     Eliminar archivos
    */
    function eliminarArchivos(archivo) {
        $.ajax({
            url: '../managers/eliminar_archivo.php',
            type: 'POST',
            timeout: 10000,
            data: {
                archivo: archivo
            },
            error: function() {
                mostrarRespuesta('Error al intentar eliminar el archivo.', false);
            },
            success: function(respuesta) {
                if (respuesta == 1) {
                    mostrarRespuesta('El archivo ha sido eliminado.', true);
                }
                else {
                    mostrarRespuesta('Error al intentar eliminar el archivo.', false);
                }
                mostrarArchivos();
            }
        });
    }

    /*
    Mostrar archivos
    */
    function mostrarArchivos() {
        $.ajax({
            url: '../managers/mostrar_archivos.php',
            dataType: 'JSON',
            success: function(respuesta) {
                if (respuesta) {
                    var html = '';
                    for (var i = 0; i < respuesta.length; i++) {
                        if (respuesta[i] != undefined) {
                            html += '<div class="row"> <span class="col-md-3"> ' + respuesta[i] + ' </span> <div class="col-md-2"> <a class="eliminar_archivo btn btn-danger " id="btn_eliminar" name="elim" href="javascript:void(0);"> Eliminar </a> </div> </div> <hr />';
                        }
                    }
                    $("#archivos_subidos").html(html);
                }
            }
        });
    }

    function mostrarRespuesta(mensaje, ok) {
        $("#respuesta").removeClass('alert-success').removeClass('alert-danger').html(mensaje);
        if (ok) {
            $("#respuesta").addClass('alert-success');
            setTimeout('document.location.reload()', 500);
        }
        else {
            $("#respuesta").addClass('alert-danger');
            var btn_continue = $('<br> <div class="row"><div class="col-md-2 col-md-offset-5"><a class="continue btn btn-danger" href="javascript:void(0);"> Continuar </a></div></div>');
            btn_continue.appendTo($("#respuesta"));

        }
    }
});
