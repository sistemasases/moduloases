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


function get_list_form(){
    global $DB;
    $forms_dwarehouse_array = array();
 
    $sql = "SELECT id AS id , id_usuario_moodle AS id_user,  accion AS name_accion, id_registro_respuesta_form AS id_respuesta, fecha_hora_registro AS fecha_act 
            FROM {talentospilos_df_dwarehouse} AS dwarehouse ";
    $results = $DB->get_records_sql($sql);

    foreach ($results as $record) {
     

        array_push($forms_dwarehouse_array, $record);
    }
    
   return $forms_dwarehouse_array;
}

function get_form_switch_id($id_form){
    global $DB;
    $form_dwarehouse_array = array();
  
    $sql = "SELECT id_usuario_moodle AS id_user, accion AS name_action, datos_previos AS dp_form, datos_enviados AS de_form, datos_almacenados AS da_form, 
    fecha_hora_registro AS fecha_form FROM {talentospilos_df_dwarehouse} AS dwarehouse WHERE dwarehouse.id = $id_form  ";

    $results = $DB->get_records_sql($sql);
    foreach ($results as $record) {
        array_push($form_dwarehouse_array, $record);
    }
    return $form_dwarehouse_array;
}
