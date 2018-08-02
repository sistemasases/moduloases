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

            function custom_actions( report, form_type ){
                if( form_type == "seguimiento_pares" ){
                    var report_size = report.length;
                    for( var x = 0; x < report_size; x++){
                        var id_estudiante = false;
                        var id_creado_por = false;
                        for( var y = 0; y < Object.keys(report[x]).length; y++ ){
                            if( !id_estudiante || !id_creado_por ){
                                if( report[x][y].local_alias == "id_estudiante" ){
                                    id_estudiante = report[x][y].respuesta;
                                }
                                if( report[x][y].local_alias == "id_creado_por" ){
                                    id_creado_por = report[x][y].respuesta;
                                }
                            }else{
                                break;
                            }
                        }
                        $.get( '../managers/dphpforms/.php', function( data ) {

                        });
                        $.get( '../managers/dphpforms/.php', function( data ) {
                            
                        });
                    }
                }else{
                    return report;
                }
            }

            function convertArrayOfObjectsToCSV(args) {  
                var result, ctr, keys, columnDelimiter, lineDelimiter, data;
        
                data = args.data || null;
                if (data == null || !data.length) {
                    return null;
                }
        
                columnDelimiter = args.columnDelimiter || ',';
                lineDelimiter = args.lineDelimiter || '\n';
                
                keys = Object.keys(data[0]);
              
                var enunciados = [];
                for( var x = 0; x <  Object.keys(data[0]).length; x++ ){
                    enunciados.push( data[0][x].local_alias );
                }
        
                result = '';
                result += enunciados.join(columnDelimiter);
                result += lineDelimiter;
        
                data.forEach(function(item) {
                    ctr = 0;
                    keys.forEach(function(key) {
                        if (ctr > 0) result += columnDelimiter;
                        try {
                            result += "\"" + item[key]['respuesta'].replace(/"/g, '\'') + "\"";
                        } catch (error) {}
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

                    var preguntas = JSON.parse( $("#dphpforms-reports-preguntas").html());
                    
                    
                    $('#progress_group').css('display','block');
                    $("#message").removeClass("alert alert-success");
                    $("#message").addClass("alert alert-info");
                    $('#message').html( "<strong>Info!</strong> Se está generando el reporte, esto puede tardar un par de minutos dependiendo de su conexión a internet, capacidad del ordenador y rapidez del campus virtual." );
                    
                    $.get( '../managers/dphpforms/dphpforms_reverse_filter.php?id_pregunta=seguimiento_pares_fecha&cast=date&criterio={"criteria":[{"operator":">=","value":"'+start_date+'"},{"operator":"<=","value":"'+end_date+'"}]}', function( data ) {
                    
                        var count_records = Object.keys( data['results'] ).length;
                        var completed_records = [];
                        var progress = 0;

                        for( var t = 0; t < count_records; t++ ){

                            $.get( '../managers/dphpforms/dphpforms_reverse_finder.php?respuesta_id=' + data['results'][t]['id'], function( answer ) {
                                $.get( '../managers/dphpforms/dphpforms_get_record.php?record_id=' + answer['result']['id_registro'], function( record ) {
                                    
                                   if(  Object.keys( record['record'] ).length > 0  ){

                                        var seguimiento_base = $.extend( true, {}, preguntas );

                                        for( var x = 0; x <  Object.keys( record['record']['campos'] ).length; x++ ){

                                            for( var k = 0; k < Object.keys( seguimiento_base ).length; k++ ){
                                                if( seguimiento_base[k].id == parseInt( record['record']['campos'][ x ]['id_pregunta'] ) ){
                                                    seguimiento_base[k].respuesta = record['record']['campos'][ x ]['respuesta'];
                                                }
                                            }
                                        };
                                        completed_records.push( seguimiento_base );
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