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
require_once(dirname(__FILE__). '/../periods_management/periods_lib.php');

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
            FROM {talentospilos_df_dwarehouse} AS dwarehouse 
            ORDER BY fecha_hora_registro DESC";
    $results = $DB->get_records_sql($sql);

    foreach ($results as $record) {
        array_push($forms_dwarehouse_array, $record);
    }
   return $forms_dwarehouse_array;
}

/**
 * Function load all registers of mdl_talentospilos_general_logs
 * @return array
 **/

function get_list_general_logs(){
    global $DB;
    $forms_dwarehouse_array = array();
 
    $sql = "SELECT talentospilos_general_logs.id AS id , talentospilos_general_logs.id_moodle_user,
                 _user.username AS username_ases_student, _user.firstname AS firstname, _user.lastname AS lastname,
                 talentospilos_events_to_logs.name_event AS name_event,  talentospilos_general_logs.fecha_registro AS fecha_act 
	            FROM  {talentospilos_general_logs}  AS talentospilos_general_logs
	                INNER JOIN {talentospilos_events_to_logs} AS talentospilos_events_to_logs  ON talentospilos_events_to_logs.id = talentospilos_general_logs.id_evento
	                    INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON talentospilos_user_extended.id_ases_user = talentospilos_general_logs.id_ases_user AND talentospilos_user_extended.tracking_status = 1
		                    INNER JOIN {user} AS _user ON _user.id = talentospilos_user_extended.id_moodle_user";
    $results = $DB->get_records_sql($sql);
    
    foreach ($results as $record) {
        array_push($forms_dwarehouse_array, $record);
    }
   return $forms_dwarehouse_array;
}

/**
 * Function that returns a set of records from talentosilos_df_dwarehouse 
 * @see get_list_form()
 * @return array
 **/

function dwarehouse_get_simple( $username, $is_student ){

    global $DB;
    $user_id = null;
    $results = null;
    $forms_dwarehouse_array = array();

    $_is_student = false;

    if($is_student == "true"){
        $_is_student = true;
    }

    if( $_is_student ){

        $sql = "SELECT UE.id_ases_user AS id FROM {user} AS U
        INNER JOIN  mdl_talentospilos_user_extended AS UE ON UE.id_moodle_user = U.id
        WHERE U.username LIKE '$username%' LIMIT 1";

        $user_id = $DB->get_record_sql($sql);

    }else{
        
        $sql = "SELECT id FROM {user}
        WHERE username LIKE '$username%'";

        $user_id = $DB->get_record_sql($sql);
    }


    if( $user_id && !$_is_student ){

        $sql = "SELECT id AS id, 
            id_usuario_moodle AS id_user,  
            accion AS name_accion, 
            id_registro_respuesta_form AS id_respuesta, 
            fecha_hora_registro AS fecha_act,  
            navegador AS nav
            FROM {talentospilos_df_dwarehouse} AS dwarehouse
            WHERE id_usuario_moodle = '$user_id->id'
            ORDER BY fecha_hora_registro DESC";

        $results = $DB->get_records_sql($sql);

    }else if( $user_id && $_is_student ){

        $alias_pregunta = "seguimiento_pares_id_estudiante";
        $alias_obj = $DB->get_record_sql( "SELECT * FROM {talentospilos_df_alias} WHERE alias = '$alias_pregunta'" ); 
        $criteria = '"id":"' . $alias_obj->id_pregunta . '","valor":"' . $user_id->id . '"';

        $alias_pregunta = "inasistencia_id_estudiante";
        $alias_obj = $DB->get_record_sql( "SELECT * FROM {talentospilos_df_alias} WHERE alias = '$alias_pregunta'" ); 
        $in_criteria = '"id":"' . $alias_obj->id_pregunta . '","valor":"' . $user_id->id . '"';

        $sql = "SELECT DISTINCT * FROM (SELECT id AS id, 
            id_usuario_moodle AS id_user,  
            accion AS name_accion, 
            id_registro_respuesta_form AS id_respuesta, 
            fecha_hora_registro AS fecha_act,  
            navegador AS nav
            FROM {talentospilos_df_dwarehouse} AS dwarehouse 
            WHERE datos_enviados LIKE '%$criteria%'

            UNION

            SELECT id AS id, 
            id_usuario_moodle AS id_user,  
            accion AS name_accion, 
            id_registro_respuesta_form AS id_respuesta, 
            fecha_hora_registro AS fecha_act,  
            navegador AS nav
            FROM {talentospilos_df_dwarehouse} AS dwarehouse 
            WHERE datos_enviados LIKE '%$in_criteria%') AS SQ
            ORDER BY SQ.fecha_act DESC";

        $results = $DB->get_records_sql($sql);

    }else{
        return [];
    }

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

