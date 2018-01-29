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
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Obtains a select given an array
 * @see get_select($role,$nombre_rol)
 * @param $roles --> array 
 * @param $nombre_rol --> name that will be assigned to the select
 * @return string html with the select obtained
 **/
function get_roles_select($roles,$nombre_rol){
    $table = "";
    $table.='<select class="form-pilos" id="'.$nombre_rol.'">';
    $table.='<option></option>';
    foreach($roles as $role){
            $table.='<option value="'.$role->username.'">'.$role->username." - ".$role->firstname." ".$role->lastname.'</option>';
     }
    $table.='</select>';
    return $table;

}


/**
 * Gets a select given an array
 * @see get_functions_select($functions,$nombre_function)
 * @param $functions --> array containing function information
 * @param $nombre_function --> function name
 * @return string html with the select obtained
 **/
function get_functions_select($functions,$nombre_function){
    $table = "";
    $table.='<select class="form-pilos" id="'.$nombre_function.'">';
    foreach($functions as $function){
            $table.='<option value="'.$function->id.'">'.$function->nombre_func.'</option>';
     }
    $table.='</select>';
    return $table;

}


/**
 * Gets a select given an array
 * @see get_actions_select($actions)
 * @param $actions --> array containing actions information
 * @return string html with the select obtained
 **/
function get_actions_select($actions){
    $table = "";
    $table.='<select class="form-pilos" id="actions">';
    foreach($actions as $action){
            $table.='<option value="'.$action->id.'">'.$action->nombre_accion.'</option>';
     }
    $table.='</select>';
    return $table;

}










?>