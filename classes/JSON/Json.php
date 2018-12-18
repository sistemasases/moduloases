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
 * Json class definition and utility functions
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once(__DIR__.'/../../managers/lib/reflection.php');
class Json {

    /**
     * Check if an input JSON string have header names compatible with the properties
     * of a given class or classname
     * @param string Json
     * @return bool If the json properties are equal to class properties
     * @throws ErrorException
     */
    public static function json_compatible_with_class($csv_file, $class_or_classname) {
        $json_properties = \reflection\get_properties(json_decode($class_or_classname));
        $class_or_classname_properties = \reflection\get_properties($class_or_classname);
        return $class_or_classname_properties == $class_or_classname_properties;
    }
    /**
     * Check if an input JSON string is valid
     * @param string Json
     * @return bool If the json is well formed return true
     */
    public static function valid_json($json_string) {
        json_decode($json_string);
        return json_last_error() == JSON_ERROR_NONE;
    }
}