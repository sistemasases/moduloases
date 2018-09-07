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
 * Ases block
 *
 * @author     Juan Pablo Castro
 * @package    block_ases
 * @copyright  2018 Juan Pablo Castro <juan.castro.vasquez@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';

function dphpforms_get_forms_dwarehouse(){
    global $DB;
    $forms_dwarehouse_array = array();
 
    $sql = "SELECT id_usuario_moodle AS id_user, firstname AS name_user, accion AS name_accion, fecha_hora_registro AS fecha_act  
            FROM {talentospilos_df_dwarehouse} AS dwarehouse INNER JOIN {user}  ON mdl_user.id = dwarehouse.id_usuario_moodle WHERE dwarehouse.id_usuario_moodle = mdl_user.id ";
    $results = $DB->get_records_sql($sql);

    foreach ($results as $record) {
     

        array_push($forms_dwarehouse_array, $record);
    }

    
   return $forms_dwarehouse_array;
}

function dphpforms_get_only_form_dwarehouse($id_form){
    global $DB;
    $form_dwarehouse_array = array();
    $id_form= 1; 

    $sql = "SELECT id_usuario_moodle AS id_user, accion AS name_action, datos_previos AS dp_form, datos_enviados AS de_form, datos_almacenados AS da_form, 
    fecha_hora_registro AS fecha_form FROM {talentospilos_df_dwarehouse} AS dwarehouse WHERE dwarehouse.id = $id_form ";

    $results = $DB->get_records_sql($sql);
    foreach ($results as $record) {
        array_push($form_dwarehouse_array, $record);
    }
    return $form_dwarehouse_array;
}

function get_only_report($instance_id){
    $columns = array();
    array_push($columns, array("title"=>"Id usuario", "name"=>"id_user", "data"=>"id_user"));
    
    array_push($columns, array("title"=>"Acción", "name"=>"name_action", "data"=>"name_action"));
    array_push($columns, array("title"=>"Datos previos", "name"=>"dp_form", "data"=>"dp_form"));
    array_push($columns, array("title"=>"Datos enviados", "name"=>"de_form", "data"=>"de_form"));
    array_push($columns, array("title"=>"Datos almacenados", "name"=>"da_form", "data"=>"da_form"));
    array_push($columns, array("title"=>"Fecha", "name"=>"fecha_form", "data"=>"fecha_form"));

    $data = array(
        "bsort" => false,
        "columns" => $columns,
        "data" => dphpforms_get_only_form_dwarehouse(),
        "language" => 
         array(
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
         ),
        "order"=> array(0, "desc")

    );

    return $data;

}


function get_array_for_reports($instance_id){
    $columns = array();
    array_push($columns, array("title"=>"Id usuario", "name"=>"id_user", "data"=>"id_user"));
    array_push($columns, array("title"=>"Nombre", "name"=>"name_user", "data"=>"name_user"));
    array_push($columns, array("title"=>"Acción", "name"=>"name_accion", "data"=>"name_accion"));
    array_push($columns, array("title"=>"Fecha", "name"=>"fecha_act", "data"=>"fecha_act"));

    $data = array(
        "bsort" => false,
        "columns" => $columns,
        "data" => dphpforms_get_forms_dwarehouse(),
        "language" => 
         array(
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
         ),
        "order"=> array(0, "desc")

    );

    return $data;

}