// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_ases/ases_report_main
 */

define(['jquery', 'block_ases/datatables.net', 'block_ases/datatables.net-buttons', 'block_ases/buttons.flash', 'block_ases/jszip', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/buttons.html5', 'block_ases/buttons.print', 'block_ases/jqueryui'], function ($) {
  return {
    init: function () {
      //Control para el botón 'Generar Reporte'
      $("#send_form_btn").on('click', function () {
        createTable();
      });

      //Controles para la tabla generada
      $(document).on('click', '#tableResult tbody tr td', function () {
        var pagina = "student_profile.php";
        var table = $("#tableResult").DataTable({
        fixedHeader: {
            header: true,
            footer: true
        }});
        var colIndex = table.cell(this).index().column;

        if (colIndex <= 2) {
          $("#formulario").each(function () {
            this.reset;
          });
          location.href = pagina + location.search + "&student_code=" + table.cell(table.row(this).index(), 0).data();
        }
      });


      //Controles para la tabla generada
      $(document).on('change', '#tableResult thead tr th select', function () {
        var table = $("#tableResult").DataTable();

        console.log($(this).parent().index()+1);
        var colIndex = $(this).parent().index()+1;
        var selectedText=$(this).parent().find(":selected").text();
        table.columns( colIndex-1 ).search( this.value ).draw();

     });




    }
  }

  function createTable() {

    var dataString = $('#form_general_report').serializeArray();

    dataString.push({
      name: 'instance_id',
      value: getIdinstancia()
    });

    $("#div_table").html('<img class="icon-loading" src="../icon/loading.gif"/>');
    $.ajax({
      type: "POST",
      data: dataString,
      url: "../managers/ases_report/asesreport_server_processing.php",
      success: function (msg) {
          //alert(msg.data);
          //console.log(msg.columns);
          $("#div_table").html('');
          $("#div_table").fadeIn(1000).append('<table id="tableResult" class="display" cellspacing="0" width="100%"><thead> </thead></table>');


        $("#tableResult").DataTable(msg.data);
        
          $('#tableResult tr').each(function () {
            $.each(this.cells, function () {
              if ($(this).html() == 'Bajo') {
                $(this).addClass('bajo');
              }
              else if ($(this).html() == 'Medio') {
                $(this).addClass('medio');
              }
              else if ($(this).html() == 'Alto') {
                $(this).addClass('alto');
              }
            });
          });

          $('#tableResult').bind("DOMSubtreeModified", function () {
            $('#tableResult tr').each(function () {
              $.each(this.cells, function () {
                if ($(this).html() == 'bajo') {
                  $(this).addClass('bajo');
                }
                else if ($(this).html() == 'medio') {
                  $(this).addClass('medio');
                }
                else if ($(this).html() == 'alto') {
                  $(this).addClass('alto');
                }
              });
            });
          });
        },
      dataType: "json",
      cache: "false",
      error: function (msg) {
        alert("Error al conectar con el servidor")
      },
    });
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
})
