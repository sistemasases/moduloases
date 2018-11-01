/**
 * Course and teacher report
 * @module amd/src/course_and_techar_report
 * @author Luis Gerardo Manrique Cardona
 * @copyright 2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @see https://datatables.net/examples/api/multi_filter_select.html
 */
define(['jquery', 'block_ases/jquery.dataTables', 'core/notification', ], function($, _, notification){

    return {
        init: function () {
            $(document).ready(function () {

            });
        },

        load_report: function (dataTable) {
            /**
             * DataTable:
             * {
             *     bsort,
             *     columns,
             *     data,
             *     language,
             *     order
             * }
             * Data:
             * {
             *     curso,
             *     nombre_profesor,
             *     estudiantes_perdiendo,
             *     estudiantes_ganando,
             *     estudiantes_sin_ninguna_nota,
             *     cantidad_estudiantes_ases,
             *     items_con_almenos_una_nota,
             *     cantidad_items
             *     critica,
             * }
             */

            var table = $("#tableFinalgradesReport").DataTable(
                {
                    data: dataTable.data,
                    bsort: dataTable.bsort,
                    columns: dataTable.columns,
                    language: dataTable.language,
                    order: dataTable.order,
                    initComplete: function () {
                        console.log(this.api().columns());
                        this.api().columns([8/*Columna 'critica'*/]).every( function () {
                            var column = this;


                            var select = $('<select><option value=""></option></select>')
                                .appendTo( $(column.header()))
                                .on( 'change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );

                                    column
                                        .search( val ? '^'+val+'$' : '', true, false )
                                        .draw();
                                } );

                            column.data().unique().sort().each( function ( d, j ) {
                                select.append( '<option value="'+d+'">'+d+'</option>' );
                            } );
                        } );
                    }
                }
            );

            // Añadir descripción a las columnas
            $("th").each(function(index) {
                dataTable.columns[index].description? $(this).attr('title', dataTable.columns[index].description): null;

            });
        },

    };

});