<?php
/**
 * Created by PhpStorm.
 * User: sistemasases
 * Date: 9/10/18
 * Time: 02:06 PM
 */

require_once (__DIR__.'/../AsesErrorFactory.php');
require_once (__DIR__.'/../AsesError.php');


/**
 * Class RegistryNotFoundData
 * @property string $table Table name where is searching
 * @property array $conditions Conditions for select
 */
class RegistryNotFoundData {

    public $table;
    public $conditions;
}

class DatabaseErrorFactory extends AsesErrorFactory
{
    const UNIQUE_KEY_CONSTRAINT_VIOLATION = 51;
    const REGISTRY_NOT_FOUND = 52;
    public static function unique_key_constraint_violation($data = null): AsesError {
        return new AsesError(
            DatabaseErrorFactory::UNIQUE_KEY_CONSTRAINT_VIOLATION,
            "Se viola la llave unica establecida para la tabla en la base de datos ",
            $data);
    }

    /**
     * Build the error 'Not found in database' for the cases where if an registry does not exist
     * an error should be returned
     *
     * Alternatively, data can contain a field with the table name where is search and the conditions reached
     *
     * Example of data:
     * $data->table = 'talentospilos_estado_icetex'
     * $data->conditions = ''
     *
     * @param stdClass|RegistryNotFoundData $data
     * @return AsesError
     */
    public static function registry_not_found($custom_message=null, $data = null): AsesError {
        $message = 'No se ha encontrado el registro';
        if(object_property_exists($data, 'table')) {
            $message .= " en la tabla $data->table";
        }
        if(object_property_exists($data, 'conditions')) {
            $conditions_string = implode(',', $data->conditions);
            $message .= " con las condiciones $conditions_string";
        }
        return new AsesError(
            DatabaseErrorFactory::REGISTRY_NOT_FOUND,
            $custom_message? $custom_message: $message,
            $data);
    }


}