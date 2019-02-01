/**
 * Massive data upload generic javascript
 * @module amd/src/course_and_techar_report
 * @author Luis Gerardo Manrique Cardona
 * @copyright 2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
        'jquery',
        'core/notification',
        'core/templates',
    'block_ases/jquery.dataTables',
    'block_ases/dataTables.buttons',
    'block_ases/buttons.html5',
    'block_ases/buttons.flash',
    'block_ases/buttons.print',
    ], function($, notification, templates, dataTables, autoFill, buttons, html5, flash, print) {
        return {
            /**
             * @param data contiene el id de la instancia
             */
            init: function (data) {
                var id_curso = data.id_curso;
                var myTable = null;
                var initial_object_properties = null;


                /**
                 * Pinta los errores individuales genericos de los objetos.
                 * Los errores genericos de los objetos son todos aquellos que estan relacionados a más de un
                 * atributo de este, como incumplimiento de llaves compuestas, etc
                 * Los demás errores que esten relacionados solo a un atributo de el objeto se pintara
                 * en la tabla jquery al pasar el raton sobre este.
                 */
                /**
                 * Actually, jquery datatables data function returns an object with more
                 * than is neccesary for get real data, this function extract the real data
                 * from datatables data whitout unnecesary info
                 * @param table Jquery Datatable
                 * @param initial_object_properties array Array of property names
                 * @return array of data
                 * @see https://datatables.net/reference/api/rows().data()
                 */
                function getTableData(table, initial_object_properties) {
                    table_data = table.rows().data();
                    var data_ = [];
                    var data = [];
                    for (i = 0; i < table_data.length; i++) {
                        data_.push(table_data[i]);
                    }
                    /* Get only the initial object properties (exclude indexes or aditional values added for jquery datatable suport*/
                    data_.forEach(element => {
                        var pure_element = {};
                        initial_object_properties.forEach(property => {
                            pure_element[property] = element[property];
                        });
                        data.push(pure_element);
                    });
                    return data;
                }

                $("#print-data").click(
                    function () {
                        console.log(getTableData(myTable, initial_object_properties));
                        console.log(get_datatable_column_index(myTable, 'error'));
                    });

                $("#send-data").click(
                    function () {
                        var data = getTableData(myTable, initial_object_properties);
                        console.log(data);
                        $.ajax({
                            url: 'receive_csv.php/estado_ases/',
                            data: {data: data},
                            type: 'POST',
                            success: function (response) {
                                console.log(response);

                            }
                        }).done(function (data, textStatus, jqXHR) {
                            if (console && console.log) {
                                console.log(data);
                                console.log("La solicitud se ha completado correctamente.");
                            }
                        })
                            .fail(function (jqXHR, textStatus, errorThrown) {
                                if (console && console.log) {
                                    console.log("La solicitud a fallado: " + textStatus);
                                    console.log(jqXHR);
                                }
                            });
                    });
                function get_datatable_column_index(table, name) {
                    var column = table.column(name);
                    return column.name;
                }
                /**
                 * Load preview datatable
                 * En caso de que el csv tenga mas o menos propiedades de las esperadas
                 * se debe mostrar una previsualización de los datos dados y cuales son las
                 * columnas que sobran o las que faltan
                 */
                function load_preview(data_table, error) {
                    myTable = $('#example').DataTable(data_table);

                    if(error.data_response && error.data_response.object_properties && error.data_response.file_headers) {
                        var correct_column_names = error.data_response.object_properties;
                        var given_column_names = error.data_response.file_headers;
                        console.log(error);
                        var missing_columns = correct_column_names.filter(element => given_column_names.indexOf(element) < 0);
                        var extra_columns = given_column_names.filter(element => {
                            return correct_column_names.indexOf(element) <= -1;
                        });
                        missing_columns.forEach(column => {
                            $('.' + column).css('background-color', '#cccccc');

                        });
                        extra_columns.forEach(column => {

                            $('.' + column).css('background-color', 'red');

                        });
                        console.log(extra_columns, missing_columns);
                    }
                }

                $('#send-file').click(
                    function () {
                        var data = new FormData($(this).closest("form").get(0));
                        var cohort_id = $('#cohorts').val();

                        $.ajax({
                            url: 'receive_csv.php/estado_ases/'+cohort_id + '/' + id_curso,
                            data: data,
                            cache: false,
                            contentType: false,
                            dataType: "html",
                            //contentType: 'multipart/form-data',
                            processData: false,
                            type: 'POST',
                            error: function (response) {
                                if (myTable) {
                                    myTable.destroy();
                                }
                                console.log(response);
                                $('#user-notifications .alert-error').remove();
                                var error_object = JSON.parse(response.responseText);
                                console.log(error_object);
                                var datatable_preview = error_object.datatable_preview;
                                if( error_object.object_errors && error_object.object_errors.generic_errors ) {
                                    var error_messages = error_object.object_errors.generic_errors.map(error => error.error_message);

                                    load_preview(datatable_preview, error_object.object_errors.generic_errors[0]);
                                    error_messages.forEach((error_message) => {
                                        var l = notification.addNotification({
                                            message: error_message,
                                            type: 'error'
                                        });
                                    });
                                }


                            },
                            success: function (response) {
                                console.log(response);

                                if (myTable) {
                                    myTable.destroy();
                                }
                                response = JSON.parse(response);

                                /**
                                 * Se guardan las propiedades iniciales de los objetos cuando llegan de el servidor
                                 * Estas son importantes ya que para gestion de la información en la tabla, los datos
                                 * sufren modificaciones estructurales, donde son añadidas algunas propiedades.
                                 */
                                initial_object_properties = response.initial_object_properties;
                                errors = response.object_errors;
                                console.log(errors);

                                var jquery_datatable = response.jquery_datatable;
                                /* Se añade el error de cada objeto a si mismo. Estos errores vienen en  response.object_errors,
                                * este objeto es un diccionario donde las llaves son las posiciones que un objeto ocupa en
                                * datatable.data*/
                                Object.keys(errors).forEach((position, index) => {
                                    jquery_datatable.data[position].errors = errors[position];
                                });
                                /* Cada elemento data de la tabla debe tener una propiedad llamada index para que la columna
                                * de indices para las filas pueda existir*/
                                jquery_datatable.data.forEach((element, index) => {
                                    element.index = index;
                                });
                                /**
                                 * Se añade la propiedad que indicara si el objeto tine o no errores, para que la columna
                                 * 'Errores' pueda existir. Esta información sera eliminada al momento de requerir los datos
                                 */
                                jquery_datatable.data.forEach((element, index) => {
                                    console.log(index);
                                    if (errors[index]) {
                                        element.error = 'SI';
                                    } else {
                                        element.error = 'NO';
                                    }

                                });

                                /*Se añade la columna que llevara los indices de las filas en orden (1,2,3,4,5...)*/
                                jquery_datatable.columns.unshift({
                                    "searchable": false,
                                    "orderable": false,
                                    "data": 'index',
                                    "targets": 0
                                });
                                /*Se añade la columna que llevara los indices de las filas en orden (1,2,3,4,5...)*/
                                jquery_datatable.columns.push({
                                    "name": 'error',
                                    "title": 'Error',
                                    "data": 'error'
                                });
                                /* Se añade la función que modificara la vista de cada fila a la tabla*/
                                jquery_datatable.rowCallback = function (row, data) {
                                    if (data.error === "SI") {
                                        $(row).addClass('error');
                                        if (data.errors) {
                                            Object.keys(data.errors).forEach(property_name => {
                                                /**
                                                 * Cada celda que tenga un error debe tener la clase error
                                                 * @see https://datatables.net/reference/option/rowCallback
                                                 */
                                                $('td.' + property_name, row).addClass('error');
                                                var error_names = data.errors[property_name].map(error => error.error_message);
                                                var error_names_concat = error_names.join('; ');
                                                /* Se añaden los mensajes de los errores al title de el campo en la tabla*/
                                                $('td.' + property_name, row).prop('title', error_names_concat);
                                            });
                                        }
                                    }
                                };
                                jquery_datatable.initComplete = function () {
                                    /*@see https://datatables.net/reference/type/column-selector*/

                                    var filter_columns = ['error:name'];
                                    this.api().columns(filter_columns).every(function () {
                                        var column = this;


                                        var select = $('<select><option value=""></option></select>')
                                            .appendTo($(column.header()))
                                            .on('change', function () {
                                                var val = $.fn.dataTable.util.escapeRegex(
                                                    $(this).val()
                                                );

                                                column
                                                    .search(val ? '^' + val + '$' : '', true, false)
                                                    .draw();
                                            });

                                        column.data().unique().sort().each(function (d, j) {
                                            select.append('<option value="' + d + '">' + d + '</option>');
                                        });
                                    });
                                };
                                /* El orden inicial de la tabla se da por su columna de indices de forma asendente*/
                                jquery_datatable.order = [[1, 'asc']];
                                if (response.errors.length === 0) {
                                    myTable = $('#example').DataTable(jquery_datatable);
                                    /* Se ordena y inicializa la columna que lleva los indices de las filas */
                                    myTable.on('order.dt search.dt', function () {
                                        myTable.column(0, {
                                            search: 'applied',
                                            order: 'applied'
                                        }).nodes().each(function (cell, i) {
                                            cell.innerHTML = i + 1;
                                        });
                                    }).draw();
                                } else {
                                    for (var error of response.errors) {
                                        console.log(error);

                                    }
                                }

                            }
                        });
                    });
            }
        };
    }
);



