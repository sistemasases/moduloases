<?php
require_once(__DIR__.'/../traits/from_std_object_or_array.php');
require_once(__DIR__.'/../../managers/lib/reflection.php');

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
 * Base DAO definition
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once (__DIR__.'/../Errors/Factories/FieldValidationErrorFactory.php');
require_once (__DIR__.'/../Errors/AsesError.php');
abstract class BaseDAO
{

    use from_std_object_or_array;

    const GENERIC_ERRORS_FIELD = 'generic_errors';

    const NO_REGISTRA = 'NO REGISTRA';

    private $_errors = array();

    /**
     * This variable contains the same errors than $_erros but are agrouped by the class properties, if exist
     * some error than does not related with a unique class attribute this is stored in $_errors_object['generic_errors']
     *
     * Example
     * The class Foo have two properties, $a and $b, $a is required and $b should be numeric, but
     * as shown below:
     *
     * '''class Foo extends BaseDAO {
     *  public $a;
     *  public $b;
     *
     *  public function __construct($a, $b) {
     *      $this->a = $a;
     *      $this->b = $b;
     *  }
     * }'''
     * If you have an instance '''$fo = new Foo(null, 'some_no_numeric');''' and execute $this->valid()
     * the $_errors_array should be equal to
     *'''
     * array(
     *  'a' => AsesError(10, 'El campo es requerido'),
     *  'b' => AsesError(11, 'El campo debe ser numerico')
     * );
     *
     *'''
     *And you can access this error array via $this->get_errors_array();
     * @var
     */
    private $_errors_array;
    public function __construct($data = null)
    {
        if ($data) {
            $this->make_from($data);
        }
    }

    public function has_error($error_id): bool {
        $this->valid();
        /* @var AsesError $error*/
        foreach ($this->_errors as $error) {
            if($error->code == $error_id) {
                return true;
            }
        }
        return false;
    }
    /**
     * Clean errors
     */
    private function clean_errors() {
        $this->_errors = array();
        $this->_errors_array = array();
    }
    /**
     * Custom validation method, rewrite this if you need make some aditional validation, this method
     * should be called when $this->valid() is called
     *
     * You also should add the errors using the method $this->add_error()
     *
     * @return bool True if the custom validation has not found any error
     * @see add_error
     */
    /**
     * Check if the current object is valid, and if is not valid add all the errors and make
     * these available by calling get_errors
     * @see get_errors
     * @return bool
     */
    public function _custom_validation(): bool {
        return true;
    }
    public function valid(): bool {

        $this->clean_errors();
        /* If at least one field than should be numeric is not numeric*/
        if( !$this->validate_numeric_fields()) {

            return false;
        }
        /* If at least one field than should be required is empty or null */
        if( !$this->validate_required_fields() ) {

            return false;
        }
        if( !$this->_custom_validation()) {
            return  false;
        }
        return true;
    }

    /**
     * Check if all fields of the object than are required have some value, and return an array with the names
     * of the fields than are invalid, empty array is returned otherwise.
     *
     * Also, if an error is found, this can be returned calling the function get_errors()
     * @see get_errors
     * @return bool True if all the requried fields have some value
     */
    private function validate_required_fields(): bool {
        $valid = true;
        $required_fields = $this->get_required_fields();
        $required_field_is_empty_error = FieldValidationErrorFactory::required_field_is_empty();
        foreach($required_fields as $required_field) {
            if(!property_exists($this, $required_field)) {
                $this->add_error($required_field_is_empty_error, $required_field);
                $valid = false;
                continue;
            } else if($this->{$required_field} == '') {
                $this->add_error($required_field_is_empty_error, $required_field);
                $valid = false;
                continue;
            }
        }
        return $valid;
    }

    /**
     * Add an error to the current object,
     * @param AsesError $error
     * @param string $fieldname Field (or object property) where the error is found, default is generic
     * errors field, this means than the error is not related with any object field or means than the error
     * is related to more than one field at the same time
     *
     */
    public function add_error(AsesError $error, $fieldname = BaseDAO::GENERIC_ERRORS_FIELD ) {
        array_push($this->_errors, $error);

        if(!$this->_errors_array[$fieldname]) {
            $this->_errors_array[$fieldname] = array ($error);
        } else {
            array_push($this->_errors_array[$fieldname] , $error);
        }
    }
    /**
     * Check if all fields of the object than should be numeric are numeric, and return an array with the names
     * of the fields than are invalid, empty array is returned otherwise.
     *
     * Also, if an error is found, this can be returned calling the function get_errors()
     * @see is_numeric
     * @see get_errors
     * @see get_numeric_fields
     * @return bool True if all object properties than should be numeric are numeric
     */
    private function validate_numeric_fields(): bool {
        $valid = true;
        $nuemric_fields = $this->get_numeric_fields();
        foreach($nuemric_fields as $numeric_field) {
            if($this->$numeric_field && !is_numeric($this->$numeric_field)) {
                $this->add_error(FieldValidationErrorFactory::numeric_field_required(array('field' => $numeric_field)), $numeric_field);
                $valid = false;
            }
        }
        return $valid;
    }

    /**
     * Return errors, instances of AsesError if at least one error exists, emtpy array otherwise
     * @see AsesError
     * @return array
     */
    public function get_errors(): array {
        return $this->_errors;
    }
    /**
     * Return errors array, AsesError agrouped under the object fields or generic errors
     * @see AsesError
     * @return array
     */
    public function get_errors_array(): array {
        return $this->_errors_array;
    }
    /**
     * Overload this function in the child classes if this have numeric
     * fields, otherwise is not necesary this definition
     *
     * Return the object attributes than should be numeric, if the class
     * does not have any numeric attributes return empty array
     * @return array Array of string than represents the column names where
     * the column is type int, double or bigint and the current value is not
     */
    public function get_numeric_fields(): array {
        return array();
    }

    /**
     * Overload this function in the child classes if this have required
     * fields, otherwise is not necesary this definition
     *
     * Return the object attributes than should be required, if the class
     * does not have any required attributes return empty array
     * @return array Array of string than represents the column names where
     * the column required and the current value is empty
     */
    public function get_required_fields(): array {
        return array();
    }


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
     * @return array|object Object instance if exists in database, empty array if does not exist
     * @throws dml_exception
     * @throws ErrorException If the given conditions specify invalid column names throws an error
     *
     */
    public static function get_by($conditions,  $sort='',  $limitfrom=0, $limitnum=0) {
        global $DB;
        $CLASS = get_called_class();
        if(!$CLASS::valid_conditions($conditions)) {
            throw new ErrorException("The given columns for conditions array are invalid, active debug mode for show de debug backtrace");
        }
        $db_records = $DB->get_records($CLASS::get_table_name(), $conditions, $sort, '*', $limitfrom, $limitnum );
        if( count($db_records)==1 ) {
            $db_record = reset($db_records);
            return new $CLASS($db_record);
        }
        if( count($db_records)>0 ) {
            return $CLASS::make_objects_from_std_objects_or_arrays($db_records);
        }

        return array();

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
     * @param array $conditions Key value array where the keys are the column names and the values are some
     * (in this method the values does not matter, only check the keys)
     * @return bool True if the condition columns are valid
     * @see https://docs.moodle.org/dev/Data_manipulation_API array conditions is common used and here conditions array
     * is the same
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
