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

/**
 * Function load all registers of mdl_talentospilos_df_dwarehouse
 * @see get_list_form()
 * @return array
 **/

function get_list_form(){
    global $DB;
    $forms_dwarehouse_array = array();
 
    $sql = "SELECT id AS id , id_usuario_moodle AS id_user,  accion AS name_accion, id_registro_respuesta_form AS id_respuesta, 
            fecha_hora_registro AS fecha_act,  navegador AS nav
            FROM {talentospilos_df_dwarehouse} AS dwarehouse ";
    $results = $DB->get_records_sql($sql);

    foreach ($results as $record) {
        array_push($forms_dwarehouse_array, $record);
    }
   return $forms_dwarehouse_array;
}
/**
 * Function that load a form switch id_form sent
 * @see get_form_switch_id($id_form)
 * @param $id_form---> id
 * @return array
 **/

function get_form_switch_id($id_form){
    global $DB;
    $form_dwarehouse_array = array();
  
    $sql = "SELECT id_usuario_moodle AS id_user, accion AS name_action, datos_previos AS datos_previos, datos_enviados AS datos_enviados, datos_almacenados AS datos_almacenados, 
    fecha_hora_registro AS fecha_form FROM {talentospilos_df_dwarehouse} AS dwarehouse WHERE dwarehouse.id = $id_form  ";

    $results = $DB->get_records_sql($sql);
    foreach ($results as $record) {
        array_push($form_dwarehouse_array, $record);
    }
    return $form_dwarehouse_array;
}

/**
 * Function that load identifier and firstname of one user in mdl_user switch username sent
 * @see get_id_switch_user($id_user)
 * @param $id_user---> username
 * @return array
 **/
function get_id_switch_user($id_user){
    global $DB;
    $form_dwarehouse_array = array();
  
    $sql = "SELECT id AS cod_user, firstname AS name_user FROM {user} AS u WHERE u.username = '$id_user' ";
    $results = $DB->get_records_sql($sql);
    foreach ($results as $record) {
        array_push($form_dwarehouse_array, $record);
    }
    return $form_dwarehouse_array;
}

/**
 * Function that load identifier and firstname of one user in mdl_user switch username sent
 * @see get_id_switch_user($id_user)
 * @param $id_user---> username
 * @return array
 **/
function get_like($cadena, $atributo){
    global $DB;
    $form_dwarehouse_array = array();
    if($atributo== "id"|| $atributo == "id_usuario_moodle" || $atributo == "id_registro_respuesta_form" || $atributo == "cod_retorno"){
        //Convert bigint to char
        $sql = "SELECT id AS id , id_usuario_moodle AS id_user,  accion AS name_accion, id_registro_respuesta_form AS id_respuesta, 
        fecha_hora_registro AS fecha_act , navegador AS nav
        FROM {talentospilos_df_dwarehouse} AS u WHERE TRIM(TO_CHAR(u.$atributo, '99999999999999999')) LIKE '%$cadena%' ";
    }else if ($atributo== "fecha_hora_registro"){
        $sql = "SELECT id AS id , id_usuario_moodle AS id_user,  accion AS name_accion, id_registro_respuesta_form AS id_respuesta, 
        fecha_hora_registro AS fecha_act , navegador AS nav
        FROM {talentospilos_df_dwarehouse} AS u WHERE TO_CHAR(u.$atributo, 'YYYY-MM-DD HH24:MI:SS.US') LIKE '%$cadena%' ";
    }else{
        //The other attributes of table are text type
    $sql = "SELECT id AS id , id_usuario_moodle AS id_user,  accion AS name_accion, id_registro_respuesta_form AS id_respuesta, 
    fecha_hora_registro AS fecha_act , navegador AS nav
     FROM {talentospilos_df_dwarehouse} AS u WHERE u.$atributo LIKE '%$cadena%' ";
    
    }
    $results = $DB->get_records_sql($sql);
    foreach ($results as $record) {
        array_push($form_dwarehouse_array, $record);
    }
    return $form_dwarehouse_array;
}