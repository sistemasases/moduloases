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

require_once(__DIR__.'/Json.php');
require_once(__DIR__.'/../../managers/lib/reflection.php');
require_once(__DIR__.'/../Errors/Factories/CsvManagerErrorFactory.php');
/**
 * For implement this trait your class should be have public property named
 * class_or_class_name and function named add_error, oherwise this doesnt work
 *
 */
trait JsonManager {
    /**
     * Check for required functions and properties for implement this trait
     */
    private function validate_caller_json() {
        $current_class =  get_class($this);
        if(property_exists($current_class, 'class_or_class_name') && method_exists ($current_class, 'add_error')) {
            return true;
        } else {
            throw new Error("La clase $current_class no cumple con los requisitos para implementar el trait JsonManager, (debe tener la propiedad class_or_class_name y el metodo add_error)");
        }
    }

    /**
     * Create instances from $POST['data']
     */
    public function create_instances_from_post(){
        global $POST;
        $instances = array();
        $objects = $POST['data'];
        return $this->create_instances_from_objects($objects);
    }
    public function create_instances_from_objects($objects) {
        if(count($objects) == 0) {
            return [];
        }
        // If the JSON is well formed but its respective std_object is not an array,
        // return an error

        if(!class_exists($this->class_or_class_name)) {
            $error = CsvManagerErrorFactory::csv_manager_class_does_not_exist(array('std_objects' => $objects, 'class'=>$this->class_or_class_name));
            $this->add_error ($error);
            return null;
        }
        $instances = [];
        foreach($objects as $std_object) {
            $instance = new \stdClass();
            try {
                $instance = \reflection\make_from_std_object($std_object, $this->class_or_class_name);
                array_push($instances, $instance);
            } catch(Exception  $e) {
                $error = ReflectionErrorsFactory::class_and_std_object_or_array_distinct_properties(array('object'=>$std_object));
                $error->message = $e->getMessage();
                $this->add_error($error);
            }

        }

        return $instances;
    }
    /**
     * Create instances of type $this->$class_or_class_name based on string formated as json
     * @param php file $file Csv file where each row is returned as class instance
     * @return array of $class_or_class_name instances
     */
    public function create_instances_from_json($string) {
        $this->validate_caller_json();
        if(!Json::valid_json($string)) {
            $this->add_error(JsonErrorsFactory::json_malformed(array('json_string'=>$string)));
            return null;
        }
        $std_objects = json_decode($string);
        if(!is_array($std_objects)) {
            $this->add_error(JsonErrorsFactory::json_should_be_an_array(array('json_string'=>$string)));
            return null;
        }
        return $this->create_instances_from_objects($std_objects);

    }


}

?>