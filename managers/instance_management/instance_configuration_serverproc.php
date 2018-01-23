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
 * Talentos Pilos
 *
 * @author     John Lourido 
 * @package    block_ases
 * @copyright  2017 JOhn Lourido <jhonkrave@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once('instance_lib.php');
require_once("../user_management/user_lib.php");
require_once("../periods_management/periods_lib.php");

if(isset($_POST['function'])){
    
    switch($_POST['function']){
        case 'search': 
            searchUser();
            break;
        case 'load_programs':
            loadPrograms();
            break;
        case 'updateUser':
            updateUser();
            break;
        case 'loadSystemAdministrators':
            loadSystemAdminstrators();
            break;
        case 'deleteUser':
            deleteAdministrator();
    }
}

/**
 * Returns a JSON with information returned in getInfoSystemDirector(PARAMETER) in casi it's successful, otherwise returns an error message
 * 
 * @see searchUser()
 * @return JSON
 */
function searchUser(){
    
    if(isset($_POST['username'])){
        
        echo json_encode(getInfoSystemDirector($_POST['username']));
        
    }else{
        $msg =  new stdClass();
        $msg->Error = "Error al obtener variable de consulta de usuario";
        echo json_encode($msg);
    }
}

/**
 * Returns a json with the loadProgramsForSystemsAdmins() function output
 * 
 * @see loadPrograms()
 * @return JSON
 */

function loadPrograms(){
    echo json_encode(loadProgramsForSystemsAdmins());
}


/**
 * Returns a json with the updateSystemDirector(PARAMETERS) function output in case it's successful, otherwise returns an error message
 * 
 * @see updateUser()
 * @return JSON
 */
function updateUser(){
    if(isset($_POST['username_input']) && isset($_POST['lista_programas']) && isset($_POST['idinstancia']) && isset($_POST['segAca']) &&  isset($_POST['segAsis']) &&  isset($_POST['segSoc'])){
        echo json_encode(updateSystemDirector($_POST['username_input'], $_POST['lista_programas'], $_POST['idinstancia'], $_POST['segAca'], $_POST['segAsis'], $_POST['segSoc']));
    }else{
        echo json_encode("Error al obtener variables para la actualización del perfil administrador");
    }
}


function loadSystemAdminstrators(){
    $columns = array();
    array_push($columns, array("title"=>"Código", "name"=>"username", "data"=>"username"));
    array_push($columns, array("title"=>"Nombres", "name"=>"firstname", "data"=>"firstname"));
    array_push($columns, array("title"=>"Apellidos", "name"=>"lastname", "data"=>"lastname"));
    array_push($columns, array("title"=>"Programa", "name"=>"nombre_rol", "data"=>"programa"));
    array_push($columns, array("title"=>"Instancia", "name"=>"nombre_rol", "data"=>"id_instancia"));
    array_push($columns, array("title"=>"Eliminar", "name"=>"button", "data"=>"button"));
    
    $data = array(
                "bsort" => false,
                "columns" => $columns,
                "data"=> getSystemAdministrators(),
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
                 ),
                 "order"=> array(0, "desc" )
        );
    header('Content-Type: application/json');
    echo json_encode($data);
}

/**
 * Returns a json with the deleteSystemAdministrator(PARAMETER) function output
 * 
 * @see deleteAdministrator()
 * @return JSON
 */
function deleteAdministrator(){
    if(isset($_POST['username'])){
        echo json_encode(deleteSystemAdministrator($_POST['username']));
    }else{
        echo "no entro";
    }
}

?>