function get_form_switch_id($id_form, $table){
    global $DB;
  //id_usuario_moodle AS id_user, accion AS name_action, datos_previos AS datos_previos, datos_enviados AS datos_enviados, datos_almacenados AS datos_almacenados, 
//  fecha_hora_registro AS fecha_form
				
    $sql = "SELECT * FROM $table AS dwarehouse WHERE dwarehouse.id = $id_form  ";

    $results = $DB->get_records_sql($sql);
  /*  foreach ($results as $record) {
        array_push($form_dwarehouse_array, $record);
    }*/
    if($table=="{talentospilos_general_logs}"){
        $array_results = $results[$id_form];
        $sql = "SELECT name_event FROM {talentospilos_events_to_logs} AS events WHERE events.id = $array_results->id_evento  ";
        $event = $DB->get_record_sql($sql);
        
        array_push($results, $event);
    }
   
    return $results;
}

/**
 * Function that load identifier and firstname of one user in {user} switch username sent
 * @see get_id_switch_user($id_user)
 * @param $id_user---> username
 * @return array
 **/
function get_id_switch_user($id_user){
    global $DB;
    $form_dwarehouse_array = array();
  if(strlen($id_user)>=7){
    $sql = "SELECT id AS cod_user, firstname AS name_user, lastname AS last_name_user FROM {user} AS u WHERE u.username LIKE '$id_user%' ";
    $results = $DB->get_records_sql($sql);
    foreach ($results as $record) {
        array_push($form_dwarehouse_array, $record);
    }
  
}
    return $form_dwarehouse_array;

}

/**
 * Function that load identifier and firstname of one user in {user}  and id_moodle_ases in mdl_talentospilos_user_extended switch username sent
 * @see get_id_switch_user_ases($id_user)
 * @param $id_user---> username
 * @return array
 **/
function get_id_switch_user_ases($id_user){
    global $DB;
    $form_dwarehouse_array = array();
  if(strlen($id_user)>=7){

    $sql = "SELECT UE.id_ases_user AS cod_user,  U.firstname AS name_user, U.lastname AS last_name_user FROM {user} AS U
                INNER JOIN  mdl_talentospilos_user_extended AS UE ON UE.id_moodle_user = U.id
                        WHERE U.username LIKE '$id_user%'";

    $results = $DB->get_records_sql($sql);
    foreach ($results as $record) {
        array_push($form_dwarehouse_array, $record);
    }
  
}
    return $form_dwarehouse_array;

}


/**
 * Function that load register in mdl_talentospilos_df_preguntas switch id sent
 * @see get_question_data($id_pregunta)
 * @param $id_pregunta---> id
 * @return array
 **/
function get_question_data($id_pregunta){
    global $DB;
    $pregunta = array();

    $sql = "SELECT   P.opciones_campo AS options_c, TP.campo AS tipo_campo FROM {talentospilos_df_preguntas} AS P
                 INNER JOIN  {talentospilos_df_tipo_campo} AS TP ON TP.id = P.tipo_campo
                         WHERE P.id = $id_pregunta";

    $results = $DB->get_records_sql($sql);
    foreach ($results as $record) {
        array_push($pregunta, $record);
    }
  

    return $pregunta;

}


/**
 * Function that load record id_registro_respuesta_form according to record id sent
 * @see get_registro_respuesta_form($id_dwarehouse_record)
 * @param $id_dwarehouse_record---> id dwarehouse record
 * @return int
 **/
function get_registro_respuesta_form($id_dwarehouse_record){
    global $DB;
    $pregunta = array();

    $sql = "SELECT * FROM {talentospilos_df_dwarehouse}  WHERE id = $id_dwarehouse_record";

    $result = $DB->get_record_sql($sql);

    return $result;

}

