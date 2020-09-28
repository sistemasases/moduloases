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
 * @param $monitor_code String
 * @return Object $monitor
 */
function get_monitor($monitor_id) {
    //ToDo
    // Consulta a la nueva bd de monitores.
    // Mientras tanto crear tabla para ensayar.
}

/**
 * Gets monitor assignments on a certain period.
 *
 * @param int $monitor_code
 * @param int $period
 *
 * @return Object $assignments.
 */
function get_monitor_assignments($monitor_code, $period) {
    // A function like this already exists, this will be just
    // a wrapper.
}

/**
 * Returns  a individual url for a specific monitor
 * 
 * @param int $monitor_code
 * @return String $url
 */
function get_individual_url($monitor_code) {

}

/**
 *  Realiza un select con los monitores de la instancia ASES
 * */
function make_select_monitors() {

    $monitors = get_all_monitors();
    $html = "<select id='select-monitores' style='width:100%'> <option selected=Selected>Seleccione un monitor</option>";

    foreach($monitors as $monitor) {
        $monitor_name = $monitor->username . " " . $monitor->firstname;
        $html .= "<option value='$monitor_name'>$monitor_name</option>";
    }
    $html .= "</select>";

    return $html;
}
