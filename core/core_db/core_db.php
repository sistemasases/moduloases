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
 * Librerias y metodos de acceso a la base de datos
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2019 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
const VERSION=1;

require_once( __DIR__ . "/../../../../config.php");
require_once( __DIR__ . "/v" . VERSION . "/entrypoint.php");
use function \core_db\{call_db_function};

function core_db_select($class_name, array $conditions = null, $fields = '*', $sort = null) {
    return call_db_function(\core_db\SELECT, $class_name, $conditions , $fields , $sort);
}


function core_db_execute($sql, $params) {
    return call_db_function(\core_db\EXECUTE, $sql, $params);
}


function core_db_save($instance, $table_name = null){
    return call_db_function(\core_db\SAVE,$instance, $table_name);
}


function core_db_update( $instance, $table_name = null) {
    return call_db_function(\core_db\UPDATE, $instance, $table_name);
}


function core_db_count($class_name, $conditions=null) {
    return call_db_function(\core_db\COUNT, $class_name, $conditions);
}


function core_db_select_sql($sql, $params = array()) {
    return call_db_function(\core_db\SELECT_SQL, $sql, $params);
}


function core_db_exists($class_name, $conditions): bool {
    return call_db_function(\core_db\EXISTS, $class_name, $conditions);
}


function core_db_select_one($class_name, $conditions=[], $fields='*') {
    return call_db_function(\core_db\SELECT_ONE, $class_name, $conditions, $fields );
}