/**
 * Function that update record from   talentospilos_df_form_resp acordging to id sent
 * @see update_record_talentospilos_df_form_resp($id_dwarehouse_record)
 * @param $id_registro_respuesta_form---> id talentospilos_df_form_resp record
 * @return boolean
 **/
function update_record_talentospilos_df_form_resp($id_registro_respuesta_form){

    global $DB;

    $sql_query = "UPDATE {talentospilos_df_form_resp} SET estado = 1 WHERE id = $id_registro_respuesta_form";  
    
    return  $DB->execute($sql_query);

}

/**
 * Insert record  into  talentospilos_df_dwarehouse
 * @see log_to_restore_into_dwarehouse($id_dwarehouse_record)
 * @param $registro_respuesta_form---> id talentospilos_df_form_resp record
 * @return boolean
 **/
function log_to_restore_into_dwarehouse($registro_respuesta_form){

    global $DB;
    $new_log = new stdClass();
    $new_log = $registro_respuesta_form;
    $new_log->accion = 'RESTORE';
    
    return  $DB->insert_record('talentospilos_df_dwarehouse', $new_log, $returnid=false, $bulk=false);

}


/**
 * Function that load identifier and firstname of one user in {user} switch username sent
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


 /**
 * Get df_alias register
 *
 * @see get_df_alias()
 * @return array
 */

function  get_df_alias()
{
    global $DB; 
   $sql_query = "SELECT * FROM {talentospilos_df_alias}";
   return $DB->get_records_sql($sql_query);
}

 /**
 * Get tipo_form 
 *
 * @see get_tipo_form(id_registro_respuesta_form)
 * @return string
 */

function  get_tipo_form($id_registro_respuesta_form)
{
    global $DB; 
   $sql_query = "SELECT df_formularios.alias AS tipo_formulario FROM {talentospilos_df_form_resp} AS df_form_resp 
                         INNER JOIN {talentospilos_df_formularios} AS df_formularios ON df_formularios.id = df_form_resp.id_formulario
                                WHERE df_form_resp.id = '$id_registro_respuesta_form' ";
   return $DB->get_record_sql($sql_query);
}


/**
 * Function that load username and firstname data switch id_ases
 * @see getDataToUrlByIdAses($id_ases_student)
 * @param $id_ases_student---> id_ases_user
 * @return array
 **/

function  getDataToUrlByIdAses($id_ases_student)
{
    global $DB; 

    $current_semester = get_current_semester()->max;

                $sql_query = "   SELECT url_data.id_instancia AS instanceid, url_data.username, url_data.firstname, url_data.lastname, _context.instanceid AS courseid
                FROM mdl_context AS _context
                         INNER JOIN (

                                SELECT * FROM mdl_block_instances AS _block_instances

                                 INNER JOIN (

                                     SELECT * FROM mdl_talentospilos_monitor_estud AS _talentospilos_monitor_estud

                                    INNER JOIN (
                                            SELECT * FROM mdl_talentospilos_user_extended AS talentospilos_user_extended
                                                 INNER JOIN mdl_user AS _user ON talentospilos_user_extended.id_moodle_user = _user.id
                                        
                ) AS data_student ON _talentospilos_monitor_estud.id_estudiante = data_student.id_ases_user AND _talentospilos_monitor_estud.id_semestre = '$current_semester'
                ) AS _contexto ON  _block_instances.id = _contexto.id_instancia
                ) AS url_data ON _context.id = url_data.parentcontextid

                    WHERE url_data.id_ases_user = '$id_ases_student' AND url_data.tracking_status = 1";

   return $DB->get_records_sql($sql_query);
}

/**
 * Function that load id_ases switch username
 * @see getIdAses($username)
 * @param $id_ases_student---> id_ases_user
 * @return array
 **/

function  getIdAses($username){
    global $DB; 
   $sql_query = "SELECT talentospilos_user_extended.id_ases_user AS id_ases_user FROM {user} AS _user 
                     INNER JOIN {talentospilos_user_extended} AS talentospilos_user_extended ON _user.id = talentospilos_user_extended.id_moodle_user
                         WHERE _user.username  LIKE '$username%'";
   return $DB->get_record_sql($sql_query);
}

