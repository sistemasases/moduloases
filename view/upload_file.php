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

    </body>


    <script>

 var myTable = null;
 /**
  * Actually, jquery datatables data function returns an object with more
  * than is neccesary for get real data, this function extract the real data
  * from datatables data whitout unnecesary info
  * @param table Jquery Datatable
  * @return array of data
  * @see https://datatables.net/reference/api/rows().data()
  */
 function getTableData(table) {
     table_data = table.rows().data();
     data =[];
     for(i=0; i < table_data.length ; i++) {
         data.push(table_data[i]);
     }
     return data;
 }
    $("#print-data").click(
        function( ) {
            console.log(getTableData(myTable));
        });

            $("#send-data").click(
                function( ) {
                    var data = getTableData(myTable);
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
                    if(response.errors.length === 0) {
                        myTable = $('#example').DataTable(response.jquery_datatable);
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