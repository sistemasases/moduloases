<?php
namespace datatable;


/**
 * Return the column for datatables for detail data, in other words, the column with '+' symbol in
 * the datatable
 * @see https://datatables.net/examples/api/row_details.html
 * @return array
 */
function get_datatable_class_column(): array {
    return array(
        "class"=> "details-control",
        "orderable"=>      false,
        "data"=>           null,
        "defaultContent"=> "");
}

/**
 * Return the common language cnofiguration for ASES datatables
 * @return array
 */
function get_datatable_common_language_config(): array {
    return array(
        "search"=> "Buscar:",
        "oPaginate" => array(
            "sFirst"=>    "Primero",
            "sLast"=>     "Último",
            "sNext"=>     "Siguiente",
            "sPrevious"=> "Anterior"
        ),
        "sProcessing"=>     "Procesando...",
        "sLengthMenu"=>     "Mostrar _MENU_ registros",
        "sZeroRecords"=>    "No se encontraron resultados",
        "sEmptyTable"=>     "Ningún dato disponible en esta tabla",
        "sInfo"=>           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix"=>    "",
        "sSearch"=>         "Buscar:",
        "sUrl"=>            "",
        "sInfoThousands"=>  ",",
        "sLoadingRecords"=> "Cargando...",
    );
}