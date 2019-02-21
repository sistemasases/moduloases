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
    'block_ases/ases_jquery_datatable',
    'core/config',
    'block_ases/jquery.dataTables',
    'block_ases/dataTables.buttons',
    'block_ases/buttons.html5',
    'block_ases/buttons.flash',
    'block_ases/buttons.print',

    ], function($, notification, templates, ases_jquery_datatable, CFG, dataTables, autoFill, buttons, html5, flash, print) {
    var instance_id = 0;
    function get_datatable_column_index(table, name) {
        var column = table.column(name);
        return column.name;
    }
    var get_cohort_id = function () {
        return $("#cohorts").val();
    };
    var get_endpoint_name = function () {
        return $("#endpoints").val();
    };
    var remove_alert_errors = function()  {
        $('#user-notifications .alert-error').remove();
    };
    var add_cell_style_and_errors = function (row, data) {
        if (data.error === "SI") {
            $(row).addClass('error');
            if (data.errors) {
                Object.keys(data.errors).forEach(property_name => {
                    /**
                     * Cada celda que tenga un error debe tener la clase error
                     * @see https://datatables.net/reference/option/rowCallback
                     */
                    $('td.' + property_name, row).addClass('error');
                    console.log( data.errors);
                    var error_names = data.errors[property_name].map(error => error.error_message);
                    var error_names_concat = error_names.join('; ');
                    /* Se añaden los mensajes de los errores al title de el campo en la tabla*/
                    $('td.' + property_name, row).prop('title', error_names_concat);
                });
            }
        }
    };
    var get_api_url = function() {

        return CFG.wwwroot + '/blocks/ases/managers/mass_management/mass_upload_api.php/'+ get_endpoint_name() + '/'+get_cohort_id() + '/' + instance_id;
    };


        var reinit_datatable = () => {
            if($.fn.DataTable.isDataTable("#example")) {
            $('#example').DataTable().destroy();
        }
        };
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
        var table_data = table.rows().data();
        var data_ = [];
        var data = [];
        for (var i = 0; i < table_data.length; i++) {
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
    var errors_object_to_erros =  (object_errors) => {
        var errors = [];
        Object.keys(object_errors).forEach( key => {
            errors = errors.concat(object_errors[key]);
        });

        return errors;
    };

    /**
     * Returned API data structure
     *
     * @property data array Array of objects returned based in the CSV registries
     * @property object_errors array Array with the same length than data, if object_errors in position
     *  0 is an object, the data[0] item have the errors contained in object_errors[0]
     *  in data array, if object_errors
     * @property warnings string[] Indexed as the same of object_errors
     * @property success_logs string[] Indexed as the same of object_errors
     * @property jquery_datatable Data table with all the info needed for show the CSV info in a datatable
     * @property error boolean Var than say if all operation has error or not
     * @property initial_object_properties string[] Array of all the property names of the objects
     *  generated from CSV data
     * @type {ApiData}
     */
    var ApiData /* @class */ = (function   () {


        function ApiData(data,
                        object_errors,
                        object_warnings,
                        success_logs_events,
                        jquery_datatable,
                        error,
                        initial_object_properties
        ) {
            this.data = data;
            this.object_errors = object_errors? object_errors: [];
            this.object_warnings = object_warnings ? object_warnings : [];
            this.success_logs_events = success_logs_events ? success_logs_events : [];
            this.error = error;
            this.initial_object_properties = initial_object_properties? initial_object_properties: [];
        }
        ApiData.prototype.get_messages = function() {
            var messages = [];
            for(var _i = 0; _i < this.data.length; _i++) {
                if (
                    (
                        this.object_errors[_i] && typeof this.object_errors[_i] === 'object' &&
                        this.object_errors[_i].constructor !== Array) ||
                    (this.object_warnings[_i] && this.object_warnings[_i].length > 0) ||
                    (this.success_logs_events[_i] && this.success_logs_events[_i].length > 0)
                ) {
                    messages.push(new MessagesObject(
                        _i + 1,
                        this.object_errors[_i],
                        this.object_warnings[_i],
                        this.success_logs_events[_i])
                    );
                }
            }
            return messages;
        };
        ApiData.get_from_response = function (response) {
            return new ApiData(
                response.data,
                response.object_errors,
                response.warnings,
                response.success_logs,
                response.jquery_datatable,
                response.error,
                response.initial_object_properties
                );
        };
        return ApiData;


    }());
    /**
     * Message object than contain the warnigns , success logs and errors for a messages table
     *
     * @property index int From 1 if posible
     * @property errors errors object of AsesError[]
     * @property warnings string[]
     * @property success_logs string[]
     * @type {MessagesObject}
     */
    var MessagesObject /* @class */ = (function   () {


        function MessagesObject(index,
                                errors,
                                warnings,
                                success_logs) {
            this.index = index;

            this.errors = errors ? errors_object_to_erros(errors) : [];
            this.warnings = warnings ? warnings : [];
            this.success_logs = success_logs ? success_logs : [];
        }
        return MessagesObject;


    }());

        return {
            /**
             * @param data contiene el id de la instancia
             */
            init: function (data) {
                instance_id = data.instance_id;
                var myTable = null;
                var initial_object_properties = null;


                /**
                 * Pinta los errores individuales genericos de los objetos.
                 * Los errores genericos de los objetos son todos aquellos que estan relacionados a más de un
                 * atributo de este, como incumplimiento de llaves compuestas, etc
                 * Los demás errores que esten relacionados solo a un atributo de el objeto se pintara
                 * en la tabla jquery al pasar el raton sobre este.
                 */


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
                            url: get_api_url(),
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
                        reinit_datatable();

                        $.ajax({
                            url: get_api_url(),
                            data: data,
                            cache: false,
                            contentType: false,
                            dataType: "html",
                            processData: false,
                            type: 'POST',
                            error: function (response) {
                                if (myTable) {
                                    myTable.destroy();
                                }
                                console.log(response);
                                remove_alert_errors();
                                var error_object = JSON.parse(response.responseText);
                                console.log(error_object);
                                var datatable_preview = error_object.datatable_preview;
                                if( error_object.object_errors && error_object.object_errors.generic_errors ) {
                                    var error_messages = error_object.object_errors.generic_errors.map(error => error.error_message);

                                    load_preview(datatable_preview, error_object.object_errors.generic_errors[0]);
                                    error_messages.forEach((error_message) => {
                                       notification.addNotification({
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
                                console.log(response);

                                /**
                                 * Se guardan las propiedades iniciales de los objetos cuando llegan de el servidor
                                 * Estas son importantes ya que para gestion de la información en la tabla, los datos
                                 * sufren modificaciones estructurales, donde son añadidas algunas propiedades.
                                 */
                                initial_object_properties = response.initial_object_properties;
                                var api_data = ApiData.get_from_response(response);
                                var errors = api_data.object_errors;
                                var jquery_datatable = response.jquery_datatable;

                                var messages = api_data.get_messages();
                                /* Se borran los mensajes previos y se muestran los nuevos*/
                                $('#messages_area').html('');
                                templates.render('block_ases/massive_upload_messages', {data: messages} )
                                    .then((html, js) => {
                                       templates.appendNodeContents('#messages_area', html, js);
                                    });
                                /**
                                 * Se añade el error de cada objeto a si mismo. Estos errores vienen en  response.object_errors,
                                 * este objeto es un diccionario donde las llaves son las posiciones que un objeto ocupa en
                                 * datatable.data
                                 */
                                Object.keys(errors).forEach((position, index) => {
                                    jquery_datatable.data[position].errors = errors[position];
                                });
                                /* Cada elemento data de la tabla debe tener una propiedad llamada index para que la columna
                                * de indices para las filas pueda existir*/
                                jquery_datatable.data.forEach((element, index) => {
                                    element.index = index + 1;
                                });
                                /**
                                 * Se añade la propiedad que indicara si el objeto tine o no errores, para que la columna
                                 * 'Errores' pueda existir. Esta información sera eliminada al momento de requerir los datos
                                 */
                                jquery_datatable.data.forEach((element, index) => {
                                    if (errors[index] && (errors[index].length > 0 || errors[index].constructor === Object ))  {
                                        element.error = 'SI';
                                    } else {
                                        element.error = 'NO';
                                    }

                                });

                                /*Se añade la columna que llevara los indices de las filas en orden (1,2,3,4,5...)*/
                                jquery_datatable.columns.unshift({
                                    "name": 'index',
                                    "data": 'index',
                                    "title": 'Linea'
                                });
                                /**
                                 * Se añade la columna que llevara el echo de si el
                                 * dato tiene error o no, puede tomar los valores 'SI' o 'NO'
                                 */
                                jquery_datatable.columns.push({
                                    "name": 'error',
                                    "title": 'Error',
                                    "data": 'error'
                                });


                                /* Se añade la función que modificara la vista de cada fila a la tabla*/
                                jquery_datatable.rowCallback = add_cell_style_and_errors;
                                /*Se añade el filtro de opciones en la columna error*/
                                jquery_datatable.initComplete = ases_jquery_datatable.add_column_filters(['error:name']);

                                if (response.errors.length === 0) {
                                    myTable = $('#example').DataTable(jquery_datatable);
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



