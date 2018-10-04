<?php
require_once(__DIR__.'/../traits/from_std_object_or_array.php');
require_once(__DIR__.'/../../managers/lib/reflection.php');
//B
abstract class BaseDAO {
    use from_std_object_or_array;

    public function __construct($data = null)
    {
        if ( $data ) {
            $this->make_from($data);
        }
    }

    const NO_REGISTRA = 'NO REGISTRA';
    /**
     * Save object to database
     * @return bool|int id if was sucessfull created return id false otherwise
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function save() {
        global $DB;
        $CLASS = get_called_class();
        $this->format();
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
     * Return a key-value array based in the database objects from database
     * The key and value are representated by param $fields
     *
     * @param string $fields a comma separated list of fields to return - the number of fields should be 2!
     * @param $conditions Array key-value than gets the conditions for get the objects
     * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
     *   all fields are returned). The first field will be used as key for the
     *   array so must be a unique field such as 'id'.
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records in total (optional, required if $limitfrom is set).
     * @example $conditions =  array('username'=> 'Camilo', 'lastname'=> 'Cifuentes')
     * @example $conditions = array(AsesUser::USER_NAME => 'Camilo', AsesUser::LAST_NAME => 'Cifuentes')
     * @see https://docs.moodle.org/dev/Data_manipulation_API
     * @return array Object instance if exists in database, empty array if does not exist
     * @throws dml_exception
     * @throws ErrorException If the given conditions specify invalid column names throws an error
     * @return array Key-value array
     */
    public static function _get_options($fields,   $sort = '', $conditions=null, $limitfrom=0, $limitnum=0) {
        global $DB;
        $CLASS = get_called_class();
        if($conditions && !$CLASS::valid_conditions($conditions)) {
            throw new ErrorException("The given columns for conditions array are invalid, active debug mode for show de debug backtrace");
        }
        return $DB->get_records_menu($CLASS::get_table_name(), $conditions, $sort, $fields, $limitfrom, $limitnum );
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
     *
     * @param $conditions Array key-value than gets the conditions for get the objects
     * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
     *   all fields are returned). The first field will be used as key for the
     *   array so must be a unique field such as 'id'.
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records in total (optional, required if $limitfrom is set).
     * @example $conditions =  array('username'=> 'Camilo', 'lastname'=> 'Cifuentes')
     * @example $conditions = array(AsesUser::USER_NAME => 'Camilo', AsesUser::LAST_NAME => 'Cifuentes')
     * @see https://docs.moodle.org/dev/Data_manipulation_API
     * @return array Object instance if exists in database, empty array if does not exist
     * @throws dml_exception
     * @throws ErrorException If the given conditions specify invalid column names throws an error
     *
     */
    public static function get_by($conditions,  $sort='',  $limitfrom=0, $limitnum=0): array {
        global $DB;
        $CLASS = get_called_class();
        if(!$CLASS::valid_conditions($conditions)) {
            throw new ErrorException("The given columns for conditions array are invalid, active debug mode for show de debug backtrace");
        }
        return $CLASS::make_objects_from_std_objects_or_arrays($DB->get_records($CLASS::get_table_name(), $conditions, $sort, '*', $limitfrom, $limitnum ));
    }

    /**
     * Check if object exists in database based in an array of a  given conditions
     * @param $conditions Array key-value than gets the conditions for get the objects
     * @example $conditions =  array('username'=> 'Camilo', 'lastname'=> 'Cifuentes')
     * @example $conditions = array(AsesUser::USER_NAME => 'Camilo', AsesUser::LAST_NAME => 'Cifuentes')
     * @return bool
     * @throws dml_exception
     */
    public static function exists($conditions): bool  {
        global $DB;
        $CLASS = get_called_class();
        if (!$CLASS::valid_conditions($conditions)) {
            return false;
        }
        $table_name = $CLASS::get_table_name();
        return $DB->record_exists($table_name, $conditions);
    }

    /**
     * Check if the given conditions are valid, this is, all the column names expressed in the kys of the array
     * exists in the current DAO model
     * @param array $conditions Key value array where the keys are the column names and the
     * @return bool True if the condition columns are valid
     * @see https://docs.moodle.org/dev/Data_manipulation_API
     */
    private static function valid_conditions(array $conditions): bool {
        $condition_column_names = array_keys($conditions);
        $CLASS = get_called_class();
        $invalid_column_names = array_diff($condition_column_names, $CLASS::get_column_names());
        $debug_backtrace = debug_backtrace();
        if(count($invalid_column_names) > 0) {
            $previous_function_name = $debug_backtrace[1]['function'];
            $invalid_column_names_string = implode(',', $invalid_column_names);
            trigger_error("The column names given are invalid, previous method called: $previous_function_name, columns invalid: ($invalid_column_names_string), called class: $CLASS");
            return false;
        }
        return true;
    }
    /**
     * Return simple string than represents the table name without prefix
     * @example return 'talentospilos_usuario';
     * @return string
     */
    public abstract static function get_table_name(): string;

    /**
     * Format the current object instance before save in database,
     * this method is always executed before save in database
     * Overload this method if you need modify format any field before insertion, for example
     * convert string date to unix time because in database the format of date is big int
     */
    public  function format() {

    }

    /**
     * Return the column names of the DAO
     * @return array Array of strings than contains the column names
     * @throws ErrorException
     */
    public static function get_column_names(): array {
        return \reflection\get_properties(get_called_class());
    }
}

?>
