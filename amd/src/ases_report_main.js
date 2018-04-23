// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module block_ases/ases_report_main
 */
define(['jquery', 
        'block_ases/jszip',
        'block_ases/pdfmake',
        'block_ases/jquery.dataTables',
        'block_ases/dataTables.autoFill',
        'block_ases/dataTables.buttons',
        'block_ases/buttons.colVis',
        'block_ases/buttons.flash',
        'block_ases/buttons.html5',
        'block_ases/buttons.print',
        'block_ases/bootstrap',
        'block_ases/sweetalert'
        ],
        function($, jszip, pdfmake, dataTables, buttons, colVis, flash, html5, print, bootstrap, sweetalert, jqueryui) {
    return {
        init: function(){
            //Control para el botón 'Generar Reporte'
            $("#send_form_btn").on('click', function() {
                createTable();
                createTableAssign();
            });

            //Asignación de estudiantes a monitores/practicantes por parte de profesional. 
            $(document).on('click', '#tableAssign tbody tr td #student_assign', function() {
                var table = $("#tableAssign").DataTable();
                var current_row = table.row($(this).parents('tr')).data();
                var instance = getIdinstancia();
                var student = current_row.username;
                var monitores = $(this).closest('tr').find('#monitors').val();
                var practicantes = $(this).closest('tr').find('#practicants').val();

                var next = true;
                var msg = "";
                if (monitores == '-1') {
                    next = false;
                    msg += "* Debe elegir monitor a asignar \n";
                }
                if (practicantes == '-1') {
                    next = false;
                    msg += "*Debe elegir practicantes a asignar";
                }

                if (next) {
                    $.ajax({
                        type: "POST",
                        data: {
                            type: "assign_student",
                            monitor: monitores,
                            practicant: practicantes,
                            instance: instance,
                            student: student

                        },
                        url: "../managers/ases_report/asesreport.php",
                        success: function(msg) {
                            alert(msg);
                        },

                        dataType: "text",
                        cache: "false",
                        error: function(msg) {
                            console.log("Error al asignar estudiantes" + msg);
                        },
                    });
                } else {
                    alert(msg);
                }
            });

            //Controles para la tabla generada
            $(document).on('click', '#tableResult tbody tr td', function() {
                var pagina = "student_profile.php";
                var table = $("#tableResult").DataTable();
                var colIndex = table.cell(this).index().column;

                if (colIndex <= 2) {
                    $("#formulario").each(function() {
                        this.reset;
                    });
                    location.href = pagina + location.search + "&student_code=" + table.cell(table.row(this).index(), 0).data();
                }
            });

            //Función para busqueda de los filtros de riesgos.

            $(document).on('change', '#tableResult thead tr th select', function() {
                var table = $("#tableResult").DataTable();
                var colIndex = $(this).parent().index() + 1;
                var selectedText = $(this).parent().find(":selected").text();
                table.columns(colIndex - 1).search(this.value).draw();
            });

            //Despliega monitores deacuerdo al practicante seleccionado

            $(document).on('change', '#tableAssign tbody tr td select#practicants', function() {

                var user = $(this).val();
                var source = "list_monitors";
                var instancia =getIdinstancia();

                $.ajax({
                    type: "POST",
                    data: {
                        user: user,
                        instance:instancia,
                        source: source
                    },
                    url: "../managers/ases_report/asesreport.php",
                    success: function(msg) {
                       $("select#monitors").find('option').remove().end();
                       $("select#monitors").append(msg);
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        alert("Error al cargar monitores con practicante seleccionado")
                    },
                });

            });
        },
        load_defaults_students: function(data){

            $("#div_table").html('');
            $("#div_table").fadeIn(1000).append('<table id="tableResult" class="display" cellspacing="0" width="100%"><thead> </thead></table>');

            $("#tableResult").DataTable(data);

        },
        create_table: function(){

        },
        get_id_instance: function(){
            var urlParameters = location.search.split('&');

            for (x in urlParameters) {
                if (urlParameters[x].indexOf('instanceid') >= 0) {
                    var intanceparameter = urlParameters[x].split('=');
                    return intanceparameter[1];
                }
            }
            return 0;
        }
    }

    //Creación de tabla de asignaciones
    function createTableAssign() {
        
        var dataString = $('#form_general_report').serializeArray();

        dataString.push({
            name: 'instance_id',
            value: getIdinstancia()
        });

        $("#not_assigned_students").html('<img class="icon-loading" src="../icon/loading.gif"/>');
        $.ajax({
            type: "POST",
            data: dataString,
            url: "../managers/ases_report/load_not_assigned_students.php",
            success: function(msg) {
                //alert(msg.data);
                //console.log(msg.columns);
                $("#not_assigned_students").html('');
                $("#not_assigned_students").fadeIn(1000).append('<table id="tableAssign" class="display" cellspacing="0" width="100%"><thead> </thead></table>');


                var table = $("#tableAssign").DataTable(msg);

            },

            dataType: "json",
            cache: "false",
            error: function(msg) {
                alert("Error al cargar estudiantes no asignados")
            },
        });
    }

    // Creación de tabla general
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
            success: function(msg) {

                $("#div_table").html('');
                $("#div_table").fadeIn(1000).append('<table id="tableResult" class="display" cellspacing="0" width="100%"><thead> </thead></table>');

                $("#tableResult").DataTable(msg);

                $('#tableResult tr').each(function() {
                    $.each(this.cells, function() {
                        if ($(this).html() == 'Bajo') {
                            $(this).addClass('bajo');
                        } else if ($(this).html() == 'Medio') {
                            $(this).addClass('medio');
                        } else if ($(this).html() == 'Alto') {
                            $(this).addClass('alto');
                        }
                    });
                });

                $('#tableResult').bind("DOMSubtreeModified", function() {
                    $('#tableResult tr').each(function() {
                        $.each(this.cells, function() {
                            if ($(this).html() == 'bajo') {
                                $(this).addClass('bajo');
                            } else if ($(this).html() == 'medio') {
                                $(this).addClass('medio');
                            } else if ($(this).html() == 'alto') {
                                $(this).addClass('alto');
                            }
                        });
                    });
                });
            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
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