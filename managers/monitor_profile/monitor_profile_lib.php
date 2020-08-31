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

/**
 * Get monitor's basic info given its student code (username)
 *
 * {
 *      cc, fullname, email, current boss, link to NDA, link to bank account,
 *      picture.
 * }
 *
 * @param $monitor_code String
 * @return Object $monitor
 */
function get_monitor($monitor_code) {
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
function make_select_monitors($monitors) {
    $html = "<select id=''monitores' style='width:100%'> <option selected=Selected>Seleccione un monitor</option>";

    foreach($monitors as $monitor) {
        $monitor_name = $monitor->username . $monitor->fullname;
        $html .= "<option value='$monitor_name'>$monitor_name</option>";
    }
    $html .= "</select>";

    return $html;
}
