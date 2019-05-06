<?php


require_once(__DIR__.'/../../../managers/lib/reflection.php');
/**
 * Return an object instances
 * @param array $std_objects_or_arrays Array of objects or Array of arrays
 * @param string $class_name
 * @return array
 * @throws ErrorException
 */
function _make_objects_from_std_objects_or_arrays($std_objects_or_arrays, $class_name) {
    $instances = array();
    foreach($std_objects_or_arrays as $stdObjectOrArray ) {

        array_push($instances, \reflection\make_from_std_object($stdObjectOrArray, $class_name));
    }
    return $instances;
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
function _valid_conditions($class_name, $conditions): bool {

    if(!$conditions){

        return true;
    }
    $condition_column_names = array_keys($conditions);

    $invalid_column_names = array_diff($condition_column_names, _get_column_names($class_name));
    $debug_backtrace = debug_backtrace();
    if(count($invalid_column_names) > 0) {
        $previous_function_name = $debug_backtrace[1]['function'];
        $invalid_column_names_string = implode(',', $invalid_column_names);
        trigger_error("The column names given are invalid, previous method called: $previous_function_name, columns invalid: ($invalid_column_names_string), called class: $class_name");
        return false;
    }
    return true;
}
/**
 * Return the column names of the DAO
 * @param string $rename new table name, and add this to the column names
 *  for example, if $rename  = 'u' return array ('u.id', 'u.'name'...)
 * @return array Array of strings than contains the column names
 * @throws ErrorException
 */
function _get_column_names($class_name, string $rename = null ): array {
    $column_names =  \reflection\get_properties($class_name);
    if ( $rename ) {
        foreach($column_names as &$column_name) {
            $column_name = "$rename.".$column_name;
        }
    }
    return $column_names;
}

function return_without_empty_properties($object) {
    $result_object = new \stdClass();
    foreach ($object as $key => $value) {
        if ($value) {
            $result_object->$key = $value;
        }
    }
    return $result_object;
}

function normalize_table_name($table_name_or_instance, $plugin_tables_prefix): string {
    global $talentospilos_classes;
    if(is_string($table_name_or_instance)){
        $table_name = $table_name_or_instance;
    } else {
        $table_name = get_class($table_name_or_instance);
    }
    if(in_array($table_name, $talentospilos_classes)) {
        return $plugin_tables_prefix.'_'.$table_name;

    } else {
        return $table_name;
    }
}

function normalize_class_name($instance_or_class_name) {
    if (is_string($instance_or_class_name)) {
       return $instance_or_class_name;
    } else if(is_object($instance_or_class_name)) {
        return get_class($instance_or_class_name);
    } else {
        throw new ErrorException("Debe ingresar un string o una intancia de clase");
    }
}

