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

require_once dirname(__FILE__) . '/../../../../config.php';
require_once $CFG->dirroot . '/grade/querylib.php';
require_once $CFG->dirroot . '/grade/report/user/lib.php';
require_once $CFG->dirroot . '/grade/lib.php';


/**
 * Function that gets object of {user} with id_moodle
 * @see get_info_monitor($id_moodle)
 * @return object 
 */

function get_info_monitor($id_moodle){
      global $DB;

    $sql_query = "select * from {user} where id='$id_moodle'";
    $info_monitor = $DB->get_record_sql($sql_query);
    return $info_monitor;
}

/**
 * Function that gets an array with dates and hours 
 * @see get_report_by_date()
 * @return object 
 */
function get_report_by_date($fecha_inicial, $fecha_final){

    global $DB;

        $sql_query = "SELECT seg.id,id_monitor,fecha,hora_ini,hora_fin
    FROM {user}  usuario INNER JOIN {talentospilos_seguimiento}  seg ON usuario.id = seg.id_monitor where fecha<=$fecha_final and fecha>=$fecha_inicial order by fecha asc";
    return $DB->get_records_sql($sql_query);
}
