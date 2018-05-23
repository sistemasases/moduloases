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
                    $.get( '../managers/dphpforms/dphpforms_reverse_filter.php?id_pregunta=1&cast=date&criterio={"criteria":[{"operator":">=","value":"'+start_date+'"},{"operator":"<=","value":"'+end_date+'"}]}', function( data ) {
                        var count_records = Object.keys( data['results'] ).length;
                        var increment = 100 / count_records;
                        var completed_records = [];
                        var progress = 0;
                        $.each(data['results'], function () {
                            $.get( '../managers/dphpforms/dphpforms_reverse_finder.php?respuesta_id=' + this.id, function( answer ) {
                                $.get( '../managers/dphpforms/dphpforms_get_record.php?record_id=' + answer['result']['id_registro'], function( record ) {
                                    
                                    var seguimiento = [];
                                    console.log( record['record']['campos']  );
                                    for( var x = 0; x <  Object.keys( record['record']['campos'] ).length; x++ ){
                                        var pregunta = {
                                            "id":record['record']['campos'][ x ]['id_pregunta'],
                                            "respuesra":record['record']['campos'][ x ]['respuesta'],
                                            "enunciado":record['record']['campos'][ x ]['enunciado']
                                        };
                                        seguimiento.push( pregunta );
                                        completed_records.push( seguimiento );
                                    };
                                    progress ++;
                                    $('#progress').text( (( 100 / count_records ) * progress).toFixed(2) );
                                    console.log("Procesado");
                                    console.log(seguimiento);
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