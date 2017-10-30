requirejs(['jquery'], function($) {

    $(document).on('click', '#academics', function(){
        var pagina = "academic_reports.php";
        location.href = pagina + location.search;
    })

    $(document).on('click', '#seguimientos', function(){
        var pagina = "seguimiento_pilos.php";
        location.href = pagina + location.search;    
    })

    $(document).on('click', '#general', function(){
        var pagina = "ases_report.php";
        location.href = pagina + location.search;    
    })

});
