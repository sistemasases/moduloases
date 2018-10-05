<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 5/10/18
 * Time: 03:11 PM
 */
require_once (__DIR__.'/../AsesErrorFactory.php');
require_once (__DIR__.'/../AsesError.php');
class FieldValidationErrorFactory extends AsesErrorFactory
{
    const REQUIRED_FIELD = 10;
    const NUMERIC_FIELD = 11;

    public static function required_field_is_empty($data = null): AsesError {
        return new AsesError(FieldValidationErrorFactory::REQUIRED_FIELD, "El campo es requerido", $data);
    }

    public static function numeric_field_required($data = null): AsesError {
        return new AsesError(FieldValidationErrorFactory::NUMERIC_FIELD, 'El campo debe ser númerico', $data);
    }


}