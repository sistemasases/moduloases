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
 * @author      David Santiago Cortés 
 * @package     block_ases
 * @copyright   2020 David Santiago Cortés <david.cortes@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../../../config.php");
require_once(__DIR__ . "/../lib/lib.php");
require_once(__DIR__ . "/../../core/module_loader.php");
require_once(__DIR__ . "/../pilos_tracking/v2/pilos_tracking_lib.php");

module_loader("periods");

$MONITORS_TABLENAME = $GLOBALS[ 'CFG' ]->prefix . "talentospilos_monitores";
$CURRENT_PERIOD = core_periods_get_current_period()->id;

/**
 * Checks if a user has the monitor_ps role assigned
 * 
 * @param string $code
 *
 * @return true if the given user has monitor_ps role assigned
 * false otherwise. 
 */
function monitor_is_monitor_ps($code)
{
    global $DB;
    global $DB_PREFIX;

    $tablename = $DB_PREFIX . "talentospilos_user_rol";
    $user_id = search_user($code)->id;

    if (!is_numeric($user_id)){
        return false;
    }

    $query = "
        SELECT *
        FROM $tablename
        WHERE estado=1
        AND id_usuario=$user_id
        AND id_rol=4";

    $result = $DB->get_record_sql($query);

    if (isset($result)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Returns all monitors belonging to the same instance and under the same boss.
 * 
 * @param int $instance_id
 * @return Array monitors
 */

function get_all_monitors(int $instance_id)
{

    global $DB;
    global $MONITORS_TABLENAME;

    $query ="
        SELECT * 
        FROM {user} WHERE id in(
            SELECT DISTINCT id_usuario
            FROM {talentospilos_user_rol}
            WHERE id_rol=4 
            AND id_instancia=$instance_id
            AND id_usuario IN(
                SELECT id_moodle_user
                FROM $MONITORS_TABLENAME
            )
        )";

    $result = $DB->get_records_sql( $query );
    return $result;
}

/**
 * Retorna todos los monitores activos de una instancia dada bajo
 * la directriz del practicante dado.
 *
 * @param int $instance_id : instancia a consultar.
 * @param int $pract_id : id moodle del practicante.
 */
function get_all_monitors_pract(int $instance_id, int $pract_id) {
   global $DB; 
   global $MONITORS_TABLENAME;

   $query ="
        SELECT * FROM {user} WHERE id in(
            SELECT DISTINCT id_usuario 
            FROM {talentospilos_user_rol}
            WHERE id_jefe=$pract_id
            AND id_instancia=$instance_id
            AND id_rol=4
            AND id_usuario IN (
                SELECT id_moodle_user
                FROM $MONITORS_TABLENAME
            )
        )";
    
    $result = $DB->get_records_sql( $query ); 
    return $result;
}


/**
 * Retorna todos los monitores de una instancia dada
 * bajo la directriz de los practicantes de un profesional dado.
 *
 * @param int $instance_id : id de la instancia a la que pertenece.
 * @param int $prof_id : id moodle del profesional.
 */
function get_all_monitors_prof(int $instance_id, int $prof_id)
{
    $practicants = get_all_practs_of_prof($instance_id, $prof_id);
    $monitors = array();
    foreach($practicants as $pract) {
       $monitors += get_all_monitors_pract($instance_id, $pract->id_usuario); 
    }

    return $monitors;
}

/**
 * Retorna los practicantes que ha tenido asignado un profesional, durante
 * cualquier semestre.
 *
 * @param int $instance_id : ID de la instancia.
 * @param int $prof_id : ID Moodle del profesional.
 *
 * @return array 
 *
 */
function get_all_practs_of_prof(int $instance_id, int $prof_id)
{
    global $DB;

    $query =
        "SELECT DISTINCT * 
        FROM {talentospilos_user_rol}
        WHERE id_jefe=$prof_id
        AND id_rol=7
        AND id_instancia=$instance_id";
    
   return $DB->get_records_sql( $query ); 
}

/**
 * Determina si un monitor esta activo:
 *
 * @param int $monitor_moodle_id : ID moodle del monitor
 * @return true sí el monitor está activo | false sino.
 */
function monitor_is_active(int $monitor_moodle_id, int $instance_id)
{
    global $DB;
    global $CURRENT_PERIOD;

    if ($monitor_moodle_id <= 0) {
        Throw New Exception('ID del mónitor es inválido', -1);
    }

    $query = 
        "SELECT *
        FROM {talentospilos_user_rol}
        WHERE id_usuario=$monitor_moodle_id
        AND id_instancia=$instance_id
        AND id_jefe IS NOT NULL
        AND id_rol=4
        AND id_semestre=$CURRENT_PERIOD";
    
    $result = $DB->get_record_sql( $query );

    if ( property_exists($result, 'id') ) {
        
        return true; 
    }
    return false;
}

/**
 * Returns all fields in MONITORS_TABLENAME
 * belonging to the given monitor (bank account, cc, etc)
 *
 * @param $monitor_id int -> monitor's moodle user
 * @return Object $monitor | null
 * @Throws Exception if there's no monitor with given id.
 */

function get_monitor(int $monitor_id) {
    global $DB;
    global $MONITORS_TABLENAME;

    if($monitor_id <= 0) {
        return null;
    }

    $query = "SELECT * FROM $MONITORS_TABLENAME WHERE id_moodle_user = $monitor_id";

    $result = $DB->get_record_sql($query); 

    if (!property_exists($result, 'id')) {
        Throw new Exception("Error consultando la información", -1);
    } else {
        return $result;
    }
}


/**
 * Handles all backend activities needed to initialize the history boss tab:
 * - Find out every boss a monitor has had during each active semester.
 * - Add the previous info to the table html.
 *
 * @param int $monitor_moodle_id
 * @param int $instance_id
 *
 * @return Object $data containing the table html.
 */
function monitor_load_bosses_tab(int $monitor_moodle_id, int $instance_id) {
    $data = new stdClass();
    $active_periods = get_active_periods($monitor_moodle_id, $instance_id ); 
    
    $table_html = 
        "<table id='table_boss'>
            <tr>
                <th>Período</th><th>Jefe (Prácticante)</th><th>Jefe (Profesional)</th>
            </tr>";
    foreach ($active_periods as $period) {
        $period_id = $period->id;
        $period_nombre = $period->nombre;

        $practicant = get_monitor_boss($monitor_moodle_id, $period_id); 
        $practicant_name = $practicant->firstname." ".$practicant->lastname;


        if (property_exists($practicant, 'id')) {
            
            $profesional = get_practicant_boss_under_period($practicant->id, $period_id);
            $profesional_name = $profesional->firstname . " " . $profesional->lastname; 
        }
        else {
            $profesional_name="";
        }
        
        //$period->jefe = $profesional_name;
        $table_html .= 
            "<tr>
                <td>".$period_nombre."</td><td>".$practicant_name."</td>
                <td>".$profesional_name."</td>
            </tr>";
    }
    $table_html .= "</table>";
    $data->table_html = $table_html;
    
    return $data;
}

/**
 * Retorna el jefe de un prácticante durante un período específico.
 *
 * @param int $pract_moodle_id : ID moodle del prácticante.
 * @param int $period_id : Período a consultar.
 *
 * @return array | null|
 * @throws Exception en caso que los id's sean menores a cero
 */
function get_practicant_boss_under_period(int $pract_moodle_id, int $period_id)
{
   global $DB; 

   if ($pract_moodle_id <=0 || $period_id <=0) {
       Throw New Exception("Argumento(s) inválido(s)");
   }

   $query = 
       "SELECT * FROM {user} WHERE id IN (
            SELECT id_jefe
            FROM {talentospilos_user_rol}
            WHERE id_usuario=$pract_moodle_id
            AND id_semestre=$period_id
            AND id_rol=7
        )";

    $result = $DB->get_record_sql( $query ); 
    if ( !property_exists($result, 'id') ) {
        return null;
    }

    return $result;
}
/**
 * Gets tracking count of given monitor during a given semester on a given instance.
 * This function is a slimmer version of pilos_tracking_get_tracking_count, adapted
 * for only counting the monitor's forms count.
 *
 * @param int $moodle_id
 * @param int $instance_id
 * @param int $period_id
 * 
 * @see pilos_tracking_get_tracking_count on managers/pilos_tracking/v2/pilos_tracking_lib
 * @return Object
 */ 
function monitor_get_tracking_count(int $moodle_id, int $instance_id, int $period_id)
{
   global $DB; 

   $fecha_inicio = null;
   $fecha_fin = null;

   $interval = core_periods_get_period_by_id($period_id);
   if (!$interval) {
       Throw new exception("ID de Período inválido: $period_id");
   }

    $fecha_inicio = getdate(strtotime($interval->fecha_inicio));
    $fecha_fin = getdate(strtotime($interval->fecha_fin));

    $mon_tmp = $fecha_inicio["mon"];
    $day_tmp = $fecha_inicio["mday"];
    if( $mon_tmp < 10 ){
        $mon_tmp = "0" . $mon_tmp;
    }
    if( $day_tmp < 10 ){
        $day_tmp = "0" . $day_tmp;
    }

    $fecha_inicio_str = $fecha_inicio["year"]."-".$mon_tmp."-".$day_tmp;

    $mon_tmp = $fecha_fin["mon"];
    $day_tmp = $fecha_fin["mday"];
    if( $mon_tmp < 10 ){
        $mon_tmp = "0" . $mon_tmp;
    }
    if( $day_tmp < 10 ){
        $day_tmp = "0" . $day_tmp;
    }

    $fecha_fin_str = $fecha_fin["year"]."-".$mon_tmp."-".$day_tmp;

    $sql_mon_estud = 
        "SELECT ME.id_estudiante, U.username, ME.id_monitor
        FROM {talentospilos_monitor_estud} AS ME
        INNER JOIN {user} AS U ON ME.id_monitor = U.id
        WHERE id_monitor=$moodle_id 
        AND id_semestre=$period_id
        AND id_instancia=$instance_id";
    
    $assigned_students = $DB->get_records_sql($sql_mon_estud);

    $to_return = new stdClass(); 
    $to_return->total_count = 0;
    foreach($assigned_students as $student) {
        $count = new stdClass();
        $count->username = $student->id_estudiante;
        $count->count = pilos_tracking_general_get_count(
            $student->id_estudiante, "estudiante_t", $fecha_inicio_str, $fecha_fin_str, $instance_id, $period_id
        );
        $to_return->total_count += ($count->count["total_profesional"]);
    }
    return $to_return;
}

/**
 * Gets the periods in which a given monitor has been active under a given instance.
 * i.e: has a record in {talentospilos_user_rol}
 *
 * @param int $monitor_id -> monitor's id from {user} table
 * @param int $instance_id -> instance which the monitor belongs to. 
 * 
 * @return array
 */

function get_active_periods(int $monitor_id, int $instance_id)
{
    global $DB;
    
    if($monitor_id <= 0 || $instance_id <= 0) {
        throw new Exception('Argumento(s) inválidos.');
    }

    $sql = "SELECT DISTINCT id_semestre 
            FROM {talentospilos_monitor_estud} 
            WHERE id_monitor = $monitor_id 
            AND id_instancia = $instance_id";
            
    
    $result = $DB->get_records_sql($sql); 
    
    $to_return = [];

    foreach($result as $item) {
        array_push($to_return, core_periods_get_period_by_id($item->id_semestre));
    }
    
    return $to_return;
}

/**
 * Se encarga de guardar la información del monitor en la bd.
 *
 * @param Array $form, contiene los campos a actualizar.
 * @return true, si la operación fue exitosa.
 * @throws Exception
 */
function monitor_save_profile($form) {

    // Se prepara el objeto que tendrá
    // todos los registros a ser actualizados.
    $dataObj = new stdClass();

    // La variable form contiene todos los campos a actualizar
    // junto con su nuevo valor, pero en la primera posición
    // contiene el id_moodle del monitor a actualizar. 
    //
    // db manager necesita el id para saber que registro
    // actualizar en la db.
    $monitor_id_moodle = array_pop($form)->value;

    // Id del registro a actualiazar.
    $id_record = get_monitor($monitor_id_moodle)->id;

    $dataObj->id = (int)$id_record;

    foreach ($form as $field) {
        switch ($field->name) {
            case "num_doc":
            case "pdf_doc":
            case "pdf_d10":
            case "pdf_cuenta_banco":
            case "pdf_acuerdo_conf":
            case "email_alternativo":
            case "telefono1":
            case "telefono2":
                $key = $field->name;
                $value = $field->value;
                $dataObj->$key = $value;
                break;
            default:
                Throw new exception("El campo $field->name no existe en el formulario.");
        }
    }
    return update_monitor_records($dataObj);
}

/**
 * Actualiza un registro específico de la tabla monitores.
 *
 * @param stdClass $data -> objeto de datos con los campos a actualizar.
 *
 * @return true, si la operación fue exitosa.
 * @throws Exception, si hubo problemas en la operación.
 */
function update_monitor_records(stdClass $data)
{
    
    global $DB;
    global $MONITORS_TABLENAME;
    try {
        $DB->update_record(substr($MONITORS_TABLENAME, 4), $data);
        return true;
    } catch (Exception $e) {
        Throw new exception($e); 
        return false;
    }
}

/**
 * Retorna el jefe de un monitor durante un período específico.
 *
 * @param int $monitor_moodle_id
 * @param int $period_id
 *
 * @return stdClass $jefe
 * @throws Exception e
 */
function get_monitor_boss(int $monitor_moodle_id, int $period_id)
{

    if ($monitor_moodle_id <= 0) {
        Throw new Exception("ID del monitor inválido");
    }

    global $DB;
    try {
        $query = 
            "SELECT * 
            FROM {user}
            WHERE id in(
                SELECT id_jefe 
                FROM {talentospilos_user_rol}
                WHERE id_usuario=$monitor_moodle_id
                AND id_semestre=$period_id)";
        
        $result = $DB->get_record_sql($query);
        return $result; 
    } catch (Exception $e){
        Throw new Exception($e);
    }
}

/**
 * Generates the html for the active periods select on the trackings tab.
 *
 * @param int $moodle_id
 * @param int $instance_id
 *
 * @return Object
 */
function make_select_active_periods($moodle_id, $instance_id) {
    $active_periods = get_active_periods($moodle_id, $instance_id); 
    $html = "<select id='select-periods' style='width:20%'> ";

    foreach($active_periods as $period) {
        $html .= "<option value='$period->id'>$period->nombre</option>";
    }
    $html .= "</select>";

    return $html;
}


/**
 *  Realiza un select con los monitores de la instancia ASES
 * */
function make_select_monitors($monitors) {

    $html = "<select id='select-monitores' style='width:100%'> <option selected=Selected>Seleccione un monitor</option>";

    foreach($monitors as $monitor) {
        $monitor_name = $monitor->username . " " . $monitor->firstname . " " . $monitor->lastname;
        $html .= "<option value='$monitor_name'>$monitor_name</option>";
    }

    $html .= "</select>";

    return $html;
}


/**
 * Retorna la URL de la imagen del pérfil de moodle del monitor, utilizando
 * la FileAPI de Moodle
 *
 * @param int $context_block_id
 * @param int $moodle_id
 *
 * @return string moodle url | empty string
 *
 * @see https://github.com/sistemasases/moduloases/wiki/Cargar-archivos-de-plugin
 */
function get_mon_URL_profile_img(int $context_block_id, int $moodle_id): string {
    $fs = get_file_storage();
    $files = $fs->get_area_files($context_block_id, 'block_ases', 'profile_image', $moodle_id);
    $image_file = array_pop($files);

    if (sizeof($files) == 0) {
        return '';
    }
    return moodle_url::make_pluginfile_url(
        $image_file->getcontextid(), $image_file->get_component(), $image_file->get_filearea(),
        $image_file->get_itemid(), $image_file->get_filepath(), $image_file->get_filename()
    );
}

/**
 * Retorna el html necesario para mostrar la imagen de pérfil del monitor en su ficha.
 *
 * @param int $context_block_id
 * @param int $moodle_id
 *
 * @return string <img> element
 *
 * @see https://github.com/sistemasases/moduloases/wiki/Cargar-archivos-de-plugin 
 *
 */
function get_mon_html_profile_img(int $context_block_id, int $moodle_id)
{
    global $OUTPUT;
    $image_url = get_mon_URL_profile_img($context_block_id, $moodle_id);

    if ($image_url != '') {
        return html_writer::empty_tag('img', array('src' => $image_url, 'alt'=>'profile_image'));
    }

    $monitor = \core_user::get_user($moodle_id, '*', MUST_EXIST);
    return $OUTPUT->user_picture($monitor, array('size'=>150, 'link'=>false, 'class'=>'img-general-fields'));
}
