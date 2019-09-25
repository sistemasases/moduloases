/**
 * Ases datatable common functions and utilities
 * @module amd/src/ases_datatable
 * @author Luis Gerardo Manrique Cardona
 * @copyright 2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @see https://datatables.net/examples/api/multi_filter_select.html
 */

define(['jquery'], function($) {
    /**
     * Function than allows add the column filters in the datatable based in the datatable column data
     * **This function is appended at initComplete function o fdatatable**
     * @param {array} column_filter_selectors Array of strings with the column selector, can be
     *  the column name, example: ['username', 'tipo_doc'] adds filter to username and tipo_doc columns
     *  if they exists in the datatable.
     *  **Each element of filter_column can be a column selector**
     *  **Use this function in a initComplete function of the datatable**
     *  @param dataTable Datatable var for append the column filters
     *      if is empty, the function for init complete is returned and you should
     *      add
     *  @see https://datatables.net/reference/option/initComplete
     *  @see https://datatables.net/reference/type/column-selector
     * @function
     */
    var add_column_filters = function (column_filter_selectors, dataTable) {
        var init_complete_function =  function () {
            this.api().columns(column_filter_selectors).every(function () {
                var column = this;


                var select = $('<select><option value=""></option></select>')
                    .appendTo($(column.header()))
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            });
        };
        if(!dataTable) {
            return init_complete_function;
        } else {
            dataTable.initComplete = init_complete_function;
        }
    };

    return {
        add_column_filters: add_column_filters,
        /**
         * Get the common language configuration
         * @returns object Return the common language configuration for jquery datatables in ases
         */
        common_lang_config: function  () {
            return {
                search: 'Buscar:',
                oPaginate: {
                    sFirst: 'Primero',
                    sLast: 'Último',
                    sNext: 'Siguiente',
                    sPrevious: 'Anterior'
                },
                sProcessing: 'Procesando...',
                sLengthMenu: 'Mostrar _MENU_ registros',
                sZeroRecords: 'No se encontraron resultados',
                sEmptyTable: 'Ningún dato disponible en esta tabla',
                sInfo: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
                sInfoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
                sInfoFiltered: '(filtrado de un total de _MAX_ registros)',
                sInfoPostFix: '',
                sSearch: 'Buscar:',
                sUrl: '',
                sInfoThousands: ',',
                sLoadingRecords: 'Cargando...'
            };
        }
    };
});