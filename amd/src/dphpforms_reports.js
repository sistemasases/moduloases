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
        
                        result += item[key]['respuesra'];
                        ctr++;
                    });
                    result += lineDelimiter;
                });
        
                return result;
            };

            function downloadCSV( data_ ) {  
                var data, filename, link;
                var csv = convertArrayOfObjectsToCSV({
                    data: data_
                });
                if (csv == null) return;
        
                filename = 'export.csv';
        
                if (!csv.match(/^data:text\/csv/i)) {
                    csv = 'data:text/csv;charset=utf-8,' + csv;
                }
                data = encodeURI(csv);
        
                link = document.createElement('a');
                link.setAttribute('href', data);
                link.setAttribute('download', filename);
                link.click();
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
                    $('#progress').text( "Espere, puede tardar un par de minutos ..." );
                    $.get( '../managers/dphpforms/dphpforms_reverse_filter.php?id_pregunta=seguimiento_pares_fecha&cast=date&criterio={"criteria":[{"operator":">=","value":"'+start_date+'"},{"operator":"<=","value":"'+end_date+'"}]}', function( data ) {
                        var count_records = Object.keys( data['results'] ).length;
                        console.log( count_records );
                        var increment = 100 / count_records;
                        var completed_records = [];
                        var progress = 0;
                        var indices_conocidos = [];
                        $.each(data['results'], function () {
                            $.get( '../managers/dphpforms/dphpforms_reverse_finder.php?respuesta_id=' + this.id, function( answer ) {
                                $.get( '../managers/dphpforms/dphpforms_get_record.php?record_id=' + answer['result']['id_registro'], function( record ) {
                                    
                                    var seguimiento = [];
                                    console.log( record['record']['campos']  );
                                    for( var x = 0; x <  Object.keys( record['record']['campos'] ).length; x++ ){
                                        seguimiento[ parseInt( record['record']['campos'][ x ]['id_pregunta'] ) ] = {
                                            "enunciado":record['record']['campos'][ x ]['enunciado'],
                                            "respuesra":record['record']['campos'][ x ]['respuesta']
                                        };
                                        completed_records.push( seguimiento );
                                        if( !is_in_array( indices_conocidos, parseInt( record['record']['campos'][ x ]['id_pregunta'] ) ) ){
                                            indices_conocidos.push( parseInt( record['record']['campos'][ x ]['id_pregunta'] ) );
                                        };
                                    };
                                    progress ++;
                                    $('#progress').text( (( 100 / count_records ) * progress).toFixed( 2 ) + "%" );
                                    console.log("Procesado");
                                    console.log( seguimiento );
                                    console.log( indices_conocidos );
                                    console.log( progress + " === " + count_records );
                                    if( progress == count_records ){
                                        console.log("CSV!!!!!!!!");
                                        downloadCSV( completed_records );
                                    };
                                });
                            });
                            $('#progress').text( Math.round( progress ) );
                        });
                        if( count_records == 0 ){ 
                            $('#progress').text( 100 );
                        };
                    });
                };
            });
        }
    };
      
})