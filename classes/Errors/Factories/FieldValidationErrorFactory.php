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
    const NOT_NULL_FIELD = 12;
    /**
     * @param array|null $data At least should be have a field named 'field'
     * @return AsesError
     */
    public static function required_field_is_empty($data = null): AsesError {
        $field = '';
        if($data['field']) {
            $field = $data['field'];
        }
        return new AsesError(FieldValidationErrorFactory::REQUIRED_FIELD, "El campo '$field' es requerido", $data);
    }
    /**
     * @param array|null $data At least should be have a field named 'field'
     * @return AsesError
     */
    public static function numeric_field_required($data = null): AsesError {
        $field = '';
        if($data['field']) {
            $field = $data['field'];
        }
        return new AsesError(FieldValidationErrorFactory::NUMERIC_FIELD, "El campo '$field' debe ser númerico", $data);
    }
    /**
     * @param array|null $data At least should be have a field named 'field'
     * @return AsesError
     */
    public static function not_null_field($data = null): AsesError {
        $field = '';
        if($data['field']) {
            $field = $data['field'];
        }
        return new AsesError(FieldValidationErrorFactory::NOT_NULL_FIELD, "El campo '$field' no puede ser nulo", $data);
    }


}