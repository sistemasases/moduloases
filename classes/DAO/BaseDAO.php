<?php
require_once(__DIR__.'/../traits/from_std_object_or_array.php');
require_once(__DIR__.'/../../managers/lib/reflection.php');
use reflection;
class BaseDAO {
    use from_std_object_or_array;
    const NO_REGISTRA = 'NO REGISTRA';
    /**
     * Save object to database
     */
    public function save() {
        global $DB;
        $CLASS = get_called_class();
        $safety_save_object = $this->__delete_not_null_fields_than_have_predefined_values_in_db($CLASS, $this);
        $response = $DB->insert_record($CLASS::get_table_name(), $safety_save_object );
    }
    /**
     * Check if object have null properties, and create a new object than does not have
     * the null propesties than the database have predefined values for
     * @param string|classname $CLASS Class from is calling the method
     * @param object $object Initial object to be proccesed
     * @return object Object without the undefined properties
     */
    private function __delete_not_null_fields_than_have_predefined_values_in_db($CLASS, $object): stdClass {
       
        $not_null_fields_than_have_predefined_values_in_db = $CLASS::get_not_null_fields_and_default_in_db();
        $result_object = new \stdClass();
        foreach ($object as $key => $value) {
            if ($value || !in_array($key, $not_null_fields_than_have_predefined_values_in_db )) {
                $result_object->$key = $value;
            }
        }
        return $result_object;
    }
    /**
     * Assign self properties from std object than can have less properties than $this object
     * but cannot have aditional properties than $this
     * @param mixed $std_object Source object 
     */
    public function make_from($std_object) {
        \reflection\assign_properties_to($std_object, $this);
    }
    public static function get_all() {
        global $DB;
        $CLASS = get_called_class();
        $nombre_tabla = $CLASS::get_table_name() ;
        $sql = 
        "
        SELECT * FROM {".$nombre_tabla."}
        ";
      
        $objects_array = $DB->get_records_sql($sql);
        $objects = $CLASS::make_objects_from_std_objects_or_arrays($objects_array);
        return $objects;
    }


    public static function get_column_names(): array {
        return \reflection\get_properties(get_called_class());
    }
}

?>