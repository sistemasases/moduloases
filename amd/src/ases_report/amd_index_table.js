<<<<<<< HEAD
requirejs(['jquery', 'datatables.net', 'datatables.net-buttons', 'buttons.flash', 'jszip'
, 'pdfmake', 'vfs_fonts', 'buttons.html5', 'buttons.print'], function($) {

    $(document).ready(function() {
        $("#btn-send-indexform").on('click', function() {
            createTable();
        });
    });

    $(document).on('click', '#tableResult tbody tr td', function() {
        var pagina = "student_profile.php";
        var table = $("#tableResult").DataTable();
        var colIndex = table.cell(this).index().column;

        if (colIndex <= 2) {
            $("#formulario").each(function() {
                this.reset;
            });
            // document.getElementById("formulario").reset();
            location.href = pagina + location.search + "&student_code=" + table.cell(table.row(this).index(), 0).data();
        }
    });

    function createTable() {
        var dataString = $('#formulario').serializeArray();
        dataString.push({
            name: 'idinstancia',
            value: getIdinstancia()
        });
        $("#div_table").html('<img class="icon-loading" src="../icon/loading.gif"/>');
        $.ajax({
            type: "POST",
            data: dataString,
            url: "../managers/ases_report/asesreport_server_processing.php",
            success: function(msg) {
                if(msg.error){
                        alert(msg.error);
                }else{
                    //alert(msg.data);
                        //console.log(msg.columns);
                        $("#div_table").html('');
                        $("#div_table").fadeIn(1000).append('<table id="tableResult" class="display" cellspacing="0" width="100%"><thead> </thead></table>');
                        $("#tableResult").DataTable(msg.data);

                        $('#tableResult tr').each(function() {
                            $.each(this.cells, function() {
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

                        $('#tableResult').bind("DOMSubtreeModified", function() {
                            $('#tableResult tr').each(function() {
                                $.each(this.cells, function() {
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
          
                }

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

});
=======
requirejs(['jquery', 'datatables.net', 'datatables.net-buttons', 'buttons.flash', 'jszip'
, 'pdfmake', 'vfs_fonts', 'buttons.html5', 'buttons.print'], function($) {

    $(document).ready(function() {
        $("#btn-send-indexform").on('click', function() {
            createTable();
        });
    });

    $(document).on('click', '#tableResult tbody tr td', function() {
        var pagina = "student_profile.php";
        var table = $("#tableResult").DataTable();
        var colIndex = table.cell(this).index().column;

        if (colIndex <= 2) {
            $("#formulario").each(function() {
                this.reset;
            });
            // document.getElementById("formulario").reset();
            location.href = pagina + location.search + "&student_code=" + table.cell(table.row(this).index(), 0).data();
        }
    });

    function createTable() {
        var dataString = $('#formulario').serializeArray();
        dataString.push({
            name: 'idinstancia',
            value: getIdinstancia()
        });
        $("#div_table").html('<img class="icon-loading" src="../icon/loading.gif"/>');
        $.ajax({
            type: "POST",
            data: dataString,
            url: "../managers/ases_report/asesreport_server_processing.php",
            success: function(msg) {
                if(msg.error){
                        alert(msg.error);
                }else{
                    //alert(msg.data);
                        //console.log(msg.columns);
                        $("#div_table").html('');
                        $("#div_table").fadeIn(1000).append('<table id="tableResult" class="display" cellspacing="0" width="100%"><thead> </thead></table>');
                        $("#tableResult").DataTable(msg.data);

                        $('#tableResult tr').each(function() {
                            $.each(this.cells, function() {
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

                        $('#tableResult').bind("DOMSubtreeModified", function() {
                            $('#tableResult tr').each(function() {
                                $.each(this.cells, function() {
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
          
                }

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

});
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
