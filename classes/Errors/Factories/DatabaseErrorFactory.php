<?php
/**
 * Created by PhpStorm.
 * User: sistemasases
 * Date: 9/10/18
 * Time: 02:06 PM
 */

require_once (__DIR__.'/../AsesErrorFactory.php');
require_once (__DIR__.'/../AsesError.php');
class DatabaseErrorFactory extends AsesErrorFactory
{
    const UNIQUE_KEY_CONSTRAINT_VIOLATION = 51;

    public static function unique_key_constraint_violation($data = null): AsesError {
        return new AsesError(DatabaseErrorFactory::UNIQUE_KEY_CONSTRAINT_VIOLATION, "Se viola la llave unica establecida para la base de datos ", $data);
    }


}