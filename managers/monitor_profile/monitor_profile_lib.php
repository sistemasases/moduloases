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
require_once(__DIR__ . "/../pilos_tracking/v2/pilos_tracking_lib.php");

$MONITORS_TABLENAME = $GLOBALS[ 'CFG' ]->prefix . "talentospilos_monitores";

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
 * Returns all monitors in the same instance
 * 
 * @param $id_instancia int
 * @return Array monitors
 */

function get_all_monitors()
{

    global $DB;
    global $MONITORS_TABLENAME;

    $query ="
        SELECT U.username, U.firstname, U.username 
        FROM $MONITORS_TABLENAME 
        INNER JOIN {user} U 
        ON $MONITORS_TABLENAME.id_moodle_user = U.id";

    $result = $DB->get_records_sql( $query );
    return $result;
}


/**
 * Get's monitor'
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
 * Handles all activities needed to load de assignments tab
 * 
 * @param int $monitor_id
 * @param int $instance_id
 */
function monitor_load_trackings_tab(string $monitor_username, int $monitor_id, int $instance_id)
{
    $active_periods = get_active_periods($monitor_id, $instance_id); 
    
    if ( count($active_periods) >= 1 ) {

        $tracking_count = array();
        
        foreach ($active_periods as $period_id) {
            $tracking_count[$period_id] = pilos_tracking_get_tracking_count($monitor_username, $period_id, $instance_id, true);
        }   

        print_r($tracking_count);

    } else {
        return 0;
    }
}


/**
 * Gets the periods in which a given monitor has been active under a given instance.
 *
 * @param int $monitor_id -> monitor's id from {user} table
 * @param int $instance_id 
 * 
 * @return array $active_periods
 */

function get_active_periods(int $monitor_id, int $instance_id)
{
    global $DB;
    
    if($monitor_id <= 0 || $instance_id <= 0) {
        throw new Exception('Argumento(s) inválidos.');
    }

    $sql = "SELECT id_semestre 
            FROM {talentospilos_monitor_estud} 
            WHERE id_monitor = $monitor_id 
            AND id_instancia = $instance_id";
    
   $result = $DB->get_records_sql($sql); 
    return $result;
}


/**
 *  Realiza un select con los monitores de la instancia ASES
 * */
function make_select_monitors($monitor=null) {

    $monitors = get_all_monitors();
        
    $html = "<select id='select-monitores' style='width:100%'> <option selected=Selected>Seleccione un monitor</option>";

    foreach($monitors as $monitor) {
        $monitor_name = $monitor->username . " " . $monitor->firstname;
        $html .= "<option value='$monitor_name'>$monitor_name</option>";
    }

    $html .= "</select>";

    return $html;
}