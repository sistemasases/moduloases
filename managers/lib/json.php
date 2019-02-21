<?php
/**
 * Json utility functions
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace json;

/**
 * Check if an input JSON string is valid
 * @param string Json
 * @return bool If the json is well formed return true
 */
function valid_json($json_string): bool {
    if(!is_string($json_string)) {
        return false;
    }
    json_decode($json_string);
    return json_last_error() == JSON_ERROR_NONE;
}
/**
 * Check if an input JSON string have header names compatible with the properties
 * of a given class or classname
 * @param string Json
 * @return bool If the json properties are equal to class properties
 * @throws \ErrorException
 */
function compatible_with_class($json_string, $class_or_classname) {
    $json_properties = \reflection\get_properties(json_decode($json_string));
    $class_or_classname_properties = \reflection\get_properties($class_or_classname);
    return $json_properties == $class_or_classname_properties;
}
