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
 * @author     Joan Manuel Tovar Guzmán
 * @package    block_ases
 * @copyright  2018 Joan Manuel Tovar GUzmán <joan.tovar@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once(dirname(__FILE__). '/../../../../config.php');
    require_once(dirname(__FILE__). '/students_no_trackings_report_lib.php');

    if(isset($_POST['loadR']) && $_POST['loadR'] == 'loadReport'){
        $columns = array();

        $data = get_array_students_with_trackings_count();        
        $monitores = array();
        $practicantes = array();
        $profesionales = array();

        foreach($data as $record){               
            array_push($monitores, $record->monitor_fullname);
            array_push($practicantes, $record->trainee_fullname);
            array_push($profesionales, $record->professional_fullname);
        }

        $monitores = array_unique($monitores);
        $practicantes = array_unique($practicantes);
        $profesionales = array_unique($profesionales);

        $monitores_options = "<select><option value=''></option> 
                             <option value='---'>---</option>";
        $practicantes_options = "<select><option value=''></option> 
                             <option value='---'>---</option>";
        $profesionales_options = "<select><option value=''></option> 
                             <option value='---'>---</option>";
        
        foreach($monitores as $monitor){
            $monitores_options .= "<option value='$monitor'> $monitor</option>";
        }

        foreach($practicantes as $practicante){
            $practicantes_options .= "<option value='$practicante'> $practicante</option>";
        }
        foreach($profesionales as $profesional){
            $profesionales_options .= "<option value='$profesional'> $profesional</option>";
        }

        
        array_push($columns, array("title"=>"Código estudiante", "name"=>"codigo", "data"=>"username"));
        array_push($columns, array("title"=>"Cédula", "name"=>"cedula", "data"=>"cedula")); 
        array_push($columns, array("title"=>"Nombres", "name"=>"firstname", "data"=>"firstname"));
        array_push($columns, array("title"=>"Apellidos", "name"=>"lastname", "data"=>"lastname"));              
        array_push($columns, array("title"=>"Cantidad de fichas", "name"=>"cantidad_seguimientos", "data"=>"cantidad_fichas"));
        array_push($columns, array("title"=>"Monitor".$monitores_options, "name"=>"monitor_fullname", "data"=>"monitor_fullname"));
        array_push($columns, array("title"=>"Practicante".$practicantes_options, "name"=>"trainee_fullname", "data"=>"trainee_fullname"));
        array_push($columns, array("title"=>"Profesional".$profesionales_options, "name"=>"professional_fullname", "data"=>"professional_fullname"));

        $data = array(
            "bsort" => false,
            "columns" => $columns,
            "data" => $data,
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
            "order"=> array(0, "desc"),
            "dom"=>'lifrtpB',

            "buttons"=>array(
                array(
                    "extend"=>'print',
                    "text"=>'Imprimir'
                ),
                array(
                    "extend"=>'csvHtml5',
                    "text"=>'CSV'
                ),
                array(
                    "extend" => "excel",
                                    "text" => 'Excel',
                                    "className" => 'buttons-excel',
                                    "filename" => 'Export excel',
                                    "extension" => '.xls'
                )
            )

        );
        header('Content-Type: application/json');
    echo json_encode($data);
}


    

		