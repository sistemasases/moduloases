<style>
    tr.error {
        background-color: indianred!important;
    }
    td.error {
        background-color: darkred!important;
    }
</style>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
                <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.7/css/select.dataTables.min.css">





        <script  src="https://code.jquery.com/jquery-3.3.1.min.js"  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="  crossorigin="anonymous"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    </head>
    <body>
        <form method="post" id="form-send-file" enctype="multipart/form-data" action="receive_csv.php">
            <input id="hi" type="file" accept=".csv"  name="fileToUpload" id="fileToUpload">
            <input value="hola" type="text"  name="text" id="text">
            <button type="button" id="send-file">Enviar</button>
        </form>


<button id = "print-data">Print data</button>
<button id = "send-data">Send data</button>
<div class="container">
  <table cellpadding="0" cellspacing="0" border="0" class="dataTable table table-striped" id="example">

  </table>
</div>
<div class="errors">

</div>
    </body>


    <script>

 var myTable = null;
 var initial_object_properties = null;
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
     var data_ =[];
     var data = [];
     for(i=0; i < table_data.length ; i++) {
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
        function( ) {
            console.log(getTableData(myTable, initial_object_properties));
        });

            $("#send-data").click(
                function( ) {
                    var data = getTableData(myTable, initial_object_properties);
                    console.log(data);
                    $.ajax({
              url: 'receive_csv.php',
            data: {data: data},
            type: 'POST',
                success: function(response) {
                        console.log(response);
                    }
            }).done(function( data, textStatus, jqXHR ) {
                        if ( console && console.log ) {
                            console.log(data);
                            console.log( "La solicitud se ha completado correctamente." );
                        }
                    })
                    .fail(function( jqXHR, textStatus, errorThrown ) {
                        if ( console && console.log ) {
                            console.log( "La solicitud a fallado: " +  textStatus);
                        }
                    });
        });
    $('#send-file').click(

        function () {
            var data = new FormData($(this).closest("form").get(0));
            $.ajax({
                url: 'receive_csv.php',
                data: data,
                cache: false,
                contentType: false,
                dataType: "html",
                //contentType: 'multipart/form-data',
                processData: false,
                type: 'POST',
                error: function(error) {
                  console.log(error);
                },
                success: function(response){
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



                    var jquery_datatable = response.jquery_datatable;
                    /* Se añade el error de cada objeto a si mismo. Estos errores vienen en  response.object_errors,
                    * este objeto es un diccionario donde las llaves son las posiciones que un objeto ocupa en
                    * datatable.data*/
                    Object.keys(errors).forEach((position, index)=> {
                        jquery_datatable.data[position].errors = errors[position];
                    });
                    /* Cada elemento data de la tabla debe tener una propiedad llamada index para que la columna
                    * de indices para las filas pueda existir*/
                    jquery_datatable.data.forEach((element, index) => {
                        element['index'] = index;
                    });
                    /**
                     * Se añade la propiedad que indicara si el objeto tine o no errores, para que la columna
                     * 'Errores' pueda existir. Esta información sera eliminada al momento de requerir los datos
                     */
                    jquery_datatable.data.forEach((element, index) => {
                        console.log(index);
                        if(errors[index]) {
                            element['error'] = 'SI';
                        } else {
                            element['error'] = 'NO';
                        }

                    });

                    /*Se añade la columna que llevara los indices de las filas en orden (1,2,3,4,5...)*/
                    jquery_datatable.columns.unshift({
                        "searchable": false,
                        "orderable": false,
                        "data": 'index',
                        "targets": 0
                    } );
                    /*Se añade la columna que llevara los indices de las filas en orden (1,2,3,4,5...)*/
                    jquery_datatable.columns.push({
                        "name": 'Error',
                        "title": 'Error',
                        "data": 'error'
                    } );
                    /* Se añade la función que modificara la vista de cada fila a la tabla*/
                    jquery_datatable.rowCallback = function(row, data) {
                        if ( data.error === "SI" ) {
                            $(row).addClass( 'error' );
                            if(data.errors) {
                                Object.keys(data.errors).forEach(property_name  => {
                                    /**
                                     * Cada celda que tenga un error debe tener la clase error
                                     * @see https://datatables.net/reference/option/rowCallback
                                     */
                                    $('td.'+ property_name , row).addClass('error');
                                    console.log(data.errors[property_name][0].error_message);
                                    var error_names = data.errors[property_name].map(error => error.error_message);
                                    var error_names_concat = error_names.join();
                                    $('td.'+ property_name , row).prop('title', error_names_concat);
                                });
                            }
                        }
                    };
                    /* El orden inicial de la tabla se da por su columna de indices de forma asendente*/
                    jquery_datatable.order = [[1, 'asc']];
                    if(response.errors.length === 0) {
                        myTable = $('#example').DataTable(jquery_datatable);
                        /* Se ordena y inicializa la columna que lleva los indices de las filas */
                        myTable.on( 'order.dt search.dt', function () {
                            myTable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                                cell.innerHTML = i+1;
                            } );
                        } ).draw();
                    } else {
                        for (error of response.errors) {
                            console.log(error);
                        }
                    }

            }
                });
            });



    </script>
</html>