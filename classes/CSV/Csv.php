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
 * CsvManager definition
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../managers/lib/reflection.php');
class Csv {

    /**
     * Normalize common csv headers as object property names
     * @param array of string $headers Csv header names
     * @return array of string String than represent a posible property name based in each header
     * @example ['doc_number', 'name'] => ['Doc Number', 'Name']
     */
    public static function csv_headers_to_property_names($headers) {
        $property_names = [];
        foreach ($headers as $header) {
            $header_with_only_letters = preg_replace('/[^a-zA-z]/','', $header);
            $property_name = strtolower($header_with_only_letters);
            array_push($property_names,$property_name);
        }
        return $property_names;
    }
    /**
     * Return the headers of an input csv, is suposed than the first row
     * contains the headers
     * @param file $csv_file
     * @return array of string
     */
    public static function csv_get_headers($csv_file) {
        $rows = array_map('str_getcsv', $csv_file);
        $headers = $rows[0];
        return $headers;
    }
    /**
     * Check if an input CSV file have header names compatible with the properties
     * of a given class or classname
     * @param Class|string class or class name
     * @return bool If the headers are equal to the property names of class
     * @throws ErrorException
     */
    public static function csv_compatible_with_class($csv_file, $class_or_classname) {
        $headers = Csv::csv_get_headers($csv_file);
        $headers_to_property_names = Csv::csv_headers_to_property_names($headers);
        $class_or_classname_properties = \reflection\get_properties($class_or_classname);
        return is_array(array_diff($class_or_classname_properties ,$headers_to_property_names));
    }
    /**
     * Return array of objects based in a file with csv file format
     * Should have headers at the first row
     * @param file $csv_file
     * @return array of stdObjects
     */
    public static function csv_file_to_std_objects($csv_file) {
        $rows = array_map('str_getcsv', $csv_file);
        $std_objects = [];
        $array_objects = [];
        $headers = $rows[0];
        $property_names =   CSV::csv_headers_to_property_names($headers);
        array_shift($rows); // delete headers
        foreach ($rows as $row) {
            $array_object = [];
            for($i = 0; $i < count($property_names); $i++) {
                $array_object[$property_names[$i]] = $row[$i];

            }
            array_push($std_objects, (object) $array_object);
        }
        return $std_objects;
    }
}