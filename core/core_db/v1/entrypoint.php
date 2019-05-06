<?php

namespace core_db;
use function call_user_func_array;
use function get_class;
use function in_array;

const SELECT = 'select';
const EXECUTE = 'execute';
const SAVE = 'save';
const COUNT = 'count';
const OPTIONS = 'options';
const UPDATE = 'update';
const EXISTS = 'exists';
const SELECT_ONE = 'select_one';
const PLUGIN_TABLES_PREFIX = 'talentospilos';
$talentospilos_classes = array(
    'usuario',
    'user_extended',
    'programa'
);
$functs_than_need_table_rename = [
    SELECT,
    EXISTS,
    UPDATE,
    COUNT,
    SAVE,
    OPTIONS,
];
require_once (__DIR__.'./../classes/exports.php');
require_once (__DIR__.'/lib.php');
/**
 * Get all elements from database converted to object instances
 *
 * **If only some fields are returned, the objects returned are stdObjects, no object instances**
 *
 * @param string $class_name Class name of returned objects
 * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default
 *   all fields are returned). The first field will be used as key for the
 *   array so must be a unique field such as 'id'.
 * @param int $limitfrom return a subset of records, starting at this point (optional).
 * @param int $limitnum return a subset comprising this many records in total (optional, required if $limitfrom is set).
 * @return array An array of Objects indexed by first column.
 * @throws \dml_exception A DML specific exception is thrown for any errors.
 * @throws \ErrorException
 */
function select($class_name, array $conditions = null, $fields = '*', $sort = null, $limitfrom=0, $limitnum=0 ) {
    global $DB, $talentospilos_classes;
    if(in_array($class_name, $talentospilos_classes) && !_valid_conditions($class_name, $conditions)) {
        throw new \ErrorException("The given columns for conditions array are invalid, active debug mode for show de debug backtrace");
    }
    $table_name = normalize_table_name($class_name);
    $objects_array = $DB->get_records($table_name, $conditions, $sort, $fields, $limitfrom, $limitnum);
    if($fields==='*'){
        $objects = _make_objects_from_std_objects_or_arrays($objects_array, $class_name);
        return $objects;

    }
    return $objects_array;
}
function select_sql($sql, $params = array()) {
    global $DB;
    return $DB->get_records_sql($sql, $params);
}
/**
 * Return one object instance from database than satisfy the conditions given
 *
 * @param $class_name string Name of the class of the instances to be returned
 * @param $conditions array key-value than gets the conditions for get the objects
 * @example $conditions =  array('username'=> 'Camilo', 'lastname'=> 'Cifuentes')
 * @example $conditions = array(AsesUser::USER_NAME => 'Camilo', AsesUser::LAST_NAME => 'Cifuentes')
 * @see https://docs.moodle.org/dev/Data_manipulation_API
 * @return false|Object Object instance if exists in database, empty array if does not exist
 * @throws \dml_exception
 * @throws \ErrorException If the given conditions specify invalid column names throws an error
 *
 */
function select_one($class_name, $conditions=[], $fields='*') {
    global $DB, $talentospilos_classes;
    $table_name = normalize_table_name($class_name);
    if(in_array($class_name, $talentospilos_classes) && !_valid_conditions($class_name, $conditions)) {
        throw new \ErrorException("The given columns for conditions array are invalid, active debug mode for show de debug backtrace");
    }
    $db_record = $DB->get_record($table_name, $conditions, $fields );
    if(!$db_record){
        return false;
    }
    return new $class_name($db_record);
}
/**
 * Executes a general sql query. Should be used only when no other method suitable.
 * Do NOT use this to make changes in db structure, use database_manager methods instead!
 * @param string $sql query
 * @param array $params query parameters
 * @return bool true
 * @throws \dml_exception A DML specific exception is thrown for any errors.
 */
function execute($sql, array $params) {
    global $DB;
    return $DB->execute($sql, $params);
}

/**
 * Check if object exists in database based in an array of a  given conditions
 * @param $class_name string Class name to check if record exists
 * @param $conditions array key-value array than gets the conditions for get the objects
 * @example $conditions =  array('username'=> 'Camilo', 'lastname'=> 'Cifuentes')
 * @example $conditions = array(AsesUser::USER_NAME => 'Camilo', AsesUser::LAST_NAME => 'Cifuentes')
 * @return bool
 * @throws \dml_exception
 */
function exists($class_name, $conditions): bool  {

    global $DB, $talentospilos_classes;
    $table_name = normalize_table_name($class_name);
    if (in_array($class_name, $talentospilos_classes) && !_valid_conditions($class_name, $conditions)) {
        throw new \ErrorException("Las condiciones dadas no son validas con respecto a la clase $class_name");
    }
    return $DB->record_exists($table_name, $conditions);
}


/**
 * Return a key-value array based in the database objects from database
 * The key and value are representated by param $fields
 *
 * @param string $fields a comma separated list of fields to return - the number of fields should be 2!
 * @param $conditions array key-value array than gets the conditions for get the objects
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 *   all fields are returned). The first field will be used as key for the
 *   array so must be a unique field such as 'id'.
 * @param int $limitfrom return a subset of records, starting at this point (optional).
 * @param int $limitnum return a subset comprising this many records in total (optional, required if $limitfrom is set).
 * @example $conditions =  array('username'=> 'Camilo', 'lastname'=> 'Cifuentes')
 * @example $conditions = array(AsesUser::USER_NAME => 'Camilo', AsesUser::LAST_NAME => 'Cifuentes')
 * @see https://docs.moodle.org/dev/Data_manipulation_API
 * @return array Object instance if exists in database, empty array if does not exist
 * @throws \dml_exception
 * @throws \ErrorException If the given conditions specify invalid column names throws an error
 * @return array Key-value array
 */
function options($class_name, $fields='*', $conditions=null, $sort = '', $limitfrom=0, $limitnum=0) {
    global $DB, $talentospilos_classes;
    $table_name = normalize_table_name($class_name);
    if(in_array($class_name, $talentospilos_classes) && $conditions && _valid_conditions($class_name, $conditions)) {
        throw new \ErrorException("The given columns for conditions array are invalid, active debug mode for show de debug backtrace");
    }
    return $DB->get_records_menu($table_name, $conditions, $sort, $fields, $limitfrom, $limitnum );
}


/**
 * Save object to database
 * @return false|int id if was sucessfull created return id false otherwise
 * @throws \dml_exception A DML specific exception is thrown for any errors.
 */
function save($instance) {
    global $DB;
    $CLASS = get_class($instance);
    $table_name = normalize_table_name($CLASS);
    $record_id =  $DB->insert_record($table_name, $instance );
    if(property_exists($instance, 'id')) {
        $instance->id = $record_id;
    }
    return $record_id;
}
function update($instance) {
    global $DB;
    $CLASS = get_class($instance);
    if(!property_exists($instance, 'id')) {
        return false;
    } else {
        return $DB->update_record($CLASS, $instance);
    }
}

/**
 * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
 * @return int The count of records returned from the specified criteria.
 * @throws \dml_exception A DML specific exception is thrown for any errors.
 */
function count($class_name, $conditions=null) {
    global $DB;
    return  $DB->count_records($class_name, $conditions);
}



function normalize_table_name(string $table_name): string {
    global $talentospilos_classes;
    if(in_array($table_name, $talentospilos_classes)) {
        return PLUGIN_TABLES_PREFIX.'_'.$table_name;

    } else {
        return $table_name;
    }
}
function call_function($func_name, ...$args) {

    global $functs_than_need_table_rename;
    return call_user_func_array(__NAMESPACE__ . "\\" . $func_name, $args);
}
