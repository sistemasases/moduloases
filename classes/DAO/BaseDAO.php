<?php
require_once(__DIR__.'/../traits/from_std_object_or_array.php');
require_once(__DIR__.'/../../managers/lib/reflection.php');
use reflection;
abstract class BaseDAO {
    use from_std_object_or_array;
    const NO_REGISTRA = 'NO REGISTRA';
    /**
     * Save object to database
     * @return bool|int id if was sucessfull created return id false otherwise
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function save() {
        global $DB;
        $CLASS = get_called_class();
        $safety_save_object = $this->__delete_not_null_fields_than_have_predefined_values_in_db($CLASS, $this);
        return $DB->insert_record($CLASS::get_table_name(), $safety_save_object );
    }
    /**
     * Check if object have null properties, and create a new object than does not have
     * the null propesties than the database have predefined values for
     * @param string|classname $CLASS Class from is calling the method
     * @param object $object Initial object to be proccesed
     * @return object Object without the undefined properties
     */
    private function __delete_not_null_fields_than_have_predefined_values_in_db($CLASS, $object) {
        if(!method_exists( $CLASS, 'get_not_null_fields_and_default_in_db')) {
            return $object;
        }
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
     * Only the shared properties between objects are assigned, the aditional properties are skipped
     * @param mixed $std_object Source object
     * @throws \ErrorException If 0 properties are shared between objects
     */
    public function make_from($std_object) {
        \reflection\assign_properties_to($std_object, $this);
    }
    public  static function get_all() {
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

    /**
     * Return object instances from database than satisfy the conditions given
     * @param $conditions Array key-value than gets the conditions for get the objects
     * @example $conditions =  array('username'=> 'Camilo', 'lastname'=> 'Cifuentes')
     * @example $conditions = array(AsesUser::USER_NAME => 'Camilo', AsesUser::LAST_NAME => 'Cifuentes')
     * @return array Object instance if exists in database, empty array if does not exist
     * @throws dml_exception
     *
     */
    public static function get_by($conditions) {
        global $DB;
        $CLASS = get_called_class();
        return $CLASS::make_objects_from_std_objects_or_arrays($DB->get_records($CLASS::get_table_name(), $conditions));
    }

    /**
     * Check if object exists in database based in an array of a  given conditions
     * @param $conditions Array key-value than gets the conditions for get the objects
     * @example $conditions =  array('username'=> 'Camilo', 'lastname'=> 'Cifuentes')
     * @example $conditions = array(AsesUser::USER_NAME => 'Camilo', AsesUser::LAST_NAME => 'Cifuentes')
     * @return bool
     * @throws dml_exception
     */
    public static function exists($conditions) {
        global $DB;
        $CLASS = get_called_class();
        $table_name = $CLASS::get_table_name();
        return $DB->record_exists($table_name, $conditions);
    }
    /**
     * Return simple string than represents the table name without prefix
     * @example return 'talentospilos_usuario';
     * @return string
     */
    public abstract static function get_table_name(): string;

    /**
     * Format the current object before save in database
     * Use this method if you need modify format any field before insertion, for example
     * convert string date to unix time because in database the format of date is big int
     */
    public abstract  function format();
    
    public static function get_column_names(): array {
        return \reflection\get_properties(get_called_class());
    }
}

?>