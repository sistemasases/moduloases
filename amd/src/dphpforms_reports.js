// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/dphpforms_reports
  */

  define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui','block_ases/select2'], function($, bootstrap, sweetalert, jqueryui, select2) {
    
    return {
        init: function() {

            function is_in_array( array_, new_element ){
                for( var i = 0; i < array_.length; i++ ){
                    if( array_[i] == new_element ){
                        return true;
                    };
                };
                return false;
            };

            function convertArrayOfObjectsToCSV(args) {  
                var result, ctr, keys, columnDelimiter, lineDelimiter, data;
        
                data = args.data || null;
                if (data == null || !data.length) {
                    return null;
                }
        
                columnDelimiter = args.columnDelimiter || ',';
                lineDelimiter = args.lineDelimiter || '\n';
        
                keys = Object.keys(data[0]);
        
                result = '';
                result += keys.join(columnDelimiter);
                result += lineDelimiter;
        
                data.forEach(function(item) {
                    ctr = 0;
                    keys.forEach(function(key) {
                        if (ctr > 0) result += columnDelimiter;
        
                        result += "\"" + item[key]['respuesta'] + "\"";
                        ctr++;
                    });
                    result += lineDelimiter;
                });
        
                return result;
            };

            function downloadCSV( data_ ) {  

                var filename, link;
                var csv = convertArrayOfObjectsToCSV({
                    data: data_
                });
                

                if( csv == null ){
                    return
                };
                
                filename = 'reporte.csv';
                csvData = new Blob([csv], { type: 'text/csv' }); 

                var link = document.createElement("a");
                if (link.download !== undefined) {
                    var url = URL.createObjectURL(csvData);
                    link.setAttribute("href", url);
                    link.setAttribute("download", filename);
                    link.style = "visibility:hidden";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                };

            };

            $('#btn-generar-reporte').click(function(){

                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();

                if( start_date >= end_date ){
                    swal(
                        {title:'Información',
                        text: 'Intervalo de fechas inválido',
                        type: 'warning'},
                        function(){}
                    );
                }else{
                    
                    $('#progress_group').css('display','block');
                    $("#message").removeClass("alert alert-success");
                    $("#message").addClass("alert alert-info");
                    $('#message').html( "<strong>Info!</strong> Se está generando el reporte, esto puede tardar un par de minutos dependiendo de su conexión a internet, capacidad del ordenador y rapidez del campus virtual." );
                    
                    $.get( '../managers/dphpforms/dphpforms_reverse_filter.php?id_pregunta=seguimiento_pares_fecha&cast=date&criterio={"criteria":[{"operator":">=","value":"'+start_date+'"},{"operator":"<=","value":"'+end_date+'"}]}', function( data ) {
                    
                        var count_records = Object.keys( data['results'] ).length;
                        var completed_records = [];
                        var progress = 0;
                        var indices_conocidos = [];

                        for( var x = 0; x < count_records; x++ ){

                            $.get( '../managers/dphpforms/dphpforms_reverse_finder.php?respuesta_id=' + data['results'][x]['id'], function( answer ) {
                                $.get( '../managers/dphpforms/dphpforms_get_record.php?record_id=' + answer['result']['id_registro'], function( record ) {
                                    
                                   var seguimiento = [];
                                   
                                   if(  Object.keys( record['record'] ).length > 0  ){
                                        for( var x = 0; x <  Object.keys( record['record']['campos'] ).length; x++ ){
                                            seguimiento[ parseInt( record['record']['campos'][ x ]['id_pregunta'] ) ] = {
                                                "enunciado":record['record']['campos'][ x ]['enunciado'],
                                                "respuesta":record['record']['campos'][ x ]['respuesta']
                                            };
                                            if( !is_in_array( indices_conocidos, parseInt( record['record']['campos'][ x ]['id_pregunta'] ) ) ){
                                                indices_conocidos.push( parseInt( record['record']['campos'][ x ]['id_pregunta'] ) );
                                            };
                                        };
                                        completed_records.push( seguimiento );
                                    };

                                    progress ++;
                                    $('#progress').text( (( 100 / count_records ) * progress).toFixed( 2 ) );
                                    if( progress == count_records ){
                                        $("#message").removeClass("alert alert-info");
                                        $("#message").addClass("alert alert-success");
                                        $("#message").html( "<strong>Info!</strong>  Reporte generado." );
                                        downloadCSV( completed_records );
                                    };
                                    
                                }).fail(function(err) {
                                    console.log(err);
                                });
                            });
                            $('#progress').text( Math.round( progress ) );
                        }
                        if( count_records == 0 ){ 
                            $('#progress').text( 100 );
                            $("#message").removeClass("alert alert-info");
                            $("#message").addClass("alert alert-success");
                            $("#message").html( "<strong>Info!</strong>  Reporte generado." );
                        };
                    });
                };
            });
        }
    };
      
});