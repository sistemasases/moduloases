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
/**
 * For implement this trait your class should be have public property named
 * class_or_class_name and function named add_error, oherwise this doesnt work
 *
 */

require_once (__DIR__ . '/Csv.php');
trait CsvManager {
    /**
     * Check for required functions and properties for implement this trait
     */
    private function validate_caller_csv() {
        $current_class =  get_class($this);
        if(property_exists($current_class, 'class_or_class_name') && method_exists ($current_class, 'add_error')) {
            return true;
        } else {
            throw new Error("La clase $current_class no cumple con los requisitos para implementar el trait CsvManager, (debe tener la propiedad class_or_class_name y el metodo add_error)");
        }
    }
    private function get_spected_file_headers(): array {
        $object_properties =  \reflection\get_properties($this->class_or_class_name);
        $custom_column_mapping = $this->custom_column_mapping();

        if(!is_array($custom_column_mapping) || empty($custom_column_mapping)) {
            return $object_properties;
        } else {

                $object_properties = array_combine($object_properties, $object_properties);// the object properties are now the keys and the values of array

                $headers_ = array_replace($object_properties, $custom_column_mapping); //replace the values with the real mappings

                return array_keys($headers_);
        }
    }

    /**
     * Create instances of type $this->$class_or_class_name based on contents of $file
     * If the columns are no compatible with the class, null is returned
     * @param php file $file Csv file where each row is returned as class instance
     * @throws ErrorException If $this->class_or_class_name does not exist
     * @return array of $class_or_class_name instances
     */
    public function create_instances_from_csv($file, $custom_mapings) {
        $this->validate_caller_csv($custom_mapings);

        if(!Csv::csv_compatible_with_class($file, $this->class_or_class_name, $this->custom_column_mapping())) {

            $spected_file_headers = $this->get_spected_file_headers();

            $file_headers = Csv::get_real_headers($file, $custom_mapings );
            $this->add_error(CsvManagerErrorFactory::csv_and_class_have_distinct_properties(array(
                'class'=>$this->class_or_class_name,
                'object_properties'=>$spected_file_headers,
                'file_headers'=>$file_headers), "El archivo tiene headers incorrectos"));

            return null;
        }
        $std_objects = Csv::csv_file_to_std_objects($file, $custom_mapings);
        if(!class_exists($this->class_or_class_name)) {
            $error = CsvManagerErrorFactory::csv_manager_class_does_not_exist(array('std_objects' => $std_objects, 'class'=>$class));
            $this->add_error ($error);
            return null;
        }
        $instances = array_map(
            function($std_object) {
                $instance = \reflection\make_from_std_object($std_object, $this->class_or_class_name);
                return $instance;
            }, $std_objects);

        return $instances;

    }
    public function custom_header_mapping() {
        
    }
    /**
     * If your csv manager have column names than does not match perfectly with the object than you want return,
     * you can make an alternative column mapping with respect to object properties names
     *
     * For example, having the csv: name1, lastname
     * and the class: name, lastname
     * you could want map the column 'name1' to object property 'name', for this you should return an array
     * array('name'=>'name1');, is not necesary to add lastname to the returned array because it matches perfectly
     *
     * If you not need custom column mapping is not necesary overload this method
     *
     * @return null|array
     */
    public function custom_column_mapping() {
        return null;
    }
}