/**
 * Iterate array to operate with data
 * @see getDataToUrl($students)
 * @param $students---> id_ases_user 
 * @return array Students data to url
 **/

function getDataToUrl($students){

    $array_students = [];
    foreach ($students as $student){
        //Se reciben los id_ases a buscar.
        //Se realiza la busquedad por cada id_ases recibido 
        $data = getDataToUrlByIdAses($student);
        if(count($data) != 0){
            array_push($array_students, $data );
        }
      
    }

    return $array_students;
}

/**
 * Function that get id_ases_user for each student in array param
 * @see getIdAsesByUsernames($username_students)
 * @param $username_students---> array with username students 
 * @return array
 **/

function  getIdAsesByUsernames($username_students)
{    
    $array_id_ases_students= [];
    //Iterate array to operate with data
    foreach($username_students as $username){

        //Traer id ases que corresponde a cada username
        $id_ases_user_switch_username = getIdAses($username);
        array_push($array_id_ases_students, $id_ases_user_switch_username->id_ases_user);
    }

    //Retornar array con id_ases de cada estudiante del arreglo recidibo

    return $array_id_ases_students;
}


 /**
 * getIdEstudianteFromRecordDwarehouse
 *
 * @see getIdEstudianteFromRecordDwarehouse($dt_alm, $dt_prev,  $local_alias, $accion_record, $alias_formulario);
 * @param object $dt_alm Representa datos almacenados  del registro que llega.
 * @param object $dt_prev Representa datos previos  del registro que llega.
 * @param string $local_alias Local alias del campo a consultar. Depende del tipo de formulario.
 * @param string $accion_record Accion registrada en el log.
 * @param string $alias_formulario Tipo de formulario.
 * @return array 
 */

function getIdStudentFromRecordDwarehouse($dt_alm, $dt_prev, $local_alias, $accion_record, $alias_formulario) {

    //Function return ['-9'] if it does not find the data, or there are inconsistencies

    $retorno_id = ['-9'];
    $auxiliar_id_estudiante_prev = '-11';
    $auxiliar_id_estudiante_alm = '-10';



    if ( $accion_record == 'RESTORE' || $accion_record == 'DELETE' || $accion_record == 'UPDATE') {
        $dt_alm = $dt_alm->record;
        $dt_prev = $dt_prev->record;
        $campos_alm = $dt_alm->campos;
        $campos_prev = $dt_prev->campos;


        //Buscar 
        foreach ($campos_alm as $campo) {
            if ($campo->local_alias == $local_alias) {
                $auxiliar_id_estudiante_alm = $campo->respuesta;
                break;
            }
        }

        foreach ($campos_prev as $campos) {
            if ($campo->local_alias == $local_alias) {
                $auxiliar_id_estudiante_prev = $campo->respuesta;
                break;
            }
        }

        if ($auxiliar_id_estudiante_alm == $auxiliar_id_estudiante_prev) {

            if ($alias_formulario->tipo_formulario != "seguimiento_grupal" ) {
                //Tienen id_ases, trae id_ases del registro
                $retorno_id = [$auxiliar_id_estudiante_alm];
            } else {
                //Traer arreglo de códigos
                $retorno_id = $campo->respuesta;
                $retorno_id = explode(",",$retorno_id);
            }

        } else {
            $retorno_id = ['-9'];
        }

    } else if ($accion_record == 'INSERT') {
        $dt_alm = $dt_alm->record;
        $campos_alm = $dt_alm->campos;

        foreach ($campos_alm as $campo) {
            if ($campo->local_alias == $local_alias) {

                if ($alias_formulario->tipo_formulario  != "seguimiento_grupal" ) {
                    //Tienen id_ases, trae id_ases del registro
                    
                    $retorno_id = [$campo->respuesta];
                    
                } else {
                    //Traer arreglo de códigos
                    $retorno_id = $campo->respuesta;
                    $retorno_id = explode(",",$retorno_id);
                }

                break;
            }
        }


    } else {
        $retorno_id = ['-9'];
        // swal(
        //     'DATA ERROR',
        //     'Oooops! Not supported',
        //     'warning'
        // );
    }


    return $retorno_id;
}