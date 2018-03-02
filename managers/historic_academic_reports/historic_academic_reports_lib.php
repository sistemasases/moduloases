<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Estrategia ASES
 *
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once __DIR__ . '/../../../../config.php';

/**
 * Función que recupera datos para la tabla de reporte historico academico por estudiantes,
 * de los estudiantes asignados a una cohorte de las relacionadas con la instancia
 *
 * @see get_datatable_array_Students($instance_id)
 * @param $instance_id  --> Instancia del módulo
 * @return Array
 */
function get_datatable_array_Students($instance_id)
{
    $default_students = $columns = array();
    array_push($columns, array("title"=>"Número de documento", "name"=>"num_doc", "data"=>"num_doc"));  
    array_push($columns, array("title"=>"Código estudiante", "name"=>"username", "data"=>"username"));
    array_push($columns, array("title"=>"Nombre(s)", "name"=>"firstname", "data"=>"firstname"));
    array_push($columns, array("title"=>"Apellido(s)", "name"=>"lastname", "data"=>"lastname"));
    array_push($columns, array("title"=>"Semestre", "name"=>"semestre", "data"=>"semestre"));
    array_push($columns, array("title"=>"Cancela", "name"=>"cancel", "data"=>"cancel"));
    array_push($columns, array("title"=>"Promedio Semestre", "name"=>"promSem", "data"=>"promSem"));        
    array_push($columns, array("title"=>"Gano Estimulo", "name"=>"estim", "data"=>"estim"));
    array_push($columns, array("title"=>"Cae en Bajo", "name"=>"bajo", "data"=>"bajo"));
    array_push($columns, array("title"=>"Promedio Acumulado", "name"=>"promAcum", "data"=>"promAcum"));
    array_push($columns, array("title"=>"Estimulos", "name"=>"Numestim", "data"=>"estim"));
    array_push($columns, array("title"=>"Bajos", "name"=>"bajos", "data"=>"bajos"));
    array_push($columns, array("title"=>"Materias Perdidas", "name"=>"perdidas", "data"=>"perdidas"));
    array_push($columns, array("title"=>"Programa", "name"=>"programa", "data"=>"programa"));
    array_push($columns, array("title"=>"Cohorte", "name"=>"cohorte", "data"=>"cohorte"));
    
   // $default_students = get_historic_report($instance_id);

    $data_to_table = array(
        "bsort" => false,
        "data"=> $default_students,
        "columns" => $columns,
        "select" => "false",
        "fixedHeader"=> array(
            "header"=> true,
            "footer"=> true
        ),
        "language" => 
        array(
            "search"=> "Buscar:",
            "oPaginate" => array (
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
            "oAria"=> array(
                "sSortAscending"=>  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending"=> ": Activar para ordenar la columna de manera descendente"
            )
        ),
        "autoFill"=>"true",
        "dom"=> "lfrtBip"
    );

    return $data_to_table;     
            
}

/**
 * Retorna un arreglo con la informacion de la tabla de historico academico
 * @see get_historic_report($id_instance)
 * @param $id_instance --> id del modulo
 * @return Array --> info historic_academic_report
*/

function get_historic_report($id_instance){
    
    global $DB;

    
}

/**
 * Función que recupera datos para la tabla de reporte historico academico por totales,
 * de las cohortes relacionadas con la instancia
 *
 * @see get_datatable_array_totals($instance_id)
 * @param $instance_id  --> Instancia del módulo
 * @return Array
 */
function get_datatable_array_totals($instance_id)
{
    $default_students = $columns = array();
    array_push($columns, array("title"=>"Cohorte", "name"=>"cohorte", "data"=>"cohorte"));
    array_push($columns, array("title"=>"Semestre", "name"=>"semestre", "data"=>"semestre"));
    array_push($columns, array("title"=>"Total Activos", "name"=>"act", "data"=>"act"));
    array_push($columns, array("title"=>"Total Inactivos", "name"=>"inact", "data"=>"inact"));
    array_push($columns, array("title"=>"Total Cohorte", "n"=>"total", "data"=>"total"));
    
   // $default_students = get_Totals_report($instance_id);

    $data_to_table = array(
        "bsort" => false,
        "data"=> $default_students,
        "columns" => $columns,
        "select" => "false",
        "fixedHeader"=> array(
            "header"=> true,
            "footer"=> true
        ),
        "language" => 
        array(
            "search"=> "Buscar:",
            "oPaginate" => array (
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
            "oAria"=> array(
                "sSortAscending"=>  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending"=> ": Activar para ordenar la columna de manera descendente"
            )
        ),
        "autoFill"=>"true",
        "dom"=> "lfrtBip"
    );

    return $data_to_table;     
            
}